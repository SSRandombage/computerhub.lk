<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=You do not have permission to access this page');
    exit();
}

// Fetch all products from the database
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../Nav.css?v=1.3">
    <link rel="stylesheet" href="admin.css?v=1.3">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="dashboard.php">Admin Home</a>
            <a href="manage_products.php">Manage Products</a>
            <!-- <a href="view_orders.php">View Orders</a> -->
        </div>
        <div class="nav-right">
            <span>Welcome, Admin (<?= htmlspecialchars($_SESSION['user_name']) ?>)!</span>
            <a href="../NewBackend/actions/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container main-content">
        <h1>Manage Products</h1>

        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?= htmlspecialchars($_GET['success']) ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
            <a href="add_product.php" class="add-product-btn" style="margin-bottom: 0;">Add New Product</a>
            <div class="admin-search-container" style="margin-bottom: 0; max-width: 320px;">
                <input type="text" id="productSearch" class="admin-search-input" placeholder="Search products...">
                <div class="admin-search-icon">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>
        </div>

        <table id="productsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>LKR <?= number_format($product['price'], 2) ?></td>
                    <td class="action-links">
                        <a href="edit_product.php?id=<?= $product['id'] ?>">Edit</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p><strong>Address:</strong> 123 Tech Street, Colombo</p>
            <p><strong>Phone:</strong> 0771234568</p>
            <p><strong>E-mail:</strong> computerhub@gmail.com</p>
        </div>
    </footer>
    
    <script>
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#productsTable tbody tr');
            
            rows.forEach(row => {
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const price = row.cells[2].textContent.toLowerCase();
                
                if (id.includes(query) || name.includes(query) || price.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 

