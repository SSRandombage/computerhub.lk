<?php
include '../database/db.php';

try {
    // Check if category filter is provided
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    
    if (!empty($category)) {
        // Fetch products by category
        $stmt = $conn->prepare("SELECT id, name, description, price, image, category, stock_quantity FROM products WHERE category = ? ORDER BY name");
        $stmt->bind_param("s", $category);
    } else {
        // Fetch all products
        $stmt = $conn->prepare("SELECT id, name, description, price, image, category, stock_quantity FROM products ORDER BY category, name");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stock_status = $row['stock_quantity'] > 0 ? "In Stock" : "Out of Stock";
            $stock_class = $row['stock_quantity'] > 0 ? "in-stock" : "out-of-stock";
            
            echo "<div class='product-item'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            if (!empty($row['description'])) {
                echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            }
            echo "<p class='price'>Price: $" . htmlspecialchars(number_format($row['price'], 2)) . "</p>";
            echo "<p class='stock " . htmlspecialchars($stock_class) . "'>" . htmlspecialchars($stock_status) . "</p>";
            if ($row['stock_quantity'] > 0) {
                echo "<form action='../actions/add_to_cart.php' method='post'>";
                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>";
                echo "<input type='number' name='quantity' value='1' min='1' max='" . htmlspecialchars($row['stock_quantity']) . "'>";
                echo "<button type='submit'>Add to Cart</button>";
                echo "</form>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error fetching products: " . $e->getMessage());
    echo "<p>Error loading products. Please try again later.</p>";
}
?>