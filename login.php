<?php
// Include the config.php file to establish the database connection
include('config.php');

// Start the session for login management
session_start();

// Check if user is already logged in, redirect to home page if so
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php'); // Redirect to main page
    exit;
}

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Fetch user data from the database
    $sql = "SELECT * FROM users WHERE name = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password against the hashed password stored in the database
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; // Menyimpan user_id untuk digunakan saat logout

            // Log the login activity
            logActivity($user['id'], 'login', 'User logged in successfully.');

            header('Location: index.php'); // Redirect to main page after successful login
            exit;
        } else {
            // Invalid password
            $error_message = "Invalid username or password.";
        }
    } else {
        // User not found
        $error_message = "Invalid username or password.";
    }
}

// Function to log activity in the database
function logActivity($user_id, $activity_type, $activity_description) {
    global $conn;  // Use the global connection from config.php

    // Prepare and execute the query to insert log
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, activity_description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $activity_type, $activity_description);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for animations -->
    <style>
        body {
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Login Akun Regent</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Login</button>
        </form>
    </div>
</body>
</html>
