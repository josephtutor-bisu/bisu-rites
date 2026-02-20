<?php
// setup_admin.php
require_once "db_connect.php";

// 1. Create Roles if they don't exist
$conn->query("INSERT IGNORE INTO system_roles (role_id, role_name) VALUES (1, 'Superadmin')");

// 2. Create a default admin user
$username = "admin";
$password = "admin123"; // This is the password you will use to login
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$firstname = "System";
$lastname = "Administrator";
$role_id = 1; // Superadmin role

$sql = "INSERT INTO users (username, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?)";

if($stmt = $conn->prepare($sql)){
    $stmt->bind_param("ssssi", $username, $password_hash, $firstname, $lastname, $role_id);
    if($stmt->execute()){
        echo "Superadmin created successfully! <br>";
        echo "Username: <b>admin</b> <br>";
        echo "Password: <b>admin123</b> <br>";
        echo "<a href='login.php'>Go to Login Page</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>