<?php
// Include the config.php file to establish the database connection
include('config.php');
include('start.php');

// Fetch all logs from the activity_log table
$sql = "SELECT a.id, u.name AS username, a.activity_type, a.activity_description, a.timestamp
        FROM activity_log a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.timestamp DESC"; // Order by timestamp to show most recent first
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            padding-top: 20px;
        }
        .table-container {
            margin: 0 auto;
            max-width: 90%;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <h2 class="text-center mb-4">Activity Logs</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Activity Type</th>
                            <th>Activity Description</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_description']); ?></td>
                                <td><?php echo $row['timestamp']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning text-center">No activity logs found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
