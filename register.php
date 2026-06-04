<?php
require_once "config.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // Insert into DB if no errors
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
         
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($stmt->execute()) {
                header("location: login.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>Register - CRUD App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-xl shadow-lg w-full max-w-md border border-slate-700">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Create an Account</h2>
        
        <form action="register.php" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $username_err; ?></span>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $password_err; ?></span>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $confirm_password_err; ?></span>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">Register</button>
            
            <p class="text-sm text-center mt-4">Already have an account? <a href="login.php" class="text-blue-400 hover:underline">Login here</a></p>
        </form>
    </div>
</body>
</html>