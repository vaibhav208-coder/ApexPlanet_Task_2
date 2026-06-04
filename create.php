<?php
// Initialize the secure session validation
session_start();

// Guard Clause: Protect the route from unauthorized web scrapers
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include the backend database handshake layer
require_once "config.php";

$title = $content = "";
$title_err = $content_err = "";

// Process data when the creation form is submitted via POST
if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
    
    // 1. Validate Post Title Input
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title for your post.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    // 2. Validate Post Content Body
    if(empty(trim($_POST["content"]))){
        $content_err = "Please enter the content text.";
    } else {
        $content = trim($_POST["content"]);
    }
    
    // 3. If no input validations failed, execute data injection into MySQL
    if(empty($title_err) && empty($content_err)){
        $sql = "INSERT INTO posts (title, content) VALUES (:title, :content)";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind parameters to shield against SQL Injection vulnerabilities
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":content", $param_content, PDO::PARAM_STR);
            
            $param_title = $title;
            $param_content = $content;
            
            if($stmt->execute()){
                // Success! Redirect the user back to the updated dashboard view
                header("location: dashboard.php");
                exit();
            } else {
                echo "Database commit failure. Please try again.";
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
    <title>Create New Database Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased">

    <header class="border-b border-slate-800 bg-slate-900/50 backdrop-blur-md px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-pen text-sm text-white"></i>
            </div>
            <div>
                <h1 class="text-md font-bold tracking-tight text-slate-200">Creation Portal</h1>
                <p class="text-xs text-slate-400">Append records directly into 'posts' table</p>
            </div>
        </div>
        <a href="dashboard.php" class="bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Hub
        </a>
    </header>

    <main class="flex-1 max-w-2xl w-full mx-auto p-6 flex flex-col justify-center">
        <div class="glass w-full p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl"></div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Post Heading / Title</label>
                    <input type="text" name="title" value="<?php echo $title; ?>" class="w-full bg-slate-900/60 border <?php echo (!empty($title_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-3 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Enter a highly descriptive record title...">
                    <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $title_err; ?></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Content Data Body</label>
                    <textarea name="content" rows="6" class="w-full bg-slate-900/60 border <?php echo (!empty($content_err)) ? 'border-rose-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-3 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none transition-colors resize-none" placeholder="Draft your main database content entry body details here..."><?php echo $content; ?></textarea>
                    <span class="text-[11px] text-rose-400 mt-1 block font-medium"><?php echo $content_err; ?></span>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl py-3 text-sm transition-colors duration-150 shadow-lg shadow-indigo-500/10 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Push Record to MySQL
                    </button>
                    <a href="dashboard.php" class="bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 px-5 py-3 rounded-xl text-sm font-semibold transition-all text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>