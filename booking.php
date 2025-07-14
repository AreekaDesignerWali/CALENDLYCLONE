<?php
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone and get current date and time for display
date_default_timezone_set('Asia/Karachi');
$current_time = date('h:i A T \o\n l, F d, Y');

$username = $_GET['username'] ?? '';
if (!$username) {
    die("No username provided. Please enter a username via the homepage.");
}

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    die("User not found. Please check the username.");
}
$user_id = $user['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book with <?php echo htmlspecialchars($username); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .booking-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .time-display { color: #2c3e50; font-size: 1.1em; margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        #slots { margin: 20px 0; }
        .error { color: red; }
        @media (max-width: 600px) { .booking-container { width: 90%; } }
    </style>
</head>
<body>
    <div class="booking-container">
        <h2>Book with <?php echo htmlspecialchars($username); ?></h2>
        <p class="time-display">Today's date and time is <?php echo $current_time; ?>.</p>
        <input type="date" id="date" min="<?php echo date('Y-m-d'); ?>">
        <div id="slots"></div>
        <form id="bookingForm" method="POST" action="book_appointment.php">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="date" id="formDate">
            <input type="hidden" name="start_time" id="formStartTime">
            <input type="text" name="booker_name" placeholder="Your Name" required>
            <input type="email" name="booker_email" placeholder="Your Email" required>
            <button type="submit">Book Appointment</button>
        </form>
        <p><a href="javascript:void(0)" onclick="window.location.href='index.php'">Back to Home</a></p>
    </div>
    <script>
        document.getElementById('date').addEventListener('change', function() {
            const date = this.value;
            const username = '<?php echo addslashes($username); ?>';
            fetch('get_slots.php?username=' + encodeURIComponent(username) + '&date=' + date)
                .then(response => response.json())
                .then(data => {
                    const slotsDiv = document.getElementById('slots');
                    slotsDiv.innerHTML = '';
                    if (data.error) {
                        slotsDiv.innerHTML = '<p class="error">' + data.error + '</p>';
                    } else if (data.length) {
                        const select = document.createElement('select');
                        select.name = 'slot';
                        select.required = true;
                        select.onchange = function() {
                            document.getElementById('formDate').value = date;
                            document.getElementById('formStartTime').value = this.value;
                        };
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.start_time;
                            option.text = slot.start_time + ' - ' + slot.end_time;
                            select.appendChild(option);
                        });
                        slotsDiv.appendChild(select);
                    } else {
                        slotsDiv.innerHTML = '<p class="error">No slots available for this date.</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('slots').innerHTML = '<p class="error">Error fetching slots: ' + error.message + '</p>';
                });
        });
    </script>
</body>
</html>
