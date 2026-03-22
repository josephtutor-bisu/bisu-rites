<?php
session_start();
require_once "../db_connect.php";

// STRICT AUTH: Only Faculty (8) and Students (9) belong in this portal
if(!isset($_SESSION["loggedin"]) || !in_array($_SESSION["role_id"], [8, 9])) {
    header("location: ../login.php"); exit;
}

$user_id = $_SESSION["id"];
$msg = ""; $msg_type = "";

// --- Handle Form Submissions (Unchanged Logic) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']); $last_name = trim($_POST['last_name']); $email = trim($_POST['email']); $college_id = !empty($_POST['college_id']) ? intval($_POST['college_id']) : NULL;
        if (empty($first_name) || empty($last_name)) { $msg = "First name and last name are required."; $msg_type = "error"; } else {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, college_id=? WHERE user_id=?");
            $stmt->bind_param("sssii", $first_name, $last_name, $email, $college_id, $user_id);
            if ($stmt->execute()) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $conn->query("INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES ($user_id, 'UPDATE', 'User updated profile', '$ip')");
                $msg = "Profile updated successfully!"; $msg_type = "success";
            } else { $msg = "Error updating profile."; $msg_type = "error"; }
        }
    }
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password']; $new_password = $_POST['new_password']; $confirm_password = $_POST['confirm_password'];
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id=?"); $stmt->bind_param("i", $user_id); $stmt->execute();
        $hashed_password = $stmt->get_result()->fetch_assoc()['password_hash'];
        if (password_verify($current_password, $hashed_password)) {
            if ($new_password === $confirm_password && strlen($new_password) >= 6) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE user_id=?"); $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                if ($update_stmt->execute()) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $conn->query("INSERT INTO system_logs (user_id, action_type, action_details, ip_address) VALUES ($user_id, 'UPDATE', 'User changed password', '$ip')");
                    $msg = "Password changed successfully!"; $msg_type = "success";
                } else { $msg = "Error updating password."; $msg_type = "error"; }
            } else { $msg = "Passwords mismatch or too short."; $msg_type = "error"; }
        } else { $msg = "Current password incorrect."; $msg_type = "error"; }
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?"); $stmt->bind_param("i", $user_id); $stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();
$colleges_result = $conn->query("SELECT * FROM colleges WHERE college_code != 'ADMIN' ORDER BY college_name ASC");
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <title>Account Settings - BISU RITES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { darkbg: '#0f172a', darkcard: '#1e293b', darkborder: '#334155' } } } }
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }
    </script>
</head>
<body class="bg-slate-50 dark:bg-darkbg h-screen overflow-hidden flex flex-col font-sans transition-colors duration-300 text-slate-800 dark:text-slate-200">

    <nav class="bg-blue-800 dark:bg-[#0b1120] text-white shadow-lg flex-none z-50 border-b border-transparent dark:border-darkborder transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-7 h-7 bg-white/10 rounded flex items-center justify-center"><i class="fas fa-microscope text-white text-sm"></i></div>
                    <span class="font-bold text-lg tracking-wider">BISU R.I.T.E.S</span>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="nav-theme-toggle" class="text-blue-200 hover:text-white dark:text-yellow-300 dark:hover:text-yellow-100 transition p-2 rounded-full">
                        <i id="nav-dark-icon" class="fas fa-moon hidden text-lg"></i>
                        <i id="nav-light-icon" class="fas fa-sun hidden text-lg"></i>
                    </button>
                    <a href="user_dashboard.php" class="text-blue-100 hover:text-white text-sm font-medium transition px-2 py-1.5 hover:bg-blue-700 dark:hover:bg-blue-900 rounded"><i class="fas fa-home"></i> Dash</a>
                    <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm transition font-semibold"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex flex-col max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full min-h-0">
        
        <div class="mb-4 border-b border-slate-200 dark:border-darkborder pb-2 flex-none">
            <h1 class="text-2xl font-extrabold tracking-tight">Account Settings</h1>
        </div>

        <?php if ($msg): ?>
            <div class="mb-4 p-3 rounded-lg flex-none flex items-start gap-2 shadow-sm <?php echo $msg_type === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800'; ?>">
                <i class="fas <?php echo $msg_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mt-0.5"></i> 
                <p class="text-sm font-medium"><?php echo $msg; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 flex-grow min-h-0 pb-4">
            
            <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-slate-200 dark:border-darkborder flex flex-col h-full overflow-hidden transition-colors">
                <div class="px-5 py-3 border-b border-slate-100 dark:border-darkborder bg-slate-50 dark:bg-slate-800/50 flex-none">
                    <h3 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-id-card text-blue-500"></i> Personal Profile</h3>
                </div>
                
                <div class="p-5 flex-grow flex flex-col overflow-y-auto">
                    <form method="post" class="flex flex-col h-full space-y-4">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">First Name *</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($current_user['first_name']); ?>" required class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Last Name *</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($current_user['last_name']); ?>" required class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($current_user['email'] ?? ''); ?>" class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">College</label>
                            <select name="college_id" class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">-- Select --</option>
                                <?php while($c = $colleges_result->fetch_assoc()) { $sel = ($c['college_id'] == $current_user['college_id']) ? "selected" : ""; echo "<option value='{$c['college_id']}' {$sel}>{$c['college_name']}</option>"; } ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-slate-500 dark:text-slate-400"><i class="fas fa-lock mr-1"></i> System Username</label>
                            <input type="text" disabled value="<?php echo htmlspecialchars($current_user['username']); ?>" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 text-slate-500 rounded p-2 text-sm cursor-not-allowed">
                        </div>

                        <div class="mt-auto pt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition shadow">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-slate-200 dark:border-darkborder flex flex-col h-full overflow-hidden transition-colors">
                <div class="px-5 py-3 border-b border-slate-100 dark:border-darkborder bg-slate-50 dark:bg-slate-800/50 flex-none">
                    <h3 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-shield-alt text-slate-700 dark:text-slate-300"></i> Security</h3>
                </div>
                
                <div class="p-5 flex-grow flex flex-col overflow-y-auto">
                    <form method="post" class="flex flex-col h-full space-y-4">
                        <input type="hidden" name="update_password" value="1">
                        
                        <div>
                            <label class="block text-sm font-semibold mb-1">Current Password *</label>
                            <input type="password" name="current_password" required class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-500 outline-none">
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-700 my-2 pt-2"></div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">New Password *</label>
                            <input type="password" name="new_password" required minlength="6" class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">Confirm Password *</label>
                            <input type="password" name="confirm_password" required minlength="6" class="w-full border border-slate-300 dark:border-slate-600 rounded p-2 text-sm bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-500 outline-none">
                        </div>

                        <div class="mt-auto pt-4">
                            <button type="submit" class="w-full bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-bold py-2.5 rounded-lg transition shadow">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
        var btn = document.getElementById('nav-theme-toggle');
        var dIcon = document.getElementById('nav-dark-icon');
        var lIcon = document.getElementById('nav-light-icon');
        if (document.documentElement.classList.contains('dark')) { lIcon.classList.remove('hidden'); } else { dIcon.classList.remove('hidden'); }
        btn.addEventListener('click', function() {
            dIcon.classList.toggle('hidden'); lIcon.classList.toggle('hidden');
            if (localStorage.getItem('color-theme') === 'light' || (!localStorage.getItem('color-theme') && !document.documentElement.classList.contains('dark'))) {
                document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark');
            } else { document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light'); }
        });
    </script>
</body>
</html>