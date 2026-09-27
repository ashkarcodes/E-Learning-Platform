<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_student();

$sid = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: browse_classes.php");
    exit();
}

$quiz_id = (int) $_POST['quiz_id'];
$answers = $_POST['answers'] ?? []; // [question_id => option_id]

$quiz = $conn->query("SELECT * FROM quizzes WHERE quiz_id=$quiz_id")->fetch_assoc();
if (!$quiz) {
    set_message("Quiz not found.", "danger");
    header("Location: browse_classes.php");
    exit();
}

// Prevent double submission
$already = $conn->query("SELECT * FROM quiz_attempts WHERE quiz_id=$quiz_id AND student_id=$sid")->fetch_assoc();
if ($already) {
    header("Location: view_results.php?attempt_id=" . $already['attempt_id']);
    exit();
}

$total_questions = count($answers);
$score = 0;

// Create the attempt first
$stmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, score, total_questions) VALUES (?,?,0,?)");
$stmt->bind_param("iii", $quiz_id, $sid, $total_questions);
$stmt->execute();
$attempt_id = $stmt->insert_id;

foreach ($answers as $question_id => $option_id) {
    $question_id = (int) $question_id;
    $option_id = (int) $option_id;

    $opt = $conn->query("SELECT is_correct FROM quiz_options WHERE option_id=$option_id AND question_id=$question_id")->fetch_assoc();
    $is_correct = ($opt && $opt['is_correct']) ? 1 : 0;
    if ($is_correct) $score++;

    $astmt = $conn->prepare("INSERT INTO quiz_answers (attempt_id, question_id, selected_option_id, is_correct) VALUES (?,?,?,?)");
    $astmt->bind_param("iiii", $attempt_id, $question_id, $option_id, $is_correct);
    $astmt->execute();
}

// Update final score
$conn->query("UPDATE quiz_attempts SET score=$score WHERE attempt_id=$attempt_id");

set_message("Quiz submitted successfully!");
header("Location: view_results.php?attempt_id=$attempt_id");
exit();
