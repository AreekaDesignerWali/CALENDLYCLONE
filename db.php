<?php
$host = 'localhost';
$dbname = 'dbkyfhqigu7h5b';
$user = 'uc7ggok7oyoza';
$password = 'gqypavorhbbc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
