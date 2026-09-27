<?php
/**
 * Shared helper functions used across all modules.
 */

// ---------- Sanitization ----------
function clean($conn, $value) {
    return htmlspecialchars(trim($conn->real_escape_string($value)));
}

// ---------- Role guards ----------
function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php");
        exit();
    }
}

function require_teacher() {
    if (!isset($_SESSION['teacher_id'])) {
        header("Location: ../teacher/login.php");
        exit();
    }
}

function require_student() {
    if (!isset($_SESSION['student_id'])) {
        header("Location: ../student/login.php");
        exit();
    }
}

// ---------- Flash messages ----------
function set_message($msg, $type = 'success') {
    $_SESSION['flash_message'] = $msg;
    $_SESSION['flash_type'] = $type;
}

function show_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        echo '<div class="alert alert-' . $type . '">' . $_SESSION['flash_message'] . '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// ---------- Activity logging ----------
function log_activity($conn, $student_id, $course_id, $material_id, $action) {
    $stmt = $conn->prepare("INSERT INTO activity_log (student_id, course_id, material_id, action) VALUES (?,?,?,?)");
    $stmt->bind_param("iiis", $student_id, $course_id, $material_id, $action);
    $stmt->execute();
    $stmt->close();
}
?>
