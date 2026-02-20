<?php
// db_connect.php
$servername = "localhost";
$username = "root";      // Default XAMPP username
$password = "";          // Default XAMPP password (leave empty)
$dbname = "bisu_rites";  // The database we created in the previous step

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // Uncomment this line if you want to test the connection
?>