<?php
// Initialize the secure session validation check
session_start();

// Guard Clause: If an unauthenticated user tries to sneak in, boot them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Attach the database configuration layer
require_once "config.php";

$posts = [];

// Fetch all database records from the posts table (Newest entries first)
try {
    $sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error loading metrics: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise CRUD Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased">

    <header class="border-b border-slate-800 bg-slate-900/50 backdrop-blur-md sticky top-0 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-layer-group text-sm text-white"></i>
            </div>
            <div>
                <h1 class="text-md font-bold tracking-tight text-slate-200">ApexPlanet Central Hub</h1>
                <p class="text-xs text-slate-400">Task 2: Full-Stack CRUD Pipeline</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Active Agent</p>
                <p class="text-sm font-medium text-indigo-400"><?php echo htmlspecialchars($_SESSION["username"]); ?></p>
            </div>
            <a href="logout.php" class="bg-slate-900 border border-slate-800 hover:bg-rose-950/30 hover:border-rose-500/30 text-slate-400 hover:text-rose-400 p-2.5 rounded-xl text-xs font-semibold transition-all duration-150">
                <i class="fa-solid fa-power-off text-sm"></i>
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-5xl w-full mx-auto p-6 flex flex-col gap-6">
        
        <div class="glass rounded-2xl p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-xl">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-100">Database Record System</h2>
                <p class="text-xs text-slate-400 mt-0.5">Performing continuous data validation audits on schema database tables.</p>
            </div>
            <a href="create.php" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl px-4 py-2.5 text-xs transition-colors shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Create New Record Entry
            </a>
        </div>

        <div class="flex flex-col gap-4">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stored Blog Posts (<?php echo count($posts); ?> Entries Found)</h3>
            
            <?php if(empty($posts)): ?>
                <div class="glass border-dashed border-2 border-slate-800 rounded-2xl p-12 text-center">
                    <div class="h-12 w-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-500">
                        <i class="fa-solid fa-folder-open text-lg"></i>
                    </div>
                    <h4 class="text-md font-semibold text-slate-300">No entries loaded in the system</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">The MySQL tables are structurally complete but contain zero records. Click the creation hook above to initialize your first entry container.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach($posts as $post): ?>
                        <div class="glass rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-slate-700/50 transition-all duration-200 shadow-md">
                            <div class="space-y-1 max-w-2xl">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-base font-bold text-slate-200 tracking-tight"><?php echo htmlspecialchars($post['title']); ?></h4>
                                    <span class="text-[10px] bg-slate-900 border border-slate-800 text-slate-500 font-mono px-2 py-0.5 rounded-md">ID: <?php echo $post['id']; ?></span>
                                </div>
                                <p class="text-sm text-slate-400 line-clamp-2 pr-4"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                <span class="text-[11px] font-mono text-slate-600 block"><i class="fa-regular fa-clock mr-1"></i> <?php echo $post['created_at']; ?></span>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end border-t border-slate-800/50 sm:border-t-0 pt-3 sm:pt-0">
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="bg-slate-900 border border-slate-800 hover:border-indigo-500/30 hover:bg-indigo-950/20 text-slate-400 hover:text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 flex items-center gap-1.5">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                </a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Execute permanent deletion procedure on this row entity?');" class="bg-slate-900 border border-slate-800 hover:border-rose-500/30 hover:bg-rose-950/20 text-slate-400 hover:text-rose-400 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash-can text-xs"></i> Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>