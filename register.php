<?php
// Include the central database connection engine
require_once "config.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$success_msg = "";

// Process form data dynamically when the user submits the registration layout
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Validate Username Input Boundaries
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement to verify database records for uniqueness
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
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    
    // 2. Validate Password Parameters
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // 3. Validate Password Match Confirmation
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // 4. If No Structural Errors Found, Insert Row Securely into Database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
         
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            $param_username = $username;
            // Native Password Hashing (Fulfilling Secure Storage Guidelines)
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($stmt->execute()) {
                $success_msg = "Registration successful! Redirecting shortly...";
                header("refresh:2;url=login.php");
            } else {
                echo "Something went wrong. Please try again.";
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
    <title>Create Enterprise Account</title>
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
        <!-- Accent Glow Decorative Element -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-600/20 rounded-full blur-2xl"></div>
        
        <div class="text-center mb-8">
            <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-user-plus text-lg text-white"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-100">Create Account</h2>
            <p class="text-xs text-slate-400 mt-1">Join the system portal to manage CRUD assets</p>
        </div>

        <!-- Success Message Toast banner -->
        <?php if(!empty($success_msg)): ?>
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check animate-pulse"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5">
            <!-- Username Input Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user text-sm"></i></span>
                    <input type="text" name="username" value="<?php echo $username; ?>" class="w-full bg-slate-900/60 border <?php echo (!empty($username_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="e.g. dev_vaibhav">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $username_err; ?></span>
            </div>

            <!-- Password Input Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock text-sm"></i></span>
                    <input type="password" name="password" class="w-full bg-slate-900/60 border <?php echo (!empty($password_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="••••••••">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $password_err; ?></span>
            </div>

            <!-- Confirm Password Input Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-shield-halved text-sm"></i></span>
                    <input type="password" name="confirm_password" class="w-full bg-slate-900/60 border <?php echo (!empty($confirm_password_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="••••••••">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $confirm_password_err; ?></span>
            </div>

            <!-- Submit Execution Button -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl py-2.5 text-sm transition-colors duration-150 shadow-lg shadow-indigo-500/10 mt-2">
                Register Intern Account
            </button>

            <p class="text-xs text-center text-slate-500 mt-4">
                Already have an operational account? <a href="login.php" class="text-indigo-400 hover:underline font-medium">Log in here</a>
            </p>
        </form>
    </div>

</body>
</html>