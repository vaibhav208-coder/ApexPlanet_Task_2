<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if an ID was passed in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    require_once "config.php";
    
    $sql = "DELETE FROM posts WHERE id = :id";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = trim($_GET["id"]);
        
        if ($stmt->execute()) {
            // Record deleted, redirect back to dashboard
            header("location: dashboard.php");
            exit();
        } else {
            echo "Error deleting record. Please try again.";
        }
        unset($stmt);
    }
    unset($pdo);
} else {
    // No ID provided, redirect to dashboard
    header("location: dashboard.php");
    exit();
}
?>