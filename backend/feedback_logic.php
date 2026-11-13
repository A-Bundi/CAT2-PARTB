<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: ../login.php"); exit(); }

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed");
}
unset($_SESSION['csrf_token']);

$conn = new mysqli("localhost", "cat2_user", "Cat2App_Pass2026", "CAT2");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

$category = $_POST['category'] ?? '';
$subject  = trim($_POST['subject'] ?? '');
$rating   = (int)($_POST['rating'] ?? 0);
$message  = trim($_POST['message'] ?? '');

if ($category === '' || $subject === '' || $message === '' || $rating < 1 || $rating > 5) {
    die("All fields are required and the rating must be between 1 and 5.");
}

$stmt = $conn->prepare("INSERT INTO feedback (user_id, category, subject, rating, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issis", $_SESSION['user_id'], $category, $subject, $rating, $message);
$stmt->execute();
$stmt->close();

header("Location: ../feedback.php?saved=1");
exit();
