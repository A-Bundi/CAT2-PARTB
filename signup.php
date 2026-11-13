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
    <title>Allan's Portal - Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <p class="brand">Allan's Portal</p>
        <h1>Sign Up</h1>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; font-size: 14px;">This username already exists.</p>
        <?php endif; ?>

        <form action="backend/signup_logic.php" method="post">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input name="first_name" type="text" placeholder="First name" required>
            <input name="last_name" type="text" placeholder="Last name" required>
            <input name="email" type="email" placeholder="Email" required>
            <input name="phone" type="tel" placeholder="Phone (e.g. +254712345678)" required>
            <input name="dob" type="date" title="Date of birth" required>
            <select name="gender" required>
                <option value="" disabled selected>Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>
            <input name="country" type="text" placeholder="Country" value="Kenya" required>
            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <input type="submit" value="Sign Up">
        </form>

        <a href="login.php">Back to Login</a>
        <p class="quote">"Sir give me bonus marks for creativity"</p>
        <p class="quote">"Allan is the best student ever"</p>
    </div>

</body>
</html>
