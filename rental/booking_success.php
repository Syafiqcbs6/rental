<?php
session_start();
include("db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get car info
if (isset($_GET['car_id'])) {
    $car_id = intval($_GET['car_id']);
    $car_query = "SELECT * FROM cars WHERE id = $car_id";
    $car_result = mysqli_query($conn, $car_query);
    $car = mysqli_fetch_assoc($car_result);
} else {
    echo "Car not found.";
    exit();
}

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price_per_day = $car['price_per_day'];

    // Calculate total days
    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
    if ($days <= 0) $days = 1;

    $total_price = $price_per_day * $days;

    // Insert booking
    $sql = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status)
            VALUES ('$user_id', '$car_id', '$start_date', '$end_date', '$total_price', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        header("Location: booking_success.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book <?= htmlspecialchars($car['name']); ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 50px;
    }
    .container {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 10px;
    }
    input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      margin-top: 15px;
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Book <?= htmlspecialchars($car['name']); ?></h2>
  <p>Model: <?= htmlspecialchars($car['model']); ?></p>
  <p>Price per day: RM<?= htmlspecialchars($car['price_per_day']); ?></p>

  <form method="POST">
    <label>Start Date:</label>
    <input type="date" name="start_date" required>

    <label>End Date:</label>
    <input type="date" name="end_date" required>

    <button type="submit">Confirm Booking</button>
  </form>
</div>

</body>
</html>
