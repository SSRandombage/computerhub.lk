<?php
session_start();
include '../database/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect_location = ($_SESSION['role'] === 'admin') ? '../../admin/dashboard.php' : '../../index.html';
    header("Location: $redirect_location");
    exit();
}

// Ensure form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Login.html');
    exit();
}

// Get and validate input
$email = trim($_POST['email']);
$password = trim($_POST['password']);
if (empty($email) || empty($password)) {
    header('Location: ../../Login.html?error=Please fill all fields');
    exit();
}

// --- Logic for Admin Login ---
if ($email === 'admin@computerhub.com') {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['user_name'] = $admin['name'];
                $_SESSION['user_email'] = $admin['email'];
                $_SESSION['role'] = 'admin';
                header('Location: ../../admin/dashboard.php');
                exit();
            }
        }
        // If any part of the admin check fails, it ends here.
        header('Location: ../../Login.html?error=Invalid admin credentials');
        exit();
    } catch (Exception $e) {
        error_log("Admin Login Error: " . $e->getMessage());
        header('Location: ../../Login.html?error=An error occurred');
        exit();
    }
}

// --- Logic for User Login ---
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = 'user';
            header('Location: ../../index.html');
            exit();
        }
    }
    // If any part of the user check fails, it ends here.
    header('Location: ../../Login.html?error=Invalid user credentials');
    exit();
} catch (Exception $e) {
    error_log("User Login Error: " . $e->getMessage());
    header('Location: ../../Login.html?error=An error occurred');
    exit();
}
?>