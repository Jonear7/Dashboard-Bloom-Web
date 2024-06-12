<?php
// Include database connection file
require_once 'dbconnection.php';

// Initialize variables
$payment_id = $payment_image = $payment_date = '';
$error = '';

// Handle insert operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validate and sanitize input data
    $payment_date = $_POST['payment_date'];

    // Check if image file was uploaded
    if ($_FILES['payment_image']['error'] == 0) {
        $payment_image = $_FILES['payment_image']['name'];
        $targetDir = "payments/";
        $targetFilePath = $targetDir . basename($payment_image);

        // Check if file already exists
        if (!file_exists($targetFilePath)) {
            // Upload file
            if (move_uploaded_file($_FILES["payment_image"]["tmp_name"], $targetFilePath)) {
                // Insert payment data into database
                $query = "INSERT INTO payment (payment_image, payment_date) VALUES ('$payment_image', '$payment_date')";
                if (mysqli_query($conn, $query)) {
                    header("Location: payments.php");
                    exit();
                } else {
                    $error = "Error: " . $query . "<br>" . mysqli_error($conn);
                }
            } else {
                $error = "Error uploading file";
            }
        } else {
            $error = "File already exists";
        }
    } else {
        $error = "No file uploaded";
    }
}

// Handle delete operation for payment and corresponding booking
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "SELECT payment_image FROM payment WHERE payment_id=$id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $image = $row['payment_image'];
    $targetFilePath = "payments/" . $image;
    // Delete image file
    unlink($targetFilePath);
    // Delete payment record from database
    $query = "DELETE FROM payment WHERE payment_id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: payments.php");
        exit();
    } else {
        $error = "Error deleting record: " . mysqli_error($conn);
    }
}

// Handle delete operation for booking and corresponding payment
if (isset($_GET['delete_booking'])) {
    $booking_id = $_GET['delete_booking'];
    // Find the payment_id related to this booking
    $query = "SELECT payment_id FROM booking WHERE booking_id=$booking_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $payment_id = $row['payment_id'];
        // Delete the payment record
        $query = "DELETE FROM payment WHERE payment_id=$payment_id";
        mysqli_query($conn, $query);
    }
    // Delete the booking record
    $query = "DELETE FROM booking WHERE booking_id=$booking_id";
    if (mysqli_query($conn, $query)) {
        header("Location: bookings.php");
        exit();
    } else {
        $error = "Error deleting record: " . mysqli_error($conn);
    }
}

// Retrieve payments data from the database with booking_id
$query = "SELECT p.payment_id, p.payment_image, p.payment_date, b.booking_id 
          FROM payment p 
          LEFT JOIN booking b ON p.payment_id = b.payment_id";
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
    <!-- Include the sidebar -->
    <?php include 'bar.php'; ?>
    
    <!-- Main content -->
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Payments</h1>
            <!-- Display payments table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-black">
                            <th class="px-4 py-2">Payment ID</th>
                            <th class="px-4 py-2">Payment Image</th>
                            <th class="px-4 py-2">Payment Date</th>
                            <th class="px-4 py-2">Booking ID</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $payment['payment_id']; ?></td>
                                <td class="border px-4 py-2 flex justify-center">
                                    <?php
                                        // Get the path to the image file
                                        $imagePath = 'payments/' . $payment['payment_image']; // Assuming the images are stored in the 'payments' directory
                                        // Check if the file exists
                                        if (file_exists($imagePath)) {
                                            // Display the image with data-fancybox attribute
                                            echo '<a href="' . $imagePath . '" data-fancybox="images"><img src="' . $imagePath . '" class="w-24 h-24 object-cover" alt="Payment Image"></a>';
                                        } else {
                                            // Display a placeholder if the image file doesn't exist
                                            echo 'Image not available';
                                        }
                                    ?>
                                </td>
                                <td class="border px-4 py-2"><?php echo $payment['payment_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $payment['booking_id'] ? $payment['booking_id'] : 'No Booking'; ?></td>
                                <td class="border px-4 py-2">
                                    <a href="?delete=<?php echo $payment['payment_id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Error handling modal -->
            <div id="errorModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <!-- Heroicon name: exclamation -->
                                    <svg class="h-6 w-6 text-red-600" xmlns="https://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6-6h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium text-gray-900" id="modal-headline">
                                        Error
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500" id="modal-content">There was an error processing your request.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button id="closeErrorModal" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include FancyBox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <script>
        // Function to show the error modal
        function showErrorModal() {
            $('#errorModal').removeClass('hidden');
        }

        // Function to hide the error modal
        function hideErrorModal() {
            $('#errorModal').addClass('hidden');
        }

        // Close modal when close button is clicked
        $('#closeErrorModal').click(function() {
            hideErrorModal();
        });

        // Trigger error modal if PHP error message is set
        <?php if (!empty($error)) : ?>
            showErrorModal();
        <?php endif; ?>
    </script>
</body>
</html>
