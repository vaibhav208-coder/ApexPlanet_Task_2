<?php
// Attach the database configuration layer
require_once "config.php";

$username = $new_password = $confirm_password = "";
$username_err = $new_password_err = $confirm_password_err = "";
$success_msg = "";

// Process data when the recovery form is submitted
if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){

    // 1. Verify username field and check if it exists in DB
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
        
        $sql = "SELECT id FROM users WHERE username = :username";
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            
            if($stmt->execute()){
                if($stmt->rowCount() != 1){
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "An error occurred. Please try again later.";
            }
            unset($stmt);
        }
    }
    
    // 2. Validate new password boundaries
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter a new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // 3. Validate matching password confirmation
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm your new password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // 4. If fields pass validation, update the password entity hash securely
    if(empty($username_err) && empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE users SET password = :password WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Securely overwrite with new operational hash
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_username = $username;
            
            if($stmt->execute()){
                $success_msg = "Password reset successfully! Redirecting to login gate...";
                header("refresh:2;url=login.php");
            } else {
                echo "Critical update failure. Please try again.";
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
    <title>Account Recovery Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="glass w-full max-w-md p-8 rounded-2xl shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl"></div>
        
        <div class="text-center mb-8">
            <div class="h-12 w-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-amber-500/5">
                <i class="fa-solid fa-key text-lg text-amber-400"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-100">Account Recovery</h2>
            <p class="text-xs text-slate-400 mt-1">Verify your credentials to reset security keys</p>
        </div>

        <?php if(!empty($success_msg)): ?>
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check animate-pulse"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Account Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user text-sm"></i></span>
                    <input type="text" name="username" value="<?php echo $username; ?>" class="w-full bg-slate-900/60 border <?php echo (!empty($username_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-amber-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Enter your username">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $username_err; ?></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">New Security Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock text-sm"></i></span>
                    <input type="password" name="new_password" class="w-full bg-slate-900/60 border <?php echo (!empty($new_password_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-amber-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="••••••••">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $new_password_err; ?></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm New Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-shield-halved text-sm"></i></span>
                    <input type="password" name="confirm_password" class="w-full bg-slate-900/60 border <?php echo (!empty($confirm_password_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-amber-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="••••••••">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $confirm_password_err; ?></span>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl py-2.5 text-sm transition-colors duration-150 shadow-lg shadow-amber-500/10 mt-2">
                Override & Update Password
            </button>

            <p class="text-xs text-center text-slate-500 mt-4">
                Remembered credentials? <a href="login.php" class="text-amber-400 hover:underline font-medium">Return to Login</a>
            </p>
        </form>
    </div>

</body>
</html>