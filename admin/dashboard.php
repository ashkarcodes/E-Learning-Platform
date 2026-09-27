<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$teacher_count = $conn->query("SELECT COUNT(*) c FROM teachers")->fetch_assoc()['c'];
$student_count = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'];
$course_count  = $conn->query("SELECT COUNT(*) c FROM courses WHERE status != 'removed'")->fetch_assoc()['c'];
$quiz_count    = $conn->query("SELECT COUNT(*) c FROM quizzes")->fetch_assoc()['c'];

$page_title = "Admin Dashboard";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Admin Dashboard</h1>
<?php show_message(); ?>

<div class="grid">
    <div class="stat-box"><div class="number"><?php echo $teacher_count; ?></div><div class="label">Teachers</div></div>
    <div class="stat-box"><div class="number"><?php echo $student_count; ?></div><div class="label">Students</div></div>
    <div class="stat-box"><div class="number"><?php echo $course_count; ?></div><div class="label">Active Courses</div></div>
    <div class="stat-box"><div class="number"><?php echo $quiz_count; ?></div><div class="label">Quizzes</div></div>
</div>

<div class="card" style="margin-top:24px;">
    <h3>Quick Actions</h3>
    <div class="action-links">
        <a class="btn" href="add_teacher.php">+ Add Teacher</a>
        <a class="btn btn-secondary" href="manage_teachers.php">Manage Teachers</a>
        <a class="btn btn-secondary" href="manage_courses.php">Manage Courses</a>
        <a class="btn btn-secondary" href="manage_students.php">Manage Students</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
