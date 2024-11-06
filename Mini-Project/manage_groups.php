<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $group_name = $_POST['group_name'];
    $stmt = $conn->prepare("INSERT INTO groups (group_name, owner_id) VALUES (?, ?)");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }
    $stmt->bind_param("si", $group_name, $user_id);
    $stmt->execute();
    $group_id = $conn->insert_id;

    // Automatically add the creator to the group
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $message = "Group '$group_name' created successfully!";
}

// Handle group joining
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_group'])) {
    $join_group_id = $_POST['join_group_id'];

    // Check if user is already in the group
    $stmt = $conn->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ii", $join_group_id, $user_id);
    $stmt->execute();
    $already_member = $stmt->get_result()->num_rows > 0;

    if ($already_member) {
        $message = "You are already in the group.";
    } else {
        // Check if group exists before joining
        $stmt = $conn->prepare("SELECT * FROM groups WHERE group_id = ?");
        $stmt->bind_param("i", $join_group_id);
        $stmt->execute();
        $group_exists = $stmt->get_result()->num_rows > 0;

        if ($group_exists) {
            $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $join_group_id, $user_id);
            $stmt->execute();
            $message = "Successfully joined the group!";
        } else {
            $message = "Group ID does not exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Groups</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Groups</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Group Creation Form -->
    <form method="POST" class="mb-4">
        <h4>Create a New Group</h4>
        <div class="form-group">
            <label for="group_name">Group Name:</label>
            <input type="text" name="group_name" class="form-control" required>
        </div>
        <button type="submit" name="create_group" class="btn btn-primary">Create Group</button>
    </form>

    <!-- Group Join Form -->
    <form method="POST">
        <h4>Join an Existing Group</h4>
        <div class="form-group">
            <label for="join_group_id">Enter Group ID:</label>
            <input type="number" name="join_group_id" class="form-control" required>
        </div>
        <button type="submit" name="join_group" class="btn btn-success">Join Group</button>
    </form>
</div>
</body>
</html>
