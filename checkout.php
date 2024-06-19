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

// Build the query based on search
$query = "SELECT c.*, u.username FROM checkin_out c JOIN users u ON c.user_id = u.user_id WHERE 1=1";

if ($search) {
    $query .= " AND (c.booking_id LIKE '%$search%' OR c.room_number LIKE '%$search%' OR c.status LIKE '%$search%' OR u.username LIKE '%$search%')";
}

// Read Operation
$result = mysqli_query($conn, $query);
$checkin_out_records = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-out Records</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function printAll() {
            var content = document.getElementById('checkin-out-table').innerHTML;
            var mywindow = window.open('', 'Print', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Check-out Records</title>');
            mywindow.document.write('<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">');
            mywindow.document.write('</head><body >');
            mywindow.document.write(content);
            mywindow.document.write('</body></html>');
            mywindow.document.close();
            mywindow.focus();
            mywindow.print();
            mywindow.close();
            return true;
        }
    </script>
</head>
<body class="bg-purple-800 text-white">
    <?php include 'bar.php'; ?>
    <!-- Main content -->
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Check-out Records</h1>

            <!-- Search form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="mb-4">
                <div class="flex items-center">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search..." class="bg-gray-200 text-black font-bold py-2 px-4 rounded-full">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full ml-2">Search</button>
                </div>
            </form>

            <!-- Print All button -->
            <div class="mb-4 text-center">
                <button onclick="printAll()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">
                    <i class="fas fa-print"></i> Print All
                </button>
            </div>

            <!-- Display checkin_out records -->
            <div id="checkin-out-table" class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-black">
                            <th class="px-4 py-2">Booking ID</th>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Check-in Date</th>
                            <th class="px-4 py-2">Check-out Date</th>
                            <th class="px-4 py-2">Total Price</th>
                            <th class="px-4 py-2">Room Number</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkin_out_records as $record): ?>
                            <tr class="text-center">
                                <td class="border px-4 py-2"><?php echo $record['booking_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['username']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['checkin_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['checkout_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['total_price']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['room_number']; ?></td>
                                <td class="border px-4 py-2"><?php echo $record['status']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
