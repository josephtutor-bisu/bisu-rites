<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in and is a Superadmin (Role 1)
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role_id"] !== 1){
    header("location: ../login.php");
    exit;
}

// --- SYSTEM STATISTICS FOR SUPERADMIN ---

// 1. Get Total Users Count
$user_count = 0;
$user_query = $conn->query("SELECT COUNT(*) as count FROM users");
if($user_query) {
    $user_count = $user_query->fetch_assoc()['count'];
}

// 2. Get Total Colleges Count
$college_count = 0;
$college_query = $conn->query("SELECT COUNT(*) as count FROM colleges");
if($college_query) {
    $college_count = $college_query->fetch_assoc()['count'];
}

// 3. Get Count of Directors (R&D, ITSO, Extension)
$director_count = 0;
$dir_query = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id IN (2, 3, 4)");
if($dir_query) {
    $director_count = $dir_query->fetch_assoc()['count'];
}

// 4. Get Faculty Count (Role ID 5)
$faculty_count = 0;
$fac_query = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id = 5");
if($fac_query) {
    $faculty_count = $fac_query->fetch_assoc()['count'];
}

// 5. Get Student Count (Role ID 6)
$student_count = 0;
$stu_query = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id = 6");
if($stu_query) {
    $student_count = $stu_query->fetch_assoc()['count'];
}

// 6. Get Recent Users (Joined with colleges to get the college code)
$recent_users = $conn->query("
    SELECT u.user_id, u.username, u.first_name, u.last_name, u.role_id, c.college_code 
    FROM users u 
    LEFT JOIN colleges c ON u.college_id = c.college_id 
    ORDER BY u.user_id DESC 
    LIMIT 5
");

// 7. Get System Health (Total Projects, IPs, Extensions)
$rd_count = $conn->query("SELECT COUNT(*) as count FROM rd_projects")->fetch_assoc()['count'];
$ip_count = $conn->query("SELECT COUNT(*) as count FROM ip_assets")->fetch_assoc()['count'];
$ext_count = $conn->query("SELECT COUNT(*) as count FROM ext_projects")->fetch_assoc()['count'];

$page_title = "System Admin Dashboard";
include "../includes/header.php";
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-card {
        animation: fadeInUp 0.5s ease-out;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
</style>

<div class="flex h-screen overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white p-8 shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Super Admin Dashboard</h1>
                    <p class="text-blue-100 text-lg">Manage departments, users, and system operations</p>
                    <div class="mt-4 flex items-center text-sm">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-blue-100">System Status: <span class="font-semibold text-green-300">Healthy</span></span>
                    </div>
                </div>
                <div class="flex items-center space-x-4 bg-white/20 backdrop-blur px-5 py-3 rounded-xl border border-white/20">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-blue-100 text-xs uppercase tracking-wide">Superadmin</p>
                        <p class="font-bold text-white"><?php echo htmlspecialchars($_SESSION["username"]); ?></p>
                    </div>
                    <button class="ml-4 bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-sign-out-alt mr-1"></i> <a href="../logout.php" style="color: white;">Logout</a>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-8 max-w-7xl mx-auto w-full">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                
                <div class="stat-card bg-white rounded-xl shadow-md border border-blue-100 p-6 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Total Users</p>
                            <h3 class="text-4xl font-bold text-slate-800"><?php echo $user_count; ?></h3>
                            <p class="text-blue-600 text-xs font-semibold mt-2">All system users</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl shadow-md border border-emerald-100 p-6 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Colleges</p>
                            <h3 class="text-4xl font-bold text-slate-800"><?php echo $college_count; ?></h3>
                            <p class="text-emerald-600 text-xs font-semibold mt-2">Departments</p>
                        </div>
                        <div class="w-14 h-14 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 text-2xl">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl shadow-md border border-purple-100 p-6 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Directors</p>
                            <h3 class="text-4xl font-bold text-slate-800"><?php echo $director_count; ?></h3>
                            <p class="text-purple-600 text-xs font-semibold mt-2">Leadership team</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 text-2xl">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl shadow-md border border-amber-100 p-6 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Faculty</p>
                            <h3 class="text-4xl font-bold text-slate-800"><?php echo $faculty_count; ?></h3>
                            <p class="text-amber-600 text-xs font-semibold mt-2">Researchers</p>
                        </div>
                        <div class="w-14 h-14 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 text-2xl">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl shadow-md border border-cyan-100 p-6 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Students</p>
                            <h3 class="text-4xl font-bold text-slate-800"><?php echo $student_count; ?></h3>
                            <p class="text-cyan-600 text-xs font-semibold mt-2">Community</p>
                        </div>
                        <div class="w-14 h-14 bg-cyan-50 rounded-lg flex items-center justify-center text-cyan-600 text-2xl">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- System Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- System Health -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6 border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-3 flex items-center">
                        <i class="fas fa-heart text-red-500 mr-2"></i> System Health
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center">
                                <i class="fas fa-flask text-blue-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-slate-700">R&D Projects</p>
                                    <p class="text-xs text-slate-500">Active research submissions</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-blue-600"><?php echo $rd_count; ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-teal-50 rounded-lg border border-teal-100">
                            <div class="flex items-center">
                                <i class="fas fa-lightbulb text-teal-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-slate-700">IP Assets</p>
                                    <p class="text-xs text-slate-500">Intellectual property disclosures</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-teal-600"><?php echo $ip_count; ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                            <div class="flex items-center">
                                <i class="fas fa-handshake text-green-600 text-lg mr-3"></i>
                                <div>
                                    <p class="font-semibold text-slate-700">Extension Projects</p>
                                    <p class="text-xs text-slate-500">Community outreach programs</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-green-600"><?php echo $ext_count; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="bg-white rounded-xl shadow-md p-6 border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-3 flex items-center">
                        <i class="fas fa-lightning-bolt text-yellow-500 mr-2"></i> Quick Access
                    </h3>
                    <div class="space-y-2">
                        <a href="admin_users.php" class="block p-3 rounded-lg hover:bg-slate-50 border border-slate-200 hover:border-blue-300 transition group">
                            <p class="font-semibold text-slate-700 group-hover:text-blue-600 flex items-center">
                                <i class="fas fa-users-cog mr-2"></i> Manage Users
                            </p>
                            <p class="text-xs text-slate-500">Edit roles and access</p>
                        </a>
                        <a href="admin_colleges.php" class="block p-3 rounded-lg hover:bg-slate-50 border border-slate-200 hover:border-emerald-300 transition group">
                            <p class="font-semibold text-slate-700 group-hover:text-emerald-600 flex items-center">
                                <i class="fas fa-university mr-2"></i> Manage Colleges
                            </p>
                            <p class="text-xs text-slate-500">View departments</p>
                        </a>
                        <a href="admin_user_add.php" class="block p-3 rounded-lg hover:bg-slate-50 border border-slate-200 hover:border-purple-300 transition group">
                            <p class="font-semibold text-slate-700 group-hover:text-purple-600 flex items-center">
                                <i class="fas fa-user-plus mr-2"></i> Add User
                            </p>
                            <p class="text-xs text-slate-500">Create new account</p>
                        </a>
                        <a href="admin_college_add.php" class="block p-3 rounded-lg hover:bg-slate-50 border border-slate-200 hover:border-amber-300 transition group">
                            <p class="font-semibold text-slate-700 group-hover:text-amber-600 flex items-center">
                                <i class="fas fa-building mr-2"></i> Add College
                            </p>
                            <p class="text-xs text-slate-500">Register department</p>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center">
                        <i class="fas fa-clock text-blue-500 mr-2"></i> Recent Users
                    </h3>
                    <a href="admin_users.php" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View All →</a>
                </div>
                <?php if($recent_users && $recent_users->num_rows > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-200">
                                    <th class="py-2 px-3 font-semibold text-slate-600">Username</th>
                                    <th class="py-2 px-3 font-semibold text-slate-600">Name</th>
                                    <th class="py-2 px-3 font-semibold text-slate-600">College</th>
                                    <th class="py-2 px-3 font-semibold text-slate-600">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // UPDATED: Map all 9 roles perfectly
                                $role_map = [
                                    1 => 'Superadmin', 
                                    2 => 'R&D Director', 
                                    3 => 'ITSO Director', 
                                    4 => 'Extension Director', 
                                    5 => 'R&D Secretary', 
                                    6 => 'ITSO Secretary', 
                                    7 => 'Extension Secretary', 
                                    8 => 'Faculty', 
                                    9 => 'Student'
                                ];
                                
                                while($user = $recent_users->fetch_assoc()): 
                                ?>
                                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                        <td class="py-3 px-3"><strong class="text-slate-700"><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                        <td class="py-3 px-3 text-slate-600"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        
                                        <td class="py-3 px-3">
                                            <?php if($user['college_code']): ?>
                                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold border border-slate-200">
                                                    <?php echo htmlspecialchars($user['college_code']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic text-xs">N/A</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-3 px-3">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?php 
                                                $role_id = $user['role_id'];
                                                if($role_id == 1) echo 'bg-red-100 text-red-700'; // Superadmin
                                                elseif(in_array($role_id, [2, 3, 4])) echo 'bg-blue-100 text-blue-700'; // Directors
                                                elseif(in_array($role_id, [5, 6, 7])) echo 'bg-purple-100 text-purple-700'; // Secretaries
                                                elseif($role_id == 8) echo 'bg-amber-100 text-amber-700'; // Faculty
                                                else echo 'bg-emerald-100 text-emerald-700'; // Students
                                            ?>">
                                                <?php echo $role_map[$role_id] ?? 'Unknown'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center py-6 text-slate-500">No users found</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>