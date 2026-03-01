<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || in_array($_SESSION["role_id"], [1, 2, 3, 4])) {
    header("location: ../login.php"); exit;
}

$user_id = $_SESSION["id"];

// Count MY active R&D proposals
$stmt_rd = $conn->prepare("SELECT COUNT(*) as count FROM rd_proponents WHERE user_id = ?");
$stmt_rd->bind_param("i", $user_id);
$stmt_rd->execute();
$my_rd_count = $stmt_rd->get_result()->fetch_assoc()['count'];

// Count MY active ITSO Disclosures
$stmt_ip = $conn->prepare("SELECT COUNT(*) as count FROM ip_inventors WHERE user_id = ?");
$stmt_ip->bind_param("i", $user_id);
$stmt_ip->execute();
$my_itso_count = $stmt_ip->get_result()->fetch_assoc()['count'];

$page_title = "My Portal - BISU RITES";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    <nav class="bg-blue-800 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 font-bold text-xl tracking-wider">
                    BISU R.I.T.E.S <span class="text-blue-300 text-sm font-normal">| Researcher Portal</span>
                </div>
                <div>
                    <span class="mr-4">Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></span>
                    <a href="../logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">My Workspace</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center hover:shadow-md transition">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl mb-4"><i class="fas fa-flask"></i></div>
                <h3 class="font-bold text-lg text-slate-800 mb-2">Research & Development</h3>
                <p class="text-slate-500 text-sm mb-4">Submit a new research proposal or thesis for funding and approval.</p>
                <a href="user_rd_submit.php" class="mt-auto w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition">Submit Proposal</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center hover:shadow-md transition border-t-4 border-t-teal-500">
                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 text-2xl mb-4"><i class="fas fa-lightbulb"></i></div>
                <h3 class="font-bold text-lg text-slate-800 mb-2">Innovation (ITSO)</h3>
                <p class="text-slate-500 text-sm mb-4">Disclose a new invention, patent, or copyright application.</p>
                <a href="user_itso_submit.php" class="mt-auto w-full bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 rounded transition">Submit IP Disclosure</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center hover:shadow-md transition opacity-75">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl mb-4"><i class="fas fa-handshake"></i></div>
                <h3 class="font-bold text-lg text-slate-800 mb-2">Extension Services</h3>
                <p class="text-slate-500 text-sm mb-4">Propose a community outreach or training program.</p>
                <button disabled class="mt-auto w-full bg-slate-200 text-slate-500 font-medium py-2 rounded cursor-not-allowed">Coming Soon</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50"><h3 class="font-bold text-slate-800">My R&D Submissions (<?php echo $my_rd_count; ?>)</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-slate-500 uppercase bg-white">
                            <tr><th class="p-3 border-b">Title</th><th class="p-3 border-b">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $my_proj_sql = "SELECT p.project_title, p.status FROM rd_proponents rp JOIN rd_projects p ON rp.rd_id = p.rd_id WHERE rp.user_id = ? ORDER BY p.rd_id DESC LIMIT 5";
                            $stmt = $conn->prepare($my_proj_sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            if($res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    echo "<tr class='hover:bg-slate-50'><td class='p-3 font-medium text-slate-800'>" . htmlspecialchars(substr($row['project_title'],0,40)) . "...</td><td class='p-3'><span class='px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700'>" . $row['status'] . "</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' class='p-6 text-center text-slate-400'>No R&D submissions yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50"><h3 class="font-bold text-slate-800">My IP Disclosures (<?php echo $my_itso_count; ?>)</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-slate-500 uppercase bg-white">
                            <tr><th class="p-3 border-b">IP Title</th><th class="p-3 border-b">Type</th><th class="p-3 border-b">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $my_ip_sql = "SELECT a.title, a.ip_type, a.status FROM ip_inventors i JOIN ip_assets a ON i.ip_id = a.ip_id WHERE i.user_id = ? ORDER BY a.ip_id DESC LIMIT 5";
                            $stmt = $conn->prepare($my_ip_sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $res_ip = $stmt->get_result();
                            if($res_ip->num_rows > 0) {
                                while($row = $res_ip->fetch_assoc()) {
                                    echo "<tr class='hover:bg-slate-50'><td class='p-3 font-medium text-slate-800'>" . htmlspecialchars(substr($row['title'],0,35)) . "...</td><td class='p-3 text-slate-600'>" . $row['ip_type'] . "</td><td class='p-3'><span class='px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700'>" . $row['status'] . "</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='p-6 text-center text-slate-400'>No Intellectual Properties disclosed yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>