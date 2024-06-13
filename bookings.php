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

// Delete Operation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the action is for deleting a booking
    if (isset($_POST["submit_delete"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);

        // Delete booking from database
        $query = "DELETE FROM booking WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Booking deleted successfully.";
        } else {
            $error_message = "Error deleting booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    // Check if the action is for checking in
    if (isset($_POST["submit_checkin"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);

        // Update booking status to 'Checked-in' in the database
        $query = "UPDATE booking SET status = 'Checked-in' WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Booking checked-in successfully.";
        } else {
            $error_message = "Error checking in booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    // Check if the action is for checking out
    if (isset($_POST["submit_checkout"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);
        $checkout_date = date('Y-m-d'); // Current date as checkout date

        // Get booking details to insert into checkin_out
        $query_select = "SELECT * FROM booking WHERE booking_id = ?";
        $stmt_select = mysqli_prepare($conn, $query_select);
        mysqli_stmt_bind_param($stmt_select, 'i', $booking_id);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        $booking_details = mysqli_fetch_assoc($result_select);
        mysqli_stmt_close($stmt_select);

        // Insert into checkin_out table
        $query_insert = "INSERT INTO checkin_out (booking_id, checkin_date, checkout_date, total_price, room_number, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, 'isdsis', $booking_id, $booking_details['checkin_date'], $checkout_date, $booking_details['total_price'], $booking_details['room_number'], 'Checked-out');
        if (mysqli_stmt_execute($stmt_insert)) {
            $success_message = "Booking checked-out successfully.";
        } else {
            $error_message = "Error checking out booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_insert);

        // Delete booking from booking table
        $query_delete = "DELETE FROM booking WHERE booking_id = ?";
        $stmt_delete = mysqli_prepare($conn, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt_delete)) {
            $success_message .= " Booking record deleted.";
        } else {
            $error_message .= " Error deleting booking record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    }
}

// Build the query based on search
$query = "SELECT booking.booking_id, booking.checkin_date, booking.checkout_date, booking.total_price, booking.room_number, booking.status, users.username, room.room_number AS room_id
          FROM booking 
          JOIN users ON booking.user_id = users.user_id 
          JOIN room ON booking.room_id = room.room_id WHERE 1=1";

if ($search) {
    $query .= " AND (users.username LIKE '%$search%' OR room.room_number LIKE '%$search%' OR booking.booking_id LIKE '%$search%')";
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
                            <th class="px-4 py-2">Room Number</th>
                            <th class="px-4 py-2">User Name</th>
                            <th class="px-4 py-2">Check-in Date</th>
                            <th class="px-4 py-2">Check-out Date</th>
                            <th class="px-4 py-2">Total Price</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr id="booking_<?php echo $booking['booking_id']; ?>">
                                <td class="border px-4 py-2"><?php echo $booking['booking_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['room_number']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['username']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkin_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkout_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['total_price']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['status']; ?></td>
                                <td class="border px-4 py-2">
                                    <?php if ($booking['status'] === 'Checked-in'): ?>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <button type="submit" name="submit_checkout" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded">Checkout</button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="submit_delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Delete</button>
                                    </form>
                                </td>
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