<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=Unauthorized access');
    exit();
}

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_products.php?error=No product selected for editing');
    exit();
}

$product_id = $_GET['id'];

// Fetch the product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: manage_products.php?error=Product not found');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
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
        <h1>Edit Product</h1>
        <form action="edit_product_action.php" method="post">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price (LKR)</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= $product['price'] ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="form-buttons">
                <button type="submit" class="submit-btn">Update Product</button>
                <a href="manage_products.php" class="cancel-btn">Cancel</a>
            </div>
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

