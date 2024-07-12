<?php
// Include database connection file
require_once 'dbconnection.php';

// Include TCPDF library
require_once 'tcpdf/tcpdf.php';

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

// Function to generate PDF using TCPDF
function generatePDF($records) {
    // Create a new TCPDF object
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Check-out Records');
    $pdf->SetSubject('Check-out Records List');
    $pdf->SetKeywords('Check-out, Records');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage('P', 'Letter');
   // Add datetime at the top left
   $date = date('Y-m-d H:i'); // Current date and time
   $pdf->SetFont('helvetica', '', 10);
   $pdf->SetXY(10, 7); // Set the position to top left
   $pdf->Cell(0, 10,  $date, 0, 1, 'L');
    // Content
    $html = '<h1 style="text-align: center; margin-bottom: 10px;">Check-out Records</h1>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f0f0f0; text-align: center;">
                        <th style="width: 7%; padding: 8px;">ID</th>
                        <th style="width: 20%; padding: 8px;">Username</th>
                        <th style="width: 15%; padding: 8px;">Check-in Date</th>
                        <th style="width: 15%; padding: 8px;">Check-out Date</th>
                        <th style="width: 15%; padding: 8px;">Total Price</th>
                        <th style="width: 15%; padding: 8px;">Room Number</th>
                        <th style="width: 15%; padding: 8px;">Status</th>
                    </tr>
                </thead>
                <tbody>';

    // Add data rows
    foreach ($records as $record) {
        $html .= '<tr style="text-align: center;">
                    <td style="width: 7%; padding: 8px;">'.$record['booking_id'].'</td>
                    <td style="width: 20%;padding: 8px;">'.$record['username'].'</td>
                    <td style="width: 15%; padding: 8px;">'.$record['checkin_date'].'</td>
                    <td style="width: 15%; padding: 8px;">'.$record['checkout_date'].'</td>
                    <td style="width: 15%; padding: 8px;">'.$record['total_price'].'</td>
                    <td style="width: 15%; padding: 8px;">'.$record['room_number'].'</td>
                    <td style="width: 15%; padding: 8px;">'.$record['status'].'</td>
                 </tr>';
    }

    $html .= '</tbody></table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('checkout_records.pdf', 'I'); // I for inline display, D for download
    exit;
}

// Check if PDF generation is requested
if (isset($_POST['generate_pdf'])) {
    generatePDF($checkin_out_records);
}
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
                <form method="post" action="">
                    <button type="submit" name="generate_pdf" class="bg-blue-400 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">
                        <i class="fas fa-print"></i> Print All
                    </button>
                </form>
            </div>

            <!-- Display checkin_out records -->
            <div class="overflow-x-auto">
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
