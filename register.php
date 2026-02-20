<?php
// register.php
require_once "db_connect.php";

$username = $password = $confirm_password = $role_id = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
            $stmt->close();
        }
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Role
    if (empty($_POST["role_id"])) {
        $role_err = "Please select a role.";
    } else {
        $role_id = $_POST["role_id"];
    }

    // Check input errors before inserting
    if (empty($username_err) && empty($password_err)) {
        $sql = "INSERT INTO users (username, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $username, $password_hash, $first_name, $last_name, $role_id);
            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $first_name = trim($_POST["first_name"]); // Add field for name
            $last_name = trim($_POST["last_name"]);   // Add field for name
            
            if ($stmt->execute()) {
                echo "<script>alert('User created successfully!'); window.location.href='login.php';</script>";
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>Create Account</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .wrapper { width: 400px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 5px 0 20px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type=submit] { background-color: #28a745; color: white; border: none; cursor: pointer; }
        input[type=submit]:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Create Director Account</h2>
        <p>Fill this form to create an office director.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label>First Name</label>
            <input type="text" name="first_name" required>
            
            <label>Last Name</label>
            <input type="text" name="last_name" required>

            <label>Username</label>
            <input type="text" name="username" value="<?php echo $username; ?>">
            <span style="color:red"><?php echo $username_err; ?></span>

            <label>Password</label>
            <input type="password" name="password" value="<?php echo $password; ?>">
            <span style="color:red"><?php echo $password_err; ?></span>

            <label>Role / Office</label>
            <select name="role_id">
                <option value="2">R&D Director</option>
                <option value="3">ITSO Director</option>
                <option value="4">Extension Director</option>
                <option value="5">Faculty</option>
            </select>

            <input type="submit" value="Create Account">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>