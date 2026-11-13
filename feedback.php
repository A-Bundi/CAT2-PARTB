<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit(); }
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$conn = new mysqli("localhost", "cat2_user", "Cat2App_Pass2026", "CAT2");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

$rows = $conn->query(
    "SELECT f.id, u.username, f.category, f.subject, f.rating, f.message, f.created_at
     FROM feedback f JOIN user u ON u.ID = f.user_id ORDER BY f.id DESC"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allan's Portal - Feedback</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { flex-direction: column; justify-content: flex-start; padding: 2rem 0; gap: 1.5rem; }
        .wide { width: 90%; max-width: 900px; box-sizing: border-box; }
        textarea { padding: 12px 14px; border: 1px solid #dcdde1; border-radius: 10px; font-size: 14px; font-family: inherit; resize: vertical; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
        th, td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { background: #0f766e; color: white; }
        .ok { color: #0f766e; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <p class="brand">Allan's Portal</p>
        <h1>Feedback Form</h1>
        <?php if (isset($_GET['saved'])): ?><p class="ok">Thank you, your feedback was saved.</p><?php endif; ?>
        <form action="backend/feedback_logic.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <select name="category" required>
                <option value="" disabled selected>Category</option>
                <option>Suggestion</option><option>Bug report</option><option>Compliment</option><option>Question</option>
            </select>
            <input name="subject" type="text" placeholder="Subject" maxlength="150" required>
            <select name="rating" required>
                <option value="" disabled selected>Rating (1 to 5)</option>
                <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
            </select>
            <textarea name="message" rows="4" placeholder="Your message" required></textarea>
            <input type="submit" value="Submit Feedback">
        </form>
        <a href="index.php">Back to dashboard</a>
    </div>

    <div class="container wide">
        <h1>Submitted Feedback</h1>
        <table>
            <tr><th>ID</th><th>User</th><th>Category</th><th>Subject</th><th>Rating</th><th>Message</th><th>Date</th></tr>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?php echo (int)$r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['username']); ?></td>
                <td><?php echo htmlspecialchars($r['category']); ?></td>
                <td><?php echo htmlspecialchars($r['subject']); ?></td>
                <td><?php echo (int)$r['rating']; ?>/5</td>
                <td><?php echo htmlspecialchars($r['message']); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
