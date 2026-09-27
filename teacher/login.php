<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['teacher_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT teacher_id, name, password, status FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();
        if ($teacher['status'] === 'blocked') {
            set_message("Your account has been blocked by the admin.", "danger");
            header("Location: login.php");
            exit();
        }
        if (password_verify($password, $teacher['password'])) {
            $_SESSION['teacher_id'] = $teacher['teacher_id'];
            $_SESSION['teacher_name'] = $teacher['name'];
            header("Location: dashboard.php");
            exit();
        }
    }
    set_message("Invalid email or password.", "danger");
    header("Location: login.php");
    exit();
}

$page_title = "Teacher Login";
$asset_path = "../";
include '../includes/header.php';
?>

<div class="auth-box">
    <h2>Teacher Login</h2>
    <?php show_message(); ?>
    <form method="POST">
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Password</label><input type="password" name="password" required></div>
        <button type="submit">Login</button>
    </form>
    <p style="margin-top:14px;font-size:0.85rem;color:var(--muted)">
        Teacher accounts are created by the Admin.
    </p>
</div>

<?php include '../includes/footer.php'; ?>
