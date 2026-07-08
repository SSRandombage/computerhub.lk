<?php
include 'NewBackend/database/db.php';

$new_password = 'admin123';
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@computerhub.com' AND role = 'admin'");
$stmt->bind_param("s", $hashed);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    echo "<h2 style='color:green'>✓ Admin password reset successfully!</h2>";
    echo "<p>Email: <strong>admin@computerhub.com</strong></p>";
    echo "<p>Password: <strong>admin123</strong></p>";
    echo "<br><a href='Login.html'>Go to Login</a>";
} else {
    echo "<h2 style='color:red'>✗ Failed — admin user not found in users table.</h2>";
    // Show what's in users table
    $result = $conn->query("SELECT id, name, email, role FROM users");
    echo "<h3>Users in database:</h3><pre>";
    while($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}
?>
