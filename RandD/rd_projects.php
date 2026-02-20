<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is R&D Director (Role ID 2)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ header("location: ../login.php"); exit; }

// Fetch R&D Projects and their College Names
$sql = "SELECT p.*, c.college_code 
        FROM rd_projects p 
        LEFT JOIN colleges c ON p.college_id = c.college_id 
        ORDER BY p.rd_id DESC";
$result = $conn->query($sql);

$page_title = "Manage Research Projects";
include "../includes/header.php";
?>

<div class="page-container">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content">
        <div class="header">
            <h1 class="header-title"><i class="fas fa-flask-vial mr-2" style="color: var(--primary);"></i> Research Projects</h1>
            <div class="header-actions">
                <a href="rd_project_add.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> New Proposal
                </a>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="card animate-fadeIn">
                <div class="card-header">
                    <h2>Research Portfolio</h2>
                    <p>Track and manage all university research proposals and ongoing studies.</p>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project Title</th>
                                <th>College</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    // Status Badge Logic
                                    $statusClass = 'badge-outline';
                                    if($row['status'] == 'Proposed') $statusClass = 'badge-warning';
                                    if($row['status'] == 'Ongoing') $statusClass = 'badge-primary';
                                    if($row['status'] == 'Completed') $statusClass = 'badge-success';
                                    
                                    echo "<tr>";
                                    echo "<td><span class='text-muted'>RD-" . $row["rd_id"] . "</span></td>";
                                    echo "<td class='font-semibold'>" . htmlspecialchars(substr($row["project_title"], 0, 50)) . "...</td>";
                                    echo "<td>" . ($row["college_code"] ? htmlspecialchars($row["college_code"]) : '<span class="text-muted italic">None</span>') . "</td>";
                                    echo "<td>₱" . number_format($row["budget"], 2) . "</td>";
                                    echo "<td><span class='badge {$statusClass}'>" . $row["status"] . "</span></td>";
                                    echo "<td class='flex gap-2'>";
                                    echo '<a href="#" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>';
                                    echo '<a href="#" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>';
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center p-8'><i class='fas fa-folder-open text-3xl text-muted mb-3'></i><p>No research projects found. Create your first proposal.</p></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>