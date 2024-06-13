<?php
require 'dbconnection.php';

// Get the current date to check against booking dates
$currentDate = date('Y-m-d');

// Fetch room data with status update based on booking dates
$query = "
    SELECT 
        room.room_id, 
        room.rmtype_id, 
        CASE 
            WHEN booking.room_id IS NOT NULL AND booking.checkin_date <= '$currentDate' AND booking.checkout_date >= '$currentDate' THEN 'unavailable' 
            ELSE room.status 
        END AS status
    FROM room
    LEFT JOIN booking ON room.room_id = booking.room_id
        AND booking.checkin_date <= '$currentDate' 
        AND booking.checkout_date >= '$currentDate'
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $roomData = array();
    while ($row = $result->fetch_assoc()) {
        $roomData[] = array(
            'room_id' => $row['room_id'],
            'rmtype_id' => $row['rmtype_id'],
            'status' => $row['status'],
        );
    }
    echo json_encode($roomData); // Return room data in JSON format
} else {
    echo json_encode(array());
}

$conn->close();
?>
