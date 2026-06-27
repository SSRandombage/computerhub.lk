<?php
session_start();

// Simple check - if not admin, redirect to the main login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=Admin access required.');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../Nav.css?v=1.3">
    <link rel="stylesheet" href="admin.css?v=1.3">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="dashboard.php">Admin Home</a>
            <a href="manage_products.php">Manage Products</a>
            <a href="view_orders.php">View Orders</a>
        </div>
        <div class="nav-right">
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
            <a href="../NewBackend/actions/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the control panel. This is where you will manage products and view orders.</p>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p><strong>Address:</strong> 123 Tech Street, Colombo</p>
            <p><strong>Phone:</strong> 0771234568</p>
            <p><strong>E-mail:</strong> computerhub@gmail.com</p>
        </div>
    </footer>
</body>
</html> 

