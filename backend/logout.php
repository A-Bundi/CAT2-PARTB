<?php
session_start();

// Alle Session-Daten löschen
$_SESSION = [];

// Session-Cookie invalidieren
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Session vollständig zerstören
session_destroy();

// Redirect zurück zum Login
header("Location: ../login.php?logout=success");
exit();
