<?php
session_start();
require 'db.php';

// Set timezone and get current date and time
date_default_timezone_set('Asia/Karachi');
$current_time = date('h:i A T \o\n l, F d, Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        echo "<script>window.location.href='dashboard.php';</script>";
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .form-container { max-width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .time-display { color: #2c3e50; font-size: 1.1em; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .error { color: red; }
        @media (max-width: 600px) { .form-container { width: 90%; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <p class="time-display">Today's date and time is <?php echo $current_time; ?>.</p>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="javascript:void(0)" onclick="window.location.href='signup.php'">Sign Up</a></p>
    </div>
</body>
</html>
