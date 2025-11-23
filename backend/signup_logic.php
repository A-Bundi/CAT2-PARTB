<?php
session_start();

// =========================
//   SESSION SECURITY
// =========================
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // 1 bei HTTPS
ini_set('session.cookie_samesite', 'Strict');
session_regenerate_id(true);

// =========================
//   CSRF VALIDATION
// =========================
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("CSRF validation failed");
}

unset($_SESSION['csrf_token']);


// =========================
//   DB CONNECTION
// =========================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_login_signup";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =========================
//   BRUTEFORCE LIMIT
// =========================
if (!isset($_SESSION['signup_attempts'])) {
    $_SESSION['signup_attempts'] = 0;
}

if ($_SESSION['signup_attempts'] > 10) {
    echo "Too many signup attempts. Try again later.";
    exit();
}


// =========================
//       SIGNUP LOGIC
// =========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])) {
        echo "<script>alert('All fields are required.')</script>";
        exit();
    }

    $inputUsername = $_POST['username'];  // Klartext
    $inputPassword = $_POST['password'];
    $inputEmail    = $_POST['email'];

    // Passwort sicher hashen
    $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);

    // Prüfen, ob Username bereits existiert
    $stmt = $conn->prepare("SELECT ID FROM user WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    usleep(150000); // Timing attack protection

    if ($stmt->num_rows > 0) {
        $_SESSION['signup_attempts']++;
        $stmt->close();
        header("Location: /signup.php?error=1");
        exit();
    }

    $stmt->close();

    // User eintragen
    $sql = "INSERT INTO user (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $inputUsername, $hashedPassword, $inputEmail);
    $stmt->execute();
    $stmt->close();

    $_SESSION['signup_attempts'] = 0;

    header("Location: ../login.php?signup=success");
    exit();
}
?>
