<?php
// Initialize secure session tracking
session_start();

// Guard Clause: Secure the page from unauthorized users
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$title = $content = "";
$title_err = $content_err = "";

// PHASE 1: Fetch the existing data to pre-populate the form fields
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    $sql = "SELECT id, title, content FROM posts WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = $id;
        
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $title = $row["title"];
                $content = $row["content"];
            } else {
                header("location: dashboard.php");
                exit();
            }
        } else {
            echo "Error loading database record.";
        }
        unset($stmt);
    }
} 

// PHASE 2: Process the updated text when the user clicks save
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id = $_POST["id"];
    
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    if(empty(trim($_POST["content"]))){
        $content_err = "Please enter content.";
    } else {
        $content = trim($_POST["content"]);
    }
    
    if(empty($title_err) && empty($content_err)){
        $sql = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":content", $param_content, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            
            $param_title = $title;
            $param_content = $content;
            $param_id = $id;
            
            if($stmt->execute()){
                header("location: dashboard.php");
                exit();
            } else {
                echo "Critical update execution failure.";
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
    <title>Edit System Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased">

    <header class="border-b border-slate-800 bg-slate-900/50 backdrop-blur-md px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-pen-to-square text-sm text-white"></i>
            </div>
            <div>
                <h1 class="text-md font-bold text-slate-200">Modification Terminal</h1>
                <p class="text-xs text-slate-400">Updating active Record Entity ID: <?php echo htmlspecialchars($_GET['id'] ?? $id); ?></p>
            </div>
        </div>
        <a href="dashboard.php" class="bg-slate-900 border border-slate-800 text-slate-300 px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i> Cancel
        </a>
    </header>

    <main class="flex-1 max-w-2xl w-full mx-auto p-6 flex flex-col justify-center">
        <div class="glass w-full p-8 rounded-2xl shadow-2xl relative">
            <form action="edit.php" method="post" class="space-y-6">
                
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Modify Title</label>
                    <input type="text" name="title" value="<?php echo $title; ?>" class="w-full bg-slate-900/60 border <?php echo (!empty($title_err)) ? 'border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none">
                    <span class="text-xs text-rose-400 mt-1 block"><?php echo $title_err; ?></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Modify Content Data Body</label>
                    <textarea name="content" rows="6" class="w-full bg-slate-900/60 border <?php echo (!empty($content_err)) ? 'border-rose-500' : 'border-slate-800 focus:border-indigo-500'; ?> rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none resize-none"><?php echo $content; ?></textarea>
                    <span class="text-xs text-rose-400 mt-1 block"><?php echo $content_err; ?></span>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl py-3 text-sm flex items-center justify-center gap-2 shadow-lg">
                        <i class="fa-solid fa-square-check text-xs"></i> Save Changes
                    </button>
                    <a href="dashboard.php" class="bg-slate-900 border border-slate-800 text-slate-400 px-5 py-3 rounded-xl text-sm font-semibold text-center">Discard</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>