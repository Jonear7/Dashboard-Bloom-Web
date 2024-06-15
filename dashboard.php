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

// Fetch the count of employees
$employee_count_query = "SELECT COUNT(*) as total_employees FROM employees";
$employee_count_result = mysqli_query($conn, $employee_count_query);
$employee_count_row = mysqli_fetch_assoc($employee_count_result);
$total_employees = $employee_count_row['total_employees'];

$checkout_data_query = "SELECT checkout_date, total_price FROM checkin_out";
$checkout_data_result = mysqli_query($conn, $checkout_data_query);

// Prepare arrays for Chart.js
$chart_labels = [];
$chart_data = [];

while ($row = mysqli_fetch_assoc($checkout_data_result)) {
    $chart_labels[] = $row['checkout_date'];
    $chart_data[] = $row['total_price'];
}


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

    <!-- Sidebar -->
    <?php include 'bar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 bg-purple-800 p-10">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Statistics Section -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold">Statistics</h2>
                <div class="mt-8">
                    <p class="text-lg font-bold">Total Users</p>
                    <p class="text-4xl mt-2"><?php echo $total_users; ?></p>
                </div>
                <div class="mt-8">
                    <p class="text-lg font-bold">Total Rooms</p>
                    <p class="text-4xl mt-2"><?php echo $total_rooms; ?></p>
                </div>
                <div class="mt-8">
                    <p class="text-lg font-bold">Total Bookings</p>
                    <p class="text-4xl mt-2"><?php echo $total_bookings; ?></p>
                </div>
                <div class="mt-8">
                    <p class="text-lg font-bold">Total Check-ins</p>
                    <p class="text-4xl mt-2"><?php echo $total_checkins; ?></p>
                </div>
                <div class="mt-8">
                    <p class="text-lg font-bold">Total Employees</p>
                    <p class="text-4xl mt-2"><?php echo $total_employees; ?></p>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-gray-800 rounded-lg p-8 col-span-1 md:col-span-3">
                <h2 class="text-2xl font-bold mb-8 text-center">P r o f i t</h2>
                <canvas id="checkout-chart" class="w-full h-96"></canvas>
            </div>

        </div> <!-- End Grid -->
    </div> <!-- End Main Content -->

    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart data fetched from PHP
        const chartData = {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Price',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                data: <?php echo json_encode($chart_data); ?>
            }]
        };

        // Initialize chart
        const ctx = document.getElementById('checkout-chart').getContext('2d');
        const checkoutChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Price'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
