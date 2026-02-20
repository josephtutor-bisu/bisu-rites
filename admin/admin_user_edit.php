<?php
session_start();
require_once "../db_connect.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ header("location: ../login.php"); exit; }

$id = $_GET['id'];
$msg = "";

// Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role_id = $_POST['role_id'];
    
    // Only update password if user typed something new
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name=?, last_name=?, role_id=?, password_hash=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $first_name, $last_name, $role_id, $password, $id);
    } else {
        $sql = "UPDATE users SET first_name=?, last_name=?, role_id=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $first_name, $last_name, $role_id, $id);
    }
    
    if($stmt->execute()){
        header("location: admin_users.php");
        exit;
    } else {
        $msg = "Error updating user.";
    }
}

// Fetch Current Data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$page_title = "Edit User";
include "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - BISU R.I.T.E.S</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Shadcn Components -->
    <link rel="stylesheet" href="../assets/shadcn.css">
</head>
<body>

<style>
    body {
        display: flex;
        margin: 0;
        padding: 0;
    }
    .page-container {
        display: flex;
        width: 100%;
    }
</style>

<div class="page-container">
    <?php include "../includes/navigation.php"; ?>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="header">
            <h1 class="header-title">
                <i class="fas fa-user-edit" style="margin-right: 0.75rem; color: var(--primary);"></i>
                Edit User Account
            </h1>
            <div class="header-actions">
                <button onclick="window.history.back()" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            
            <!-- Form Card -->
            <div class="card animate-fadeIn" style="max-width: 700px;">
                <div class="card-header">
                    <h2>Update User Information</h2>
                    <p>Modify user account details and role assignments</p>
                </div>
                
                <div class="card-body">
                    <?php if($msg): ?>
                        <div class="alert alert-destructive mb-6">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div class="alert-content">
                                <h4>Error</h4>
                                <p><?php echo htmlspecialchars($msg); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form action="" method="post" class="space-y-6">
                        <!-- Name Fields Row -->
                        <div class="grid grid-cols-2">
                            <div class="form-group">
                                <label for="first_name" class="block text-sm font-medium text-foreground mb-2">
                                    <i class="fas fa-user-circle mr-2 text-primary"></i>First Name
                                </label>
                                <input 
                                    type="text" 
                                    id="first_name"
                                    name="first_name" 
                                    class="input" 
                                    value="<?php echo htmlspecialchars($user['first_name']); ?>"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="last_name" class="block text-sm font-medium text-foreground mb-2">
                                    <i class="fas fa-user-circle mr-2 text-primary"></i>Last Name
                                </label>
                                <input 
                                    type="text" 
                                    id="last_name"
                                    name="last_name" 
                                    class="input" 
                                    value="<?php echo htmlspecialchars($user['last_name']); ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <!-- Username Section -->
                        <div>
                            <h3 class="text-sm font-semibold text-foreground mb-4 pb-2 border-b border-border">Account Information</h3>
                            
                            <div class="form-group">
                                <label for="username" class="block text-sm font-medium text-foreground mb-2">
                                    <i class="fas fa-at mr-2 text-muted"></i>Username (Cannot be changed)
                                </label>
                                <input 
                                    type="text" 
                                    id="username"
                                    value="<?php echo htmlspecialchars($user['username']); ?>"
                                    class="input" 
                                    disabled
                                    style="opacity: 0.7; cursor: not-allowed;"
                                >
                                <p class="text-xs text-muted mt-1">Username is permanent and cannot be modified.</p>
                            </div>
                        </div>
                        
                        <!-- Password Section -->
                        <div>
                            <h3 class="text-sm font-semibold text-foreground mb-4 pb-2 border-b border-border">Change Password</h3>
                            
                            <div class="form-group">
                                <label for="password" class="block text-sm font-medium text-foreground mb-2">
                                    <i class="fas fa-lock mr-2 text-primary"></i>New Password
                                </label>
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    class="input" 
                                    placeholder="Leave blank to keep current password"
                                >
                                <p class="text-xs text-muted mt-1">Enter a new password only if you want to change it. Otherwise leave blank.</p>
                            </div>
                        </div>
                        
                        <!-- Role Selection -->
                        <div>
                            <h3 class="text-sm font-semibold text-foreground mb-4 pb-2 border-b border-border">Role Assignment</h3>
                            
                            <div class="form-group">
                                <label for="role_id" class="block text-sm font-medium text-foreground mb-2">
                                    <i class="fas fa-shield-alt mr-2 text-primary"></i>User Role
                                </label>
                                <select name="role_id" id="role_id" class="input" required>
                                    <option value="2" <?php if($user['role_id']==2) echo 'selected'; ?>>R&D Director</option>
                                    <option value="3" <?php if($user['role_id']==3) echo 'selected'; ?>>ITSO Director</option>
                                    <option value="4" <?php if($user['role_id']==4) echo 'selected'; ?>>Extension Director</option>
                                    <option value="5" <?php if($user['role_id']==5) echo 'selected'; ?>>Faculty</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Warning Alert -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <div class="alert-content">
                                <h4>Important</h4>
                                <p>Changes to user information will take effect immediately. The user will need to log out and log back in if their password is changed.</p>
                            </div>
                        </div>
                        
                        <div class="card-footer" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
                            <a href="admin_users.php" class="btn btn-ghost">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>