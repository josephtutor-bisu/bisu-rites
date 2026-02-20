<?php
session_start();
require_once "../db_connect.php";

// Check if Superadmin
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ header("location: ../login.php"); exit; }

// Fetch All Colleges
$sql = "SELECT * FROM colleges ORDER BY college_name ASC";
$result = $conn->query($sql);

$page_title = "Manage Colleges";
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
            <h1 class="header-title"><i class="fas fa-university mr-2"></i> Manage Colleges</h1>
            <div class="header-actions">
                <a href="admin_college_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add College
                </a>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            
            <!-- Table Card -->
            <div class="card animate-fadeIn">
                <div class="card-header">
                    <h2>Colleges & Departments</h2>
                    <p>Manage all colleges and departments in your institution</p>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 8%;"><i class="fas fa-hashtag"></i> ID</th>
                                <th style="width: 15%;"><i class="fas fa-barcode"></i> Code</th>
                                <th style="width: 50%;"><i class="fas fa-building"></i> College Name</th>
                                <th style="width: 27%;"><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><span class='badge badge-primary'>" . $row["college_id"] . "</span></td>";
                                    echo "<td><strong>" . htmlspecialchars($row["college_code"]) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($row["college_name"]) . "</td>";
                                    echo "<td class='flex gap-4'>";
                                    echo '<a href="admin_college_edit.php?id='. $row["college_id"] .'" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                                    echo '<a href="admin_college_delete.php?id='. $row["college_id"] .'" class="btn btn-destructive btn-sm" onclick="return confirm(\'Are you sure? This action cannot be undone.\')"><i class="fas fa-trash"></i> Delete</a>';
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center p-8'><i class='fas fa-inbox text-2xl text-muted mb-3'></i><p>No colleges found. <a href='admin_college_add.php' style='color: var(--primary);'>Add one now</a></p></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-primary mt-6">
                <i class="fas fa-info-circle alert-icon"></i>
                <div class="alert-content">
                    <h4>Tip</h4>
                    <p>College codes are used as shortcuts across the system (e.g., CEA, CAS). Make sure to use meaningful and consistent acronyms.</p>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>