<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];

$attempts = $conn->query("
    SELECT qa.*, s.name AS student_name, s.email, q.title AS quiz_title, c.title AS course_title
    FROM quiz_attempts qa
    JOIN students s ON qa.student_id = s.student_id
    JOIN quizzes q ON qa.quiz_id = q.quiz_id
    JOIN courses c ON q.course_id = c.course_id
    WHERE c.teacher_id = $tid
    ORDER BY qa.attempted_at DESC
");

$page_title = "Student Performance";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Student Performance</h1>

<div class="card">
<table>
<tr><th>Student</th><th>Class</th><th>Quiz</th><th>Score</th><th>Attempted On</th></tr>
<?php while ($a = $attempts->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($a['student_name']); ?><br><small style="color:var(--muted)"><?php echo htmlspecialchars($a['email']); ?></small></td>
    <td><?php echo htmlspecialchars($a['course_title']); ?></td>
    <td><?php echo htmlspecialchars($a['quiz_title']); ?></td>
    <td><?php echo $a['score']; ?> / <?php echo $a['total_questions']; ?></td>
    <td><?php echo date('d M Y, h:i A', strtotime($a['attempted_at'])); ?></td>
</tr>
<?php endwhile; ?>
<?php if ($attempts->num_rows === 0): ?>
<tr><td colspan="5" style="text-align:center;color:var(--muted)">No quiz attempts yet.</td></tr>
<?php endif; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
