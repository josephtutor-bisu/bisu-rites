<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || in_array($_SESSION["role_id"], [1, 2, 3, 4])) {
    header("location: ../login.php"); exit;
}

$user_id = $_SESSION["id"];

// 1. Count R&D
$stmt_rd = $conn->prepare("SELECT COUNT(*) as count FROM rd_proponents WHERE user_id = ?");
$stmt_rd->bind_param("i", $user_id);
$stmt_rd->execute();
$my_rd_count = $stmt_rd->get_result()->fetch_assoc()['count'];

// 2. Count ITSO
$stmt_ip = $conn->prepare("SELECT COUNT(*) as count FROM ip_inventors WHERE user_id = ?");
$stmt_ip->bind_param("i", $user_id);
$stmt_ip->execute();
$my_itso_count = $stmt_ip->get_result()->fetch_assoc()['count'];

// 3. Count Extension
$stmt_ext = $conn->prepare("SELECT COUNT(*) as count FROM ext_proponents WHERE user_id = ?");
$stmt_ext->bind_param("i", $user_id);
$stmt_ext->execute();
$my_ext_count = $stmt_ext->get_result()->fetch_assoc()['count'];

$page_title = "My Portal - BISU RITES";
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#0f172a',
                        darkcard: '#1e293b',
                        darkborder: '#334155'
                    }
                }
            }
        }
    </script>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    <style>
        .neon-blue:hover { box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); border-color: rgba(59, 130, 246, 0.6); }
        .neon-teal:hover { box-shadow: 0 0 20px rgba(20, 184, 166, 0.4); border-color: rgba(20, 184, 166, 0.6); }
        .neon-green:hover { box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); border-color: rgba(34, 197, 94, 0.6); }
        .neon-purple:hover { box-shadow: 0 0 20px rgba(168, 85, 247, 0.4); border-color: rgba(168, 85, 247, 0.6); }
        .card-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-darkbg text-slate-800 dark:text-slate-200 min-h-screen flex flex-col transition-colors duration-300">

    <nav class="bg-blue-800 dark:bg-[#0b1120] text-white shadow-md border-b border-transparent dark:border-darkborder transition-colors duration-300 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-8 h-8 bg-white/10 rounded flex items-center justify-center">
                        <i class="fas fa-microscope text-white"></i>
                    </div>
                    <span class="font-bold text-xl tracking-wider">BISU R.I.T.E.S</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="mr-2 hidden md:inline text-sm">Welcome, <strong class="text-blue-200 dark:text-blue-400"><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></span>
                    
                    <button id="theme-toggle" class="text-blue-200 hover:text-white dark:text-yellow-300 dark:hover:text-yellow-100 transition p-2 rounded-full hover:bg-white/10">
                        <i id="theme-toggle-dark-icon" class="fas fa-moon hidden text-lg"></i>
                        <i id="theme-toggle-light-icon" class="fas fa-sun hidden text-lg"></i>
                    </button>

                    <a href="user_downloads.php" class="text-blue-100 hover:text-white transition font-medium text-sm flex items-center bg-blue-700 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-500 px-3 py-1.5 rounded-md shadow-sm border border-blue-600 dark:border-blue-500">
                        <i class="fas fa-file-download mr-1.5"></i> Get Forms
                    </a>
                    
                    <a href="user_settings.php" class="text-blue-200 hover:text-white transition" title="Account Settings"><i class="fas fa-cog text-lg"></i></a>
                    <span class="text-blue-400 dark:text-slate-600">|</span>
                    <a href="../logout.php" class="bg-red-500/90 hover:bg-red-500 dark:bg-red-600/80 dark:hover:bg-red-500 px-3 py-1.5 rounded text-sm transition shadow-sm font-semibold">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-white dark:bg-darkcard border-b border-slate-200 dark:border-darkborder shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-1 overflow-x-auto py-2 hide-scrollbar">
                <a href="user_dashboard.php" class="px-4 py-2 text-sm font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-md">Dashboard</a>
                <a href="user_my_rd.php" class="px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">My R&D</a>
                <a href="user_my_itso.php" class="px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/20 transition">My IP Disclosures</a>
                <a href="user_my_ext.php" class="px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-green-600 dark:hover:text-green-400 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 transition">My Extensions</a>
                <a href="user_my_comm.php" class="px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">My Commercialization</a>
            </div>
        </div>
    </div>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight mb-8">My Workspace</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            
            <div class="card-transition bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-slate-200 dark:border-darkborder p-6 flex flex-col items-center text-center hover:-translate-y-1 dark:neon-blue">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl mb-4 shadow-inner"><i class="fas fa-flask"></i></div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Research & Development</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 flex-grow">Submit a new research proposal or thesis for funding and approval.</p>
                <a href="user_rd_submit.php" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-600/80 dark:hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg transition shadow-md dark:shadow-none">Submit Proposal</a>
            </div>

            <div class="card-transition bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-slate-200 dark:border-darkborder p-6 flex flex-col items-center text-center hover:-translate-y-1 dark:neon-teal">
                <div class="w-16 h-16 bg-teal-100 dark:bg-teal-900/50 rounded-2xl flex items-center justify-center text-teal-600 dark:text-teal-400 text-2xl mb-4 shadow-inner"><i class="fas fa-lightbulb"></i></div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Innovation (ITSO)</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 flex-grow">Disclose a new invention, patent, or copyright application.</p>
                <a href="user_itso_submit.php" class="w-full bg-teal-600 hover:bg-teal-700 dark:bg-teal-600/80 dark:hover:bg-teal-500 text-white font-bold py-2.5 rounded-lg transition shadow-md dark:shadow-none">Submit Disclosure</a>
            </div>

            <div class="card-transition bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-slate-200 dark:border-darkborder p-6 flex flex-col items-center text-center hover:-translate-y-1 border-t-4 border-t-green-500 dark:border-t-green-400 dark:neon-green">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-2xl flex items-center justify-center text-green-600 dark:text-green-400 text-2xl mb-4 shadow-inner"><i class="fas fa-handshake"></i></div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Extension Services</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 flex-grow">Propose a community outreach, training, or technology transfer.</p>
                <a href="user_ext_submit.php" class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-600/80 dark:hover:bg-green-500 text-white font-bold py-2.5 rounded-lg transition shadow-md dark:shadow-none">Propose Extension</a>
            </div>

            <div class="card-transition bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-slate-200 dark:border-darkborder p-6 flex flex-col items-center text-center hover:-translate-y-1 border-t-4 border-t-purple-500 dark:border-t-purple-400 dark:neon-purple">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl mb-4 shadow-inner"><i class="fas fa-file-contract"></i></div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Commercialization</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 flex-grow">Request technology transfer or licensing services.</p>
                <a href="user_comm_request.php" class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-600/80 dark:hover:bg-purple-500 text-white font-bold py-2.5 rounded-lg transition shadow-md dark:shadow-none">Request Service</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-slate-200 dark:border-darkborder overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-darkborder bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200">My R&D <span class="bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded-full text-xs ml-1"><?php echo $my_rd_count; ?></span></h3>
                    <a href="user_my_rd.php" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto p-1">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php
                            $my_proj_sql = "SELECT p.rd_id, p.project_title, p.status FROM rd_proponents rp JOIN rd_projects p ON rp.rd_id = p.rd_id WHERE rp.user_id = ? ORDER BY p.rd_id DESC LIMIT 4";
                            $stmt = $conn->prepare($my_proj_sql); $stmt->bind_param("i", $user_id); $stmt->execute(); $res = $stmt->get_result();
                            if($res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    echo "<tr class='hover:bg-slate-50 dark:hover:bg-slate-800/50 transition cursor-pointer' onclick=\"window.location='user_rd_view.php?id=".$row['rd_id']."'\">";
                                    echo "<td class='p-3 text-blue-700 dark:text-blue-400 font-medium'>" . htmlspecialchars(substr($row['project_title'],0,30)) . "...</td>";
                                    echo "<td class='p-3 text-right'><span class='px-2.5 py-1 rounded-md text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700'>" . $row['status'] . "</span></td>";
                                    echo "</tr>";
                                }
                            } else { echo "<tr><td class='p-6 text-center text-slate-400 dark:text-slate-500'>No R&D submissions.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-slate-200 dark:border-darkborder overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-darkborder bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200">My IPs <span class="bg-teal-100 dark:bg-teal-900/60 text-teal-700 dark:text-teal-400 px-2 py-0.5 rounded-full text-xs ml-1"><?php echo $my_itso_count; ?></span></h3>
                    <a href="user_my_itso.php" class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-medium">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto p-1">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php
                            $my_ip_sql = "SELECT a.ip_id, a.title, a.status FROM ip_inventors i JOIN ip_assets a ON i.ip_id = a.ip_id WHERE i.user_id = ? ORDER BY a.ip_id DESC LIMIT 4";
                            $stmt = $conn->prepare($my_ip_sql); $stmt->bind_param("i", $user_id); $stmt->execute(); $res_ip = $stmt->get_result();
                            if($res_ip->num_rows > 0) {
                                while($row = $res_ip->fetch_assoc()) {
                                    echo "<tr class='hover:bg-slate-50 dark:hover:bg-slate-800/50 transition cursor-pointer' onclick=\"window.location='user_itso_view.php?id=".$row['ip_id']."'\">";
                                    echo "<td class='p-3 text-teal-700 dark:text-teal-400 font-medium'>" . htmlspecialchars(substr($row['title'],0,30)) . "...</td>";
                                    echo "<td class='p-3 text-right'><span class='px-2.5 py-1 rounded-md text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700'>" . $row['status'] . "</span></td>";
                                    echo "</tr>";
                                }
                            } else { echo "<tr><td class='p-6 text-center text-slate-400 dark:text-slate-500'>No IPs disclosed.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-slate-200 dark:border-darkborder overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-darkborder bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200">My Extensions <span class="bg-green-100 dark:bg-green-900/60 text-green-700 dark:text-green-400 px-2 py-0.5 rounded-full text-xs ml-1"><?php echo $my_ext_count; ?></span></h3>
                    <a href="user_my_ext.php" class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto p-1">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php
                            $my_ext_sql = "SELECT e.ext_id, e.project_title, e.service_status FROM ext_proponents ep JOIN ext_projects e ON ep.ext_id = e.ext_id WHERE ep.user_id = ? ORDER BY e.ext_id DESC LIMIT 4";
                            $stmt = $conn->prepare($my_ext_sql); $stmt->bind_param("i", $user_id); $stmt->execute(); $res_ext = $stmt->get_result();
                            if($res_ext->num_rows > 0) {
                                while($row = $res_ext->fetch_assoc()) {
                                    echo "<tr class='hover:bg-slate-50 dark:hover:bg-slate-800/50 transition cursor-pointer' onclick=\"window.location='user_ext_view.php?id=".$row['ext_id']."'\">";
                                    echo "<td class='p-3 text-green-700 dark:text-green-400 font-medium'>" . htmlspecialchars(substr($row['project_title'],0,30)) . "...</td>";
                                    echo "<td class='p-3 text-right'><span class='px-2.5 py-1 rounded-md text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700'>" . $row['service_status'] . "</span></td>";
                                    echo "</tr>";
                                }
                            } else { echo "<tr><td class='p-6 text-center text-slate-400 dark:text-slate-500'>No Extension projects.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Initial setup
        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>