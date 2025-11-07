<?php
session_start();
include("../db_connect.php");

// Check if admin logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Handle soft delete
if (isset($_GET['delete'])) {
  $carId = intval($_GET['delete']);
  $query = "UPDATE cars SET availability_status = 'Not Available' WHERE id = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "i", $carId);
  mysqli_stmt_execute($stmt);
  header("Location: cars.php");
  exit;
}

// Handle change availability
if (isset($_GET['toggle'])) {
  $carId = intval($_GET['toggle']);
  $statusQuery = "UPDATE cars 
                  SET availability_status = IF(availability_status='Available', 'Not Available', 'Available') 
                  WHERE id = ?";
  $stmt = mysqli_prepare($conn, $statusQuery);
  mysqli_stmt_bind_param($stmt, "i", $carId);
  mysqli_stmt_execute($stmt);
  header("Location: cars.php?changed=1");
  exit;
}

// Fetch all cars
$sql = "SELECT * FROM cars ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$changed = isset($_GET['changed']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Cars - Admin</title>
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

    img {
      width: 100px;
      border-radius: 8px;
    }

    .btn {
      background: transparent;
      border: 2px solid #ff8533;
      color: #ff8533;
      border-radius: 8px;
      padding: 6px 14px;
      cursor: pointer;
      transition: 0.3s ease;
      text-decoration: none;
      display: inline-block;
      font-weight: 600;
    }

    .btn:hover {
      background: #ff8533;
      color: #fff;
      transform: scale(1.05);
    }

    .status {
      font-weight: bold;
    }
    .status.Available { color: #28a745; }
    .status.NotAvailable { color: #dc3545; }

    tr.not-available-row { background: rgba(255, 0, 0, 0.05); }

    /* Toast notification */
    #toast {
      visibility: hidden;
      min-width: 260px;
      background: rgba(20,30,45,0.95);
      color: #fff;
      text-align: center;
      border-radius: 12px;
      padding: 16px;
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      font-weight: 600;
      box-shadow: 0 6px 20px rgba(0,0,0,0.4);
      transform: translateY(-40px);
      opacity: 0;
      transition: all 0.4s ease;
    }
    #toast.show {
      visibility: visible;
      opacity: 1;
      transform: translateY(0);
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #111827;
      color: #fff;
      padding: 30px 40px;
      border-radius: 14px;
      text-align: center;
      width: 350px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
      animation: fadeIn 0.3s ease;
    }
    .modal-content h3 { margin-bottom: 10px; color: #ffb37a; }
    .modal-content p { margin-bottom: 20px; color: #ddd; }
    .modal-buttons button {
      margin: 0 10px;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: 0.2s ease;
    }
    #confirmYes {
      background: #007bff;
      color: white;
    }
    #confirmYes:hover {
      background: #0056b3;
    }
    #confirmNo {
      background: #ccc;
      color: #000;
    }
    #confirmNo:hover {
      background: #999;
      color: #fff;
    }
    @keyframes fadeIn {
      from { transform: scale(0.9); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
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
      <li><a href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
      <li><a href="bookings.php"><i class="fa-solid fa-calendar-check"></i><span>Bookings</span></a></li>
      <li><a href="cars.php" class="active"><i class="fa-solid fa-car"></i><span>Cars</span></a></li>
      <li><a href="add_car.php"><i class="fa-solid fa-plus"></i><span>Add Car</span></a></li>
      <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Car Management</h1>
    </div>

    <div class="table-section">
      <h2>All Cars</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Model</th>
          <th>Price (RM/day)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) {
          $isNotAvailable = ($row['availability_status'] === 'Not Available');
        ?>
          <tr class="<?= $isNotAvailable ? 'not-available-row' : '' ?>">
            <td><?= $row['id'] ?></td>
            <td><img src="../uploads/<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['model']) ?>"></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?></td>
            <td><?= $row['price_per_day'] ?></td>
            <td class="status <?= str_replace(' ', '', $row['availability_status']) ?>">
              <?= $row['availability_status'] ?>
            </td>
            <td>
              <a href="edit_cars.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
              <a href="#" class="btn" data-action="toggle" data-id="<?= $row['id'] ?>">Change Availability</a>
              <a href="#" class="btn" data-action="delete" data-id="<?= $row['id'] ?>">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>

  <div id="toast">✅ Car availability updated!</div>

  <div id="confirmModal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle">Confirm Action</h3>
      <p id="modalMessage">Are you sure you want to continue?</p>
      <div class="modal-buttons">
        <button id="confirmYes">Yes</button>
        <button id="confirmNo">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const modal = document.getElementById("confirmModal");
      const modalTitle = document.getElementById("modalTitle");
      const modalMessage = document.getElementById("modalMessage");
      const confirmYes = document.getElementById("confirmYes");
      const confirmNo = document.getElementById("confirmNo");
      let currentAction = "";
      let carId = "";

      document.querySelectorAll(".btn[data-action]").forEach(btn => {
        btn.addEventListener("click", e => {
          e.preventDefault();
          currentAction = btn.dataset.action;
          carId = btn.dataset.id;

          if (currentAction === "toggle") {
            modalTitle.textContent = "Change Availability";
            modalMessage.textContent = "Are you sure you want to change this car’s availability?";
          } else {
            modalTitle.textContent = "Set Not Available";
            modalMessage.textContent = "Are you sure you want to set this car as Not Available?";
          }

          modal.style.display = "flex";
        });
      });

      confirmYes.addEventListener("click", () => {
        if (currentAction === "toggle") {
          window.location.href = `cars.php?toggle=${carId}`;
        } else {
          window.location.href = `cars.php?delete=${carId}`;
        }
      });

      confirmNo.addEventListener("click", () => {
        modal.style.display = "none";
      });

      const changed = <?= $changed ? 'true' : 'false' ?>;
      if (changed) {
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
      }
    });
  </script>
</body>
</html>
