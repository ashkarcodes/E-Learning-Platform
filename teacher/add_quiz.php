<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];
$course_id = (int) ($_GET['course_id'] ?? $_POST['course_id'] ?? 0);

$course = $conn->query("SELECT * FROM courses WHERE course_id=$course_id AND teacher_id=$tid")->fetch_assoc();
if (!$course) {
    set_message("Class not found.", "danger");
    header("Location: manage_classes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($conn, $_POST['title']);
    $show_result = isset($_POST['show_result']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO quizzes (course_id, title, show_result_immediately) VALUES (?,?,?)");
    $stmt->bind_param("isi", $course_id, $title, $show_result);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    set_message("Quiz created. Now add questions.");
    header("Location: add_question.php?quiz_id=$quiz_id");
    exit();
}

$page_title = "Create Quiz";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Create Quiz for: <?php echo htmlspecialchars($course['title']); ?></h1>
<div class="card">
    <form method="POST">
        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
        <div><label>Quiz Title</label><input type="text" name="title" required></div>
        <div style="flex-direction:row;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="show_result" id="show_result" checked style="width:auto;">
            <label for="show_result" style="font-weight:400;">Show results to students immediately after submission</label>
        </div>
        <button type="submit">Create & Add Questions</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
