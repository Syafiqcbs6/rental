<?php
session_start();
include("db_connect.php");

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = "user"; 

    // check username/email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<p style='color:red;'>Username or email already exists!</p>";
    } else {
        // SQL: Masukkan data tanpa 'profile_pic'
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, MD5(?), ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        // BIND PARAM: "ssss" untuk username, email, password(MD5), role
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);

        if (mysqli_stmt_execute($stmt)) {
            // Pendaftaran berjaya → arahkan semula ke halaman INDEX (atau login.php jika anda guna fail login.php berasingan)
            header("Location: index.php?action=signin"); // Arahkan ke modal Sign In di index.php
            exit;
        } else {
            $message = "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - RideWithPG</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* CSS Modal Content (Diselaraskan dengan index.php) */
        body { 
            background-color: #0d1a2b; 
            background-image: linear-gradient(to bottom right, #1a2a47, #0d1a2b, #473a2e);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        /* Menggunakan nama kelas modal-content untuk konsistensi */
        .modal-content {
            background-color: #121c2a;
            padding: 30px;
            border: 1px solid #333;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            position: relative;
            color: #fff;
            text-align: center;
        }
        .modal-content h2 { color: #ff8533; margin-bottom: 20px; }
        
        /* Butang Tutup X */
        .close-btn { 
            color: #ccc; 
            font-size: 28px; 
            font-weight: bold; 
            position: absolute; 
            top: 10px; 
            right: 15px; 
            cursor: pointer;
            text-decoration: none;
        }
        .close-btn:hover { color: #fff; }

        .modal-content input[type="text"], 
        .modal-content input[type="email"], 
        .modal-content input[type="password"] {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #333;
            border-radius: 8px; 
            background: #0d1a2b; 
            color: #fff; 
            box-sizing: border-box;
        }
        .modal-content button {
            width: 100%; 
            background: linear-gradient(180deg,#ff7a1a,#ff6600); 
            border: none;
            color: white; 
            padding: 12px; 
            border-radius: 10px; 
            font-weight: 700;
            cursor: pointer; 
            transition: transform 0.12s ease;
        }
        .modal-content button:hover { 
            transform: translateY(-2px); 
        }
        .modal-content a { 
            color: #ff8533; 
            text-decoration: none; 
            font-size: 0.9em; 
            display: block; 
            margin-top: 15px; 
        }
    </style>
</head>
<body>

<div class="modal-content">
    <a href="index.php" class="close-btn">&times;</a>
    <h2>Sign Up</h2>
    <?php echo $message; ?>
    <form method="POST" action="signup.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already have an account? Sign In</a>
</div>

</body>
</html>