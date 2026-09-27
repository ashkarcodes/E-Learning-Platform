<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

$tid = $_SESSION['teacher_id'];
$course_id = (int) ($_GET['course_id'] ?? $_POST['course_id'] ?? 0);

// Verify ownership
$course = $conn->query("SELECT * FROM courses WHERE course_id=$course_id AND teacher_id=$tid")->fetch_assoc();
if (!$course) {
    set_message("Class not found.", "danger");
    header("Location: manage_classes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $type = $_POST['type'] === 'video' ? 'video' : 'note';
    $title = clean($conn, $_POST['title']);

    $target_dir = $type === 'video' ? '../uploads/videos/' : '../uploads/notes/';
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $safe_name = uniqid($type . '_') . '.' . $ext;
    $target_path = $target_dir . $safe_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        $db_path = "uploads/" . ($type === 'video' ? 'videos/' : 'notes/') . $safe_name;
        $stmt = $conn->prepare("INSERT INTO materials (course_id, type, title, file_path) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $course_id, $type, $title, $db_path);
        $stmt->execute();
        set_message("Material uploaded successfully.");
    } else {
        set_message("Upload failed. Please try again.", "danger");
    }
    header("Location: upload_material.php?course_id=$course_id");
    exit();
}

// Delete material
if (isset($_GET['delete'])) {
    $mid = (int) $_GET['delete'];
    $conn->query("DELETE FROM materials WHERE material_id=$mid AND course_id=$course_id");
    set_message("Material removed.");
    header("Location: upload_material.php?course_id=$course_id");
    exit();
}

$materials = $conn->query("SELECT * FROM materials WHERE course_id=$course_id ORDER BY uploaded_at DESC");

$page_title = "Upload Material";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Materials for: <?php echo htmlspecialchars($course['title']); ?></h1>
<?php show_message(); ?>

<div class="card">
    <h3>Upload New Material</h3>
    <form method="POST" enctype="multipart/form-data" class="wide">
        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
        <div><label>Title</label><input type="text" name="title" required></div>
        <div>
            <label>Type</label>
            <select name="type" required>
                <option value="video">Recorded Video</option>
                <option value="note">Text Notes / PDF</option>
            </select>
        </div>
        <div><label>File</label><input type="file" name="file" required></div>
        <button type="submit">Upload</button>
    </form>
</div>

<div class="card">
    <h3>Uploaded Materials</h3>
    <?php while ($m = $materials->fetch_assoc()): ?>
    <div class="material-item">
        <strong><?php echo htmlspecialchars($m['title']); ?></strong>
        (<?php echo strtoupper($m['type']); ?>)
        &mdash;
        <a href="../<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank">View</a>
        &mdash;
        <a class="confirm-action" data-confirm="Remove this material?" href="?course_id=<?php echo $course_id; ?>&delete=<?php echo $m['material_id']; ?>">Delete</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>
