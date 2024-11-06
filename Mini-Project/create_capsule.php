<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user groups
$stmt = $conn->prepare("SELECT g.group_id, g.group_name FROM groups g JOIN group_members gm ON g.group_id = gm.group_id WHERE gm.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$groups = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content_type = $_POST['content_type'];
    $access_type = $_POST['access_type'];
    $shared_group_id = ($access_type == 'group') ? $_POST['shared_group_id'] : null;
    $unlock_date = $_POST['unlock_date'] . ' ' . $_POST['unlock_time'];
    $content = '';

    if ($content_type == "text") {
        $content = $_POST['content'];
    } elseif ($content_type == "image" || $content_type == "video") {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = array("jpg", "jpeg", "png", "gif", "mp4", "avi", "mkv");
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        if ($_FILES["fileToUpload"]["error"] === UPLOAD_ERR_OK) {
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $content = $file_name;
                } else {
                    echo "<script>alert('Error uploading file.');</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Invalid file type.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('File upload error: " . $_FILES["fileToUpload"]["error"] . "');</script>";
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO capsules (user_id, title, content_type, content, access_type, shared_group_id, unlock_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssis", $user_id, $title, $content_type, $content, $access_type, $shared_group_id, $unlock_date);
    if ($stmt->execute()) {
        echo "<script>
            alert('Capsule created successfully!');
            window.location.href = 'dashboard.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Capsule</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create Capsule</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content_type">Content Type:</label>
            <select class="form-control" name="content_type" required id="content_type">
                <option value="text" selected>Text</option>
                <option value="image">Image</option>
                <option value="video">Video</option>
            </select>
        </div>
        
        <div class="form-group" id="text_area_container">
            <label for="content">Content:</label>
            <textarea class="form-control" name="content" id="content" rows="4"></textarea>
        </div>
        
        <div class="form-group" id="file_input_container" style="display: none;">
            <label for="fileToUpload">Upload File:</label>
            <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload">
        </div>
        
        <div class="form-group">
            <label for="access_type">Access Type:</label>
            <select class="form-control" name="access_type" id="access_type">
                <option value="private">Private</option>
                <option value="public">Public</option>
                <option value="group">Group</option>
            </select>
        </div>
        
        <div class="form-group" id="groupSelect" style="display: none;">
            <label for="shared_group_id">Select Group:</label>
            <select class="form-control" name="shared_group_id" id="shared_group_id">
                <option value="">None</option>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <option value="<?php echo $group['group_id']; ?>"><?php echo htmlspecialchars($group['group_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="unlock_date">Unlock Date:</label>
            <input type="date" class="form-control" name="unlock_date" required>
        </div>
        
        <div class="form-group">
            <label for="unlock_time">Unlock Time:</label>
            <input type="time" class="form-control" name="unlock_time" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Capsule</button>
    </form>
</div>

<script>
    document.getElementById('content_type').addEventListener('change', function() {
        var contentType = this.value;
        document.getElementById('text_area_container').style.display = contentType === 'text' ? 'block' : 'none';
        document.getElementById('file_input_container').style.display = contentType !== 'text' ? 'block' : 'none';
    });

    document.getElementById('access_type').addEventListener('change', function() {
        document.getElementById('groupSelect').style.display = this.value === 'group' ? 'block' : 'none';
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
