<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Clear existing availability
        $pdo->prepare("DELETE FROM availability WHERE user_id = ?")->execute([$user_id]);
        
        $days = $_POST['days'] ?? [];
        $start = $_POST['start'] ?? [];
        $end = $_POST['end'] ?? [];
        
        $stmt = $pdo->prepare("INSERT INTO availability (user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $saved = false;
        foreach ($days as $day) {
            if (!empty($start[$day]) && !empty($end[$day]) && $start[$day] < $end[$day]) {
                $stmt->execute([$user_id, $day, $start[$day], $end[$day]]);
                $saved = true;
            }
        }
        if ($saved) {
            echo "<script>alert('Availability saved successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('No valid time slots provided.'); window.location.href='dashboard.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error saving availability: " . addslashes($e->getMessage()) . "'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='dashboard.php';</script>";
}
?>
