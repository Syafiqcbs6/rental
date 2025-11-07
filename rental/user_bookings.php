<?php
session_start();
include("db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Check if we have a booking success message from redirect
$booking_success = isset($_GET['success']) && $_GET['success'] == 1;

// Fetch all bookings for the current user
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

  <!-- Main site CSS -->
  <link rel="stylesheet" href="style.css">

  <style>
    /* -------------------- Back Button -------------------- */
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

    /* -------------------- Booking Cards -------------------- */
    .bookings-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 22px;
      margin-top: 30px;
    }
    .booking-card {
      background: rgba(6,12,20,0.7);
      padding: 18px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(2,6,12,0.6);
      color: #eef6ff;
      text-align: center;
      transition: transform 0.28s ease, background 0.28s ease;
    }
    .booking-card:hover {
      transform: translateY(-6px);
      background: rgba(255,255,255,0.08);
    }
    .booking-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 12px;
    }
    .booking-info p {
      margin: 6px 0;
      color: #cfe7ff;
    }
    .status {
      font-weight: 700;
      text-transform: capitalize;
    }
    .status.pending { color: #ffc107; }
    .status.approved { color: #28a745; }
    .status.cancelled { color: #dc3545; }

    /* -------------------- Toast Notification -------------------- */
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
      .bookings-container {
        grid-template-columns: 1fr;
      }
      #toast {
        width: 80%;
        left: 50%;
      }
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
        echo "
        <div class='booking-card'>
          <img src='uploads/{$row['image']}' alt='{$row['car_name']}'>
          <div class='booking-info'>
            <p><strong>Car:</strong> {$row['car_name']}</p>
            <p><strong>From:</strong> {$row['start_date']}</p>
            <p><strong>To:</strong> {$row['end_date']}</p>
            <p><strong>Total:</strong> RM {$row['total_price']}</p>
            <p class='status {$row['status']}'><strong>Status:</strong> {$row['status']}</p>
          </div>
        </div>
        ";
      }
    } else {
      echo "<p style='text-align:center; color:#cfe7ff;'>No bookings yet.</p>";
    }
    ?>
  </div>

  <!-- Toast -->
  <div id="toast">Booking successful! Pending approval.</div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const toast = document.getElementById("toast");

      // Show toast if redirected with success
      const bookingSuccess = localStorage.getItem('booking_success') === 'true';
if (bookingSuccess) {
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
        localStorage.removeItem('booking_success'); // remove after showing
    }, 3500);
}

    });
  </script>

</body>
</html>
