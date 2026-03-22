<?php
// register.php
require_once "db_connect.php";

$username = $password = $role_id = "";
$first_name = $last_name = "";
$username_err = $password_err = $role_err = $register_err = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");

    // 1. Validate Username (Must end with @bisu.edu.ph)
    $input_username = trim($_POST["username"]);
    if (empty($input_username)) {
        $username_err = "Please enter your BISU email address.";
    } elseif (!preg_match('/@bisu\.edu\.ph$/i', $input_username)) {
        $username_err = "You must use a valid @bisu.edu.ph institutional email.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $input_username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows >= 1) {
                    $username_err = "This BISU email is already registered.";
                } else {
                    $username = $input_username;
                }
            }
            $stmt->close();
        }
    }

    // 2. Validate Password (Minimum 6 characters)
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // 3. Validate Role (8 = Faculty, 9 = Student based on system_roles)
    if (empty($_POST["role_id"]) || !in_array($_POST["role_id"], ['8', '9'])) {
        $role_err = "Please select a valid role.";
    } else {
        $role_id = $_POST["role_id"];
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        $sql = "INSERT INTO users (username, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("ssssi", $username, $password_hash, $first_name, $last_name, $role_id);
            
            if ($stmt->execute()) {
                $success = true;
            } else {
                $register_err = "Something went wrong. Please try again later.";
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
    <title>Create Account - BISU R.I.T.E.S</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .register-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* LEFT SIDE - REGISTER FORM */
        .register-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 35px;
            background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        }
        
        .register-form-container {
            width: 100%;
            max-width: 420px;
        }
        
        .form-header {
            margin-bottom: 18px;
            text-align: center;
        }
        
        .form-header-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        
        .form-header-icon i {
            font-size: 24px;
            color: white;
        }
        
        .form-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        
        .form-header p {
            font-size: 13px;
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
            border-color: #059669;
            background: white;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.05), inset 0 0 0 1px rgba(5, 150, 105, 0.1);
            outline: none;
        }
        
        .input-field.error {
            border-color: #ef4444;
            background-color: #fff5f5;
        }
        
        .form-group {
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #059669;
            font-size: 13px;
        }
        
        .form-group.two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
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
            align-items: center;
            font-size: 12px;
            margin-bottom: 10px;
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
        
        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .form-divider {
            text-align: center;
            margin: 8px 0;
            font-size: 12px;
            color: #cbd5e1;
            font-weight: 500;
        }
        
        .form-footer-text {
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        
        .form-footer-text a {
            color: #059669;
            text-decoration: none;
            font-weight: 600;
        }
        
        .form-footer-text a:hover {
            color: #047857;
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
        
        /* RIGHT SIDE - COVER IMAGE */
        .register-cover {
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
        
        .register-cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.7) 0%, rgba(42, 82, 152, 0.7) 100%);
            z-index: 1;
        }
        
        .register-cover::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }
        
        .cover-content {
            position: relative;
            z-index: 2;
            text-align: center;
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
        
        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .register-cover {
                display: none;
            }
            
            .register-form-section {
                flex: 1;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                padding: 40px 30px;
            }
            
            .register-form-container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            
            .form-group.two-col {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <!-- LEFT SIDE - FORM -->
        <div class="register-form-section">
            <div class="register-form-container">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="form-header-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2>Create Account</h2>
                    <p>Join BISU R.I.T.E.S today</p>
                </div>
                
                <!-- Error Alert -->
                <?php 
                if(!empty($register_err)){
                    echo '<div class="error-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>' . htmlspecialchars($register_err) . '</span>
                    </div>';
                }        
                ?>
                
                <!-- Register Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                    <!-- Name Fields -->
                    <div class="form-group two-col">
                        <div>
                            <label for="first_name">
                                <i class="fas fa-user"></i>First Name
                            </label>
                            <input 
                                type="text" 
                                id="first_name"
                                name="first_name" 
                                class="input-field w-full px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none" 
                                placeholder="Juan"
                                value="<?php echo htmlspecialchars($first_name); ?>"
                                required
                            >
                        </div>
                        <div>
                            <label for="last_name">
                                <i class="fas fa-user"></i>Last Name
                            </label>
                            <input 
                                type="text" 
                                id="last_name"
                                name="last_name" 
                                class="input-field w-full px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none" 
                                placeholder="Dela Cruz"
                                value="<?php echo htmlspecialchars($last_name); ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <!-- Email/Username Field -->
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-envelope"></i>BISU Email (Username)
                        </label>
                        <input 
                            type="email" 
                            id="username"
                            name="username" 
                            class="input-field w-full px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none <?php echo (!empty($username_err)) ? 'error' : ''; ?>" 
                            placeholder="your.email@bisu.edu.ph"
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
                                class="input-field w-full px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none <?php echo (!empty($password_err)) ? 'error' : ''; ?>" 
                                placeholder="At least 6 characters"
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
                    
                    <!-- Account Type Field -->
                    <div class="form-group">
                        <label for="role_id">
                            <i class="fas fa-id-badge"></i>Account Type
                        </label>
                        <select 
                            id="role_id"
                            name="role_id" 
                            class="input-field w-full px-3 py-2 text-slate-900 focus:outline-none <?php echo (!empty($role_err)) ? 'error' : ''; ?>" 
                            required
                        >
                            <option value="">-- Select Account Type --</option>
                            <option value="8" <?php echo $role_id == '8' ? 'selected' : ''; ?>>Faculty</option>
                            <option value="9" <?php echo $role_id == '9' ? 'selected' : ''; ?>>Student</option>
                        </select>
                        <?php if(!empty($role_err)): ?>
                            <div class="error-message">
                                <i class="fas fa-times-circle"></i>
                                <span><?php echo htmlspecialchars($role_err); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Terms Checkbox -->
                    <div class="form-footer">
                        <label>
                            <input type="checkbox" name="agree_terms" required>
                            <span>I agree to the <a href="#" style="text-decoration: underline;">Terms & Conditions</a></span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-check"></i>
                        <span>Create Account</span>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="form-divider">────────────────</div>
                
                <!-- Login Link -->
                <div class="form-footer-text">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
                <div class="form-footer-text" style="margin-top: 12px;">
                    <a href="mailto:support@bisu.edu">Need help?</a>
                </div>
            </div>
        </div>
        
        <!-- RIGHT SIDE - COVER IMAGE -->
        <div class="register-cover" style="background-image: url('./assets/images/register-cover.png');">
            <div class="cover-content">
                <div class="cover-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="cover-title">BISU R.I.T.E.S</h1>
                <p class="cover-subtitle">Research, Innovation, and Extension System</p>
                
                <div class="cover-features">
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Submit research proposals</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Track project progress</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Collaborate with researchers</span>
                    </div>
                    <div class="cover-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Access institutional support</span>
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

        document.getElementById('role_id').addEventListener('input', function() {
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