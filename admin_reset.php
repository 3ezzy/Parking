<?php
// This is a utility script to create or reset an admin user
// Delete this file after use for security

// Connect to database
$host = 'localhost';
$dbname = 'parking_management';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create admin user with password: admin123
    $name = 'Admin User';
    $email = 'admin@parking.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';
    
    // Check if admin user already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Update existing admin
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        echo "Admin user password has been reset!";
    } else {
        // Create new admin
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        echo "New admin user has been created!";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
