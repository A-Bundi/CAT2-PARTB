<?php
session_start();

// CSRF Token generieren
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp - Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <h1>Sign Up</h1>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; font-size: 14px;">This username already exists.</p>
        <?php endif; ?>

        <form action="backend/signup_logic.php" method="post">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input name="email" type="email" placeholder="Email" required>
            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <input type="submit" value="Sign Up">
        </form>

        <a href="login.php">Back to Login</a>
    </div>

</body>
</html>
