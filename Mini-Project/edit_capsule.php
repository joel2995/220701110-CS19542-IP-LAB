<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$capsule_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch capsule details
$stmt = $conn->prepare("SELECT * FROM capsules WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $capsule_id, $user_id);
$stmt->execute();
$capsule = $stmt->get_result()->fetch_assoc();

if (!$capsule) {
    echo "Capsule not found or access denied.";
    exit();
}

$unlock_time = strtotime($capsule['unlock_date']);
$current_time = time();

// Allow editing only before unlock date
if ($current_time >= $unlock_time) {
    echo "<p>This capsule is unlocked and cannot be edited.</p>";
    exit();
}

// Fetch user groups for selection in case of group access
$group_stmt = $conn->prepare("SELECT g.group_id, g.group_name FROM groups g JOIN group_members gm ON g.group_id = gm.group_id WHERE gm.user_id = ?");
$group_stmt->bind_param("i", $user_id);
$group_stmt->execute();
$groups = $group_stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $new_content_type = $_POST['content_type'];
    $new_access_type = $_POST['access_type'];
    $new_shared_group_id = ($new_access_type == 'group') ? $_POST['shared_group_id'] : null;
    $new_unlock_date = $_POST['unlock_date'] . ' ' . $_POST['unlock_time'];
    $new_content = ($new_content_type === 'text') ? $_POST['content'] : $capsule['content'];

    // Detect if changes were made by comparing new values to existing ones
    $changes_made = (
        $title !== $capsule['title'] || 
        $new_access_type !== $capsule['access_type'] || 
        (string) $new_shared_group_id !== (string) $capsule['shared_group_id'] || // Convert to strings for comparison
        $new_unlock_date !== $capsule['unlock_date'] ||
        ($new_content_type !== 'text' && isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK)
    );

    if (!$changes_made) {
        echo "<script>alert('No changes made'); window.location.href='dashboard.php';</script>";
        exit();
    }

    // Handle file uploads if content type is image or video
    if ($new_content_type !== 'text' && isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif", "mp4", "avi", "mkv"];

        if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $new_content = $file_name;
        } else {
            echo "Error uploading file.";
            exit();
        }
    }

    // Update the capsule with new values
    $stmt = $conn->prepare("UPDATE capsules SET title = ?, content = ?, unlock_date = ?, access_type = ?, shared_group_id = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssiiii", $title, $new_content, $new_unlock_date, $new_access_type, $new_shared_group_id, $capsule_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Capsule updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error updating capsule: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Capsule</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Capsule</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($capsule['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="content_type">Content Type:</label>
            <select name="content_type" class="form-control" id="content_type">
                <option value="text" <?php if ($capsule['content_type'] == "text") echo "selected"; ?>>Text</option>
                <option value="image" <?php if ($capsule['content_type'] == "image") echo "selected"; ?>>Image</option>
                <option value="video" <?php if ($capsule['content_type'] == "video") echo "selected"; ?>>Video</option>
            </select>
        </div>
        <div class="form-group" id="text_area_container" style="<?php echo ($capsule['content_type'] == 'text') ? 'display: block;' : 'display: none;'; ?>">
            <label for="content">Text Content:</label>
            <textarea class="form-control" name="content"><?php echo htmlspecialchars($capsule['content']); ?></textarea>
        </div>
        <div class="form-group" id="file_input_container" style="<?php echo ($capsule['content_type'] != 'text') ? 'display: block;' : 'display: none;'; ?>">
            <label for="fileToUpload">Upload File (only if updating):</label>
            <input type="file" class="form-control" name="fileToUpload">
        </div>
        <div class="form-group">
            <label for="unlock_date">Unlock Date:</label>
            <input type="date" class="form-control" name="unlock_date" value="<?php echo date('Y-m-d', $unlock_time); ?>" required>
        </div>
        <div class="form-group">
            <label for="unlock_time">Unlock Time:</label>
            <input type="time" class="form-control" name="unlock_time" value="<?php echo date('H:i', $unlock_time); ?>" required>
        </div>
        <div class="form-group">
            <label for="access_type">Access Type:</label>
            <select name="access_type" class="form-control" id="access_type">
                <option value="private" <?php if ($capsule['access_type'] == "private") echo "selected"; ?>>Private</option>
                <option value="group" <?php if ($capsule['access_type'] == "group") echo "selected"; ?>>Group</option>
                <option value="public" <?php if ($capsule['access_type'] == "public") echo "selected"; ?>>Public</option>
            </select>
        </div>
        <div class="form-group" id="groupSelect" style="<?php echo ($capsule['access_type'] == 'group') ? 'display: block;' : 'display: none;'; ?>">
            <label for="shared_group_id">Select Group:</label>
            <select class="form-control" name="shared_group_id" id="shared_group_id">
                <option value="">None</option>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <option value="<?php echo $group['group_id']; ?>" <?php if ($capsule['shared_group_id'] == $group['group_id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($group['group_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Capsule</button>
    </form>
</div>

<script>
    document.getElementById('content_type').addEventListener('change', function() {
        if (this.value === 'text') {
            document.getElementById('text_area_container').style.display = 'block';
            document.getElementById('file_input_container').style.display = 'none';
        } else {
            document.getElementById('text_area_container').style.display = 'none';
            document.getElementById('file_input_container').style.display = 'block';
        }
    });

    document.getElementById('access_type').addEventListener('change', function() {
        document.getElementById('groupSelect').style.display = this.value === 'group' ? 'block' : 'none';
    });
</script>
</body>
</html>
