<?php
session_start();
include 'config.php'; // Ensure database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to book a room.");
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Print out $_POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $user_id = $_SESSION['user_id'];
    $room_name = $_POST['room'] ?? ''; // Use null coalescing to avoid errors
    $booking_date = $_POST['booking_date'] ?? '';
    $checkin_time = $_POST['checkin_time'] ?? '';
    $checkout_time = $_POST['checkout_time'] ?? '';

    // Validate input data
    if (empty($room_name) || empty($booking_date) || empty($checkin_time) || empty($checkout_time)) {
        $error_message = "All fields are required.";
        echo $error_message; // Display error message
        exit;
    } else {
        // Get the room_id for the selected room
        $room_query = "SELECT room_id FROM rooms WHERE room_name = ?";
        $stmt = $conn->prepare($room_query);
        $stmt->bind_param("s", $room_name);
        $stmt->execute();
        $room_result = $stmt->get_result();

        if ($room_result->num_rows === 0) {
            $error_message = "Invalid room selection.";
            echo $error_message; // Display error message
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
                echo $error_message; // Display error message
                exit;
            } else {
                // Insert the booking into the database
                $insert_query = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, status) 
                                 VALUES (?, ?, ?, ?, ?, 'Confirmed')";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iisss", $user_id, $room_id, $booking_date, $checkin_time, $checkout_time);

                if ($insert_stmt->execute()) {
                    header("Location: my_bookings.php?message=Booking confirmed.");
                    exit;
                } else {
                    $error_message = "Error: " . $conn->error;
                    echo $error_message; // Display error message
                    exit;
                }
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
    <title>Sign In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('./image/Background.jpeg');
            background-size: cover;
            background-position: center;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background-color: #fff;
            max-width: 600px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container .button-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .form-container button {
            background-color: #2a9d8f;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #21867a;
        }
        .error-message {
            color: red;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Sign In</h1>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Enter your username" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <div class="button-container">
                <button type="submit">Login</button>
                <button type="button" onclick="location.href='guest_signup.php';">Guest Sign Up</button>
            </div>
        </form>
    </div>
</body>
</html>
