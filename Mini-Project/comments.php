<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$capsule_id = $_GET['capsule_id'] ?? null;

// Verify capsule exists and is accessible by the user
$stmt = $conn->prepare("SELECT * FROM capsules WHERE id = ? AND (user_id = ? OR access_type = 'public' OR (access_type = 'group' AND shared_group_id IN (SELECT group_id FROM group_members WHERE user_id = ?)))");
$stmt->bind_param("iii", $capsule_id, $user_id, $user_id);
$stmt->execute();
$capsule_result = $stmt->get_result();
$capsule = $capsule_result->fetch_assoc();

if (!$capsule) {
    echo "Error: Capsule ID does not exist or access is denied.";
    exit();
}

// Handle new comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments (capsule_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $capsule_id, $user_id, $comment);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Comment added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}

// Fetch existing comments
$stmt = $conn->prepare("SELECT c.comment, u.email FROM comments c JOIN users u ON c.user_id = u.id WHERE c.capsule_id = ?");
$stmt->bind_param("i", $capsule_id);
$stmt->execute();
$comments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Comments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <h2><?php echo htmlspecialchars($capsule['title']); ?> - Comments</h2>
    <form method="post">
        <div class="form-group">
            <textarea class="form-control" name="comment" placeholder="Add a comment..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Comment</button>
    </form>

    <h3>Memory Vault</h3>
    <?php while ($comment = $comments_result->fetch_assoc()): ?>
        <div class="memory">
            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
            <small>- <?php echo htmlspecialchars($comment['email']); ?></small>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
