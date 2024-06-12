<?php
// Database configuration
require_once 'dbconnection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get booking data from POST request
    $data = json_decode(file_get_contents("php://input"));

    // Check if all required fields are present
    if(isset($data->user_id, $data->room_id, $data->checkin_date, $data->checkout_date, $data->room_amount, $data->total_price, $data->payment_id)) {
        $user_id = $data->user_id;
        $room_id = $data->room_id;
        $checkin_date = $data->checkin_date;
        $checkout_date = $data->checkout_date;
        $room_amount = $data->room_amount;
        $total_price = $data->total_price;
        $payment_id = $data->payment_id;
        
        // Check if the user exists
        $user_check_query = "SELECT * FROM users WHERE user_id = ?";
        $stmt_user = $conn->prepare($user_check_query);
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        if($result->num_rows === 0) {
            // User does not exist
            echo json_encode(array("status" => "error", "message" => "User with ID $user_id does not exist"));
            exit(); // Terminate script execution
        }
        $stmt_user->close();
        
        // Prepare and execute SQL statement to insert booking data into the database
        $stmt = $conn->prepare("INSERT INTO booking (user_id, room_id, checkin_date, checkout_date, room_amount, total_price, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssis", $user_id, $room_id, $checkin_date, $checkout_date, $room_amount, $total_price, $payment_id);

        try {
            if ($stmt->execute()) {
                // Booking successful
                echo json_encode(array("status" => "success"));
            } else {
                // Booking failed
                throw new Exception("Failed to insert booking data");
            }
        } catch (Exception $e) {
            // Handle exceptions
            echo json_encode(array("status" => "error", "message" => $e->getMessage()));
        } finally {
            // Close statement
            $stmt->close();
        }
    } else {
        // Missing required fields
        echo json_encode(array("status" => "error", "message" => "Missing required fields"));
    }
} else {
    // Invalid request method
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

// Close connection
$conn->close();
?>
