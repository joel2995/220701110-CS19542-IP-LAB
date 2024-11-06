<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$capsule_id = $_GET['id'] ?? null;

// Fetch capsule details based on access level and unlock time
$stmt = $conn->prepare("
    SELECT * FROM capsules 
    WHERE id = ? 
    AND (user_id = ? OR access_type = 'public' 
    OR (access_type = 'group' AND shared_group_id IN 
        (SELECT group_id FROM group_members WHERE user_id = ?)))
");
$stmt->bind_param("iii", $capsule_id, $user_id, $user_id);
$stmt->execute();
$capsule = $stmt->get_result()->fetch_assoc();

if (!$capsule) {
    echo "Capsule not found or access denied.";
    exit();
}

$is_creator = ($capsule['user_id'] == $user_id);
$unlock_time = strtotime($capsule['unlock_date']);
$current_time = time();

// Check if the capsule is accessible
if (!$is_creator && $current_time < $unlock_time) {
    echo "<p>This capsule is locked and cannot be viewed until " . date('Y-m-d H:i:s', $unlock_time) . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Capsule</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2><?php echo htmlspecialchars($capsule['title']); ?></h2>
    <p><strong>Content Type:</strong> <?php echo htmlspecialchars($capsule['content_type']); ?></p>
    <p><strong>Unlock Date:</strong> <?php echo htmlspecialchars($capsule['unlock_date']); ?></p>

    <?php if ($capsule['content_type'] == 'text'): ?>
        <p><?php echo nl2br(htmlspecialchars($capsule['content'])); ?></p>
    <?php elseif ($capsule['content_type'] == 'image'): ?>
        <img src="uploads/<?php echo htmlspecialchars($capsule['content']); ?>" alt="Image" class="img-fluid">
    <?php elseif ($capsule['content_type'] == 'video'): ?>
        <video controls class="img-fluid">
            <source src="uploads/<?php echo htmlspecialchars($capsule['content']); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>
</div>
</body>
</html>
