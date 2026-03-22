<?php
session_start();
require_once "db_connect.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, username, password_hash, role_id FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $role_id);
                    if ($stmt->fetch()) {
                        // In a real app, use password_verify($password, $hashed_password)
                        // For this setup, we assume you might strictly compare strings if not using hash yet
                        // But let's stick to best practice:
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role_id"] = $role_id;                            
                            
                            // Redirect user based on Role ID
                            switch($_SESSION["role_id"]) {
                                case 1: // Superadmin
                                    header("location: admin/admin_dashboard.php");
                                    break;
                                case 2: // R&D Director
                                case 5: // R&D Secretary
                                    header("location: RandD/rd_dashboard.php");
                                    break;
                                case 3: // ITSO Director
                                case 6: // ITSO Secretary
                                    header("location: itso/itso_dashboard.php");
                                    break;
                                case 4: // Extension Director
                                case 7: // Extension Secretary
                                    header("location: extension/extension_dashboard.php");
                                    break;
                                case 8: // Faculty
                                case 9: // Student
                                    header("location: users/user_dashboard.php"); 
                                    break;
                                default:
                                    header("location: login.php"); // Fallback
                                    break;
                            }
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BISU-R.I.T.E.S Login</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        
        body {
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* RIGHT SIDE - COVER IMAGE */
        .login-cover {
            flex: 1;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        
        .login-cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.7) 0%, rgba(42, 82, 152, 0.7) 100%);
            z-index: 1;
        }
        
        .login-cover::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }
        
        .cover-content {
            position: relative;
            z-index: 2;
            text-center;
            color: white;
            padding: 40px;
        }
        
        .cover-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            backdrop-filter: blur(10px);
            animation: float 3s ease-in-out infinite;
        }
        
        .cover-logo i {
            font-size: 40px;
            color: #fff;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .cover-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .cover-subtitle {
            font-size: 14px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .cover-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            max-width: 300px;
        }
        
        .cover-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            opacity: 0.95;
        }
        
        .cover-feature i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        /* LEFT SIDE - LOGIN FORM */
        .login-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 60px;
            background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        }
        
        .login-form-container {
            width: 100%;
            max-width: 420px;
        }
        
        .form-header {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .form-header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .form-header-icon i {
            font-size: 28px;
            color: white;
        }
        
        .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }
        
        .form-header p {
            font-size: 15px;
            color: #64748b;
            font-weight: 400;
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1.5px solid #e2e8f0;
            background: white;
            border-radius: 10px;
            font-size: 14px;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.05), inset 0 0 0 1px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .input-field.error {
            border-color: #ef4444;
            background-color: #fff5f5;
        }
        
        .form-group {
            transition: all 0.3s ease;
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #3b82f6;
            font-size: 13px;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 15px;
            padding: 6px 10px;
            transition: all 0.2s;
        }
        
        .password-toggle:hover {
            color: #475569;
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 28px;
            gap: 10px;
        }
        
        .form-footer label {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #475569;
            font-weight: 500;
            margin: 0;
        }
        
        .form-footer label input {
            margin-right: 6px;
            cursor: pointer;
        }
        
        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }
        
        .form-footer a:hover {
            color: #2563eb;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 13px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-divider {
            text-align: center;
            margin: 28px 0;
            font-size: 13px;
            color: #cbd5e1;
            font-weight: 500;
        }
        
        .form-footer-text {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 12px;
        }
        
        .form-footer-text a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }
        
        .form-footer-text a:hover {
            color: #2563eb;
        }
        
        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }
        
        .error-alert {
            background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
            border: 1.5px solid #fca5a5;
            color: #991b1b;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s ease-in-out;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1);
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .login-cover {
                display: none;
            }
            
            .login-form-section {
                flex: 1;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            }
            
            .login-form-container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- LEFT SIDE - FORM -->
        <div class="login-form-section">
            <div class="login-form-container">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="form-header-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account</p>
                </div>
                
                <!-- Error Alert -->
                <?php 
                if(!empty($login_err)){
                    echo '<div class="error-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>' . htmlspecialchars($login_err) . '</span>
                    </div>';
                }        
                ?>
                
                <!-- Login Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                    <!-- Username Field -->
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>Username
                        </label>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            class="input-field w-full px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none <?php echo (!empty($username_err)) ? 'error' : ''; ?>" 
                            placeholder="Enter your username"
                            value="<?php echo htmlspecialchars($username); ?>"
                            required
                        >
                        <?php if(!empty($username_err)): ?>
                            <div class="error-message">
                                <i class="fas fa-times-circle"></i>
                                <span><?php echo htmlspecialchars($username_err); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <div class="password-field">
                            <input 
                                type="password" 
                                id="password"
                                name="password" 
                                class="input-field w-full px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none <?php echo (!empty($password_err)) ? 'error' : ''; ?>" 
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" id="togglePassword" class="password-toggle" tabindex="-1">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <?php if(!empty($password_err)): ?>
                            <div class="error-message">
                                <i class="fas fa-times-circle"></i>
                                <span><?php echo htmlspecialchars($password_err); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer -->
                    <div class="form-footer">
                        <label>
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#">Forgot password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="form-divider">────────────────</div>
                
                <!-- Register Link -->
                <div class="form-footer-text">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
                <div class="form-footer-text" style="margin-top: 12px;">
                    <a href="mailto:support@bisu.edu">Need help?</a>
                </div>
            </div>
        </div>
        
        <!-- RIGHT SIDE - COVER IMAGE -->
        <div class="login-cover" style="background-image: url('./assets/images/login-cover.png');">
            <div class="cover-content">
                <div class="cover-logo">
                    <i class="fas fa-microscope"></i>
                </div>
                <h1 class="cover-title">BISU R.I.T.E.S</h1>
                <p class="cover-subtitle">Research, Innovation, and Extension System</p>
                
                <div class="cover-features">
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Manage research projects efficiently</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Track IP commercialization</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Monitor extension programs</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Collaborate with team members</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Remove error class on input
        document.getElementById('username').addEventListener('input', function() {
            this.classList.remove('error');
        });

        document.getElementById('password').addEventListener('input', function() {
            this.classList.remove('error');
        });

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function(e) {
            e.preventDefault();
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
