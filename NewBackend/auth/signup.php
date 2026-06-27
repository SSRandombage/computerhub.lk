<?php
session_start();
include '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        header('Location: ../../signup.html?error=empty_fields');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../../signup.html?error=invalid_email');
        exit();
    }

    if (strlen($password) < 6) {
        header('Location: ../../signup.html?error=weak_password');
        exit();
    }

    if ($password !== $confirm_password) {
        header('Location: ../../signup.html?error=password_mismatch');
        exit();
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        header('Location: ../../signup.html?error=email_exists');
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user with prepared statement
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        header("Location: ../../Login.html?success=registration_complete");
        exit();
    } else {
        header('Location: ../../signup.html?error=registration_failed');
    }
    
    $stmt->close();
}
?>