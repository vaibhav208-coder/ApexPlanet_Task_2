<?php
// Database configuration parameters
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // WAMP default password is empty
define('DB_NAME', 'blog');

/* Attempt to establish a secure database handshake using a PDO instance */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    
    // Configure error reporting mode to throw clean exceptions if a query fails
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable emulated prepared statements to safeguard against SQL Injection vulnerabilities
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch(PDOException $e) {
    // Terminate script execution gracefully and display a readable failure log
    die("CRITICAL DATABASE CONNECTION ERROR: " . $e->getMessage());
}
?>