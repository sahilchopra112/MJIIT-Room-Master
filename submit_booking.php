<?php
session_start();
include 'config.php'; // Include your database connection setup

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $room = $_POST['room'];
    $date = $_POST['booking_date'];
    $checkin_time = $_POST['checkin_time'];
    $checkout_time = $_POST['checkout_time'];

    // Insert booking request into the database
    $sql = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, status)
            VALUES (?, (SELECT room_id FROM rooms WHERE room_name = ?), ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $room, $date, $checkin_time, $checkout_time);

    if ($stmt->execute()) {
        // Redirect back with a success message
        header("Location: booking.php?success=true");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
