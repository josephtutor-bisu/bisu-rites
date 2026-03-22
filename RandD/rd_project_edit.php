<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is R&D Director (Role ID 2)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ header("location: ../login.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";

// 1. Fetch Existing Data FIRST to check its current status
$stmt = $conn->prepare("SELECT * FROM rd_projects WHERE rd_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if(!$data) { header("location: rd_projects.php"); exit; }

// Determine if the core details should be locked
$is_locked = in_array($data['status'], ['Approved', 'Ongoing', 'Completed', 'Published']);

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // SECURITY: If locked, override any POST attempts with the original database values
    $title = $is_locked ? $data['project_title'] : trim($_POST["project_title"]);
    $abstract = $is_locked ? $data['abstract'] : trim($_POST["abstract"]);
    $college_id = $is_locked ? $data['college_id'] : (empty($_POST["college_id"]) ? NULL : intval($_POST["college_id"]));
    
    // Unlocked fields (Directors can still update status, dates, and budget)
    $status = trim($_POST["status"]);
    $budget = empty($_POST["budget"]) ? 0.00 : floatval($_POST["budget"]);
    $start_date = empty($_POST["start_date"]) ? NULL : $_POST["start_date"];
    $end_date = empty($_POST["end_date"]) ? NULL : $_POST["end_date"];

    $sql = "UPDATE rd_projects SET project_title=?, abstract=?, status=?, budget=?, start_date=?, end_date=?, college_id=? WHERE rd_id=?";
    if($stmt_up = $conn->prepare($sql)){
        $stmt_up->bind_param("sssdssii", $title, $abstract, $status, $budget, $start_date, $end_date, $college_id, $id);
        if($stmt_up->execute()){
            
            // --- SYSTEM LOG ENTRY ---
            $log_action = "UPDATE";
            $log_details = "Director edited Research Project RD-" . $id . ". Status changed to: " . $status;
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_sql = "INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES (?, ?, ?, ?)";
            if($log_stmt = $conn->prepare($log_sql)){
                $log_stmt->bind_param("isss", $_SESSION['id'], $log_action, $log_details, $ip);
                $log_stmt->execute();
                $log_stmt->close();
            }
            // ------------------------

            header("location: rd_projects.php");
            exit;
        } else {
            $error = "Error updating project record.";
        }
    }
}

$colleges_result = $conn->query("SELECT college_id, college_name FROM colleges WHERE college_code != 'ADMIN' ORDER BY college_name ASC");

$page_title = "Edit Research Project";
include "../includes/header.php";
?>

<div class="flex h-screen overflow-hidden bg-slate-50">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto w-full">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800"><i class="fas fa-edit mr-2 text-blue-600"></i> Edit Project</h1>
                <a href="rd_projects.php" class="text-slate-500 hover:text-slate-700 font-medium"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                
                <?php if($error): ?>
                    <div class="bg-red-50 text-red-800 p-4 rounded-md mb-6 border border-red-200"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if($is_locked): ?>
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-md mb-6 border border-blue-200 text-sm">
                        <i class="fas fa-lock mr-2 text-blue-600 text-lg align-middle"></i>
                        This project is currently marked as <strong><?php echo $data['status']; ?></strong>. To preserve data integrity, the core details (Title, Abstract, and College) are locked and cannot be edited.
                    </div>
                <?php endif; ?>
                
                <form method="post" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Project Title *</label>
                        <input type="text" name="project_title" value="<?php echo htmlspecialchars($data['project_title']); ?>" required 
                               <?php echo $is_locked ? 'readonly class="w-full border border-slate-200 rounded-md p-2 bg-slate-100 text-slate-500 cursor-not-allowed"' : 'class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"'; ?>>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Abstract / Description</label>
                        <textarea name="abstract" rows="4" 
                                  <?php echo $is_locked ? 'readonly class="w-full border border-slate-200 rounded-md p-2 bg-slate-100 text-slate-500 cursor-not-allowed"' : 'class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"'; ?>><?php echo htmlspecialchars($data['abstract']); ?></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Lead College / Department</label>
                            
                            <?php if($is_locked): ?>
                                <input type="hidden" name="college_id" value="<?php echo $data['college_id']; ?>">
                                <?php 
                                    // Get the name for display
                                    $college_name = "Unknown";
                                    while($c = $colleges_result->fetch_assoc()) {
                                        if($c['college_id'] == $data['college_id']) { $college_name = $c['college_name']; break; }
                                    }
                                ?>
                                <input type="text" readonly value="<?php echo htmlspecialchars($college_name); ?>" class="w-full border border-slate-200 rounded-md p-2 bg-slate-100 text-slate-500 cursor-not-allowed">
                            <?php else: ?>
                                <select name="college_id" class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500">
                                    <option value="">-- Select College --</option>
                                    <?php 
                                    if ($colleges_result->num_rows > 0) {
                                        while($c = $colleges_result->fetch_assoc()) {
                                            $selected = ($c['college_id'] == $data['college_id']) ? "selected" : "";
                                            echo "<option value='{$c['college_id']}' {$selected}>{$c['college_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500 font-semibold text-slate-800">
                                <?php
                                $statuses = ['Draft', 'Submitted', 'Under Review', 'Approved', 'Ongoing', 'Completed', 'Published', 'Deferred', 'Rejected'];
                                foreach($statuses as $st) {
                                    $selected = ($st == $data['status']) ? "selected" : "";
                                    echo "<option value='{$st}' {$selected}>{$st}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Budget (₱)</label>
                            <input type="number" step="0.01" name="budget" value="<?php echo $data['budget']; ?>" class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="<?php echo $data['start_date']; ?>" class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="<?php echo $data['end_date']; ?>" class="w-full border border-slate-300 rounded-md p-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition shadow-sm">
                            <i class="fas fa-save mr-1"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>