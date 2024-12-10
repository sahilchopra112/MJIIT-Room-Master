<?php
session_start();
include 'config.php'; // Include your database connection setup

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
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
            z-index: 10;
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

        .navbar-profile i {
            font-size: 24px;
        }

        .booking-container {
            margin-top: 50px;
            text-align: center;
            padding: 20px;
        }

        .room-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: 0 auto;
        }

        .room-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }

        .room-info {
            margin-top: 20px;
        }

        .room-info p {
            font-size: 1.2em;
            color: #333;
        }

        .form-inputs {
            margin-top: 30px;
            text-align: left;
        }

        .form-inputs label {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
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
            padding: 10px 20px;
            font-size: 1.2em;
            background-color: #800000;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }

        .book-now-button:hover {
            background-color: #5f2a1e;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
        }

        .modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .modal-buttons a {
            padding: 10px 20px;
            background-color: #800000;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
        }

        .modal-buttons a:hover {
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
            <a href="home.html">Home</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="#">Rooms</a>
            <a href="#">Analytics</a>
            <a href="#">Help</a>
        </div>
        <div class="navbar-profile">
            <i class="fa-regular fa-user"></i>
        </div>
    </div>

    <!-- Booking Container -->
    <div class="booking-container">
        <div class="room-details">
            <img src="room-image.jpg" alt="Room Image" class="room-image">
            <div class="room-info">
                <p><strong>Room Name:</strong> <span id="room-name-detail"></span></p>
                <p><strong>Capacity:</strong> <span id="capacity"></span> People</p>
                <p><strong>Equipment:</strong> <span id="equipment"></span></p>
            </div>

            <!-- Booking Form -->
            <form action="submit_booking.php" method="POST">
                <input type="hidden" name="room" id="room" value="">
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
    </div>

    <script>
        // Get URL parameters and populate room details
        const urlParams = new URLSearchParams(window.location.search);
        const roomName = urlParams.get('room');
        const capacity = urlParams.get('capacity');
        const equipment = urlParams.get('equipment');
        const imageUrl = urlParams.get('image');

        document.getElementById('room-name-detail').innerText = roomName;
        document.getElementById('capacity').innerText = capacity;
        document.getElementById('equipment').innerText = equipment;
        document.querySelector('.room-image').src = imageUrl;
        document.getElementById('room').value = roomName;
    </script>
</body>
</html>
