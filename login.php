<?php
// Initialize the secure session mechanism
session_start();

// If the user is already logged in, redirect them directly to the dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Pull in the database connection layer
require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

// Process data when the form is submitted
if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){

    // 1. Check if fields are blank
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // 2. Authenticate credentials against records
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);
            
            if($stmt->execute()){
                // Check if username exists
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        
                        // Verify the entered password against the hashed database string
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, start a fresh session state
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to the dashboard landing space
                            header("location: dashboard.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "An error occurred. Please try again later.";
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
    <title>Enterprise System Gate</title>
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
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-600/20 rounded-full blur-2xl"></div>
        
        <div class="text-center mb-8">
            <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-shield text-lg text-white"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-100">Sign In</h2>
            <p class="text-xs text-slate-400 mt-1">Authenticate to access your CRUD dashboard</p>
        </div>

        <?php if(!empty($login_err)): ?>
            <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $login_err; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user text-sm"></i></span>
                    <input type="text" name="username" value="<?php echo $username; ?>" class="w-full bg-slate-900/60 border <?php echo (!empty($username_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Enter username">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $username_err; ?></span>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Password</label>
    <a href="forgot-password.php" class="text-[11px] text-indigo-400 hover:underline font-medium">Forgot Password?</a>
</div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock text-sm"></i></span>
                    <input type="password" name="password" class="w-full bg-slate-900/60 border <?php echo (!empty($password_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="••••••••">
                </div>
                <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $password_err; ?></span>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl py-2.5 text-sm transition-colors duration-150 shadow-lg shadow-indigo-500/10 mt-2">
                Login to Portal
            </button>

            <p class="text-xs text-center text-slate-500 mt-4">
                Don't have an operational workspace account? <a href="register.php" class="text-indigo-400 hover:underline font-medium">Register here</a>
            </p>
        </form>
    </div>

</body>
</html>