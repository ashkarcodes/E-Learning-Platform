<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($conn, $_POST['name']);
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        set_message("Passwords do not match.", "danger");
        header("Location: register.php");
        exit();
    }

    $check = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        set_message("An account with this email already exists.", "danger");
        header("Location: register.php");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO students (name, email, password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $hashed);
    $stmt->execute();

    set_message("Registration successful. Please log in.");
    header("Location: login.php");
    exit();
}

$page_title = "Student Registration";
$asset_path = "../";
include '../includes/header.php';
?>

<div class="auth-box">
    <h2>Student Registration</h2>
    <?php show_message(); ?>
    <form method="POST">
        <div><label>Full Name</label><input type="text" name="name" required></div>
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Password</label><input type="password" name="password" required></div>
        <div><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
        <button type="submit">Register</button>
    </form>
    <p style="margin-top:14px;font-size:0.9rem;">Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include '../includes/footer.php'; ?>
