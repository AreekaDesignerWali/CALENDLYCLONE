<?php
session_start();
require 'db.php';

// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone for consistency
date_default_timezone_set('Asia/Karachi');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $booker_name = $_POST['booker_name'] ?? '';
    $booker_email = $_POST['booker_email'] ?? '';
    
    if (!$user_id || !$date || !$start_time || !$booker_name || !$booker_email) {
        $error = "All fields are required.";
    } else {
        try {
            // Calculate end time (30 minutes after start time)
            $end_time = date('H:i:s', strtotime($start_time) + 30 * 60);
            
            // Check if the slot is still available
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ? AND start_time = ? AND status = 'confirmed'");
            $stmt->execute([$user_id, $date, $start_time]);
            if ($stmt->fetchColumn() > 0) {
                $error = "This time slot is already booked.";
            } else {
                // Insert appointment
                $stmt = $pdo->prepare("INSERT INTO appointments (user_id, appointment_date, start_time, end_time, booker_name, booker_email, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
                $stmt->execute([$user_id, $date, $start_time, $end_time, $booker_name, $booker_email]);
                $success = "Appointment booked successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error booking appointment: " . $e->getMessage();
        }
    }
} else {
    $error = "Invalid request.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <style>
        body { font-family: Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .success { color: green; font-size: 1.1em; margin-bottom: 20px; }
        .error { color: red; font-size: 1.1em; margin-bottom: 20px; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #2980b9; }
        @media (max-width: 600px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Booking Status</h2>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
            <button onclick="window.location.href='index.php'">Back to Home</button>
        <?php elseif (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
            <button onclick="window.location.href='booking.php?username=<?php echo urlencode($_GET['username'] ?? ''); ?>'">Try Again</button>
        <?php endif; ?>
    </div>
    <?php if (isset($success)): ?>
        <script>
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 3000); // Redirect to index.php after 3 seconds
        </script>
    <?php endif; ?>
</body>
</html>
