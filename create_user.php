<?php
// Include the config.php file to establish the database connection
include('config.php');

// Check if the database connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the 'users' table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
)";
if (!mysqli_query($conn, $sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

// Insert the user with name 'fatkhur' and password 'fcnqxds7'
$name = 'fatkhur';
$password = 'fcnqxds7';

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the insert query
$stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $hashed_password);

// Execute the query
if ($stmt->execute()) {
    echo "User added successfully.";
} else {
    echo "Error adding user: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
