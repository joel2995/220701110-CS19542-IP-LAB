<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Public view count
$public_stmt = $conn->prepare("SELECT COUNT(*) AS public_views FROM capsules WHERE access_type = 'public'");
$public_stmt->execute();
$public_views = $public_stmt->get_result()->fetch_assoc()['public_views'];

// Group view count
$group_stmt = $conn->prepare("SELECT COUNT(*) AS group_views FROM capsules WHERE access_type = 'group' AND shared_group_id IN (SELECT group_id FROM group_members WHERE user_id = ?)");
$group_stmt->bind_param("i", $user_id);
$group_stmt->execute();
$group_views = $group_stmt->get_result()->fetch_assoc()['group_views'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Analytics</h2>
    <canvas id="viewChart" width="400" height="200"></canvas>
</div>

<script>
    var ctx = document.getElementById('viewChart').getContext('2d');
    var viewChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Public Views', 'Group Views'],
            datasets: [{
                label: '# of Views',
                data: [<?php echo $public_views; ?>, <?php echo $group_views; ?>],
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
