<?php
// Database configuration
require_once 'dbconnection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the file is set and uploaded successfully
    if (isset($_FILES['payment_image']) && $_FILES['payment_image']['error'] === UPLOAD_ERR_OK) {
        // Directory where the images will be stored
        $uploadDirectory = 'payments/';

        // Ensure the directory exists
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Generate a unique filename
        $fileName = uniqid() . '-' . basename($_FILES['payment_image']['name']);
        $filePath = $uploadDirectory . $fileName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['payment_image']['tmp_name'], $filePath)) {
            // File upload successful, proceed to insert payment data into the database
            $payment_date = date("Y-m-d"); // Assuming you want to use the current date
            $payment_total = $_POST['payment_total']; // Assuming payment_total is sent via POST

            // Retrieve user_id from session or wherever it's stored
            session_start();
            $user_id = $_POST['user_id']; // Adjust this according to your POST data structure

            // Prepare and bind parameters for insertion
            $insertPaymentStmt = $conn->prepare("INSERT INTO payment_online (payment_image, payment_date, payment_total, user_id) VALUES (?, ?, ?, ?)");
            $insertPaymentStmt->bind_param("sssi", $fileName, $payment_date, $payment_total, $user_id);

            if ($insertPaymentStmt->execute()) {
                // Payment data inserted successfully
                $payment_id = $conn->insert_id; // Retrieve the payment_id of the newly inserted payment
                echo json_encode(array("status" => "success", "payment_id" => $payment_id));
            } else {
                // Failed to insert payment data
                echo json_encode(array("status" => "error", "message" => "Failed to insert payment data"));
            }

            // Close statement
            $insertPaymentStmt->close();
        } else {
            // Failed to move the file
            echo json_encode(array("status" => "error", "message" => "Failed to move uploaded file"));
        }
    } else {
        // No file uploaded or upload failed
        echo json_encode(array("status" => "error", "message" => "No file uploaded or upload failed"));
    }
} else {
    // Invalid request method
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>
