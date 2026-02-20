<?php
session_start();
require_once "../db_connect.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ header("location: ../login.php"); exit; }

$code = $name = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = strtoupper(trim($_POST["college_code"])); // Force Uppercase (e.g., 'cea' -> 'CEA')
    $name = trim($_POST["college_name"]);

    if(empty($code) || empty($name)){
        $error = "Please fill in all fields.";
    } else {
        $sql = "INSERT INTO colleges (college_code, college_name) VALUES (?, ?)";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("ss", $code, $name);
            if($stmt->execute()){
                header("location: admin_colleges.php");
                exit;
            } else {
                $error = "Error: College Code might already exist.";
            }
        }
    }
}

$page_title = "Add College";
include "../includes/header.php";
?>

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
                <i class="fas fa-plus-circle" style="margin-right: 0.75rem; color: var(--primary);"></i>
                Add New College
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
            <div class="card animate-fadeIn" style="max-width: 600px;">
                <div class="card-header">
                    <h2>College Information</h2>
                    <p>Add a new college or department to the system</p>
                </div>
                
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-destructive mb-6">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div class="alert-content">
                                <h4>Error</h4>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="space-y-6">
                        <div class="form-group">
                            <label for="college_code" class="block text-sm font-medium text-foreground mb-2">
                                <i class="fas fa-tag mr-2 text-primary"></i>College Code (Acronym)
                            </label>
                            <input 
                                type="text" 
                                id="college_code"
                                name="college_code" 
                                class="input" 
                                placeholder="e.g., CEA, CAS, CTE"
                                value="<?php echo htmlspecialchars($code); ?>"
                                required
                            >
                            <p class="text-xs text-muted mt-1">Use uppercase letters (e.g., CEA for College of Engineering and Architecture)</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="college_name" class="block text-sm font-medium text-foreground mb-2">
                                <i class="fas fa-building mr-2 text-primary"></i>Full College Name
                            </label>
                            <input 
                                type="text" 
                                id="college_name"
                                name="college_name" 
                                class="input" 
                                placeholder="e.g., College of Engineering and Architecture"
                                value="<?php echo htmlspecialchars($name); ?>"
                                required
                            >
                            <p class="text-xs text-muted mt-1">Enter the complete official name of the college</p>
                        </div>
                        
                        <div class="card-footer" style="border-top: 1px solid var(--border); padding-top: 1.5rem; flex-justify: flex-end;">
                            <a href="admin_colleges.php" class="btn btn-ghost">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save College
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>