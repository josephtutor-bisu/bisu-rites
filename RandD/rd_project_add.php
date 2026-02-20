<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ header("location: ../login.php"); exit; }

$title = $abstract = $status = $budget = $start_date = $end_date = $college_id = "";
$error = "";

// Fetch Colleges for the dropdown
$colleges_sql = "SELECT college_id, college_name FROM colleges ORDER BY college_name ASC";
$colleges_result = $conn->query($colleges_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["project_title"]);
    $abstract = trim($_POST["abstract"]);
    $status = trim($_POST["status"]);
    $budget = empty($_POST["budget"]) ? 0.00 : floatval($_POST["budget"]);
    $start_date = empty($_POST["start_date"]) ? NULL : $_POST["start_date"];
    $end_date = empty($_POST["end_date"]) ? NULL : $_POST["end_date"];
    $college_id = empty($_POST["college_id"]) ? NULL : intval($_POST["college_id"]);

    if(empty($title)){
        $error = "Project Title is required.";
    } else {
        $sql = "INSERT INTO rd_projects (project_title, abstract, status, budget, start_date, end_date, college_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("sssdssi", $title, $abstract, $status, $budget, $start_date, $end_date, $college_id);
            if($stmt->execute()){
                header("location: rd_projects.php");
                exit;
            } else {
                $error = "Database Error: " . $conn->error;
            }
        }
    }
}

$page_title = "New Research Proposal";
include "../includes/header.php";
?>

<div class="page-container">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content">
        <div class="header">
            <h1 class="header-title"><i class="fas fa-plus-circle mr-2 text-primary"></i> New Proposal</h1>
            <div class="header-actions">
                <button onclick="window.history.back()" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Back</button>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="card animate-fadeIn" style="max-width: 800px; margin: 0 auto;">
                <div class="card-header">
                    <h2>Research Details</h2>
                    <p>Enter the information for the new research study.</p>
                </div>
                
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-destructive mb-6">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div class="alert-content"><p><?php echo htmlspecialchars($error); ?></p></div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="space-y-6">
                        <div class="form-group">
                            <label class="block text-sm font-medium mb-2">Project Title *</label>
                            <input type="text" name="project_title" class="input" required>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium mb-2">Abstract / Description</label>
                            <textarea name="abstract" class="textarea"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2">
                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Lead College / Department</label>
                                <select name="college_id" class="input" required>
                                    <option value="">-- Select College --</option>
                                    <?php 
                                    if ($colleges_result->num_rows > 0) {
                                        while($c = $colleges_result->fetch_assoc()) {
                                            echo "<option value='{$c['college_id']}'>{$c['college_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Status</label>
                                <select name="status" class="input">
                                    <option value="Proposed">Proposed</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3">
                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Budget (₱)</label>
                                <input type="number" step="0.01" name="budget" class="input" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Target Start Date</label>
                                <input type="date" name="start_date" class="input">
                            </div>
                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Target End Date</label>
                                <input type="date" name="end_date" class="input">
                            </div>
                        </div>
                        
                        <div class="card-footer" style="border-top: 1px solid var(--border); padding-top: 1.5rem; justify-content: flex-end;">
                            <a href="rd_projects.php" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Proposal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>