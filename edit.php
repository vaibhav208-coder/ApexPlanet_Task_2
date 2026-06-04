<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$title = $content = "";
$title_err = $content_err = "";

// Fetch existing data when the page loads
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);
    
    $sql = "SELECT id, title, content FROM posts WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = $id;
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $title = $row["title"];
                $content = $row["content"];
            } else {
                header("location: dashboard.php");
                exit();
            }
        } else {
            echo "Error fetching data.";
        }
        unset($stmt);
    }
} 

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Title is required.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Content is required.";
    } else {
        $content = trim($_POST["content"]);
    }
    
    // Update database if no errors
    if (empty($title_err) && empty($content_err)) {
        $sql = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":content", $param_content, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            
            $param_title = $title;
            $param_content = $content;
            $param_id = $id;
            
            if ($stmt->execute()) {
                header("location: dashboard.php");
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
    <title>Edit Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 p-8">
    <div class="max-w-2xl mx-auto bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-white">Edit Post</h2>
        
        <form action="edit.php" method="post" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" name="title" value="<?php echo $title; ?>" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none">
                <span class="text-red-400 text-xs"><?php echo $title_err; ?></span>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Content</label>
                <textarea name="content" rows="6" class="w-full p-2 bg-slate-900 border border-slate-600 rounded focus:border-blue-500 outline-none"><?php echo $content; ?></textarea>
                <span class="text-red-400 text-xs"><?php echo $content_err; ?></span>
            </div>
            
            <div class="flex gap-4 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded">Save Changes</button>
                <a href="dashboard.php" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-6 rounded text-center">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>