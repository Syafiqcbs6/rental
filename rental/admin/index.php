<?php
session_start();
include("../db_connect.php");

// Check if user is admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Get quick stats
$totalCars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cars"))['total'] ?? 0;
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Scroll fix & layout consistency */
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

    /* Dashboard cards */
    .dashboard {
      margin-top: 40px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 22px;
    }

    .card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      padding: 22px;
      border-radius: 14px;
      text-align: center;
      box-shadow: 0 6px 18px rgba(0,0,0,0.5);
      transition: transform 0.25s ease, background 0.25s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      background: rgba(255, 255, 255, 0.12);
    }

    .card h3 {
      font-family: 'Bricolage Grotesque', sans-serif;
      color: #ffb37a;
      margin-bottom: 8px;
    }

    .card p {
      color: #c9d6e6;
      font-size: 1.8rem;
      font-weight: 700;
    }

    /* Table */
    .table-section {
      margin-top: 50px;
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

    /* Status colors */
    .status-pending {
      color: #ffb37a;
      font-weight: 600;
    }

    .status-approved {
      color: #90ee90;
      font-weight: 600;
    }

    .status-rejected,
    .status-cancelled {
      color: #ff6b6b;
      font-weight: 600;
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
      <li><a href="../index.php"><i class="fa-solid fa-house"></i><span>Main Page</span></a></li>
      <li><a href="index.php" class="active"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
      <li><a href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
      <li><a href="bookings.php"><i class="fa-solid fa-calendar-check"></i><span>Bookings</span></a></li>
      <li><a href="cars.php"><i class="fa-solid fa-car"></i><span>Cars</span></a></li>
      <li><a href="add_car.php"><i class="fa-solid fa-plus"></i><span>Add Car</span></a></li>
      <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> 👋</h1>
    </div>

    <div class="dashboard">
      <div class="card">
        <h3>Total Cars</h3>
        <p><?php echo $totalCars; ?></p>
      </div>
      <div class="card">
        <h3>Total Users</h3>
        <p><?php echo $totalUsers; ?></p>
      </div>
      <div class="card">
        <h3>Total Bookings</h3>
        <p><?php echo $totalBookings; ?></p>
      </div>
    </div>

    <div class="table-section">
      <h2>Recent Bookings</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Car</th>
          <th>From</th>
          <th>To</th>
          <th>Status</th>
        </tr>
        <?php
        $res = mysqli_query($conn, "SELECT b.id, u.username, c.model, b.start_date, b.end_date, b.status 
                                    FROM bookings b 
                                    JOIN users u ON b.user_id=u.id 
                                    JOIN cars c ON b.car_id=c.id 
                                    ORDER BY b.id DESC LIMIT 6");

        if ($res && mysqli_num_rows($res) > 0) {
          while ($row = mysqli_fetch_assoc($res)) {
            $status = strtolower($row['status']);
            $statusClass = "status-" . $status;
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['model']}</td>
                    <td>{$row['start_date']}</td>
                    <td>{$row['end_date']}</td>
                    <td class='{$statusClass}'>" . ucfirst($row['status']) . "</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6' class='empty'>No bookings yet</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>
</body>
</html>