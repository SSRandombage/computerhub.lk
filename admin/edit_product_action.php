<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=Unauthorized access');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Basic validation
    if (empty($id) || empty($name) || empty($price)) {
        header('Location: edit_product.php?id=' . $id . '&error=Name and price are required');
        exit();
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $name, $description, $price, $id);
    
    if ($stmt->execute()) {
        // Success
        header('Location: manage_products.php?success=Product updated successfully');
        exit();
    } else {
        // Error
        header('Location: edit_product.php?id=' . $id . '&error=Failed to update product');
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // Not a POST request
    header('Location: manage_products.php');
    exit();
}
?> 