<?php
// Database connection settings
$host = "localhost";
$user = "root";       // Your MySQL username
$pass = "";           // Your MySQL password
$dbname = "ridewithpg"; // Your database name

// Create a new connection using MySQLi (object-oriented style)
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set character set to UTF-8 for proper text encoding
$conn->set_charset("utf8");

// ✅ Optional success message for testing only (you can remove this later)
// echo "✅ Database connected successfully";
?>
