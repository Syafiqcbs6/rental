<?php
session_start();
include("../db_connect.php");

// Check if admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Handle booking approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = trim($_POST['action']);

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        $status = 'Pending';
    }

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();
    $stmt->close();

    header("Location: bookings.php?updated=1");
    exit;
}

// Fetch all bookings
$sql = "
SELECT 
    b.id AS booking_id,
    u.username AS user_name,
    CONCAT(c.brand, ' ', c.model) AS car_name,
    b.start_date,
    b.end_date,
    b.total_price,
    b.status,
    b.payment_proof
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN cars c ON b.car_id = c.id
ORDER BY b.id DESC
";
$result = $conn->query($sql);

// Store all rows and reverse for "latest at bottom"
$bookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $bookings = array_reverse($bookings);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Bookings | RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
      body { overflow-y: auto; }
      table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 0.95rem; }
      th, td { padding: 6px 10px; text-align: left; word-wrap: break-word; }

      /* Column widths */
      th:nth-child(1), td:nth-child(1) { width: 5%; }
      th:nth-child(2), td:nth-child(2) { width: 12%; }
      th:nth-child(3), td:nth-child(3) { width: 20%; }
      th:nth-child(4), td:nth-child(4) { width: 10%; }
      th:nth-child(5), td:nth-child(5) { width: 10%; }
      th:nth-child(6), td:nth-child(6) { width: 10%; }
      th:nth-child(7), td:nth-child(7) { width: 10%; }
      th:nth-child(8), td:nth-child(8) { width: 23%; }

      .action-btn {
          padding: 5px 10px;
          border: none;
          border-radius: 6px;
          cursor: pointer;
          color: #fff;
          font-weight: 600;
          margin-right: 4px;
          transition: 0.3s;
      }
      .btn-approve { background: #28a745; }
      .btn-reject { background: #dc3545; }
      .btn-view { background: #007bff; }
      .btn-approve:hover, .btn-reject:hover, .btn-view:hover { opacity: 0.9; transform: translateY(-1px); }

      /* Status colors */
      .status.Pending { color: #ffc107; font-weight: bold; }
      .status.Approved { color: #00d26a; font-weight: bold; }
      .status.Rejected { color: #ff4747; font-weight: bold; }
      .status.Cancelled { color: #ff4747; font-weight: bold; }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo"><h2>RideWithPG</h2></div>
  <ul>
    <li><a href="../index.php"><i class="fa-solid fa-house"></i><span>Main Page</span></a></li>
    <li><a href="index.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
    <li><a href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
    <li><a href="bookings.php" class="active"><i class="fa-solid fa-calendar-check"></i><span>Bookings</span></a></li>
    <li><a href="cars.php"><i class="fa-solid fa-car"></i><span>Cars</span></a></li>
    <li><a href="add_car.php"><i class="fa-solid fa-plus"></i><span>Add Car</span></a></li>
    <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
  </ul>
</div>

<div class="main-content">
  <div class="main-header"><h1>Bookings Management</h1></div>

  <div class="table-section">
    <h2>All Bookings</h2>
    <table>
      <tr>
        <th>Bil.</th>
        <th>User</th>
        <th>Car</th>
        <th>From</th>
        <th>To</th>
        <th>Total (RM)</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php if (!empty($bookings)): ?>
        <?php $bil = 1; ?>
        <?php foreach ($bookings as $row): ?>
          <?php $status = ucfirst(trim($row['status'])); ?>
          <tr>
            <td><?php echo $bil++; ?></td>
            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
            <td><?php echo htmlspecialchars($row['car_name']); ?></td>
            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
            <td><?php echo number_format($row['total_price'], 2); ?></td>
            <td class="status <?php echo $status; ?>"><?php echo htmlspecialchars($status); ?></td>
            <td>
              <?php if ($row['payment_proof']): ?>
                <a href="../uploads/payments/<?php echo $row['payment_proof']; ?>" target="_blank" class="action-btn btn-view">View Proof</a>
              <?php endif; ?>

              <?php if (strtolower($status) === 'pending'): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                  <button type="submit" name="action" value="approve" class="action-btn btn-approve">Approve</button>
                </form>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                  <button type="submit" name="action" value="reject" class="action-btn btn-reject">Reject</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8">No bookings found.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

</body>
</html>