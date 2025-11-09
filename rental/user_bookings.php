<?php
session_start();
include("db_connect.php");

// Check login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Handle cancel booking
if (isset($_GET['cancel'])) {
  $booking_id = intval($_GET['cancel']);
  $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id AND user_id = $user_id";
  mysqli_query($conn, $update_sql);
  header("Location: user_bookings.php?cancelled=1");
  exit;
}

// Fetch bookings
$sql = "SELECT b.*, c.model AS car_name, c.image 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.id 
        WHERE b.user_id = $user_id 
        ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings - RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <style>
    a.back {
      display: inline-block;
      margin: 40px 0 20px 20px;
      padding: 12px 24px;
      background: linear-gradient(135deg, #ff8533, #ff6600);
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      border-radius: 30px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.3);
      transition: all 0.25s ease;
      font-family: 'Bricolage Grotesque', sans-serif;
    }
    a.back:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      background: linear-gradient(135deg, #ff9b4a, #ff7a1a);
    }

    .bookings-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 22px;
      margin-top: 30px;
      padding: 0 20px;
    }

    .booking-card {
      background: rgba(6,12,20,0.7);
      padding: 18px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(2,6,12,0.6);
      color: #eef6ff;
      text-align: center;
      transition: transform 0.28s ease, background 0.28s ease;
      overflow: hidden;
      position: relative;
    }

    .booking-card:hover {
      transform: translateY(-6px);
      background: rgba(255,255,255,0.08);
    }

    .booking-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .booking-main p {
      margin: 6px 0;
      color: #cfe7ff;
    }

    /* STATUS COLORS */
    .status {
      font-weight: 700;
      text-transform: capitalize;
    }
    .status.pending {
      color: #ffc107; /* yellow */
    }
    .status.approved {
      color: #28a745; /* green */
    }
    .status.rejected,
    .status.cancelled {
      color: #dc3545; /* red */
    }

    .btn-group {
      margin-top: 10px;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .details-btn, .cancel-btn {
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      font-family: 'Bricolage Grotesque', sans-serif;
      transition: all 0.25s ease;
    }

    .details-btn {
      background: linear-gradient(135deg, #4f9cff, #0066cc);
      color: white;
    }
    .details-btn:hover {
      background: linear-gradient(135deg, #6fb3ff, #007bff);
      transform: translateY(-2px);
    }

    .cancel-btn {
      background: linear-gradient(135deg, #ff4444, #cc0000);
      color: white;
    }
    .cancel-btn:hover {
      background: linear-gradient(135deg, #ff6666, #e60000);
      transform: translateY(-2px);
    }

    .booking-details {
      margin-top: 12px;
      padding-top: 10px;
      border-top: 1px solid rgba(255,255,255,0.1);
      display: none;
      animation: fadeSlide 0.4s ease;
    }

    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    #toast {
      visibility: hidden;
      min-width: 280px;
      max-width: 400px;
      background: rgba(0,0,0,0.85);
      color: #fff;
      text-align: center;
      border-radius: 14px;
      padding: 18px 26px;
      position: fixed;
      top: 30%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.8);
      z-index: 10000;
      font-weight: 600;
      font-family: 'Bricolage Grotesque', sans-serif;
      box-shadow: 0 10px 28px rgba(0,0,0,0.6);
      opacity: 0;
      transition: all 0.35s ease;
    }
    #toast.show {
      visibility: visible;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }

    @media (max-width: 560px) {
      .bookings-container { grid-template-columns: 1fr; }
      #toast { width: 80%; left: 50%; }
    }
  </style>
</head>
<body>

  <a href="index.php" class="back">&#8592; Back to Home</a>
  <h2 style="text-align:center; color:#ffb37a; font-family:'Bricolage Grotesque', sans-serif;">My Bookings</h2>

  <div class="bookings-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $status_lower = strtolower($row['status']); // ensure CSS class matches
        echo "
        <div class='booking-card'>
          <img src='uploads/{$row['image']}' alt='{$row['car_name']}'>
          <div class='booking-main'>
            <p><strong>Car:</strong> {$row['car_name']}</p>
            <p class='status {$status_lower}'><strong>Status:</strong> {$row['status']}</p>
          </div>

          <div class='btn-group'>
            <button class='details-btn' onclick='toggleDetails(this)'>Details</button>";
            
        if ($status_lower != 'cancelled') {
          echo "<button class='cancel-btn' onclick='confirmCancel({$row['id']})'>Cancel</button>";
        }

        echo "</div>
          <div class='booking-details'>
            <p><strong>From:</strong> {$row['start_date']}</p>
            <p><strong>To:</strong> {$row['end_date']}</p>
            <p><strong>Total:</strong> RM {$row['total_price']}</p>
          </div>
        </div>";
      }
    } else {
      echo "<p style='text-align:center; color:#cfe7ff;'>No bookings yet.</p>";
    }
    ?>
  </div>

  <div id="toast"></div>

  <script>
    const toast = document.getElementById("toast");

    function showToast(msg) {
      toast.innerText = msg;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3500);
    }

    function confirmCancel(id) {
      if (confirm("Are you sure you want to cancel this booking?")) {
        window.location.href = "user_bookings.php?cancel=" + id;
      }
    }

    function toggleDetails(button) {
      const details = button.parentElement.nextElementSibling;
      if (details.style.display === "block") {
        details.style.display = "none";
        button.innerText = "Details";
      } else {
        details.style.display = "block";
        button.innerText = "Hide Details";
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get("success") === "1") {
        showToast("Booking successful! Pending approval.");
      } else if (urlParams.get("cancelled") === "1") {
        showToast("Booking cancelled successfully.");
      }
    });
  </script>

</body>
</html>