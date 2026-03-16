<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is Superadmin (Role 1)
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 1){ 
    header("location: ../login.php"); 
    exit; 
}

// Optional filtering
$filter_action = isset($_GET['action_type']) ? $_GET['action_type'] : '';
$where_clause = "";
if ($filter_action) {
    $where_clause = "WHERE l.action_type = '" . $conn->real_escape_string($filter_action) . "'";
}

// Fetch logs joining with users table
$sql = "SELECT l.*, u.username, u.first_name, u.last_name, r.role_name 
        FROM system_logs l 
        LEFT JOIN users u ON l.user_id = u.user_id 
        LEFT JOIN system_roles r ON u.role_id = r.role_id
        $where_clause
        ORDER BY l.log_date DESC LIMIT 100"; // Limit to recent 100 logs for performance
        
$result = $conn->query($sql);

$page_title = "System Audit Logs";
include "../includes/header.php";
?>

<div class="page-container flex h-screen overflow-hidden">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto p-8 bg-slate-50">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800"><i class="fas fa-clipboard-list mr-2 text-slate-600"></i> System Audit Logs</h1>
                <p class="text-slate-500 text-sm mt-1">Track user activities, logins, and data modifications.</p>
            </div>
            
            <form method="GET" class="flex gap-2">
                <select name="action_type" class="border border-slate-300 rounded-md p-2 text-sm focus:ring-blue-500">
                    <option value="">All Actions</option>
                    <option value="LOGIN" <?php if($filter_action=='LOGIN') echo 'selected'; ?>>Logins</option>
                    <option value="CREATE" <?php if($filter_action=='CREATE') echo 'selected'; ?>>Creations</option>
                    <option value="UPDATE" <?php if($filter_action=='UPDATE') echo 'selected'; ?>>Updates</option>
                    <option value="DELETE" <?php if($filter_action=='DELETE') echo 'selected'; ?>>Deletions</option>
                </select>
                <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-md transition text-sm font-medium">Filter</button>
                <?php if($filter_action): ?>
                    <a href="admin_system_logs.php" class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-2 rounded-md transition text-sm font-medium"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800 border-b border-slate-700 text-white text-sm">
                            <th class="p-4 font-semibold w-48">Timestamp</th>
                            <th class="p-4 font-semibold">User</th>
                            <th class="p-4 font-semibold">Action Type</th>
                            <th class="p-4 font-semibold">Details</th>
                            <th class="p-4 font-semibold text-center">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                
                                // Color code the action badges
                                $actionColor = 'bg-slate-100 text-slate-700';
                                if($row['action_type'] == 'LOGIN') $actionColor = 'bg-blue-100 text-blue-700';
                                if($row['action_type'] == 'CREATE') $actionColor = 'bg-green-100 text-green-700';
                                if($row['action_type'] == 'UPDATE') $actionColor = 'bg-amber-100 text-amber-700';
                                if($row['action_type'] == 'DELETE') $actionColor = 'bg-red-100 text-red-700';
                                
                                echo "<tr class='hover:bg-slate-50 transition'>";
                                echo "<td class='p-4 text-slate-500 font-mono text-xs'>" . date('M j, Y - H:i:s', strtotime($row["log_date"])) . "</td>";
                                
                                echo "<td class='p-4'>";
                                if ($row['username']) {
                                    echo "<span class='font-bold text-slate-800'>" . htmlspecialchars($row['username']) . "</span><br>";
                                    echo "<span class='text-xs text-slate-500'>" . htmlspecialchars($row['role_name']) . "</span>";
                                } else {
                                    echo "<span class='italic text-slate-400'>System / Deleted User</span>";
                                }
                                echo "</td>";
                                
                                echo "<td class='p-4'><span class='px-2 py-1 rounded text-xs font-bold {$actionColor}'>" . htmlspecialchars($row["action_type"]) . "</span></td>";
                                echo "<td class='p-4 text-slate-700'>" . htmlspecialchars($row["action_details"]) . "</td>";
                                echo "<td class='p-4 text-center text-slate-400 font-mono text-xs'>" . htmlspecialchars($row["ip_address"] ?? 'N/A') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='p-8 text-center text-slate-500'><i class='fas fa-folder-open text-3xl mb-2 text-slate-300 block'></i>No logs found matching your criteria.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>