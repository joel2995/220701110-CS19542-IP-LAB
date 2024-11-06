<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$capsule_id = $_GET['capsule_id'] ?? null;

// Fetch group viewers and public viewers
$stmt = $conn->prepare("
    SELECT u.id, u.email, gm.group_id AS group_access 
    FROM users u 
    LEFT JOIN group_members gm ON u.id = gm.user_id 
    LEFT JOIN capsules c ON c.shared_group_id = gm.group_id 
    WHERE c.id = ? AND (c.access_type = 'public' OR gm.group_id IS NOT NULL)
");
$stmt->bind_param("i", $capsule_id);
$stmt->execute();
$viewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Separate viewers by type
$group_viewers = [];
$public_viewers = [];
foreach ($viewers as $viewer) {
    if ($viewer['group_access']) {
        $group_viewers[] = $viewer;
    } else {
        $public_viewers[] = $viewer;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Notifications</h2>
    
    <h3>Group Viewers</h3>
    <?php if ($group_viewers): ?>
        <?php foreach ($group_viewers as $viewer): ?>
            <p><?php echo htmlspecialchars($viewer['email']); ?> viewed this capsule.</p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No group members have viewed this capsule yet.</p>
    <?php endif; ?>

    <h3>Public Viewers</h3>
    <?php if ($public_viewers): ?>
        <p><?php echo count($public_viewers); ?> people have viewed this capsule publicly.</p>
    <?php else: ?>
        <p>No public views yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
