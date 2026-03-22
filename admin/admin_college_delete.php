<?php
session_start();
require_once "../db_connect.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ header("location: ../login.php"); exit; }

if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = $_GET["id"];
    
    // Check if it's the ADMIN college (ID 1 usually) - prevent deleting system default
    if($id == 1){
         echo "<script>alert('Cannot delete the default System Admin department.'); window.location.href='admin_colleges.php';</script>";
         exit;
    }

    // Fetch college name first for the log
    $c_sql = "SELECT college_name, college_code FROM colleges WHERE college_id = ?";
    $c_stmt = $conn->prepare($c_sql);
    $c_stmt->bind_param("i", $id);
    $c_stmt->execute();
    $target_college = $c_stmt->get_result()->fetch_assoc();
    $target_name = $target_college ? $target_college['college_name'] . " (" . $target_college['college_code'] . ")" : "Unknown College";

    $sql = "DELETE FROM colleges WHERE college_id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            
            // --- SYSTEM LOG ENTRY ---
            $log_action = "DELETE";
            $log_details = "Deleted college department: " . $target_name;
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_sql = "INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES (?, ?, ?, ?)";
            if($log_stmt = $conn->prepare($log_sql)){
                $log_stmt->bind_param("isss", $_SESSION['id'], $log_action, $log_details, $ip);
                $log_stmt->execute();
                $log_stmt->close();
            }
            // ------------------------

            header("location: admin_colleges.php");
            exit;
        } else {
            echo "Error deleting record.";
        }
    }
} else {
    header("location: admin_colleges.php");
    exit;
}
?>