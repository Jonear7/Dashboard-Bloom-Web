<?php
// Assuming you have a database connection already established
// Replace DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME with your actual database credentials

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if user_id parameter is provided
if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];

  // Select booking data with room name for the specified user
  $sql = "SELECT booking.*
          FROM booking 
          INNER JOIN room ON booking.room_id = room.room_id
          WHERE booking.user_id = $user_id";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $bookings = array();
    // Output data of each row
    while($row = $result->fetch_assoc()) {
      $bookings[] = $row;
    }
    echo json_encode($bookings);
  } else {
    // Return an empty array if there are no bookings for the user
    echo json_encode([]);
  }
} else {
  // user_id parameter is missing
  echo json_encode(array('error' => 'user_id parameter is missing.'));
}

$conn->close();

