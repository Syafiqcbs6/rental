<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}

// Optional: Get more info from database if needed
include("db_connect.php");

$username = $_SESSION['username'];
$email = $_SESSION['email'] ?? 'Not set';
$profile_pic = $_SESSION['profile_pic'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>RideWithPG</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="logout.php">Log Out</a>
  </nav>
</header>

<main style="padding:20px;">
  <h2>My Profile</h2>
  <div style="display:flex;align-items:center;gap:16px;">
    <img src="<?php 
        echo $profile_pic ? 'data:image/jpeg;base64,'.base64_encode($profile_pic) : 'profile.png'; 
    ?>" alt="Profile Pic" style="width:100px;height:100px;border-radius:50%;border:2px solid #ff8533;">
    <div>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    </div>
  </div>
</main>

</body>
</html>
