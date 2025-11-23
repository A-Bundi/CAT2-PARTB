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
//       DB CONNECTION
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
//      BRUTEFORCE PROTECTION
// =========================
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] > 10) {
    echo "Too many attempts. Try again later.";
    exit();
}

// =========================
//      LOGIN LOGIC
// =========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["username"]) || empty($_POST["password"])) {
        echo "<script>alert('Username and Password are required!');</script>";
        exit;
    }

    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    $users = fetch_data($conn);
    $validLogin = false;
    $userId = null;

    foreach ($users as $user) {

        if (password_verify($inputUsername, $user['username'])) {

            if (password_verify($inputPassword, $user['password'])) {
                $validLogin = true;
                $userId = $user['ID'];
                break;
            }
        }
    }

    usleep(150000); 

    if ($validLogin) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['login_attempts'] = 0;

        echo "Login successful";
        header("Location: index.html");
        exit();
    } else {
        $_SESSION['login_attempts']++;
        echo "Invalid credentials.";
    }
}

?>
