<?php
session_start();
include("db_connect.php");

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check booking ID
if (!isset($_GET['booking_id'])) {
    echo "Booking not specified!";
    exit;
}

$booking_id = intval($_GET['booking_id']);

// Fetch booking details
$sql = "SELECT b.*, c.model, c.brand, c.price_per_day, c.image
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        WHERE b.id = $booking_id AND b.user_id = ".$_SESSION['user_id'];
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);

if(!$booking){
    echo "Booking not found!";
    exit;
}

// Calculate number of days
$start = strtotime($booking['start_date']);
$end = strtotime($booking['end_date']);
$days = ($end - $start)/(60*60*24) + 1;

// Handle file upload
$success_msg = "";
$error_msg = "";
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_FILES['payment_proof'])){
    $file = $_FILES['payment_proof'];
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if($file['error'] === 0 && in_array($ext, $allowed)){
        $newName = "payment_".$booking_id."_".time().".".$ext;
        $uploadDir = "uploads/payments/";
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if(move_uploaded_file($file['tmp_name'], $uploadDir.$newName)){
            // Update database
            $update = "UPDATE bookings SET payment_proof = '$newName', status='pending' WHERE id = $booking_id";
            if(mysqli_query($conn, $update)){
                $success_msg = "Payment proof uploaded successfully!";
                // Refresh booking info
                $booking['payment_proof'] = $newName;
                $booking['status'] = 'pending';
            } else {
                $error_msg = "Failed to update booking.";
            }
        } else {
            $error_msg = "Failed to move uploaded file.";
        }
    } else {
        $error_msg = "Invalid file type. Only JPG, PNG, GIF allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment for Booking #<?php echo $booking_id; ?></title>
<link rel="stylesheet" href="style.css">
<style>
.payment-container {
    max-width: 520px;
    margin: 60px auto;
    padding: 22px;
    background: rgba(6,12,20,0.9);
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.7);
    color: #fff;
}
.payment-container img.car-image {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 16px;
}
.payment-container img.qr-code {
    width: 180px; /* QR code size */
    display: block;
    margin: 20px auto;
    border: 2px solid #ff8533; /* highlight QR code */
    border-radius: 12px;
}
.payment-container h2, h3 { margin-bottom: 14px; }
.payment-container p { margin-bottom: 10px; }
.payment-container form { display: flex; flex-direction: column; gap: 12px; }
.payment-container input[type="file"] { padding: 6px; border-radius: 6px; background: #111b2c; border: 1px solid #333; color: #fff; }
.payment-container button { background: linear-gradient(180deg,#ff7a1a,#ff6600); border: none; color: #fff; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: transform 0.12s ease; }
.payment-container button:hover { transform: translateY(-2px); }
.success-msg { color: #9fe5a9; font-weight: 600; }
.error-msg { color: #ff5e5e; font-weight: 600; }
.back-btn { display:inline-block; margin-bottom:16px; color:#ffb37a; text-decoration:none; }
</style>
</head>
<body>

<div class="payment-container">
    <a href="user_bookings.php" class="back-btn">&#8592; Back to My Bookings</a>

    <?php if(empty($booking['payment_proof'])): ?>
        <h2>Payment</h2>
        <p><strong>Car:</strong> <?php echo $booking['brand']." ".$booking['model']; ?></p>
        <p><strong>Price per day:</strong> RM <?php echo $booking['price_per_day']; ?></p>
        <p><strong>Days rented:</strong> <?php echo $days; ?></p>
        <p><strong>Total Price:</strong> RM <?php echo $booking['total_price']; ?></p>

        <!-- Highlight: QR code place -->
        <img src="uploads/qrcode.jpg" alt="Scan QR to pay" class="qr-code">


        <?php if($success_msg) echo "<p class='success-msg'>$success_msg</p>"; ?>
        <?php if($error_msg) echo "<p class='error-msg'>$error_msg</p>"; ?>

        <h3>Upload Payment Proof</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="payment_proof" required>
            <button type="submit">Upload</button>
        </form>
    <?php else: ?>
        <h2>Booking Summary</h2>
        <img src="uploads/<?php echo $booking['image']; ?>" alt="Car" class="car-image">
        <p><strong>Car:</strong> <?php echo $booking['brand']." ".$booking['model']; ?></p>
        <p><strong>Start Date:</strong> <?php echo $booking['start_date']; ?></p>
        <p><strong>End Date:</strong> <?php echo $booking['end_date']; ?></p>
        <p><strong>Total Price:</strong> RM <?php echo $booking['total_price']; ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></p>
        <p><strong>Payment Proof:</strong> <a href="uploads/payments/<?php echo $booking['payment_proof']; ?>" target="_blank">View File</a></p>
    <?php endif; ?>
</div>

</body>
</html>
