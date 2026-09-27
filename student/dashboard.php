<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$sid = $_SESSION['student_id'];

$courses_engaged = $conn->query("SELECT COUNT(DISTINCT course_id) c FROM activity_log WHERE student_id=$sid")->fetch_assoc()['c'];
$quizzes_taken = $conn->query("SELECT COUNT(*) c FROM quiz_attempts WHERE student_id=$sid")->fetch_assoc()['c'];
$avg_score = $conn->query("SELECT AVG(score/total_questions)*100 avg_pct FROM quiz_attempts WHERE student_id=$sid AND total_questions > 0")->fetch_assoc()['avg_pct'];

$page_title = "Student Dashboard";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Student Dashboard</h1>
<?php show_message(); ?>

<div class="grid">
    <div class="stat-box"><div class="number"><?php echo $courses_engaged; ?></div><div class="label">Classes Engaged</div></div>
    <div class="stat-box"><div class="number"><?php echo $quizzes_taken; ?></div><div class="label">Quizzes Attempted</div></div>
    <div class="stat-box"><div class="number"><?php echo $avg_score ? round($avg_score) . '%' : 'N/A'; ?></div><div class="label">Average Score</div></div>
</div>

<div class="card" style="margin-top:24px;">
    <h3>Quick Links</h3>
    <div class="action-links">
        <a class="btn" href="browse_classes.php">Browse Classes</a>
        <a class="btn btn-secondary" href="view_results.php">My Quiz Results</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
