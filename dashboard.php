<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$posts = [];
// Fetch all posts
try {
    $sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error fetching posts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Dashboard - Vaibhav Dubey</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200">
    
    <nav class="bg-slate-800 p-4 border-b border-slate-700 flex justify-between items-center">
        <div>
            <h1 class="text-lg font-bold text-white">Task 2 - CRUD App</h1>
            <span class="text-xs text-slate-400">Logged in as: <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        </div>
        <a href="logout.php" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-sm font-semibold">Logout</a>
    </nav>

    <div class="max-w-4xl mx-auto mt-8 p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Manage Posts</h2>
            <a href="create.php" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded text-sm font-semibold">+ Add New Post</a>
        </div>

        <div class="space-y-4">
            <?php if (empty($posts)): ?>
                <div class="bg-slate-800 p-6 rounded text-center border border-slate-700">
                    <p class="text-slate-400">No posts found. Create one to get started!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="bg-slate-800 p-5 rounded border border-slate-700 shadow flex justify-between items-start">
                        <div class="max-w-2xl">
                            <h3 class="font-bold text-lg text-white"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="text-slate-300 text-sm mt-2"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <span class="text-xs text-slate-500 block mt-3">Posted on: <?php echo $post['created_at']; ?></span>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <a href="edit.php?id=<?php echo $post['id']; ?>" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1 rounded text-xs font-semibold">Edit</a>
                            <a href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');" class="bg-red-900/50 hover:bg-red-600 border border-red-800 text-white px-3 py-1 rounded text-xs font-semibold">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>