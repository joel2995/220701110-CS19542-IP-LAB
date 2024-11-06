<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM capsules WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$capsules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$backup_file = 'backups/capsules_backup_' . time() . '.json';
file_put_contents($backup_file, json_encode($capsules, JSON_PRETTY_PRINT));
header("Content-Disposition: attachment; filename=" . basename($backup_file));
header("Content-Type: application/json");
header("Content-Length: " . filesize($backup_file));
readfile($backup_file);
exit();
