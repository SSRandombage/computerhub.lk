<?php
/**
 * Shopping Cart Page
 * 
 * Displays the items currently added to the logged-in user's cart, calculates the 
 * total cost, and provides options to remove items or place an order.
 */

// Start the session to track logged-in users
session_start();

// Include database connection settings
include 'NewBackend/database/db.php';

// 1. LOGIN ENFORCEMENT
// If the user_id session variable is not set, redirect the user back to the Login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. FETCH CART ITEMS FROM DATABASE
// We use a SQL JOIN query to retrieve the quantity from cart_items, 
// and the product name, price, and ID from the products table.
$stmt = $conn->prepare("
    SELECT ci.quantity, p.id, p.name, p.price 
    FROM cart_items ci 
    JOIN products p ON ci.product_id = p.id 
    WHERE ci.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
$cart_count = 0;

// Loop through each row returned by the SQL query and add it to our array
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    // Calculate subtotal for each item (price * quantity) and add to total
    $total += $row['price'] * $row['quantity'];
    // Accumulate total quantity of items in the cart
    $cart_count += $row['quantity'];
}

$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <!-- Include stylesheet files for styling and navigation bar layout -->
    <link rel="stylesheet" href="Cart.css?v=1.3" >
    <link rel="stylesheet" href="Nav.css?v=1.3" >
</head>
<body>
    <!-- 3. NAVIGATION BAR -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.html">Home</a>
            <a href="Categories.html">Categories</a>
            <a href="About Us.html">About Us</a>
        </div>
        <div class="nav-right">
            <!-- Display the user's name securely using htmlspecialchars to prevent script injection -->
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
            <a href="Cart.php" class="cart-link">
                <img class="cart-icon" src="Cart.png" alt="Cart">
                <!-- If cart has items, show the count badge -->
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
            <a href="NewBackend/actions/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
    
    <!-- 4. NOTIFICATION CONTAINER (For success/error messages) -->
    <div id="message-container" style="display: none;">
        <p id="message-text"></p>
    </div>
    
    <h1 class="cart-header">Your Cart</h1>
    
    <!-- 5. CART ITEMS LIST -->
    <?php if (empty($cart_items)): ?>
        <!-- Displayed if no items are in the cart database -->
        <div class="empty-cart-message">
            <p>Your cart is empty.</p>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item-card">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p class="price">LKR <?= number_format($item['price'], 2) ?></p>
                <p class="quantity">Quantity: <?= $item['quantity'] ?></p>
                <p class="subtotal">Subtotal: LKR <?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                
                <!-- Remove item button form -->
                <form action="NewBackend/actions/remove_from_cart.php" method="get" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="remove-button">Remove</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Total Cost Display -->
        <div class="total-section">
            <div class="total-label">Total:</div>
            <div class="total-amount">LKR <?= number_format($total, 2) ?></div>
        </div>
        
        <!-- Checkout Button -->
        <div class="checkout-section">
            <form action="NewBackend/actions/place_order.php" method="post">
                <button type="submit" class="checkout-button">Place Order</button>
            </form>
        </div>
    <?php endif; ?>
    
    <!-- Link to return back to shopping categories -->
    <a href="Categories.html"><button class="category-button">Return to Shop</button></a>

    <!-- Footer Area -->
    <footer class="footer">
        <div class="footer-content">
            <p><strong>Address:</strong> 123 Tech Street, Colombo</p>
            <p><strong>Phone:</strong> 0771234568</p>
            <p><strong>E-mail:</strong> computerhub@gmail.com</p>
        </div>
    </footer>

    <!-- 6. ERROR/SUCCESS QUERY STRING HANDLER -->
    <script>
        // Check if the URL has ?error=... or ?success=... parameters to display alert messages
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const success = urlParams.get('success');
        
        const messageContainer = document.getElementById('message-container');
        const messageText = document.getElementById('message-text');
        
        if (error) {
            messageContainer.style.display = 'block';
            messageContainer.style.backgroundColor = '#ffebee';
            messageContainer.style.color = '#c62828';
            messageContainer.style.padding = '10px';
            messageContainer.style.borderRadius = '5px';
            messageContainer.style.marginBottom = '20px';
            messageContainer.style.textAlign = 'center';
            
            const errorMessages = {
                'invalid_product': 'Invalid product selected.',
                'cart_error': 'Error processing cart operation. Please try again.',
                'order_error': 'Error placing order. Please try again.',
                'insufficient_stock': 'Some items are out of stock. Please check your cart.'
            };
            
            messageText.textContent = errorMessages[error] || 'An error occurred. Please try again.';
        }
        
        if (success) {
            messageContainer.style.display = 'block';
            messageContainer.style.backgroundColor = '#e8f5e8';
            messageContainer.style.color = '#2e7d32';
            messageContainer.style.padding = '10px';
            messageContainer.style.borderRadius = '5px';
            messageContainer.style.marginBottom = '20px';
            messageContainer.style.textAlign = 'center';
            
            const successMessages = {
                'removed_from_cart': 'Product removed from cart successfully!',
                'added_to_cart': 'Product added to cart successfully!',
                'order_placed': 'Order placed successfully! Your order has been confirmed.'
            };
            
            messageText.textContent = successMessages[success] || 'Operation completed successfully.';
        }
    </script>
</body>
</html>
