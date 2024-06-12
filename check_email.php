<?php
// Database configuration
require_once 'dbconnection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email from POST request
    $email = $_POST['email'];

    // Check if the email is in a valid format and contains "@gmail.com"
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/@gmail\.com$/", $email)) {
        // Invalid email format
        echo json_encode("InvalidEmailFormat");
        exit(); // Terminate script execution
    }

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Return response based on whether the email exists
    if ($result->num_rows > 0) {
        // Email exists
        echo json_encode("EmailExists");
    } else {
        // Email does not exist
        echo json_encode("EmailAvailable");
    }
} else {
    // Invalid request method
    echo json_encode("InvalidRequestMethod");
}

// Close connection
$conn->close();

