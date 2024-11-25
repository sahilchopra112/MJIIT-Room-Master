<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to book a room.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room - MJIIT RoomMaster</title>
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
            padding: 10px 20px;
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
            color: #8B0000;
        }

        .navbar-profile i {
            font-size: 24px;
        }

        /* Booking Form Styling */
        .booking-container {
            margin: 50px auto;
            text-align: center;
            max-width: 700px;
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
        }

        .room-details {
            text-align: center;
        }

        .room-details h2 {
            font-size: 1.8rem;
            color: #8B0000;
            margin-bottom: 20px;
        }

        .room-details p {
            font-size: 1.1rem;
            margin: 10px 0;
        }

        .form-inputs label {
            font-weight: bold;
            font-size: 1.1em;
            display: block;
            margin-bottom: 5px;
        }

        .form-inputs input {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border-radius: 8px;
            border: 2px solid #800000;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        .book-now-button {
            padding: 12px 30px;
            font-size: 1.2em;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }

        .book-now-button:hover {
            background-color: #5f2a1e;
        }

        .error-message {
            color: red;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
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
        <div class="navbar-profile">
            <i class="fa-regular fa-user"></i>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="booking-container">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="room-details">
            <img src="room-image.jpg" alt="Room Image" class="room-image">
            <h2><?php echo htmlspecialchars($_GET['room'] ?? 'Room Name'); ?></h2>
            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($_GET['capacity'] ?? '0'); ?> People</p>
            <p><strong>Equipment:</strong> <?php echo htmlspecialchars($_GET['equipment'] ?? 'None'); ?></p>
        </div>

        <form action="submit_booking.php" method="POST">
            <input type="hidden" name="room" value="<?php echo htmlspecialchars($_GET['room'] ?? ''); ?>">
            <div class="form-inputs">
                <label for="booking-date">Booking Date:</label>
                <input type="date" id="booking-date" name="booking_date" required>

                <label for="checkin-time">Check-In Time:</label>
                <input type="time" id="checkin-time" name="checkin_time" required>

                <label for="checkout-time">Check-Out Time:</label>
                <input type="time" id="checkout-time" name="checkout_time" required>
            </div>

            <button type="submit" class="book-now-button">Book Now</button>
        </form>
    </div>
</body>
</html>
