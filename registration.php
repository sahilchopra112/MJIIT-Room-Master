<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Query the correct table: registereduserid
    $sql_check = "SELECT * FROM registereduserid WHERE username = ? OR email = ?";  // Correct table name here
    $stmt_check = $conn->prepare($sql_check);

    // If there is an issue preparing the statement, display an error
    if ($stmt_check === false) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Bind parameters to the SQL query
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // If the username or email already exists, display a message
    if ($result_check->num_rows > 0) {
        echo "Username or Email is already taken. Please choose a different one.";
    } else {
        // Prepare SQL query to insert new user into the database
        $sql_insert = "INSERT INTO registereduserid (username, password, email, role) VALUES (?, ?, ?, ?)";  // Correct table name here
        $stmt_insert = $conn->prepare($sql_insert);

        // If there is an issue preparing the insert statement, display an error
        if ($stmt_insert === false) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        // Bind parameters to the insert SQL query
        $stmt_insert->bind_param("ssss", $username, $password, $email, $role);

        // Execute the insert query and check if the insertion is successful
        if ($stmt_insert->execute()) {
            // Redirect to the login page after successful registration
            header('Location: login.php');
            exit;
        } else {
            // If there is an error executing the query, display the error
            echo "Error inserting record: " . $stmt_insert->error;
        }

        // Close the prepared statement
        $stmt_insert->close();
    }

    // Close the check statement
    $stmt_check->close();
    // Close the database connection
    $conn->close();
}
?>

