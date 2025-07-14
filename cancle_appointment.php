<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE appointments SET status = 'canceled' WHERE appointment_id = ? AND user_id = ?");
$stmt->execute([$appointment_id, $user_id]);

echo "<script>window.location.href='dashboard.php';</script>";
?>
