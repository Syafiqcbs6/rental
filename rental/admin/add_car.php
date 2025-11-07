<?php
session_start();
include("../db_connect.php");

// Check if admin logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $model = $_POST['model'];
  $brand = $_POST['brand'];
  $price_per_day = $_POST['price_per_day'];
  $availability = $_POST['availability'];
  $image = "";

  // Image upload
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif"];

    if (in_array($imageFileType, $allowed)) {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $image = $fileName;
      } else {
        echo "<script>alert('Error uploading image.');</script>";
      }
    } else {
      echo "<script>alert('Only JPG, PNG, and GIF files are allowed.');</script>";
    }
  }

  // Insert into database
  $sql = "INSERT INTO cars (model, brand, price_per_day, availability_status, image)
          VALUES (?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ssiss", $model, $brand, $price_per_day, $availability, $image);

  if (mysqli_stmt_execute($stmt)) {
    header("Location: cars.php?added=1");
    exit();
  } else {
    echo "<script>alert('Error adding car: " . mysqli_error($conn) . "');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Car - RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background: #0a0f18;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      display: flex;
    }

    .main-content {
      flex: 1;
      padding: 30px 50px;
    }

    h1, h2 {
      font-family: 'Bricolage Grotesque', sans-serif;
      color: #ffb37a;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 15px;
      max-width: 600px;
      margin: 40px auto;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: 500;
    }

    input, select {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: none;
      border-radius: 8px;
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 15px;
    }

    input[type="file"] {
      background: none;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background: #ffb37a;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-size: 16px;
      color: #000;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #ff9f4a;
      color: #fff;
    }

    .main-header {
      margin-bottom: 30px;
    }

    /* Toast Notification */
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
      <li><a href="cars.php"><i class="fa-solid fa-car"></i><span>Cars</span></a></li>
      <li><a href="add_car.php" class="active"><i class="fa-solid fa-plus"></i><span>Add Car</span></a></li>
      <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Add a New Car</h1>
    </div>

    <div class="form-container">
      <form method="POST" enctype="multipart/form-data">
        <label>Car Model:</label>
        <input type="text" name="model" placeholder="e.g. Vios 1.5G AT" required>

        <label>Car Brand:</label>
        <input type="text" name="brand" placeholder="e.g. Toyota" required>

        <label>Price per Day (RM):</label>
        <input type="number" name="price_per_day" placeholder="e.g. 180" required>

        <label>Availability:</label>
        <select name="availability" required>
          <option value="Available">Available</option>
          <option value="Not Available">Not Available</option>
        </select>

        <label>Car Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Car</button>
      </form>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toast">✅ Car added successfully!</div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Toast notification
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('added') === '1') {
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => {
          toast.classList.remove('show');
        }, 3000);
        window.history.replaceState({}, document.title, "add_car.php");
      }
    });
  </script>

</body>
</html>
