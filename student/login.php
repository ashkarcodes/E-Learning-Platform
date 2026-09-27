<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT student_id, name, password, status FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        if ($student['status'] === 'blocked') {
            set_message("Your account has been blocked by the admin.", "danger");
            header("Location: login.php");
            exit();
        }
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['name'];
            header("Location: dashboard.php");
            exit();
        }
    }
    set_message("Invalid email or password.", "danger");
    header("Location: login.php");
    exit();
}

$page_title = "Student Login";
$asset_path = "../";
include '../includes/header.php';
?>

<div class="auth-box">
    <h2>Student Login</h2>
    <?php show_message(); ?>
    <form method="POST">
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Password</label><input type="password" name="password" required></div>
        <button type="submit">Login</button>
    </form>
    <p style="margin-top:14px;font-size:0.9rem;">No account? <a href="register.php">Register here</a></p>
</div>

<?php include '../includes/footer.php'; ?>
