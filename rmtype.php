<?php
// Include your database connection file
require 'dbconnection.php';

// Query to fetch rmtype data
$query = "SELECT * FROM rmtype";
$result = mysqli_query($conn, $query);

if ($result) {
    $rmtypeData = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Decode the image URLs stored as a JSON array in the 'image' column
        $imageUrls = json_decode($row['image'], true);
        
        $rmtypeData[] = array(
            'rmtype_id' => $row['rmtype_id'],
            'type_name' => $row['type_name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'imageUrls' => $imageUrls, // Store image URLs as an array
        );
    }
    echo json_encode($rmtypeData); // Return rmtype data in JSON format
} else {
    // Handle database query error
    $error = mysqli_error($conn);
    http_response_code(500); // Internal Server Error
    exit("Database query error: $error");
}

// Close the database connection
mysqli_close($conn);
?>
