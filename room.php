<?php
require 'dbconnection.php';

// Query to fetch room data with status update based on booking
$query = "
    SELECT 
        room.room_id,
        room.room_number,
        room.rmtype_id,
        CASE 
            WHEN booking.room_id IS NOT NULL THEN 'unavailable' 
            ELSE room.status 
        END AS status
    FROM room
    LEFT JOIN booking ON room.room_id = booking.room_id
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $roomData = array();
    while ($row = $result->fetch_assoc()) {
        $roomData[] = $row; // Add each row to the result array
    }
    echo json_encode($roomData); // Return room data in JSON format
} else {
    echo json_encode(array()); // Return an empty array if no rooms found
}

$conn->close();

