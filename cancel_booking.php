<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

// Check if `id` is set in POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $booking_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    // Check if the booking exists and belongs to the logged-in user
    $query = "DELETE FROM bookings WHERE booking_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $booking_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Booking canceled successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to cancel the booking or booking not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
