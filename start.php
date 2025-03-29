
<?php
// Start the session to handle login validation
session_start();


// Fungsi untuk mencatat log aktivitas
function logActivity($user_id, $activity_type, $activity_description) {
    include('config.php');  // Memasukkan file koneksi database

    // Menggunakan prepared statements untuk menghindari SQL injection
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, activity_description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $activity_type, $activity_description);
    $stmt->execute();
    $stmt->close();
}
// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
?>

