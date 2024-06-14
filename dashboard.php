<?php
// Include database connection file
require_once 'dbconnection.php';

// Fetch the count of users
$user_count_query = "SELECT COUNT(*) as total_users FROM users";
$user_count_result = mysqli_query($conn, $user_count_query);
$user_count_row = mysqli_fetch_assoc($user_count_result);
$total_users = $user_count_row['total_users'];

// Fetch the count of rooms
$room_count_query = "SELECT COUNT(*) as total_rooms FROM room";
$room_count_result = mysqli_query($conn, $room_count_query);
$room_count_row = mysqli_fetch_assoc($room_count_result);
$total_rooms = $room_count_row['total_rooms'];

// Fetch the count of bookings
$booking_count_query = "SELECT COUNT(*) as total_bookings FROM booking";
$booking_count_result = mysqli_query($conn, $booking_count_query);
$booking_count_row = mysqli_fetch_assoc($booking_count_result);
$total_bookings = $booking_count_row['total_bookings'];

// Fetch the count of current check-ins (assuming current check-ins are bookings where checkin_date is today or in the past and checkout_date is in the future)
$current_checkins_query = "SELECT COUNT(*) as total_checkins FROM booking WHERE checkin_date <= CURDATE() AND checkout_date > CURDATE()";
$current_checkins_result = mysqli_query($conn, $current_checkins_query);
$current_checkins_row = mysqli_fetch_assoc($current_checkins_result);
$total_checkins = $current_checkins_row['total_checkins'];

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-purple-800 text-white">
    <!-- Include the sidebar -->
    <?php include 'bar.php'; ?>
    
    <!-- Main content -->
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Admin Dashboard</h1>

            <!-- Display the counts -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-800 p-6 rounded-lg text-center">
                    <h2 class="text-xl font-bold">Total Users</h2>
                    <p class="text-3xl mt-2"><?php echo $total_users; ?></p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg text-center">
                    <h2 class="text-xl font-bold">Total Rooms</h2>
                    <p class="text-3xl mt-2"><?php echo $total_rooms; ?></p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg text-center">
                    <h2 class="text-xl font-bold">Total Bookings</h2>
                    <p class="text-3xl mt-2"><?php echo $total_bookings; ?></p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg text-center">
                    <h2 class="text-xl font-bold">Total Check-ins</h2>
                    <p class="text-3xl mt-2"><?php echo $total_checkins; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
