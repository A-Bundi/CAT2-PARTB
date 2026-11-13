<?php
session_start();

// CSRF-Token generieren
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allan's Portal - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <p class="brand">Allan's Portal</p>
        <h1>Login</h1>

        <!-- Error Message -->
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; font-size: 14px;">Username or password are invalid.</p>
        <?php endif; ?>

        <form action="backend/login_logic.php" method="post">

            <!-- CSRF TOKEN -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>

        <a href="signup.php">Create an account</a>
        <p class="quote">"Sir give me bonus marks for creativity"</p>
        <p class="quote">"Allan is the best student ever"</p>
    </div>

</body>
</html>
