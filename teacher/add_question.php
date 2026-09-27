<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];
$quiz_id = (int) ($_GET['quiz_id'] ?? $_POST['quiz_id'] ?? 0);

$quiz = $conn->query("
    SELECT q.*, c.title AS course_title, c.teacher_id FROM quizzes q
    JOIN courses c ON q.course_id = c.course_id
    WHERE q.quiz_id=$quiz_id AND c.teacher_id=$tid
")->fetch_assoc();

if (!$quiz) {
    set_message("Quiz not found.", "danger");
    header("Location: manage_classes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = clean($conn, $_POST['question_text']);
    $options = $_POST['options'];        // array of option text
    $correct = (int) $_POST['correct'];  // index of correct option

    $stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text) VALUES (?,?)");
    $stmt->bind_param("is", $quiz_id, $question_text);
    $stmt->execute();
    $question_id = $stmt->insert_id;

    foreach ($options as $i => $opt_text) {
        $opt_text = clean($conn, $opt_text);
        if (trim($opt_text) === '') continue;
        $is_correct = ($i == $correct) ? 1 : 0;
        $ostmt = $conn->prepare("INSERT INTO quiz_options (question_id, option_text, is_correct) VALUES (?,?,?)");
        $ostmt->bind_param("isi", $question_id, $opt_text, $is_correct);
        $ostmt->execute();
    }

    set_message("Question added.");
    header("Location: add_question.php?quiz_id=$quiz_id");
    exit();
}

// Existing questions
$questions = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id=$quiz_id");

$page_title = "Add Questions";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Questions for: <?php echo htmlspecialchars($quiz['title']); ?></h1>
<p style="color:var(--muted)">Class: <?php echo htmlspecialchars($quiz['course_title']); ?></p>
<?php show_message(); ?>

<div class="card">
    <h3>Add a Question</h3>
    <form method="POST" class="wide">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <div><label>Question</label><textarea name="question_text" required></textarea></div>

        <div class="option-row"><input type="radio" name="correct" value="0" required><input type="text" name="options[]" placeholder="Option A" required></div>
        <div class="option-row"><input type="radio" name="correct" value="1"><input type="text" name="options[]" placeholder="Option B" required></div>
        <div class="option-row"><input type="radio" name="correct" value="2"><input type="text" name="options[]" placeholder="Option C (optional)"></div>
        <div class="option-row"><input type="radio" name="correct" value="3"><input type="text" name="options[]" placeholder="Option D (optional)"></div>
        <small style="color:var(--muted)">Select the radio button next to the correct answer.</small>
        <button type="submit">Add Question</button>
    </form>
</div>

<div class="card">
    <h3>Existing Questions (<?php echo $questions->num_rows; ?>)</h3>
    <?php while ($q = $questions->fetch_assoc()):
        $opts = $conn->query("SELECT * FROM quiz_options WHERE question_id=" . $q['question_id']);
    ?>
    <div class="quiz-question">
        <strong><?php echo htmlspecialchars($q['question_text']); ?></strong>
        <ul>
        <?php while ($o = $opts->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($o['option_text']); ?> <?php echo $o['is_correct'] ? '✅' : ''; ?></li>
        <?php endwhile; ?>
        </ul>
    </div>
    <?php endwhile; ?>
</div>

<a class="btn btn-secondary" href="manage_quiz.php?course_id=<?php echo $quiz['course_id']; ?>">Done — Back to Quizzes</a>

<?php include '../includes/footer.php'; ?>
