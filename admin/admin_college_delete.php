<?php
session_start();
require_once "../db_connect.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ header("location: ../login.php"); exit; }

if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = $_GET["id"];
    
    // Check if it's the ADMIN college (ID 1 usually) - prevent deleting system default
    // Assuming ID 1 is the 'System Administration' we created in the SQL setup
    if($id == 1){
         echo "<script>alert('Cannot delete the default System Admin department.'); window.location.href='admin_colleges.php';</script>";
         exit;
    }

    $sql = "DELETE FROM colleges WHERE college_id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            header("location: admin_colleges.php");
        } else {
            echo "Error deleting record.";
        }
    }
}
?>