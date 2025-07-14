<?php
session_start();

// Set timezone and get current date and time
date_default_timezone_set('Asia/Karachi');
$current_time = date('h:i A T \o\n l, F d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduling Platform</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        .hero { background: #2c3e50; color: white; padding: 50px; text-align: center; }
        .hero h1 { font-size: 2.5em; margin: 0; }
        .hero p { font-size: 1.2em; }
        .time-display { font-size: 1.1em; margin: 10px 0; }
        .btn { padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; margin: 10px; border-radius: 5px; }
        .btn:hover { background: #2980b9; }
        .booking-form { margin: 20px auto; max-width: 400px; text-align: center; }
        .booking-form input { padding: 10px; width: 70%; border: 1px solid #ddd; border-radius: 5px; }
a            .hero h1 { font-size: 1.5em; } 
            .hero p { font-size: 1em; } 
            .btn { display: block; width: 80%; margin: 10px auto; } 
            .booking-form input { width: 90%; } 
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to Your Scheduling Platform</h1>
        <p>Schedule meetings easily with your personalized booking link.</p>
        <p class="time-display">Today's date and time is <?php echo $current_time; ?>.</p>
        <button class="btn" onclick="window.location.href='signup.php'">Sign Up</button>
        <button class="btn" onclick="window.location.href='login.php'">Log In</button>
    </div>
    <div class="booking-form">
        <h2>Book a Meeting</h2>
        <input type="text" id="username" placeholder="Enter username">
        <button class="btn" onclick="goToBooking()">Go to Booking Page</button>
    </div>
    <script>
        function goToBooking() {
            const username = document.getElementById('username').value;
            if (username) {
                window.location.href = 'booking.php?username=' + encodeURIComponent(username);
            } else {
                alert('Please enter a username');
            }
        }
    </script>
</body>
</html>
