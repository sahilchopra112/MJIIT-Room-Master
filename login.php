<?php
session_start();  // Start or resume a session

include 'config.php';  // This includes your database connection from 'config.php'

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];  // You should hash this password and compare against hashed password in DB

    // SQL to check the username and password
    $sql = "SELECT user_id FROM users WHERE username = ? AND password = ?";  // Adjust this to use password hashing
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];  // Set user_id in session
        header("Location: index.html");  // Redirect to the homepage or dashboard
        exit;
    } else {
        echo "Invalid username or password";
    }
    $stmt->close();
}
$conn->close();
?>

<!-- HTML for login form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MJIIT RoomMaster</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>
