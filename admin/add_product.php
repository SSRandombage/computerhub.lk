<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=You do not have permission to access this page');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Product</title>
    <link rel="stylesheet" href="../Nav.css?v=1.3">
    <link rel="stylesheet" href="admin.css?v=1.3">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="dashboard.php">Admin Home</a>
            <a href="manage_products.php">Manage Products</a>
        </div>
        <div class="nav-right">
            <span>Welcome, Admin (<?= htmlspecialchars($_SESSION['user_name']) ?>)!</span>
            <a href="../NewBackend/actions/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container main-content">
        <h1>Add New Product</h1>
        <form action="add_product_action.php" method="post">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Price (LKR)</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <button type="submit" class="submit-btn">Add Product</button>
        </form>
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


