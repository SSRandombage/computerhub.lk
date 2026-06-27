<?php
include '../NewBackend/database/db.php';

echo "<h2>Admin Setup</h2>";

try {
    // Create admin table with long enough password column
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )";
    
    $conn->query($sql);
    echo "✓ Admin table ready<br>";
    
    // Fix existing table structure if it's wrong
    $alter_sql = "ALTER TABLE admins MODIFY password VARCHAR(255) NOT NULL";
    $conn->query($alter_sql);
    
    // Create admin user
    $name = 'Admin';
    $email = 'admin@computerhub.com';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    $stmt->execute();
    
    echo "✓ Admin user created<br>";
    echo "<br><strong>Admin Login:</strong><br>";
    echo "Email: admin@computerhub.com<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='login.html'>Go to Admin Login</a>";
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "✓ Admin user already exists<br>";
        echo "<br><strong>Admin Login:</strong><br>";
        echo "Email: admin@computerhub.com<br>";
        echo "Password: admin123<br>";
        echo "<br><a href='login.html'>Go to Admin Login</a>";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?> 