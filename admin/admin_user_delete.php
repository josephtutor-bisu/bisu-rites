<?php
session_start();
require_once "../db_connect.php";

// Check if user is Superadmin (Role ID 1)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){
    header("location: ../login.php");
    exit;
}

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    // Prevent superadmin from deleting themselves
    if($id == $_SESSION["id"]) {
        echo "<script>alert('You cannot delete your own active session!'); window.location.href='admin_users.php';</script>";
        exit;
    }
    
    // Fetch username first for the log
    $u_sql = "SELECT username FROM users WHERE user_id = ?";
    $u_stmt = $conn->prepare($u_sql);
    $u_stmt->bind_param("i", $id);
    $u_stmt->execute();
    $target_user = $u_stmt->get_result()->fetch_assoc();
    $target_username = $target_user ? $target_user['username'] : 'Unknown User';

    // Proceed to delete
    $sql = "DELETE FROM users WHERE user_id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            
            // --- SYSTEM LOG ENTRY ---
            $log_action = "DELETE";
            $log_details = "Deleted user account: " . $target_username . " (ID: " . $id . ")";
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_sql = "INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES (?, ?, ?, ?)";
            if($log_stmt = $conn->prepare($log_sql)){
                $log_stmt->bind_param("isss", $_SESSION['id'], $log_action, $log_details, $ip);
                $log_stmt->execute();
                $log_stmt->close();
            }
            // ------------------------

            header("location: admin_users.php");
            exit;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
} else {
    header("location: admin_users.php");
    exit;
}
$conn->close();
?>