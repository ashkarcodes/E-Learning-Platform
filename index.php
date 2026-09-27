<?php
require_once 'config/db.php';
$page_title = "Home";
$asset_path = "";
include 'includes/header.php';
?>

<div class="landing-hero">
    <h1>Learn Anything, Anytime.</h1>
    <p>A simple platform where teachers publish classes & quizzes, and students learn and get evaluated.</p>

    <div class="role-cards">
        <div class="role-card">
            <h3>🛠 Admin</h3>
            <p>Manage teachers, courses & platform activity.</p>
            <a class="btn" href="admin/login.php">Admin Login</a>
        </div>
        <div class="role-card">
            <h3>🎓 Teacher</h3>
            <p>Publish classes, notes, videos & quizzes.</p>
            <a class="btn" href="teacher/login.php">Teacher Login</a>
        </div>
        <div class="role-card">
            <h3>🧑‍🎓 Student</h3>
            <p>Learn from classes and attempt quizzes.</p>
            <a class="btn" href="student/login.php">Student Login</a>
            <a class="btn btn-secondary" href="student/register.php">Register</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
