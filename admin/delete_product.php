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
    header('Location: manage_products.php?error=No product selected for deletion');
    exit();
}

$product_id = $_GET['id'];

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    // Success
    header('Location: manage_products.php?success=Product deleted successfully');
    exit();
} else {
    // Error
    header('Location: manage_products.php?error=Failed to delete product');
    exit();
}

$stmt->close();
$conn->close();
?> 