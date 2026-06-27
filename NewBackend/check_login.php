<?php
session_start();
header('Content-Type: application/json');
include 'database/db.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $user_name = $_SESSION['user_name'] ?? 'User';
    
    // Fetch sum of quantity in cart
    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $cart_count = (int)($result['count'] ?? 0);
    $stmt->close();
    
    echo json_encode([
        'logged_in' => true,
        'user_id' => $user_id,
        'user_name' => $user_name,
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?> 