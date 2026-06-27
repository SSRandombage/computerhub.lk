<?php
session_start();
include '../NewBackend/database/db.php';

// Protection guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login.html?error=Unauthorized access');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Basic validation
    if (empty($name) || empty($price)) {
        // Handle error - maybe redirect back with an error message
        header('Location: add_product.php?error=Name and price are required');
        exit();
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $description, $price);
    
    if ($stmt->execute()) {
        // Success
        header('Location: manage_products.php?success=Product added successfully');
        exit();
    } else {
        // Error
        header('Location: add_product.php?error=Failed to add product');
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // Not a POST request
    header('Location: add_product.php');
    exit();
}
?>