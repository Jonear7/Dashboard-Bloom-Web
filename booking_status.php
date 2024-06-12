<?php

// Assume your database connection is established and named $conn

// Check if the HTTP POST request contains 'room_id'
if (isset($_POST['room_id'])) {
    // Sanitize and store the room_id
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);

    // Query to check if the room is booked
    $query = "SELECT * FROM booking WHERE room_id = '$room_id'"; // Enclose $room_id in single quotes

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if there are any rows returned
    if ($result && mysqli_num_rows($result) > 0) { // Check if $result is valid before using mysqli_num_rows
        // Room is booked
        $response = array('status' => 'booked');
    } else {
        // Room is available
        $response = array('status' => 'available');
    }

    // Convert the response array to JSON and echo it
    echo json_encode($response);
} else {
    // Handle the case where 'room_id' is not provided in the POST request
    $response = array('error' => 'Room ID is not provided');
    echo json_encode($response);
}

?>
