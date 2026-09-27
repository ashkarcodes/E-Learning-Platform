<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($conn, $_POST['name']);
    $email = clean($conn, $_POST['email']);
    $subject = clean($conn, $_POST['subject_expertise']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT teacher_id FROM teachers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        set_message("A teacher with this email already exists.", "danger");
        header("Location: add_teacher.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO teachers (name, email, password, subject_expertise, added_by) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssi", $name, $email, $password, $subject, $_SESSION['admin_id']);
    $stmt->execute();

    set_message("Teacher added successfully.");
    header("Location: manage_teachers.php");
    exit();
}

$page_title = "Add Teacher";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Add New Teacher</h1>
<div class="card">
    <form method="POST">
        <div><label>Full Name</label><input type="text" name="name" required></div>
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Subject Expertise</label><input type="text" name="subject_expertise" placeholder="e.g. Mathematics"></div>
        <div><label>Temporary Password</label><input type="password" name="password" required></div>
        <button type="submit">Add Teacher</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
