<?php
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = $_GET['username'] ?? '';
$date = $_GET['date'] ?? '';
if (!$username || !$date) {
    exit(json_encode(['error' => 'Username or date not provided']));
}

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    exit(json_encode(['error' => 'User not found']));
}
$user_id = $user['user_id'];

$day_of_week = date('w', strtotime($date)); // 0=Sunday, 1=Monday, etc.
$stmt = $pdo->prepare("SELECT start_time, end_time FROM availability WHERE user_id = ? AND day_of_week = ?");
$stmt->execute([$user_id, $day_of_week]);
$availability = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$availability) {
    exit(json_encode(['error' => 'No availability set for this day']));
}

$slots = [];
$slot_duration = 30 * 60; // 30-minute slots

// Get booked slots to avoid conflicts
$booked = $pdo->prepare("SELECT start_time, end_time FROM appointments WHERE user_id = ? AND appointment_date = ? AND status = 'confirmed'");
$booked->execute([$user_id, $date]);
$booked_slots = $booked->fetchAll(PDO::FETCH_ASSOC);

foreach ($availability as $avail) {
    $start = strtotime($avail['start_time']);
    $end = strtotime($avail['end_time']);
    
    for ($time = $start; $time < $end; $time += $slot_duration) {
        $slot_start = date('H:i', $time); // Format as HH:MM
        $slot_end = date('H:i', $time + $slot_duration); // Format as HH:MM
        $is_booked = false;
        
        foreach ($booked_slots as $bs) {
            $bs_start = strtotime($bs['start_time']);
            $bs_end = strtotime($bs['end_time']);
            if ($time < $bs_end && ($time + $slot_duration) > $bs_start) {
                $is_booked = true;
                break;
            }
        }
        
        if (!$is_booked) {
            $slots[] = ['start_time' => $slot_start, 'end_time' => $slot_end];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($slots);
?>
