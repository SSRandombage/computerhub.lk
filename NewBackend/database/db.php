<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "computerhub";

// Create connection with error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set character encoding to UTF-8
    $conn->set_charset("utf8mb4");
    
    // Set timezone (optional - adjust as needed)
    // $conn->query("SET time_zone = '+05:30'");
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}

// Function to safely close database connection
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Register shutdown function to close connection
register_shutdown_function('closeConnection');
?>