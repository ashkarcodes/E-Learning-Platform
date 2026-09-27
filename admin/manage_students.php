<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'block') {
        $conn->query("UPDATE students SET status='blocked' WHERE student_id=$id");
        set_message("Student blocked.");
    } elseif ($_GET['action'] === 'unblock') {
        $conn->query("UPDATE students SET status='active' WHERE student_id=$id");
        set_message("Student unblocked.");
    }
    header("Location: manage_students.php");
    exit();
}

$students = $conn->query("
    SELECT s.*,
    (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.student_id = s.student_id) AS quiz_attempts,
    (SELECT COUNT(DISTINCT al.course_id) FROM activity_log al WHERE al.student_id = s.student_id) AS courses_engaged
    FROM students s
    ORDER BY s.created_at DESC
");

$page_title = "Manage Students";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Monitor Students</h1>
<?php show_message(); ?>

<div class="card">
<table>
<tr><th>Name</th><th>Email</th><th>Courses Engaged</th><th>Quiz Attempts</th><th>Status</th><th>Actions</th></tr>
<?php while ($s = $students->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($s['name']); ?></td>
    <td><?php echo htmlspecialchars($s['email']); ?></td>
    <td><?php echo $s['courses_engaged']; ?></td>
    <td><?php echo $s['quiz_attempts']; ?></td>
    <td><span class="badge badge-<?php echo $s['status']; ?>"><?php echo ucfirst($s['status']); ?></span></td>
    <td class="action-links">
        <?php if ($s['status'] === 'active'): ?>
            <a href="?action=block&id=<?php echo $s['student_id']; ?>">Block</a>
        <?php else: ?>
            <a href="?action=unblock&id=<?php echo $s['student_id']; ?>">Unblock</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
