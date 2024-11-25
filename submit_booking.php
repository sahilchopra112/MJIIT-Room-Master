<?php
session_start();
include 'config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to book a room.");
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $room_name = $_POST['room'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $checkin_time = $_POST['checkin_time'] ?? '';
    $checkout_time = $_POST['checkout_time'] ?? '';

    // Validate input data
    if (empty($room_name) || empty($booking_date) || empty($checkin_time) || empty($checkout_time)) {
        $error_message = "All fields are required.";
        header("Location: booking.php?error=" . urlencode($error_message));
        exit;
    }

    // Get the room_id for the selected room
    $room_query = "SELECT room_id FROM rooms WHERE room_name = ?";
    $stmt = $conn->prepare($room_query);
    $stmt->bind_param("s", $room_name);
    $stmt->execute();
    $room_result = $stmt->get_result();

    if ($room_result->num_rows === 0) {
        $error_message = "Invalid room selection.";
        header("Location: booking.php?error=" . urlencode($error_message));
        exit;
    } else {
        $room_data = $room_result->fetch_assoc();
        $room_id = $room_data['room_id'];

        // Check if the room is already booked during the selected time
        $availability_query = "SELECT * FROM bookings 
                               WHERE room_id = ? 
                               AND booking_date = ? 
                               AND (start_time < ? AND end_time > ?)";
        $availability_stmt = $conn->prepare($availability_query);
        $availability_stmt->bind_param("isss", $room_id, $booking_date, $checkout_time, $checkin_time);
        $availability_stmt->execute();
        $availability_result = $availability_stmt->get_result();

        if ($availability_result->num_rows > 0) {
            $error_message = "This room is already booked during the selected time.";
            header("Location: booking.php?error=" . urlencode($error_message));
            exit;
        } else {
            // Insert the booking into the database
            $insert_query = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, status) 
                             VALUES (?, ?, ?, ?, ?, 'Confirmed')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iisss", $user_id, $room_id, $booking_date, $checkin_time, $checkout_time);

            if ($insert_stmt->execute()) {
                // Update the room status to "Booked"
                $update_room_query = "UPDATE rooms SET status = 'Booked' WHERE room_id = ?";
                $update_stmt = $conn->prepare($update_room_query);
                $update_stmt->bind_param("i", $room_id);
                $update_stmt->execute();

                header("Location: my_bookings.php?message=Booking confirmed.");
                exit;
            } else {
                $error_message = "Error: " . $conn->error;
                header("Location: booking.php?error=" . urlencode($error_message));
                exit;
            }
        }
    }
} else {
    echo "Invalid request method.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Booking - MJIIT RoomMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styling */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('bg website.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

        .navbar {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            color: rgb(114, 4, 4);
            padding: 8px 20px;
            justify-content: space-between;
            width: 100%;
            border-bottom: 2px solid #8B0000;
        }

        .navbar-title {
            display: flex;
            align-items: center;
        }

        .navbar-title img {
            max-height: 30px;
            margin-right: 10px;
        }

        .navbar-title p {
            font-weight: bold;
            font-size: 20px;
            margin: 0;
        }

        .navbar-links {
            display: flex;
            align-items: center;
        }

        .navbar-links a {
            color: rgb(119, 4, 4);
            text-decoration: none;
            margin-right: 20px;
            font-size: 14px;
        }

        .navbar-links a:hover {
            color: #ddd;
        }

        .error-message, .success-message {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            margin: 20px auto;
            max-width: 600px;
        }

        .error-message {
            color: red;
            border: 1px solid red;
        }

        .success-message {
            color: green;
            border: 1px solid green;
        }

        .confirmation-container {
            text-align: center;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .confirmation-container h1 {
            color: #8B0000;
        }

        .confirmation-container p {
            font-size: 1.2em;
        }

        .back-button {
            background-color: #8B0000;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1em;
            display: inline-block;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #5f2a1e;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-title">
            <img src="UTM-LOGO-FULL.png" alt="UTM Logo">
            <img src="Mjiit RoomMaster logo.png" alt="MJIIT Logo">
            <p>BookingSpace</p>
        </div>
        <div class="navbar-links">
            <a href="home.php">Home</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="rooms.php">Rooms</a>
            <a href="#">Help</a>
        </div>
    </div>

    <div class="confirmation-container">
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="success-message">Booking successfully confirmed!</div>
        <?php endif; ?>

        <h1>Booking Confirmation</h1>
        <p>
            Thank you for using BookingSpace. Your booking has been successfully submitted and confirmed.
        </p>
        <a href="my_bookings.php" class="back-button">View My Bookings</a>
    </div>
</body>
</html>
