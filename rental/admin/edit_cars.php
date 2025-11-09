<?php
include("../db_connect.php");

// Get car ID from URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$car_id = intval($_GET['id']);

// Fetch existing car details
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo "Car not found.";
    exit();
}
$car = $result->fetch_assoc();
$stmt->close();

// Update car details when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $model = $_POST['model'];
    $brand = $_POST['brand'];
    $price = (float) $_POST['price'];
    $availability = strtolower($_POST['availability']);

    $stmt = $conn->prepare("UPDATE cars SET model=?, brand=?, price_per_day=?, availability_status=? WHERE id=?");
    $stmt->bind_param("ssdsi", $model, $brand, $price, $availability, $car_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php?updated=1");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car - Admin Dashboard</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #000000 0%, #1a0d00 40%, #ff6600 100%);
      color: #eef6ff;
      padding: 40px 20px;
    }

    .booking-edit {
      max-width: 500px;
      width: 100%;
      background: rgba(0,0,0,0.6); /* black glass */
      border: 2px solid rgba(255,102,0,0.5); /* orange border */
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
      color: #eef6ff;
      backdrop-filter: blur(12px);
      animation: fadeInDown 0.5s ease;
    }

    .booking-edit h2 {
      font-family: 'Bricolage Grotesque', sans-serif;
      color: #ffb37a;
      text-align: center;
      margin-bottom: 25px;
    }

    .booking-edit label {
      font-weight: 600;
      color: #c9d6e6;
      margin-top: 12px;
      display: block;
    }

    .booking-edit input,
    .booking-edit select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.1);
      background: rgba(0,0,0,0.4);
      color: #eef6ff;
      outline: none;
      transition: border 0.25s ease, box-shadow 0.25s ease;
    }

    .booking-edit input:focus,
    .booking-edit select:focus {
      border: 1px solid rgba(255,150,60,0.85);
      box-shadow: 0 6px 18px rgba(255,110,30,0.2);
    }

    .booking-edit button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      border: none;
      background: linear-gradient(180deg,#ff7a1a,#ff6600);
      color: white;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.12s ease;
    }

    .booking-edit button:hover { transform: translateY(-3px); }

    .back-btn {
      display: inline-block;
      margin-bottom: 30px;
      padding: 12px 25px;
      font-weight: 700;
      border-radius: 30px;
      text-decoration: none;
      color: #fff;
      background: linear-gradient(180deg,#ff8533,#ff6600);
      border: 2px solid #ff8533;
      transition: 0.3s ease;
    }

    .back-btn:hover {
      background: transparent;
      color: #ff8533;
      transform: scale(1.05);
    }

    @keyframes fadeInDown {
      from { transform: translateY(-20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 560px) {
      .booking-edit { padding: 20px; }
      .back-btn { margin-bottom: 20px; padding: 10px 20px; }
    }
  </style>
</head>
<body>

  <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

  <div class="booking-edit">
    <h2>Edit Car Details</h2>

    <form method="POST">
        <label for="model">Model</label>
        <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>

        <label for="brand">Brand</label>
        <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>

        <label for="price">Price per Day (RM)</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($car['price_per_day']) ?>" required>

        <label for="availability">Availability</label>
        <select id="availability" name="availability">
            <option value="available" <?= $car['availability_status'] === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="unavailable" <?= $car['availability_status'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
        </select>

        <button type="submit">Update Car</button>
    </form>
  </div>

</body>
</html>