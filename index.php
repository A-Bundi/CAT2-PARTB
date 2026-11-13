<?php
session_start();

// User muss eingeloggt sein
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// =========================
//   DB CONNECTION
// =========================
$servername = "localhost";
$username   = "cat2_user";
$password   = "Cat2App_Pass2026";
$dbname     = "CAT2";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =========================
//   USERNAME AUS DB LADEN
// =========================
$userId = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT username FROM user WHERE ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Falls der User existiert
$displayName = htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main - Page</title>

    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="card">
            <h1>Welcome back</h1>
            <p>You are logged in as:</p>

            <div class="user-box">
                <span><?php echo $displayName; ?></span>
            </div>

            <a class="logout-btn" href="backend/logout.php">Logout</a>
        </div>
    </div>

</body>
</html>
