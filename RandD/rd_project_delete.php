<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is R&D Director (Role ID 2)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ 
    header("location: ../login.php"); 
    exit; 
}

if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = intval($_GET["id"]);
    
    // Optional: You could add logic here to prevent deleting "Completed" projects.
    
    $sql = "DELETE FROM rd_projects WHERE rd_id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Redirect back to the R&D projects list
header("location: rd_projects.php");
exit;
?>