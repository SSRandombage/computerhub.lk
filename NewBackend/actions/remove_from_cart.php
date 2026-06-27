<?php
session_start();
include '../database/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];
    $user_id = (int)$_SESSION['user_id'];
    
    // Validate input
    if ($product_id <= 0) {
        header("Location: ../../Cart.php?error=invalid_product");
        exit();
    }
    
    // Remove item from database cart (only for this user)
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: ../../Cart.php?success=removed_from_cart");
    exit();
} else {
    header("Location: ../../Cart.php");
    exit();
}
?>