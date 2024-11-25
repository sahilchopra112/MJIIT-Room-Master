<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room - MJIIT RoomMaster Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Add New Room</h1>
        </div>
    </header>

    <main>
        <form action="save_room.php" method="POST">
            <label for="room_name">Room Name:</label>
            <input type="text" id="room_name" name="room_name" required>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" required>

            <label for="equipment">Equipment:</label>
            <input type="text" id="equipment" name="equipment">

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Available">Available</option>
                <option value="Booked">Booked</option>
            </select>

            <input type="submit" value="Add Room">
        </form>
    </main>

    <footer>
        <p>&copy; 2024 MJIIT RoomMaster</p>
    </footer>
</body>
</html>
