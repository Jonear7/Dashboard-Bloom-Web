<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$database = "bloom"; // Replace with your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get login data from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute SQL statement to retrieve user data from the database
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Authentication successful
        $response = array(
            "status" => "success",
            "user" => array(
                "user_id" => $row['user_id'],
                "username" => $row['username'],
                "email" => $row['email'],
                "phone" => $row['phone']
            )
        );
        echo json_encode($response);
    } else {
        // Authentication failed (incorrect password)
        echo json_encode(array("status" => "error", "message" => "Incorrect password"));
    }
} else {
    // Authentication failed (user not found)
    echo json_encode(array("status" => "error", "message" => "User not found"));
}

// Close connection
$conn->close();

