<?php
// Include database connection file
require_once 'dbconnection.php';

// Function to sanitize user input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Initialize variable for search
$search = '';

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['search'])) {
        $search = sanitize_input($conn, $_GET['search']);
    }
}

// Create Operation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_create"])) {
        // Sanitize input data
        $user_id = sanitize_input($conn, $_POST['user_id']);
        $room_id = sanitize_input($conn, $_POST['room_id']);
        $payment_id = sanitize_input($conn, $_POST['payment_id']);
        $checkin_date = sanitize_input($conn, $_POST['checkin_date']);
        $checkout_date = sanitize_input($conn, $_POST['checkout_date']);
        $total_price = sanitize_input($conn, $_POST['total_price']);
        $room_amount = sanitize_input($conn, $_POST['room_amount']);

        // Insert data into database using prepared statement
        $query = "INSERT INTO booking (user_id, room_id, payment_id, checkin_date, checkout_date, total_price, room_amount) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiisssi', $user_id, $room_id, $payment_id, $checkin_date, $checkout_date, $total_price, $room_amount);
        if (mysqli_stmt_execute($stmt)) {
            // Success message
            $success_message = "Booking added successfully.";
        } else {
            // Error message
            $error_message = "Error adding booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Build the query based on search
$query = "SELECT booking.booking_id, booking.checkin_date, booking.checkout_date, booking.total_price, booking.room_amount, users.username, room.room_id
          FROM booking 
          JOIN users ON booking.user_id = users.user_id 
          JOIN room ON booking.room_id = room.room_id WHERE 1=1";


if ($search) {
    $query .= " AND (users.username LIKE '%$search%' OR room.room_id LIKE '%$search%' OR booking.booking_id LIKE '%$search%')";
}

// Read Operation
$result = mysqli_query($conn, $query);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Admin Dashboard</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 1s ease-out;
        }
    </style>
</head>
<body class="bg-purple-800 text-white">
<?php include 'bar.php'; ?>
    <!-- Main content -->
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Bookings</h1>
            <!-- Display success or error messages -->
            <?php if(isset($success_message)): ?>
                <div id="success-message" class="bg-green-500 text-white p-4 mb-4"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if(isset($error_message)): ?>
                <div id="error-message" class="bg-red-500 text-white p-4 mb-4"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Search form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="mb-4">
                <div class="flex items-center">
                    <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search..." class="bg-gray-200 text-black font-bold py-2 px-4 rounded">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">Search</button>
                </div>
            </form>

            <!-- Display bookings table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-black">
                            <th class="px-4 py-2">Booking ID</th>
                            <th class="px-4 py-2">Room ID</th> <!-- Add this line -->
                            <th class="px-4 py-2">User Name</th>
                            <th class="px-4 py-2">Check-in Date</th>
                            <th class="px-4 py-2">Check-out Date</th>
                            <th class="px-4 py-2">Total Price</th>
                            <th class="px-4 py-2">Room Amount</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $booking['booking_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['room_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['username']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkin_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkout_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['total_price']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['room_amount']; ?></td>
                               
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Function to hide messages after 5 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        let errorMessage = document.getElementById('error-message');
        if (successMessage) {
            successMessage.classList.add('fade-out');
        }
        if (errorMessage) {
            errorMessage.classList.add('fade-out');
        }
    }, 5000);
</script>
</body>
</html>
