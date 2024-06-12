<?php
require 'dbconnection.php';

$query = "SELECT * FROM room";
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
