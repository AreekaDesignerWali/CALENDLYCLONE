<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'];

// Fetch upcoming appointments
$upcoming = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? AND appointment_date >= CURDATE() AND status = 'confirmed' ORDER BY appointment_date, start_time");
$upcoming->execute([$user_id]);
$appointments = $upcoming->fetchAll();

// Set timezone and get current date and time
date_default_timezone_set('Asia/Karachi');
$current_time = date('h:i A T \o\n l, F d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .dashboard { max-width: 800px; margin: 0 auto; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .time-display { color: #2c3e50; font-size: 1.1em; margin-bottom: 20px; }
        form { display: flex; flex-wrap: wrap; gap: 10px; }
        input[type="checkbox"] { margin-right: 5px; }
        input[type="time"] { padding: 5px; }
        button { padding: 10px 20px; background: #e74c3c; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #c0392b; }
        .appointment { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .logout { background: #95a5a6; }
        .logout:hover { background: #7f8c8d; }
        @media (max-width: 600px) { .section { padding: 10px; } form { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="section">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
            <p class="time-display">Today's date and time is <?php echo $current_time; ?>.</p>
            <p>Your booking link: <a href="booking.php?username=<?php echo urlencode($username); ?>">http://rsoa180.rehanschool.us/CALENDLYCLONE/booking.php?username=<?php echo urlencode($username); ?></a></p>
            <button class="logout" onclick="window.location.href='login.php'">Logout</button>
        </div>
        <div class="section">
            <h2>Set Availability</h2>
            <form method="POST" action="set_availability.php">
                <?php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($days as $i => $day) {
                    echo "<label><input type='checkbox' name='days[]' value='$i'> $day</label>";
                    echo "<input type='time' name='start[$i]' step='1800'> - <input type='time' name='end[$i]' step='1800'><br>";
                }
                ?>
                <button type="submit">Save Availability</button>
            </form>
        </div>
        <div class="section">
            <h2>Upcoming Appointments</h2>
            <?php foreach ($appointments as $appt): ?>
                <div class="appointment">
                    <p><?php echo htmlspecialchars($appt['booker_name']) . " on " . $appt['appointment_date'] . " at " . substr($appt['start_time'], 0, 5); ?></p>
                    <button onclick="cancelAppointment(<?php echo $appt['appointment_id']; ?>)">Cancel</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function cancelAppointment(id) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                window.location.href = 'cancel_appointment.php?id=' + id;
            }
        }
    </script>
</body>
</html>
