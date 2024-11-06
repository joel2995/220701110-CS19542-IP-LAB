<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch capsules that are either created by the user, public, or shared with the user via group
$stmt = $conn->prepare("
    SELECT * FROM capsules 
    WHERE user_id = ? 
    OR access_type = 'public' 
    OR (access_type = 'group' AND shared_group_id IN 
        (SELECT group_id FROM group_members WHERE user_id = ?))
    ORDER BY unlock_date ASC");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar for Navigation -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="create_capsule.php" class="list-group-item list-group-item-action">Create Capsule</a>
                <a href="profile.php" class="list-group-item list-group-item-action">Profile</a>
                <a href="notify.php" class="list-group-item list-group-item-action">Notifications</a>
                <a href="analytics.php" class="list-group-item list-group-item-action">Analytics</a>
                <a href="search.php" class="list-group-item list-group-item-action">Search Capsules</a>
                <a href="manage_groups.php" class="list-group-item list-group-item-action">Manage Groups</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
            </div>
        </div>

        <!-- Main Content Area for Capsules -->
        <div class="col-md-9">
            <h2>Your Capsules</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $unlock_time = strtotime($row['unlock_date']);
                    $current_time = time();
                    $is_creator = ($row['user_id'] == $user_id);
                ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p><strong>Access Type:</strong> <?php echo htmlspecialchars($row['access_type']); ?></p>
                            <p><strong>Unlocks on:</strong> <?php echo date('Y-m-d H:i:s', $unlock_time); ?></p>
                            
                            <p>
                                <?php if ($is_creator): ?>
                                    <?php if ($current_time < $unlock_time): ?>
                                        <a href="edit_capsule.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit Capsule</a>
                                    <?php else: ?>
                                        <em>This capsule is unlocked and cannot be edited.</em>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($current_time >= $unlock_time): ?>
                                        <?php if ($row['content_type'] == "text"): ?>
                                            <?php echo htmlspecialchars($row['content']); ?>
                                        <?php elseif ($row['content_type'] == "image" || $row['content_type'] == "video"): ?>
                                            <a href='uploads/<?php echo htmlspecialchars($row['content']); ?>' target='_blank'>View File</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <em>This capsule is locked and not yet available for viewing.</em>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No capsules found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
