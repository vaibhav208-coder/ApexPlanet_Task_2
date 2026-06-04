<?php
// Initialize session security filters
session_start();

// Guard Clause: Secure data entries from unauthenticated deletion loops
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Process data only if parameters point to a specific valid entity ID
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    require_once "config.php";
    
    $sql = "DELETE FROM posts WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = trim($_GET["id"]);
        
        if($stmt->execute()){
            // Row dropped successfully. Refresh the core dashboard array view.
            header("location: dashboard.php");
            exit();
        } else {
            echo "Critical server-side error during data deletion pipeline.";
        }
        unset($stmt);
    }
    unset($pdo);
} else {
    header("location: dashboard.php");
    exit();
}
?>