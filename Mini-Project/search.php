<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search_term = $_POST['search'] ?? '';

$stmt = $conn->prepare("
    SELECT * FROM capsules 
    WHERE (title LIKE ? OR content LIKE ?) 
    AND (user_id = ? OR access_type = 'public' OR (access_type = 'group' AND shared_group_id IN (SELECT group_id FROM group_members WHERE user_id = ?)))
");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$search_like = "%$search_term%";
$stmt->bind_param("ssii", $search_like, $search_like, $user_id, $user_id);
$stmt->execute();
$capsules = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Capsules</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Search Capsules</h2>
    <form method="POST" id="searchForm">
        <input type="text" name="search" id="searchInput" placeholder="Search by keyword..." class="form-control" required>
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <h3 class="mt-4">Search Results</h3>
    <div id="searchResults">
        <?php while ($capsule = $capsules->fetch_assoc()): ?>
            <div class="result mt-3 p-3 border rounded">
                <h5><?php echo htmlspecialchars($capsule['title']); ?></h5>
                <p><?php echo htmlspecialchars($capsule['content']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    $('#searchInput').on('input', function() {
        let searchTerm = $(this).val();
        $.post('search.php', { search: searchTerm }, function(data) {
            $('#searchResults').html(data);
        });
    });
</script>
</body>
</html>
