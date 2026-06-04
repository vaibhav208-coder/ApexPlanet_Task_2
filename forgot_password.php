<?php
require_once "config.php";

$username = $new_password = $confirm_password = "";
$username_err = $new_password_err = $confirm_password_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
        
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() != 1) {
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a new password.";     
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // Update password if no errors
    if (empty($username_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE users SET password = :password WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Hash the new password
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $success_msg = "Password updated successfully! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                echo "Error updating password.";
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
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-xl shadow-lg w-full max-w-md border border-slate-700">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Reset Password</h2>
        
        <?php if(!empty($success_msg)): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-400 p-2 rounded mb-4 text-sm text-center">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <form action="forgot_password.php" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 focus:outline-none">
                <span class="text-red-400 text-xs"><?php echo $username_err; ?></span>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="new_password" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 focus:outline-none">
                <span class="text-red-400 text-xs"><?php echo $new_password_err; ?></span>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 focus:outline-none">
                <span class="text-red-400 text-xs"><?php echo $confirm_password_err; ?></span>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">Reset Password</button>
            <p class="text-sm text-center mt-4">Remembered? <a href="login.php" class="text-blue-400 hover:underline">Back to Login</a></p>
        </form>
    </div>
</body>
</html>