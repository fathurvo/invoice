<?php
// Include the config.php file to establish the database connection
include('config.php');

// Start the session for logout management
session_start();

// Check if user is not logged in, redirect to login page if so
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit;
}

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out successfully.');
}

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit;

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
