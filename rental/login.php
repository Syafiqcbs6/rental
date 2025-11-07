<?php
session_start();
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate user credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND password = MD5('$password')";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Store user info in session
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["user_id"] = $user["id"];

        // Redirect based on role
        if ($user["role"] === "admin") {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        // Login failed, redirect with message
        header("Location: index.php?login=failed");
        exit;
    }
}
?>
