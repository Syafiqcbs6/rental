<?php
session_start();
include("db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>";
    echo "alert('Please sign in first');";
    echo "window.location.href = 'login.php';"; 
    echo "</script>";
    exit; 
}

// Get car info
if (!isset($_GET['car_id'])) {
    echo "Car not selected!";
    exit;
}

$car_id = intval($_GET['car_id']);
$sql = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $sql);
$car = mysqli_fetch_assoc($result);

if (!$car) {
    echo "Car not found!";
    exit;
}

// Fetch booked dates
$booked_dates = [];
$sql_dates = "SELECT start_date, end_date FROM bookings WHERE car_id = $car_id AND status != 'cancelled'";
$result_dates = mysqli_query($conn, $sql_dates);
if ($result_dates->num_rows > 0) {
    while ($row = $result_dates->fetch_assoc()) {
        $start = strtotime($row['start_date']);
        $end = strtotime($row['end_date']);
        for ($date = $start; $date <= $end; $date += 86400) {
            $booked_dates[] = date('Y-m-d', $date);
        }
    }
}

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $range = explode(" to ", $_POST['booking_range']);
    $start_date = $range[0];
    $end_date = $range[1] ?? $range[0];
    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
    $total_price = $days * $car['price_per_day'];

    // Check if dates are available
    $check = "SELECT * FROM bookings 
              WHERE car_id = $car_id 
              AND status != 'cancelled'
              AND (start_date <= '$end_date' AND end_date >= '$start_date')";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Sorry, this car is already booked on those dates.');</script>";
    } else {
        $sql_insert = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status)
                       VALUES ($user_id, $car_id, '$start_date', '$end_date', $total_price, 'pending')";
        if (mysqli_query($conn, $sql_insert)) {
            $booking_id = $conn->insert_id;
            header("Location: user_pay.php?booking_id=$booking_id");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book <?php echo $car['name']; ?> - RideWithPG</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="style.css">
<style>
.booking-card {
    position: relative;
    max-width: 500px;
    margin: 60px auto;
    padding: 22px;
    background: rgba(6, 12, 20, 0.85);
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.7);
    color: #fff;
    text-align: center;
}
.back-btn {
    position: absolute;
    top: 16px;
    left: 16px;
    padding: 6px 14px;
    background: rgba(255, 133, 51, 0.2);
    color: #ffb37a;
    font-weight: 600;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.25s ease, transform 0.2s ease;
}
.back-btn:hover {
    background: rgba(255, 133, 51, 0.4);
    transform: translateX(-3px);
}
.booking-card img {
    width: 100%;
    border-radius: 12px;
    margin-top: 40px;
    margin-bottom: 14px;
}
.price { color: #9fe5a9; font-weight: 700; margin-bottom: 16px; }
#calendar-wrapper { display: flex; justify-content: center; margin: 16px 0; }
.flatpickr-day.flatpickr-disabled { background: #333 !important; color: #999 !important; cursor: not-allowed; }
.flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #ff8533 !important; color: white !important; }
#bookingForm { display: flex; flex-direction: column; gap: 12px; margin-top: 16px; }
#bookingForm button { background: linear-gradient(180deg,#ff7a1a,#ff6600); border: none; color: white; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: transform 0.12s ease; }
#bookingForm button:hover { transform: translateY(-3px); }
#totalPrice { font-weight:700; color:#9fe5a9; margin: 10px 0 0; font-size: 17px; }
</style>
</head>
<body>

<div class="booking-card">
    <a href="index.php" class="back-btn">&#8592; Back</a>
    <img src="uploads/<?php echo $car['image']; ?>" alt="Car">
    <h2><?php echo $car['model']; ?></h2>
    <p class="price">RM <?php echo $car['price_per_day']; ?> / day</p>
    <h3>Select Booking Dates:</h3>
    <div id="calendar-wrapper">
        <div id="calendar"></div>
    </div>

    <form method="POST" id="bookingForm">
        <input type="hidden" name="booking_range" id="bookingRange" required>
        <!-- Paparan jumlah harga -->
        <p id="totalPrice">Total: RM 0</p>
        <button type="submit">Confirm Booking</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const bookedDates = <?php echo json_encode($booked_dates); ?>;
    const pricePerDay = <?php echo $car['price_per_day']; ?>;

    flatpickr("#calendar", {
        mode: "range",
        inline: true,
        minDate: "today",
        disable: bookedDates,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) {
            document.getElementById('bookingRange').value = dateStr;
            const totalDisplay = document.getElementById('totalPrice');

            if (selectedDates.length === 2) {
                const start = selectedDates[0];
                const end = selectedDates[1];
                const days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                const total = days * pricePerDay;
                totalDisplay.innerText = "Total: RM " + total.toFixed(2);
            } else if (selectedDates.length === 1) {
                totalDisplay.innerText = "Total: RM " + pricePerDay.toFixed(2);
            } else {
                totalDisplay.innerText = "Total: RM 0";
            }
        },
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            if (dayElem.classList.contains("flatpickr-disabled")) {
                dayElem.style.backgroundColor = "#333";
                dayElem.style.color = "#999";
                dayElem.style.cursor = "not-allowed";
            }
        }
    });
});
</script>

</body>
</html>