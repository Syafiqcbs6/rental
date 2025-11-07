<?php
session_start();
include("../db_connect.php");

// Check if user is admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = intval($_GET['id']);

// Fetch user info
$sql = "SELECT id, username, email, role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "User not found!";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $role     = $_POST['role'];

    $updateSql = "UPDATE users SET username=?, email=?, role=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $userId);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect to users.php with toast trigger
        header("Location: users.php?updated=1");
        exit;
    } else {
        echo "Error updating user: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User | RideWithPG Admin</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    min-height: 100vh;
    background: linear-gradient(to bottom, #000000 0%, #071029 45%, #0d2436 80%, #895032 100%);
    color: #f4f6f8;
    font-family: 'Poppins', sans-serif;
    padding-top: 60px;
}

.main-header {
    width: 100%;
    max-width: 480px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.main-header h1 {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-size: 2rem;
    color: #ffb37a;
}

.profile-container {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.profile-pic {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: 2px solid #ff8533;
    background: url('../profile.png') center/cover no-repeat;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 48px;
    right: 0;
    background: rgba(20, 30, 45, 0.95);
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.4);
    overflow: hidden;
    min-width: 140px;
    z-index: 999;
}

.dropdown-menu a {
    display: block;
    padding: 10px 16px;
    color: #fff;
    text-decoration: none;
    font-size: 0.9rem;
}

.dropdown-menu a:hover {
    background: rgba(255, 133, 51, 0.2);
}

.show-menu {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 18px;
    background: rgba(255, 133, 51, 0.2);
    color: #ffb37a;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: 0.3s;
}

.back-link:hover {
    background: rgba(255, 133, 51, 0.35);
}

.edit-user-form {
    width: 100%;
    max-width: 480px;
    background: rgba(6,12,20,0.7);
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(2,6,12,0.6);
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.edit-user-form label {
    font-weight: 600;
    color: #c9d6e6;
}

.edit-user-form input,
.edit-user-form select {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.06);
    background: rgba(20,30,45,0.6);
    color: #eef6ff;
    outline: none;
}

.edit-user-form input:focus,
.edit-user-form select:focus {
    border: 1px solid rgba(255,150,60,0.85);
    box-shadow: 0 6px 18px rgba(255,110,30,0.08);
}

.edit-user-form button {
    background: linear-gradient(180deg,#ff7a1a,#ff6600);
    border: none;
    color: white;
    padding: 12px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.12s ease;
}

.edit-user-form button:hover { transform: translateY(-3px); }
</style>
</head>
<body>

<div class="main-header">
    <h1>Edit User</h1>
    <div class="profile-container" id="userProfile">
        <div class="profile-pic"></div>
        <span class="profile-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        <ul class="dropdown-menu" id="dropdownMenu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
</div>

<a href="users.php" class="back-link">← Back to Users</a>

<form method="POST" class="edit-user-form">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

    <label for="role">Role:</label>
    <select id="role" name="role">
        <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
        <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
    </select>

    <button type="submit">Update User</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const profile = document.getElementById("userProfile");
    const dropdown = document.getElementById("dropdownMenu");

    profile.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("show-menu");
    });

    document.addEventListener("click", () => {
        dropdown.classList.remove("show-menu");
    });
});
</script>
</body>
</html>
