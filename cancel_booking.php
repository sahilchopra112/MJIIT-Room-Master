<?php
include 'config.php';

$booking_id = $_POST['booking_id'];

$stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
if ($stmt->execute()) {
    echo "Booking cancelled successfully!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
