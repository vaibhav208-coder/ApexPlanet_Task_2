<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate inputs
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Check credentials
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        
                        // Verify password hash
                        if (password_verify($password, $hashed_password)) {
                            // Start session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: dashboard.php");
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
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-xl shadow-lg w-full max-w-md border border-slate-700">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Login</h2>
        
        <?php if(!empty($login_err)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 p-2 rounded mb-4 text-sm text-center">
                <?php echo $login_err; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $username_err; ?></span>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-sm font-medium">Password</label>
                    <a href="forgot_password.php" class="text-xs text-blue-400 hover:underline">Forgot Password?</a>
                </div>
                <input type="password" name="password" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $password_err; ?></span>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">Login</button>
            
            <p class="text-sm text-center mt-4">Don't have an account? <a href="register.php" class="text-blue-400 hover:underline">Register here</a></p>
        </form>
    </div>
</body>
</html>