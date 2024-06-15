<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');

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
$query = "SELECT * FROM checkin_out WHERE 1=1";

if ($search) {
    $query .= " AND (booking_id LIKE '%$search%' OR room_number LIKE '%$search%' OR status LIKE '%$search%')";
}

// Read Operation
$result = mysqli_query($conn, $query);
$checkin_out_records = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close database connection
mysqli_close($conn);

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Check-out Records');
$pdf->SetSubject('Check-out Records');

// Add a page
$pdf->AddPage();

// Content
$html = '<h1 style="text-align:center;">Check-out Records</h1>';

$html .= '<table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Total Price</th>
                    <th>Room Number</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

foreach ($checkin_out_records as $record) {
    $html .= '<tr>
                <td>' . $record['booking_id'] . '</td>
                <td>' . $record['checkin_date'] . '</td>
                <td>' . $record['checkout_date'] . '</td>
                <td>' . $record['total_price'] . '</td>
                <td>' . $record['room_number'] . '</td>
                <td>' . $record['status'] . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Print text using Write()
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('check-out-records.pdf', 'I');
