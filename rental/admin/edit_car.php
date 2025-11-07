<?php
include("../db_connect.php");

// Get car ID from URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$car_id = intval($_GET['id']);

// Fetch existing car details
$result = mysqli_query($conn, "SELECT * FROM cars WHERE id = $car_id");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Car not found.";
    exit();
}

$car = mysqli_fetch_assoc($result);

// Update car details when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $price = (float) $_POST['price'];
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);

    $update_sql = "UPDATE cars 
                   SET name = '$name', model = '$model', price_per_day = '$price', availability_status = '$availability' 
                   WHERE id = $car_id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: index.php?updated=1");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car - Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      padding: 30px;
    }
    h2 {
      text-align: center;
      color: #333;
    }
    form {
      max-width: 500px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      display: block;
      margin: 20px auto 0;
      padding: 10px 15px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0069d9;
    }
    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      color: #007bff;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <a href="index.php" class="back-link">&larr; Back to Dashboard</a>

  <h2>Edit Car Details</h2>

  <form method="POST">
    <label for="name">Car Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($car['name']) ?>" required>

    <label for="model">Model:</label>
    <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>

    <label for="price">Price per Day (RM):</label>
    <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($car['price_per_day']) ?>" required>

    <label for="availability">Availability:</label>
    <select id="availability" name="availability">
      <option value="Available" <?= $car['availability_status'] === 'Available' ? 'selected' : '' ?>>Available</option>
      <option value="Unavailable" <?= $car['availability_status'] === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
    </select>

    <button type="submit">Update Car</button>
  </form>

</body>
</html>
