<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../database/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get cart items for the user
    $stmt = $conn->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price, p.stock_quantity
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    if (empty($cart_items)) {
        throw new Exception("Cart is empty");
    }
    
    // Check stock availability
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock_quantity']) {
            throw new Exception("Insufficient stock for " . $item['name']);
        }
    }
    
    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Create order
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, status, order_date) 
        VALUES (?, ?, 'pending', NOW())
    ");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Create order items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) 
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($cart_items as $item) {
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        
        // Update stock
        $new_stock = $item['stock_quantity'] - $item['quantity'];
        $update_stmt = $conn->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_stock, $item['product_id']);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $stmt->close();
    
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // Redirect with success message
    header('Location: ../../Cart.php?success=order_placed');
    exit();
    
} catch (Exception $e) {
    // Rollback transaction
    if ($conn) {
        $conn->rollback();
    }
    
    // Log the error
    $user_id_for_log = isset($user_id) ? $user_id : 'not_set';
    $error_message = date('[Y-m-d H:i:s] ') . "Order placement failed for user_id: {$user_id_for_log}. Error: " . $e->getMessage() . "\n";
    file_put_contents(__DIR__ . '/../logs/error.log', $error_message, FILE_APPEND);
    
    // Redirect with a generic error message
    header('Location: ../../Cart.php?error=order_failed');
    exit();
}
?> 