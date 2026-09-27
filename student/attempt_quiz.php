<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$sid = $_SESSION['student_id'];
$quiz_id = (int) ($_GET['quiz_id'] ?? 0);

$quiz = $conn->query("SELECT q.*, c.title AS course_title FROM quizzes q JOIN courses c ON q.course_id=c.course_id WHERE q.quiz_id=$quiz_id")->fetch_assoc();
if (!$quiz) {
    set_message("Quiz not found.", "danger");
    header("Location: browse_classes.php");
    exit();
}

// Prevent re-attempting
$already = $conn->query("SELECT * FROM quiz_attempts WHERE quiz_id=$quiz_id AND student_id=$sid")->fetch_assoc();
if ($already) {
    header("Location: view_results.php?attempt_id=" . $already['attempt_id']);
    exit();
}

$questions = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id=$quiz_id");

$page_title = "Attempt Quiz";
$asset_path = "../";
include '../includes/header.php';
?>

<h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
<p style="color:var(--muted)">Class: <?php echo htmlspecialchars($quiz['course_title']); ?></p>

<form method="POST" action="submit_quiz.php" id="quizForm" class="wide">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
    <?php while ($q = $questions->fetch_assoc()):
        $opts = $conn->query("SELECT * FROM quiz_options WHERE question_id=" . $q['question_id']);
    ?>
    <div class="card">
        <strong><?php echo htmlspecialchars($q['question_text']); ?></strong>
        <?php while ($o = $opts->fetch_assoc()): ?>
            <div class="option-row">
                <input type="radio" name="answers[<?php echo $q['question_id']; ?>]" value="<?php echo $o['option_id']; ?>" required>
                <label style="font-weight:400;"><?php echo htmlspecialchars($o['option_text']); ?></label>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endwhile; ?>
    <?php if ($questions->num_rows === 0): ?>
        <p style="color:var(--muted)">This quiz has no questions yet.</p>
    <?php else: ?>
        <button type="submit">Submit Quiz</button>
    <?php endif; ?>
</form>

<?php include '../includes/footer.php'; ?>
