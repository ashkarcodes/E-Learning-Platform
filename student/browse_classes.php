<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$courses = $conn->query("
    SELECT c.*, t.name AS teacher_name,
    (SELECT COUNT(*) FROM materials m WHERE m.course_id=c.course_id) AS material_count,
    (SELECT COUNT(*) FROM quizzes q WHERE q.course_id=c.course_id) AS quiz_count
    FROM courses c
    JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE c.status = 'active'
    ORDER BY c.created_at DESC
");

$page_title = "Browse Classes";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Browse Classes</h1>

<div class="grid">
<?php while ($c = $courses->fetch_assoc()): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($c['title']); ?></h3>
        <p style="color:var(--muted)"><?php echo htmlspecialchars($c['description']); ?></p>
        <p style="font-size:0.85rem;">Instructor: <?php echo htmlspecialchars($c['teacher_name']); ?></p>
        <p style="font-size:0.85rem;color:var(--muted)"><?php echo $c['material_count']; ?> materials · <?php echo $c['quiz_count']; ?> quizzes</p>
        <a class="btn" href="view_class.php?course_id=<?php echo $c['course_id']; ?>">Open Class</a>
    </div>
<?php endwhile; ?>
<?php if ($courses->num_rows === 0): ?>
    <p style="color:var(--muted)">No classes available yet. Check back soon.</p>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
