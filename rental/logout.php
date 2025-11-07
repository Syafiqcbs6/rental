<?php
session_start();
session_unset();   // Clear all session variables
session_destroy(); // Destroy the session
header("Location: /rental123/rental/index.php"); // <-- full relative path to your main page
exit;
?>
