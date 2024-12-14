<?php
include 'config.php'; // Include database connection setup

// Default SQL query
$sql = "SELECT room_name, location, capacity, equipment, image FROM rooms";

// If filters are applied, modify the query
$conditions = [];
if (isset($_GET['floor']) && !empty($_GET['floor'])) {
    $floor = (int)$_GET['floor'];
    $conditions[] = "CAST(SUBSTRING_INDEX(location, '.', 1) AS UNSIGNED) = $floor"; // Extract and compare the floor number
}
if (isset($_GET['capacity']) && !empty($_GET['capacity'])) {
    $conditions[] = "capacity >= " . (int)$_GET['capacity'];
}
if (isset($_GET['equipment']) && !empty($_GET['equipment'])) {
    $conditions[] = "equipment LIKE '%" . $conn->real_escape_string($_GET['equipment']) . "%'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - BookingSpace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
            justify-content: space-between;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-bottom: 2px solid #8B0000;
        }

        .navbar .navbar-title {
            display: flex;
            align-items: center;
        }

        .navbar .navbar-title img {
            max-height: 30px;
            margin-right: 10px;
        }

        .navbar .navbar-title p {
            font-weight: bold;
            font-size: 20px;
            color: rgb(114, 4, 4);
            margin: 0;
        }

        .navbar .navbar-links {
            display: flex;
            align-items: center;
        }

        .navbar .navbar-links a {
            color: rgb(119, 4, 4);
            text-decoration: none;
            margin-right: 20px;
            font-size: 14px;
        }

        .navbar .navbar-links a:hover {
            text-decoration: underline;
        }

        .filter-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-container form {
            display: flex;
            gap: 15px;
        }

        .filter-container input, .filter-container select, .filter-container button {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .filter-container button {
            background-color: #8B0000;
            color: white;
            border: none;
        }

        .filter-container button:hover {
            background-color: #5f2a1e;
        }

        .rooms-container {
            max-width: 1200px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .room-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .room-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .room-card .room-details {
            padding: 15px;
        }

        .room-card h3 {
            font-size: 1.2em;
            color: #8B0000;
            margin-bottom: 10px;
        }

        .room-card p {
            font-size: 0.9em;
            color: #333;
            margin: 5px 0;
        }

        .room-card:hover {
            transform: scale(1.05);
        }

        .btn-book {
            display: block;
            background-color: #8B0000;
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
        }

        .btn-book:hover {
            background-color: #5f2a1e;
        }

        .filter-container {
    max-width: 1200px;
    margin: 20px auto 10px; /* Smaller bottom margin */
    padding: 15px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(3, 1fr) auto; /* Input fields take equal space, button adjusts */
    align-items: center; /* Vertically align items */
    gap: 15px; /* Gap between form items */
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.filter-item label {
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 14px;
}

.filter-item input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.filter-button {
    display: flex;
    justify-content: flex-end; /* Push the button to the rightmost side */
    margin-left: auto; /* Ensure it stays aligned to the right of the container */
}

.filter-button button {
    padding: 10px 20px;
    font-size: 14px;
    background-color: #8B0000;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.filter-button button:hover {
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

    <div class="filter-container">
    <form method="GET" action="rooms.php" class="filter-form">
        <!-- Floor Filter -->
        <div class="filter-item">
            <label for="floor">Floor Number</label>
            <input 
                type="number" 
                id="floor" 
                name="floor" 
                placeholder="e.g., 3" 
                value="<?php echo $_GET['floor'] ?? ''; ?>" 
                title="Enter the floor number (e.g., 3 for the third floor)">
        </div>

        <!-- Capacity Filter -->
        <div class="filter-item">
            <label for="capacity">Minimum Capacity</label>
            <input 
                type="number" 
                id="capacity" 
                name="capacity" 
                placeholder="e.g., 10" 
                value="<?php echo $_GET['capacity'] ?? ''; ?>" 
                title="Enter the minimum capacity required (e.g., 10 for 10 people)">
        </div>

        <!-- Equipment Filter -->
        <div class="filter-item">
            <label for="equipment">Equipment</label>
            <input 
                type="text" 
                id="equipment" 
                name="equipment" 
                placeholder="e.g., Projector" 
                value="<?php echo $_GET['equipment'] ?? ''; ?>" 
                title="Enter the equipment you need (e.g., Projector, Whiteboard)">
        </div>

        <!-- Filter Button -->
        <div class="filter-button">
            <button type="submit" title="Click to apply the filters">Filter</button>
        </div>
    </form>
</div>

    <!-- Rooms Container -->
    <div class="rooms-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Fetch room details
                $roomName = htmlspecialchars($row['room_name']);
                $location = htmlspecialchars($row['location']);
                $capacity = htmlspecialchars($row['capacity']);
                $equipment = htmlspecialchars($row['equipment']);
                $image = htmlspecialchars($row['image']);
                ?>

                <!-- Room Card -->
                <div class="room-card">
                    <img src="<?php echo $image; ?>" alt="Image of <?php echo $roomName; ?>">
                    <div class="room-details">
                        <h3><?php echo $roomName; ?></h3>
                        <p><strong>Location:</strong> <?php echo $location; ?></p>
                        <p><strong>Capacity:</strong> <?php echo $capacity; ?> People</p>
                        <p><strong>Equipment:</strong> <?php echo $equipment; ?></p>
                        <a href="booking.php?room=<?php echo urlencode($roomName); ?>&location=<?php echo urlencode($location); ?>&capacity=<?php echo urlencode($capacity); ?>&equipment=<?php echo urlencode($equipment); ?>&image=<?php echo urlencode($image); ?>" class="btn-book">Book Now</a>
                    </div>
                </div>

                <?php
            }
        } else {
            echo "<p>No rooms match your filters.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
