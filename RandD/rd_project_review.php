<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is R&D Director (Role ID 2)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ 
    header("location: ../login.php"); 
    exit; 
}

// 1. Get the Project ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = "";
$msg_type = ""; // success or error

// 2. Handle Status Updates (Approve, Review, Reject)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status'])) {
    $new_status = $_POST['new_status'];
    
    // Validate the status against our database ENUM
    $valid_statuses = ['Under Review', 'Approved', 'Rejected', 'Ongoing', 'Completed'];
    
    if (in_array($new_status, $valid_statuses)) {
        $update_sql = "UPDATE rd_projects SET status = ? WHERE rd_id = ?";
        if ($stmt = $conn->prepare($update_sql)) {
            $stmt->bind_param("si", $new_status, $id);
            if ($stmt->execute()) {
                $msg = "Project status successfully updated to '$new_status'.";
                $msg_type = "success";
            } else {
                $msg = "Error updating status.";
                $msg_type = "error";
            }
        }
    }
}

// 3. Fetch the Project Details
$sql = "SELECT p.*, c.college_name 
        FROM rd_projects p 
        LEFT JOIN colleges c ON p.college_id = c.college_id 
        WHERE p.rd_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// If project doesn't exist, send them back
if ($result->num_rows === 0) {
    header("location: rd_projects.php");
    exit;
}
// Fetch attached documents for this project
$doc_sql = "SELECT * FROM documents WHERE module_type = 'RD' AND reference_id = ?";
$doc_stmt = $conn->prepare($doc_sql);
$doc_stmt->bind_param("i", $id);
$doc_stmt->execute();
$documents = $doc_stmt->get_result();

$project = $result->fetch_assoc();

$page_title = "Review Proposal - " . htmlspecialchars($project['project_title']);
include "../includes/header.php";
?>

<div class="flex h-screen overflow-hidden bg-slate-50">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto w-full">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Proposal Review</h1>
                    <p class="text-slate-500 text-sm">Reviewing Project ID: RD-<?php echo $project['rd_id']; ?></p>
                </div>
                <a href="rd_projects.php" class="text-slate-500 hover:text-slate-700 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>

            <?php if ($msg): ?>
                <div class="mb-6 p-4 rounded-md <?php echo $msg_type == 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                    <i class="fas <?php echo $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <div class="mb-4 flex justify-between items-start">
                            <h2 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($project['project_title']); ?></h2>
                            
                            <?php 
                                // Status Badge
                                $statusColor = 'bg-slate-100 text-slate-800';
                                if(in_array($project['status'], ['Submitted', 'Under Review'])) $statusColor = 'bg-amber-100 text-amber-800';
                                if($project['status'] == 'Approved') $statusColor = 'bg-emerald-100 text-emerald-800';
                                if($project['status'] == 'Ongoing') $statusColor = 'bg-blue-100 text-blue-800';
                                if($project['status'] == 'Rejected') $statusColor = 'bg-red-100 text-red-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $statusColor; ?>">
                                <?php echo $project['status']; ?>
                            </span>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Abstract / Description</h3>
                            <p class="text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                                <?php echo nl2br(htmlspecialchars($project['abstract'] ?? 'No abstract provided.')); ?>
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                            <div>
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">College / Department</h3>
                                <p class="font-medium text-slate-800"><?php echo htmlspecialchars($project['college_name'] ?? 'Not Assigned'); ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Requested Budget</h3>
                                <p class="font-medium text-slate-800">₱<?php echo number_format($project['budget'], 2); ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Target Start Date</h3>
                                <p class="font-medium text-slate-800"><?php echo $project['start_date'] ? date('F j, Y', strtotime($project['start_date'])) : 'TBD'; ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Target End Date</h3>
                                <p class="font-medium text-slate-800"><?php echo $project['end_date'] ? date('F j, Y', strtotime($project['end_date'])) : 'TBD'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fas fa-folder-open text-blue-500 mr-2"></i> Attached Documents</h3>
                    
                    <?php if($documents->num_rows > 0): ?>
                        <ul class="space-y-3">
                            <?php while($doc = $documents->fetch_assoc()): ?>
                                <li class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                    <div class="flex items-center overflow-hidden">
                                        <i class="fas fa-file-pdf text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                        <div class="truncate">
                                            <p class="text-sm font-bold text-slate-700 truncate"><?php echo htmlspecialchars($doc['file_name']); ?></p>
                                            <p class="text-xs text-slate-500"><?php echo $doc['doc_category']; ?></p>
                                        </div>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="ml-2 bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs font-bold hover:bg-blue-200">
                                        View
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 italic">No documents attached.</p>
                    <?php endif; ?>
                </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Director Actions</h3>
                        
                        <?php if(in_array($project['status'], ['Draft', 'Completed', 'Published', 'Deferred'])): ?>
                            <p class="text-sm text-slate-500 italic">No review actions available for this current status.</p>
                        <?php else: ?>
                            <form method="post" class="space-y-3">
                                
                                <?php if($project['status'] == 'Submitted'): ?>
                                    <button type="submit" name="new_status" value="Under Review" class="w-full bg-amber-100 hover:bg-amber-200 text-amber-800 font-bold py-2 px-4 rounded transition flex justify-center items-center">
                                        <i class="fas fa-search mr-2"></i> Mark as 'Under Review'
                                    </button>
                                <?php endif; ?>

                                <?php if(in_array($project['status'], ['Submitted', 'Under Review'])): ?>
                                    <button type="submit" name="new_status" value="Approved" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                                        <i class="fas fa-check-circle mr-2"></i> Approve Proposal
                                    </button>
                                    
                                    <button type="submit" name="new_status" value="Rejected" class="w-full bg-red-100 hover:bg-red-200 text-red-800 font-bold py-2 px-4 rounded transition flex justify-center items-center mt-4" onclick="return confirm('Are you sure you want to reject this proposal?');">
                                        <i class="fas fa-times-circle mr-2"></i> Reject Proposal
                                    </button>
                                <?php endif; ?>

                                <?php if($project['status'] == 'Approved'): ?>
                                    <button type="submit" name="new_status" value="Ongoing" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                                        <i class="fas fa-play mr-2"></i> Start Project (Mark Ongoing)
                                    </button>
                                <?php endif; ?>

                                <?php if($project['status'] == 'Ongoing'): ?>
                                    <button type="submit" name="new_status" value="Completed" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center" onclick="return confirm('Mark this project as completed?');">
                                        <i class="fas fa-flag-checkered mr-2"></i> Mark as Completed
                                    </button>
                                <?php endif; ?>
                                
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>