<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$sid = $_SESSION['student_id'];
$course_id = (int) ($_GET['course_id'] ?? 0);

$course = $conn->query("SELECT c.*, t.name AS teacher_name FROM courses c JOIN teachers t ON c.teacher_id=t.teacher_id WHERE c.course_id=$course_id AND c.status='active'")->fetch_assoc();

if (!$course) {
    set_message("Class not available.", "danger");
    header("Location: browse_classes.php");
    exit();
}

// Track that the student opened this class
log_activity($conn, $sid, $course_id, null, 'viewed_class');

// Track material view clicks
if (isset($_GET['view_material'])) {
    $mid = (int) $_GET['view_material'];
    log_activity($conn, $sid, $course_id, $mid, 'viewed_material');
}

$videos = $conn->query("SELECT * FROM materials WHERE course_id=$course_id AND type='video' ORDER BY uploaded_at");
$notes = $conn->query("SELECT * FROM materials WHERE course_id=$course_id AND type='note' ORDER BY uploaded_at");
$quizzes = $conn->query("SELECT * FROM quizzes WHERE course_id=$course_id ORDER BY created_at");

$page_title = $course['title'];
$asset_path = "../";
include '../includes/header.php';
?>

<h1><?php echo htmlspecialchars($course['title']); ?></h1>
<p style="color:var(--muted)"><?php echo htmlspecialchars($course['description']); ?> — Instructor: <?php echo htmlspecialchars($course['teacher_name']); ?></p>

<div class="card">
    <h3>🎥 Video Lectures</h3>
    <?php if ($videos->num_rows === 0): ?>
        <p style="color:var(--muted)">No videos uploaded yet.</p>
    <?php endif; ?>
    <?php while ($v = $videos->fetch_assoc()): ?>
        <div class="material-item">
            <?php echo htmlspecialchars($v['title']); ?>
            &mdash;
            <a href="../<?php echo htmlspecialchars($v['file_path']); ?>?view_material=<?php echo $v['material_id']; ?>"
               onclick="fetch('view_class.php?course_id=<?php echo $course_id; ?>&view_material=<?php echo $v['material_id']; ?>')"
               target="_blank">Watch</a>
        </div>
    <?php endwhile; ?>
</div>

<div class="card">
    <h3>📄 Notes & Study Materials</h3>
    <?php if ($notes->num_rows === 0): ?>
        <p style="color:var(--muted)">No notes uploaded yet.</p>
    <?php endif; ?>
    <?php while ($n = $notes->fetch_assoc()): ?>
        <div class="material-item">
            <?php echo htmlspecialchars($n['title']); ?>
            &mdash;
            <a href="../<?php echo htmlspecialchars($n['file_path']); ?>"
               onclick="fetch('view_class.php?course_id=<?php echo $course_id; ?>&view_material=<?php echo $n['material_id']; ?>')"
               target="_blank">View / Download</a>
        </div>
    <?php endwhile; ?>
</div>

<div class="card">
    <h3>📝 Quizzes</h3>
    <?php if ($quizzes->num_rows === 0): ?>
        <p style="color:var(--muted)">No quizzes available yet.</p>
    <?php endif; ?>
    <?php while ($q = $quizzes->fetch_assoc()):
        $attempted = $conn->query("SELECT * FROM quiz_attempts WHERE quiz_id={$q['quiz_id']} AND student_id=$sid")->fetch_assoc();
    ?>
        <div class="material-item">
            <?php echo htmlspecialchars($q['title']); ?>
            <?php if ($attempted): ?>
                — <span class="badge badge-active">Completed (<?php echo $attempted['score']; ?>/<?php echo $attempted['total_questions']; ?>)</span>
            <?php else: ?>
                — <a class="btn" href="attempt_quiz.php?quiz_id=<?php echo $q['quiz_id']; ?>">Attempt Quiz</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<a class="btn btn-secondary" href="browse_classes.php">← Back to Classes</a>

<?php include '../includes/footer.php'; ?>
