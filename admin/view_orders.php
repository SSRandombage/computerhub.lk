<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=You do not have permission to access this page');
    exit();
}

// Fetch all orders with user details
$query = "
    SELECT 
        o.id AS order_id,
        u.name AS user_name,
        u.email AS user_email,
        o.total_amount,
        o.status,
        o.order_date,
        COUNT(oi.id) AS item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.order_date DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
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
            <span>Welcome, Admin (<?= htmlspecialchars($_SESSION['user_name']) ?>)!</span>
            <a href="../NewBackend/actions/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>View Orders</h1>
        
        <div style="display: flex; justify-content: flex-end; margin-bottom: 25px;">
            <div class="admin-search-container" style="margin-bottom: 0; max-width: 350px;">
                <input type="text" id="orderSearch" class="admin-search-input" placeholder="Search orders...">
                <div class="admin-search-icon">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>
        </div>
        
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['user_email']) ?></td>
                        <td>LKR <?= number_format($order['total_amount'], 2) ?></td>
                        <td><span class="status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td><?= $order['item_count'] ?> items</td>
                        <td><?= date('Y-m-d H:i:s', strtotime($order['order_date'])) ?></td>
                        <td>
                            <a href="order_details.php?id=<?= $order['order_id'] ?>" class="view-details">View Details</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No orders found.</td>
                    </tr>
                <?php endif; ?>
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
        document.getElementById('orderSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length < 8) return;
                
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const amount = row.cells[3].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();
                const date = row.cells[6].textContent.toLowerCase();
                
                if (id.includes(query) || name.includes(query) || email.includes(query) || 
                    amount.includes(query) || status.includes(query) || date.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 

