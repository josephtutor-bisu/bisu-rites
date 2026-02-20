<?php
session_start();
require_once "../db_connect.php";

// Check if Superadmin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){
    header("location: ../login.php");
    exit;
}

if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = $_GET["id"];
    
    // Prevent deleting your own account
    if($id == $_SESSION["id"]){
        echo "<script>alert('You cannot delete your own account!'); window.location.href='admin_users.php';</script>";
        exit;
    }
    
    $sql = "DELETE FROM users WHERE user_id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            header("location: admin_users.php");
        } else {
            echo "Oops! Something went wrong.";
        }
    }
    $stmt->close();
}
$conn->close();
?>