<?php
session_start();
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = "user"; // default user

    // handle profile picture
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['size'] > 0) {
        $imgData = addslashes(file_get_contents($_FILES['profile_pic']['tmp_name']));
    } else {
        $imgData = null;
    }

    // check username/email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Username or email already exists'); window.history.back();</script>";
        exit;
    }

    // Insert new user
    $sql = "INSERT INTO users (username, email, password, role, profile_pic) VALUES (?, ?, MD5(?), ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $password, $role, $imgData);

    if (mysqli_stmt_execute($stmt)) {
        // Registration successful → redirect to main page
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
