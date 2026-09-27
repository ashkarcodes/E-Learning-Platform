<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];
$course_id = (int) ($_GET['course_id'] ?? 0);

$course = $conn->query("SELECT * FROM courses WHERE course_id=$course_id AND teacher_id=$tid")->fetch_assoc();
if (!$course) {
    set_message("Class not found.", "danger");
    header("Location: manage_classes.php");
    exit();
}

if (isset($_GET['delete'])) {
    $qid = (int) $_GET['delete'];
    $conn->query("DELETE FROM quizzes WHERE quiz_id=$qid AND course_id=$course_id");
    set_message("Quiz deleted.");
    header("Location: manage_quiz.php?course_id=$course_id");
    exit();
}

$quizzes = $conn->query("
    SELECT q.*,
    (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id=q.quiz_id) AS question_count,
    (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.quiz_id) AS attempt_count
    FROM quizzes q WHERE q.course_id=$course_id ORDER BY q.created_at DESC
");

$page_title = "Manage Quizzes";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Quizzes for: <?php echo htmlspecialchars($course['title']); ?></h1>
<?php show_message(); ?>
<a class="btn" href="add_quiz.php?course_id=<?php echo $course_id; ?>">+ Create Quiz</a>

<div class="card" style="margin-top:18px;">
<table>
<tr><th>Title</th><th>Questions</th><th>Attempts</th><th>Actions</th></tr>
<?php while ($q = $quizzes->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($q['title']); ?></td>
    <td><?php echo $q['question_count']; ?></td>
    <td><?php echo $q['attempt_count']; ?></td>
    <td class="action-links">
        <a href="add_question.php?quiz_id=<?php echo $q['quiz_id']; ?>">Add Questions</a>
        <a class="confirm-action" data-confirm="Delete this quiz?" href="?course_id=<?php echo $course_id; ?>&delete=<?php echo $q['quiz_id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
