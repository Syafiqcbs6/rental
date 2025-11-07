<?php
session_start();
include("../db_connect.php");

// Check if user is admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch all users except the currently logged-in admin
$currentAdminId = $_SESSION["user_id"];
$sql = "SELECT id, username, email, role FROM users WHERE id != ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $currentAdminId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users Management | RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Scrollable content fix */
    .main-content {
      height: 100vh;
      overflow-y: auto;
      padding: 30px;
      margin-left: 80px;
      flex: 1;
      transition: margin-left 0.3s ease-in-out;
    }

    .sidebar:hover ~ .main-content {
      margin-left: 230px;
    }

    .main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(6, 12, 20, 0.6);
      padding: 16px 22px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.4);
    }

    .main-header h1 {
      font-family: 'Bricolage Grotesque', sans-serif;
      color: #ff8533;
      font-size: 1.5rem;
    }

    /* Table section */
    .table-section {
      margin-top: 40px;
      background: rgba(6,12,20,0.6);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 8px 22px rgba(0,0,0,0.5);
      overflow-x: auto;
    }

    .table-section h2 {
      color: #ffb37a;
      margin-bottom: 18px;
      font-family: 'Bricolage Grotesque', sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      color: #eef6ff;
    }

    th, td {
      padding: 12px 14px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    th {
      background: rgba(255,255,255,0.08);
      color: #ffb37a;
      font-weight: 700;
    }

    tr:nth-child(even) {
      background: rgba(255,255,255,0.05);
    }

    tr:hover {
      background: rgba(255,255,255,0.12);
    }

    /* Buttons */
    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      border: 2px solid #ff8533;
      border-radius: 8px;
      padding: 6px 12px;
      text-decoration: none;
      color: #ff8533;
      font-weight: 600;
      transition: 0.3s ease;
      background: transparent;
    }

    .action-btn i {
      font-size: 14px;
    }

    .action-btn:hover {
      background: #ff8533;
      color: #fff;
      transform: scale(1.05);
    }

    .action-btn.delete {
      border-color: #dc3545;
      color: #dc3545;
    }

    .action-btn.delete:hover {
      background: #dc3545;
      color: #fff;
    }

    .empty {
      text-align: center;
      color: #ccc;
      padding: 20px 0;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <h2>RideWithPG</h2>
    </div>
    <ul>
      <li><a href="index.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
      <li><a href="users.php" class="active"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
      <li><a href="bookings.php"><i class="fa-solid fa-calendar-check"></i><span>Bookings</span></a></li>
      <li><a href="cars.php"><i class="fa-solid fa-car"></i><span>Cars</span></a></li>
      <li><a href="add_car.php"><i class="fa-solid fa-plus"></i><span>Add Car</span></a></li>
      <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Users Management</h1>
    </div>

    <div class="table-section">
      <h2>All Users</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['role']}</td>
                        <td>
                          <a href='edit_user.php?id={$row['id']}' class='action-btn'><i class='fa-solid fa-pen-to-square'></i>Edit</a>
                          <a href='delete_user.php?id={$row['id']}' class='action-btn delete' onclick=\"return confirm('Are you sure you want to delete this user?')\"><i class='fa-solid fa-trash'></i>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='empty'>No other users found.</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>
</body>
</html>
