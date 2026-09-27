<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'hide') {
        $conn->query("UPDATE courses SET status='hidden' WHERE course_id=$id");
        set_message("Course hidden from students.");
    } elseif ($_GET['action'] === 'unhide') {
        $conn->query("UPDATE courses SET status='active' WHERE course_id=$id");
        set_message("Course made active again.");
    } elseif ($_GET['action'] === 'remove') {
        $conn->query("UPDATE courses SET status='removed' WHERE course_id=$id");
        set_message("Course removed for inappropriate content.");
    }
    header("Location: manage_courses.php");
    exit();
}

$courses = $conn->query("
    SELECT c.*, t.name AS teacher_name,
    (SELECT COUNT(*) FROM materials m WHERE m.course_id = c.course_id) AS material_count,
    (SELECT COUNT(*) FROM quizzes q WHERE q.course_id = c.course_id) AS quiz_count
    FROM courses c
    JOIN teachers t ON c.teacher_id = t.teacher_id
    ORDER BY c.created_at DESC
");

$page_title = "Manage Courses";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Monitor Courses</h1>
<?php show_message(); ?>

<div class="card">
<table>
<tr><th>Title</th><th>Teacher</th><th>Materials</th><th>Quizzes</th><th>Status</th><th>Actions</th></tr>
<?php while ($c = $courses->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($c['title']); ?></td>
    <td><?php echo htmlspecialchars($c['teacher_name']); ?></td>
    <td><?php echo $c['material_count']; ?></td>
    <td><?php echo $c['quiz_count']; ?></td>
    <td><span class="badge badge-<?php echo $c['status']==='active'?'active':'blocked'; ?>"><?php echo ucfirst($c['status']); ?></span></td>
    <td class="action-links">
        <?php if ($c['status'] === 'active'): ?>
            <a href="?action=hide&id=<?php echo $c['course_id']; ?>">Hide</a>
        <?php elseif ($c['status'] === 'hidden'): ?>
            <a href="?action=unhide&id=<?php echo $c['course_id']; ?>">Unhide</a>
        <?php endif; ?>
        <?php if ($c['status'] !== 'removed'): ?>
            <a class="confirm-action" data-confirm="Remove this course for inappropriate content?" href="?action=remove&id=<?php echo $c['course_id']; ?>">Remove</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
