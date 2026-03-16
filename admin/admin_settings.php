<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is Superadmin (Role 1)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ 
    header("location: ../login.php"); 
    exit; 
}

$msg = "";
$msg_type = "";

// 1. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uni_name = trim($_POST['university_name']);
    $sys_name = trim($_POST['system_name']);
    $acad_year = trim($_POST['academic_year']);
    $maint_mode = isset($_POST['maintenance_mode']) ? '1' : '0';

    // Update settings
    $conn->query("UPDATE system_settings SET setting_value = '$uni_name' WHERE setting_key = 'university_name'");
    $conn->query("UPDATE system_settings SET setting_value = '$sys_name' WHERE setting_key = 'system_name'");
    $conn->query("UPDATE system_settings SET setting_value = '$acad_year' WHERE setting_key = 'academic_year'");
    $conn->query("UPDATE system_settings SET setting_value = '$maint_mode' WHERE setting_key = 'maintenance_mode'");

    // LOG THIS ACTION IN SYSTEM LOGS!
    $log_sql = "INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES (?, 'UPDATE', 'Updated global system settings', ?)";
    $log_stmt = $conn->prepare($log_sql);
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("is", $_SESSION['id'], $ip);
    $log_stmt->execute();

    $msg = "System settings successfully updated!";
    $msg_type = "success";
}

// 2. Fetch Current Settings
$settings = [];
$result = $conn->query("SELECT * FROM system_settings");
while($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$page_title = "System Settings";
include "../includes/header.php";
?>

<div class="page-container flex h-screen overflow-hidden">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto p-8 bg-slate-50">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800"><i class="fas fa-cogs mr-2 text-slate-600"></i> Global Settings</h1>
                <p class="text-slate-500 text-sm mt-1">Configure system-wide parameters and maintenance modes.</p>
            </div>
        </div>

        <?php if ($msg): ?>
            <div class="mb-6 p-4 rounded-md <?php echo $msg_type == 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                <i class="fas <?php echo $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-3xl">
            <form method="post" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">University Name</label>
                        <input type="text" name="university_name" value="<?php echo htmlspecialchars($settings['university_name'] ?? ''); ?>" required class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">System Name</label>
                        <input type="text" name="system_name" value="<?php echo htmlspecialchars($settings['system_name'] ?? ''); ?>" required class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Current Academic Year</label>
                    <input type="text" name="academic_year" value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" required placeholder="e.g. 2023-2024" class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                </div>

                <div class="bg-amber-50 p-4 rounded border border-amber-200 mt-6">
                    <h3 class="font-bold text-amber-800 mb-2"><i class="fas fa-tools mr-2"></i> Maintenance Mode</h3>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" value="1" <?php echo ($settings['maintenance_mode'] == '1') ? 'checked' : ''; ?> class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                        <span class="text-sm font-medium text-amber-900">Enable Maintenance Mode (Restricts access for non-admins)</span>
                    </label>
                    <p class="text-xs text-amber-700 mt-2">Warning: Turning this on will prevent Faculty and Students from logging in.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold">
                        <i class="fas fa-save mr-1"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>