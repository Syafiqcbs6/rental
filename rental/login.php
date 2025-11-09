<?php
session_start();
include("db_connect.php");

// Logik untuk mendapatkan URL yang hendak di-redirect jika ada
$redirect_url = '';
if (isset($_GET['redirect_to'])) {
    $redirect_url = htmlspecialchars($_GET['redirect_to']);
}

// LOGIK PEMPROSESAN LOGIN (POST REQUEST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $post_redirect_url = $_POST['redirect_to'] ?? ''; // Ambil URL dari hidden field

    // KESELAMATAN: Menggunakan Prepared Statements 
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = MD5(?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["user_id"] = $user["id"];

        // Redirection Berjaya: Keutamaan kepada URL asal (dari booking.php)
        if (!empty($post_redirect_url)) {
            header("Location: " . $post_redirect_url);
            exit;
        }

        // Logik Redirection Default
        if ($user["role"] === "admin") {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php"); 
        }
        $stmt->close();
        exit;
    } else {
        // Redirection Gagal: Kekalkan redirect_to jika ada
        $fail_redirect = "login.php?login=failed";
        if (!empty($post_redirect_url)) {
            $fail_redirect .= "&redirect_to=" . urlencode($post_redirect_url);
        }
        header("Location: " . $fail_redirect); 
        $stmt->close();
        exit;
    }
    $stmt->close();
}
// AKHIR LOGIK PEMPROSESAN LOGIN
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - RideWithPG</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Gaya Latar Belakang Penuh Skrin (Mengikut gaya index.php) */
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

        /* Kelas Modal-Content yang diselaraskan dengan index.php */
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
        .modal-content .close-btn { 
            color: #ccc; 
            font-size: 28px; 
            font-weight: bold; 
            position: absolute; 
            top: 10px; 
            right: 15px; 
            cursor: pointer;
            text-decoration: none;
        }
        .modal-content .close-btn:hover { color: #fff; }
        
        .modal-content input {
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
        .register-link {
            display: block; 
            margin-top: 15px; 
            color: #ff8533; 
            text-decoration: none; 
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="modal-content">
    <a href="index.php" class="close-btn">&times;</a>
    <h2>Sign In</h2>
    
    <?php if (isset($_GET['login']) && $_GET['login'] == 'failed'): ?>
        <p style="color:red; margin-bottom: 15px;">Invalid username or password!</p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <?php if(!empty($redirect_url)): ?>
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirect_url) ?>">
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    
    <a href="signup.php" class="register-link">Don't have an account? Sign Up</a>
</div>

</body>
</html>