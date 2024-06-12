<?php
// Include database connection file
require 'dbconnection.php';

// Extract room ID, check-in date, and check-out date from the POST data
$roomId = $_POST['id'];
$checkinDate = $_POST['checkinDate'];
$checkoutDate = $_POST['checkoutDate'];

// Initialize response array
$response = [];

// Check if the room ID and dates are provided
if ($roomId && $checkinDate && $checkoutDate) {
    // Prepare query to check for overlapping bookings
    $query = "SELECT * FROM booking WHERE room_id = ? AND ((checkin_date <= ? AND checkout_date >= ?) OR (checkin_date <= ? AND checkout_date >= ?) OR (checkin_date >= ? AND checkout_date <= ?))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $roomId, $checkinDate, $checkinDate, $checkoutDate, $checkoutDate, $checkinDate, $checkoutDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any overlapping bookings exist
    if ($result->num_rows > 0) {
        // Fetch overlapping booking dates
        while ($row = $result->fetch_assoc()) {
            $response[] = $row['checkin_date'];
            $response[] = $row['checkout_date'];
        }
    }
}

// Close database connection
$stmt->close();
$conn->close();

// Send response
header('Content-Type: application/json');
echo json_encode($response);

