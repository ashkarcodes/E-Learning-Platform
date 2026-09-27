<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];

$course_count = $conn->query("SELECT COUNT(*) c FROM courses WHERE teacher_id=$tid AND status!='removed'")->fetch_assoc()['c'];
$quiz_count = $conn->query("SELECT COUNT(*) c FROM quizzes q JOIN courses c ON q.course_id=c.course_id WHERE c.teacher_id=$tid")->fetch_assoc()['c'];
$material_count = $conn->query("SELECT COUNT(*) c FROM materials m JOIN courses c ON m.course_id=c.course_id WHERE c.teacher_id=$tid")->fetch_assoc()['c'];
$attempt_count = $conn->query("SELECT COUNT(*) c FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.quiz_id JOIN courses c ON q.course_id=c.course_id WHERE c.teacher_id=$tid")->fetch_assoc()['c'];

$page_title = "Teacher Dashboard";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Teacher Dashboard</h1>
<?php show_message(); ?>

<div class="grid">
    <div class="stat-box"><div class="number"><?php echo $course_count; ?></div><div class="label">My Classes</div></div>
    <div class="stat-box"><div class="number"><?php echo $material_count; ?></div><div class="label">Materials Uploaded</div></div>
    <div class="stat-box"><div class="number"><?php echo $quiz_count; ?></div><div class="label">Quizzes Created</div></div>
    <div class="stat-box"><div class="number"><?php echo $attempt_count; ?></div><div class="label">Quiz Attempts</div></div>
</div>

<div class="card" style="margin-top:24px;">
    <h3>Quick Actions</h3>
    <div class="action-links">
        <a class="btn" href="add_class.php">+ Create Class</a>
        <a class="btn btn-secondary" href="manage_classes.php">Manage Classes</a>
        <a class="btn btn-secondary" href="student_performance.php">Student Performance</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
