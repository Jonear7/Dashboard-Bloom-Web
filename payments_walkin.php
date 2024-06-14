<?php
// Include database connection file
require_once 'dbconnection.php';

// Initialize variables
$error_message = '';
$success_message = '';

// Function to sanitize user input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Handle delete operation for walk-in payment
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Prepare and execute delete query
    $delete_query = "DELETE FROM payment_walk_in WHERE payment_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);

    if ($stmt === false) {
        $error_message = "Error: Could not prepare delete statement";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Payment record deleted successfully";
        } else {
            $error_message = "Error deleting payment record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch walk-in payments
$query = "SELECT * FROM payment_walk_in ORDER BY payment_date DESC";
$result = mysqli_query($conn, $query);
$payments = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
   
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Admin Dashboard</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include FancyBox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-purple-800 text-white">
    <!-- Navigation Bar -->
    <?php include 'bar.php'; ?>

    <!-- Main content -->
    <div class="container mx-auto py-16 text-center ">
        <h1 class="text-3xl font-bold mb-8">Walk-in Payments</h1>

        <!-- Display success message if payment is added successfully -->
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-500 text-white p-4 mb-4"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Display error message if there's any issue -->
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-500 text-white p-4 mb-4"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Display table of walk-in payments -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-white text-black">
                        <th class="border px-4 py-2">Payment ID</th>
                        <th class="border px-4 py-2">Payment Date</th>
                        <th class="border px-4 py-2">Payment Total</th>
                        <th class="border px-4 py-2">Actions</th>
                        <!-- Add more headers as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr class=" border-b text-white">
                            <td class="border px-4 py-2"><?php echo $payment['payment_id']; ?></td>
                            <td class="border px-4 py-2"><?php echo $payment['payment_date']; ?></td>
                            <td class="border px-4 py-2"><?php echo $payment['payment_total']; ?></td>
                            <td class="border px-4 py-2">
                                <a href="?delete=<?php echo $payment['payment_id']; ?>" onclick="return confirm('Are you sure you want to delete this payment record?')" class="text-red-600 hover:text-red-800">Delete</a>
                            </td>
                            <!-- Add more columns as needed -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
