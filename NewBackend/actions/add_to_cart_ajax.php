<?php
/**
 * Add Product to Cart (Asynchronous Backend Action)
 * 
 * This script processes requests sent from the client-side JavaScript (using fetch)
 * to add items to the user's shopping cart. It returns a JSON response indicating
 * success or failure, which Javascript reads to display toast alerts.
 */

// Start PHP session to access $_SESSION variables (to see who is currently logged in)
session_start();

// Include database connection details ($conn variable)
include '../database/db.php';

// Tell the browser that this script will return data in JSON format instead of HTML
header('Content-Type: application/json');

// 1. AUTHENTICATION CHECK
// If 'user_id' is not set in the session, the user is not logged in.
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please login to add items to cart'
    ]);
    exit(); // Stop executing the script
}

// 2. REQUEST METHOD VALIDATION
// Make sure this script was accessed via a POST request (which is standard for sending data)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
    exit();
}

// 3. INPUT RETRIEVAL & SANITIZATION
// Retrieve product ID and quantity from the POST request data
// Cast them to integers using (int) to ensure they are numeric values (prevents SQL Injection)
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

// Validate that we received logical values
if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid product or quantity'
    ]);
    exit();
}

// Restrict maximum quantity for a single add request
if ($quantity > 10) {
    echo json_encode([
        'success' => false, 
        'message' => 'Maximum quantity limit is 10'
    ]);
    exit();
}

try {
    // 4. CHECK PRODUCT STOCK IN DATABASE
    // Prepare a secure SQL query to select product name and stock quantity
    $stmt = $conn->prepare("SELECT name, stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id); // Bind the product ID to the '?' placeholder
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // If no product is found, return error
    if (!$product) {
        echo json_encode([
            'success' => false, 
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Verify that the requested quantity doesn't exceed stock
    if ($quantity > $product['stock_quantity']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Insufficient stock available'
        ]);
        exit();
    }

    // 5. INSERT OR UPDATE CART ITEMS
    // If the product is already in the cart for this user, we increment the quantity.
    // Otherwise, we insert a new row. The SQL 'ON DUPLICATE KEY UPDATE' handles this cleanly.
    $sql = "
        INSERT INTO cart_items (user_id, product_id, quantity) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    $stmt->close();
    
    // 6. RETURN SUCCESS RESPONSE
    echo json_encode([
        'success' => true, 
        'message' => 'Added to cart successfully!',
        'product_name' => $product['name']
    ]);
    
} catch (Exception $e) {
    // If any database or runtime error occurs, log it on the server and return error message
    error_log("Cart error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error adding to cart. Please try again.'
    ]);
}
?>