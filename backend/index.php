<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    // Not logged in → redirect to login page
    header("Location: /login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main - Page</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_id"], ENT_QUOTES, 'UTF-8'); ?>!</h1>
</body>
</html>
