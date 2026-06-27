<?php
session_start();
include '../database/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../Categories.php");
    exit();
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

// Validate input
if ($product_id <= 0 || $quantity <= 0) {
    header("Location: ../../Categories.php?error=invalid_product");
    exit();
}

// Validate quantity limits
if ($quantity > 10) {
    header("Location: ../../Categories.php?error=quantity_limit");
    exit();
}

try {
    // Check if product exists and has sufficient stock
    $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        header("Location: ../../Categories.php?error=product_not_found");
        exit();
    }
    
    // Check stock
    if ($quantity > $product['stock_quantity']) {
        header("Location: ../../Categories.php?error=insufficient_stock");
        exit();
    }

    // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both new and existing items
    $sql = "
        INSERT INTO cart_items (user_id, product_id, quantity) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to cart
    header("Location: ../../Cart.php?success=added_to_cart");
    exit();
    
} catch (Exception $e) {
    error_log("Cart error: " . $e->getMessage());
    header("Location: ../../Categories.php?error=cart_error");
    exit();
}
?>