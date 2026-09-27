<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$sid = $_SESSION['student_id'];
$page_title = "Quiz Results";
$asset_path = "../";

// ---- Single attempt detail view ----
if (isset($_GET['attempt_id'])) {
    $attempt_id = (int) $_GET['attempt_id'];
    $attempt = $conn->query("
        SELECT qa.*, q.title AS quiz_title, q.show_result_immediately, c.title AS course_title
        FROM quiz_attempts qa
        JOIN quizzes q ON qa.quiz_id = q.quiz_id
        JOIN courses c ON q.course_id = c.course_id
        WHERE qa.attempt_id=$attempt_id AND qa.student_id=$sid
    ")->fetch_assoc();

    if (!$attempt) {
        set_message("Result not found.", "danger");
        header("Location: view_results.php");
        exit();
    }

    include '../includes/header.php';
    ?>
    <h1>Result: <?php echo htmlspecialchars($attempt['quiz_title']); ?></h1>
    <p style="color:var(--muted)">Class: <?php echo htmlspecialchars($attempt['course_title']); ?></p>
    <?php show_message(); ?>

    <?php if ($attempt['show_result_immediately']): ?>
        <div class="card">
            <h2>Score: <?php echo $attempt['score']; ?> / <?php echo $attempt['total_questions']; ?></h2>
            <p><?php echo round(($attempt['score'] / max($attempt['total_questions'],1)) * 100); ?>% correct</p>
        </div>

        <?php
        $answers = $conn->query("
            SELECT qa.*, qq.question_text, qo.option_text AS selected_text
            FROM quiz_answers qa
            JOIN quiz_questions qq ON qa.question_id = qq.question_id
            LEFT JOIN quiz_options qo ON qa.selected_option_id = qo.option_id
            WHERE qa.attempt_id=$attempt_id
        ");
        ?>
        <div class="card">
            <h3>Answer Review</h3>
            <?php while ($a = $answers->fetch_assoc()): ?>
                <div class="quiz-question">
                    <strong><?php echo htmlspecialchars($a['question_text']); ?></strong>
                    <p>Your answer: <?php echo htmlspecialchars($a['selected_text'] ?? 'No answer'); ?>
                        <?php echo $a['is_correct'] ? '✅ Correct' : '❌ Incorrect'; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <p>Your quiz has been submitted. Results will be released by your teacher.</p>
        </div>
    <?php endif; ?>

    <a class="btn btn-secondary" href="view_results.php">← All Results</a>
    <?php
    include '../includes/footer.php';
    exit();
}

// ---- All attempts list ----
$attempts = $conn->query("
    SELECT qa.*, q.title AS quiz_title, c.title AS course_title
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.quiz_id
    JOIN courses c ON q.course_id = c.course_id
    WHERE qa.student_id=$sid
    ORDER BY qa.attempted_at DESC
");

include '../includes/header.php';
?>

<h1>My Quiz Results</h1>

<div class="card">
<table>
<tr><th>Class</th><th>Quiz</th><th>Score</th><th>Date</th><th></th></tr>
<?php while ($a = $attempts->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($a['course_title']); ?></td>
    <td><?php echo htmlspecialchars($a['quiz_title']); ?></td>
    <td><?php echo $a['score']; ?> / <?php echo $a['total_questions']; ?></td>
    <td><?php echo date('d M Y', strtotime($a['attempted_at'])); ?></td>
    <td><a href="?attempt_id=<?php echo $a['attempt_id']; ?>">View</a></td>
</tr>
<?php endwhile; ?>
<?php if ($attempts->num_rows === 0): ?>
<tr><td colspan="5" style="text-align:center;color:var(--muted)">No quizzes attempted yet.</td></tr>
<?php endif; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
