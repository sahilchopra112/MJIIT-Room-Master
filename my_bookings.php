<?php
session_start(); // Start the session to access session variables
include 'config.php'; // Make sure this file has the correct database connection setup

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='login.php'>login</a> to view your bookings.";
    exit;
}

// Assuming `user_id` is stored in session upon login
$user_id = $_SESSION['user_id'];

// Fetch bookings from the database
$sql = "SELECT b.*, r.room_name FROM bookings b
        JOIN rooms r ON b.room_id = r.room_id
        WHERE b.user_id = ? ORDER BY b.booking_date DESC, b.start_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>My Bookings</h1>";
if ($result->num_rows > 0) {
    echo "<table><tr><th>Room Name</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row['room_name']) . "</td>
                  <td>" . htmlspecialchars($row['booking_date']) . "</td>
                  <td>" . htmlspecialchars($row['start_time']) . "</td>
                  <td>" . htmlspecialchars($row['end_time']) . "</td>
                  <td>" . htmlspecialchars($row['status']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "You have no bookings.";
}
$conn->close();
?>
