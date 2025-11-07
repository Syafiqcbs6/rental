<?php
include("../db_connect.php");

// Check if 'id' parameter exists in the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$car_id = intval($_GET['id']); // Ensure ID is an integer

// Confirm the car exists
$check_sql = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $check_sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Car not found.'); window.location.href='index.php';</script>";
    exit();
}

// Delete the car
$delete_sql = "DELETE FROM cars WHERE id = $car_id";
if (mysqli_query($conn, $delete_sql)) {
    echo "<script>alert('Car deleted successfully!'); window.location.href='index.php';</script>";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
