<?php
/**
 * Database connection
 * Update these credentials to match your local MySQL setup.
 */
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "elearning_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Start session globally for all pages that include this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
