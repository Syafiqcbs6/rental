<?php
session_start();
session_unset();
session_destroy();
// Gunakan jalur relatif langsung ke file index.php
header("Location: index.php"); 
exit;
?>