<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_teacher();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($conn, $_POST['title']);
    $description = clean($conn, $_POST['description']);

    $stmt = $conn->prepare("INSERT INTO courses (teacher_id, title, description) VALUES (?,?,?)");
    $stmt->bind_param("iss", $_SESSION['teacher_id'], $title, $description);
    $stmt->execute();

    set_message("Class created successfully.");
    header("Location: manage_classes.php");
    exit();
}

$page_title = "Create Class";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Create New Class</h1>
<div class="card">
    <form method="POST">
        <div><label>Class Title</label><input type="text" name="title" required></div>
        <div><label>Description</label><textarea name="description"></textarea></div>
        <button type="submit">Create Class</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
