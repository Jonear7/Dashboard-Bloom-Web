<?php
// Include database connection file
require_once 'dbconnection.php';

// Include TCPDF library
require_once 'tcpdf/tcpdf.php';

// Initialize variables
$payment_online_id = $payment_image = $payment_date = $payment_total = '';
$error = '';

// Handle insert operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validate and sanitize input data
    $payment_date = $_POST['payment_date'];
    $payment_total = $_POST['payment_total'];

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
                $query = "INSERT INTO payment_online (payment_image, payment_date, payment_total) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssd", $payment_image, $payment_date, $payment_total);
                
                if ($stmt->execute()) {
                    header("Location: payments.php");
                    exit();
                } else {
                    $error = "Error: " . $query . "<br>" . $conn->error;
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

// Handle delete operation for payment record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "SELECT payment_image FROM payment_online WHERE payment_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($payment_image);
        $stmt->fetch();

        // Delete image file
        $targetFilePath = "payments/" . $payment_image;
        if (file_exists($targetFilePath)) {
            unlink($targetFilePath);
        }

        // Delete payment record from database
        $query = "DELETE FROM payment_online WHERE payment_online_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: payments.php");
            exit();
        } else {
            $error = "Error deleting record: " . $conn->error;
        }
    } else {
        $error = "Record not found";
    }
}

// Retrieve payments data from the database including username
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT p.payment_id, p.payment_image, p.payment_date, p.payment_total, u.username 
          FROM payment_online p 
          INNER JOIN users u ON p.user_id = u.user_id
          WHERE p.payment_id LIKE '%$search%' OR u.username LIKE '%$search%'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $payments = [];
}

// Check if PDF generation is requested
if (isset($_POST['generate_pdf'])) {
    // Create a new TCPDF object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Online Payments');
    $pdf->SetSubject('Online Payments List');
    $pdf->SetKeywords('Payments, Online');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();
    // Add datetime at the top left
    $date = date('Y-m-d H:i'); // Current date and time
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY(10, 5); // Set the position to top left
    $pdf->Cell(0, 10,  $date, 0, 1, 'L');
    // Content
    $html = '<h1 style="text-align: center; margin-bottom: 10px;">Online Payments</h1>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f0f0f0; text-align: center;">
                        <th style="width: 25%; padding: 4px;">Payment ID</th>
                        <th style="width: 25%; padding: 4px;">Payment Date</th>
                        <th style="width: 25%; padding: 4px;">Payment Total</th>
                        <th style="width: 25%; padding: 4px;">Username</th>
                    </tr>
                </thead>
                <tbody>';

    // Add data rows
    foreach ($payments as $payment) {
        $html .= '<tr style="text-align: center;">
                    <td style="padding: 4px;">'.$payment['payment_id'].'</td>
                    <td style="padding: 4px;">'.$payment['payment_date'].'</td>
                    <td style="padding: 4px;">'.$payment['payment_total'].'</td>
                    <td style="padding: 4px;">'.$payment['username'].'</td>
                 </tr>';
    }

    $html .= '</tbody></table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('online_payments.pdf', 'I'); // I for inline display, D for download
    exit;
}

// Close database connection
$conn->close();
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
            <h1 class="text-3xl font-bold mb-8 text-center">Online Payments</h1>
            
            <!-- Form to generate PDF -->
            <form method="post" action="">
                <button type="submit" name="generate_pdf" class="bg-blue-400 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Print PDF</button>
            </form>

            <!-- Search form -->
            <form method="get" action="" class="mb-4">
                <input type="text" name="search" placeholder="Search by Payment ID or Username" class="p-2 rounded-md border border-gray-300">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 ml-2 rounded inline-block">Search</button>
            </form>

          <!-- Display payments table -->
<div class="overflow-x-auto">
    <table class="table-auto w-full">
        <thead>
            <tr class="bg-gray-200 text-black">
                <th class="px-4 py-2">Payment ID</th>
                <th class="px-4 py-2">Payment Date</th>
                <th class="px-4 py-2">Payment Total</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Image</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $payment['payment_id']; ?></td>
                    <td class="border px-4 py-2"><?php echo $payment['payment_date']; ?></td>
                    <td class="border px-4 py-2"><?php echo $payment['payment_total']; ?></td>
                    <td class="border px-4 py-2"><?php echo $payment['username']; ?></td>
                    <td class="border px-4 py-2">
                        <?php if (!empty($payment['payment_image'])): ?>
                            <a href="payments/<?php echo $payment['payment_image']; ?>" data-fancybox="gallery">
                                <img src="payments/<?php echo $payment['payment_image']; ?>" class="h-16 w-16 object-cover" alt="Payment Image">
                            </a>
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td class="border px-4 py-2">
                        <a href="?delete=<?php echo $payment['payment_id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4">No payments found.</td>
                </tr>
            <?php endif; ?>
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
