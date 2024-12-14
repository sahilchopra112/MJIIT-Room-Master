<?php
include 'config.php'; // This includes your database connection setup

// Initialize search query and results
$searchQuery = "";
$result = null;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['room_name'], $_GET['checkin_time'], $_GET['checkout_time'], $_GET['date'])) {
    $room_name = $_GET['room_name'];
    $checkin_time = $_GET['checkin_time'];
    $checkout_time = $_GET['checkout_time'];
    $date = $_GET['date'];

    $searchQuery = " WHERE 1=1";

    if (!empty($room_name)) {
        $searchQuery .= " AND room_name LIKE '%" . $conn->real_escape_string($room_name) . "%'";
    }
    if (!empty($checkin_time) && !empty($checkout_time) && !empty($date)) {
        $searchQuery .= " AND NOT EXISTS (
            SELECT 1 FROM bookings 
            WHERE bookings.room_id = rooms.room_id 
            AND bookings.booking_date = '" . $conn->real_escape_string($date) . "'
            AND (
                (bookings.start_time BETWEEN '$checkin_time' AND '$checkout_time') OR
                (bookings.end_time BETWEEN '$checkin_time' AND '$checkout_time') OR
                ('$checkin_time' BETWEEN bookings.start_time AND bookings.end_time) OR
                ('$checkout_time' BETWEEN bookings.start_time AND bookings.end_time)
            )
        )";
    }

    $sql = "SELECT room_name, location, capacity, equipment, image, status FROM rooms" . $searchQuery;
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BookingSpace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
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

        /* Navbar Styling */
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

        /* Welcome Text Container */
        .welcome-text-container {
            background-color: #8B0000;
            color: white;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            max-width: 900px;
            margin: 20px auto;
        }

        /* Search Bar Container */
        .search-bar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border: 2px solid #8B0000;
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 20px auto;
        }

        .search-bar-item {
            display: flex;
            flex-direction: column;
            padding: 10px;
            flex: 1; /* Equal space for all inputs */
        }

        .search-button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
        }

        .search-button:hover {
            background-color: #0056b3;
        }

        /* Rooms Container */
        .rooms-container {
            max-width: 900px;
            width: 75%;
            margin-left: auto;
            margin-right: auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        /* Original Room Card Styling */
        .room {
            background-color: white;
            color: black;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 100%;
            min-height: 300px;
            padding: 10px;
            box-sizing: border-box;
        }

        .room img {
            width: calc(100% - 20px);
            margin: 0 auto;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }

        .room-details {
            padding: 10px;
            text-align: left;
            flex-grow: 1;
        }

        .room-details h3 {
            font-size: 1.1em;
            font-weight: bold;
            color: rgb(114, 4, 4);
            margin-bottom: 5px;
        }

        .room-details p {
            font-size: 1em;
            margin-bottom: 5px;
        }

        .room:hover {
            transform: translateY(-5px);
        }
        .btn-book-now {
        display: block;
        width: calc(100% - 20px); /* Match image width */
        margin: 0 auto;
        padding: 10px 0;
        background-color: #8B0000; /* Red color */
        color: white;
        text-decoration: none;
        text-align: center;
        border-radius: 5px;
        font-size: 1em;
        font-weight: bold;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    .btn-book-now:hover {
        background-color: #8B0000; /* Darker red on hover */
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
            <a href="#">Home</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="rooms.php">Rooms</a>
            <a href="#">Analytics</a>
            <a href="#">Help</a>
        </div>
        <div class="navbar-profile">
            <i class="fa-regular fa-user"></i>
        </div>
    </div>

    <!-- Welcome Page -->
    <div class="welcome-text-container">
        <h2>Welcome to BookingSpace</h2>
        <p>Efficiently manage and book rooms at MJIIT, Universiti Teknologi Malaysia.</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="search-bar-container">
        <div class="search-bar-item">
            <label for="room_name">Room Name</label>
            <input type="text" id="room_name" name="room_name" placeholder="Enter room name">
        </div>
        <div class="search-bar-item">
            <label for="date">Date</label>
            <input type="date" id="date" name="date">
        </div>
        <div class="search-bar-item">
            <label for="checkin_time">Check-in Time</label>
            <input type="time" id="checkin_time" name="checkin_time">
        </div>
        <div class="search-bar-item">
            <label for="checkout_time">Check-out Time</label>
            <input type="time" id="checkout_time" name="checkout_time">
        </div>
        <button type="submit" class="search-button">Search</button>
    </form>

<!-- Rooms Container -->
<?php if ($result): ?>
    <div class="rooms-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="room">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['room_name']); ?>">
                    <div class="room-details">
                        <h3><?php echo htmlspecialchars($row['room_name']); ?></h3>
                        <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                        <p>Capacity: <?php echo htmlspecialchars($row['capacity']); ?> People</p>
                        <p>Equipment: <?php echo htmlspecialchars($row['equipment']); ?></p>
                        <!-- Book Now Button -->
                        <a 
                            href="booking.php?room_name=<?php echo urlencode($row['room_name']); ?>&date=<?php echo isset($date) ? urlencode($date) : ''; ?>&checkin_time=<?php echo isset($checkin_time) ? urlencode($checkin_time) : ''; ?>&checkout_time=<?php echo isset($checkout_time) ? urlencode($checkout_time) : ''; ?>" 
                            class="btn-book-now">
                            Book Now
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No rooms available for the selected criteria.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
