<?php
include("db_connect.php");

if (!isset($_GET['car_id'])) {
    echo json_encode([]);
    exit;
}

$car_id = intval($_GET['car_id']);

// Fetch all booked date ranges for this car
$sql = "SELECT start_date, end_date FROM bookings WHERE car_id = $car_id AND status != 'cancelled'";
$result = $conn->query($sql);

$booked_dates = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $start = strtotime($row['start_date']);
        $end = strtotime($row['end_date']);
        for ($date = $start; $date <= $end; $date += 86400) {
            $booked_dates[] = date('Y-m-d', $date);
        }
    }
}

echo json_encode($booked_dates);
?>
