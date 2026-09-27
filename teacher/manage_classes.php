<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    // Ensure the course belongs to this teacher before modifying
    $owns = $conn->query("SELECT course_id FROM courses WHERE course_id=$id AND teacher_id=$tid");
    if ($owns->num_rows === 1) {
        if ($_GET['action'] === 'delete') {
            $conn->query("DELETE FROM courses WHERE course_id=$id");
            set_message("Class deleted.");
        }
    }
    header("Location: manage_classes.php");
    exit();
}

$courses = $conn->query("
    SELECT c.*,
    (SELECT COUNT(*) FROM materials m WHERE m.course_id=c.course_id) AS material_count,
    (SELECT COUNT(*) FROM quizzes q WHERE q.course_id=c.course_id) AS quiz_count
    FROM courses c WHERE c.teacher_id=$tid ORDER BY c.created_at DESC
");

$page_title = "Manage Classes";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>My Classes</h1>
<?php show_message(); ?>
<a class="btn" href="add_class.php">+ Create Class</a>

<div class="card" style="margin-top:18px;">
<table>
<tr><th>Title</th><th>Materials</th><th>Quizzes</th><th>Status</th><th>Actions</th></tr>
<?php while ($c = $courses->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($c['title']); ?></td>
    <td><?php echo $c['material_count']; ?></td>
    <td><?php echo $c['quiz_count']; ?></td>
    <td><span class="badge badge-<?php echo $c['status']==='active'?'active':'blocked'; ?>"><?php echo ucfirst($c['status']); ?></span></td>
    <td class="action-links">
        <a href="upload_material.php?course_id=<?php echo $c['course_id']; ?>">Upload Material</a>
        <a href="manage_quiz.php?course_id=<?php echo $c['course_id']; ?>">Quizzes</a>
        <a class="confirm-action" data-confirm="Delete this class and all its content?" href="?action=delete&id=<?php echo $c['course_id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
