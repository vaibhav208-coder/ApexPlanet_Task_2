<?php
session_start();
$_SESSION = array(); // Wipe state matrices
session_destroy();   // Teardown token container
header("location: login.php"); // Bounce out to portal gate
exit;
?>