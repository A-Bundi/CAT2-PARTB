<?php
session_start();

// =========================
//      SESSION SECURITY
// =========================
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
session_regenerate_id(true);

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed");
}


// =========================
//      DB CONNECTION
// =========================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_login_signup";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =========================
//      FETCH DATA
// =========================
function fetch_data(mysqli $conn): array
{
    $sql = "SELECT ID, username, password, email FROM user";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $data;
}

// =========================
//    BRUTEFORCE PROTECTION
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
        exit;
    }

    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];
    $inputEmail    = $_POST['email'];

    $hashedUsername = password_hash($inputUsername, PASSWORD_DEFAULT);
    $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);

    $users = fetch_data($conn);

    $exists = false;
    foreach ($users as $user) {
        if (password_verify($inputUsername, $user['username'])) {
            $exists = true;
            break;
        }
    }

    usleep(150000);

    if ($exists) {
        $_SESSION['signup_attempts']++;
        echo "<script>alert('Username already exists.')</script>";
        exit;
    }

    $sql = "INSERT INTO user (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $hashedUsername, $hashedPassword, $inputEmail);
    $stmt->execute();
    $stmt->close();

    $_SESSION['signup_attempts'] = 0;
    echo "<script>alert('Account created successfully!')</script>";
}

?>
