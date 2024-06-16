<?php
// Include TCPDF library and database connection file
require_once 'tcpdf/tcpdf.php';
require_once 'dbconnection.php';

// Function to sanitize user input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Check if booking_id is provided in the URL
if (isset($_GET['booking_id'])) {
    $booking_id = sanitize_input($conn, $_GET['booking_id']);

    // Fetch the booking details from the database
    $query = "
        SELECT 
            co.checkin_out_id,
            co.booking_id, 
            co.checkin_date, 
            co.checkout_date, 
            co.total_price, 
            co.room_number
        FROM 
            checkin_out co
        WHERE 
            co.booking_id = '$booking_id'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Error retrieving booking details: ' . mysqli_error($conn));
    }

    $record = mysqli_fetch_assoc($result);

    if (!$record) {
        die('Booking record not found.');
    }

    // Create a new TCPDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Hotel Bloom');
    $pdf->SetTitle('Hotel Receipt');
    $pdf->SetSubject('Hotel Receipt');

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add logo at the center top
    $logo = '<div style="text-align: center;"><img src="images/logo.jpg" style="width: 150px; height: auto; margin-bottom: 10px;" /></div>';
    $pdf->writeHTML($logo, true, false, true, false, '');

    // Add hotel details and address within a container table
    $html = '
        <table style="width: 100%; margin: 0 auto; border-collapse: collapse;">
            <tr>
                <td style="text-align: center;">
                    <h1 style="font-size: 24px; font-weight: bold; color: #2c3e50;">Hotel Bloom Receipt</h1>
                    <p style="font-size: 12px; color: #2980b9; margin-bottom: 5px;">Wat XiengNgeun Alley, Sethathirath Road,</p>
                    <p style="font-size: 12px; color: #2980b9; margin-bottom: 5px;">XiengNgeun Village, Chanthaboury District</p>
                    <p style="font-size: 12px; color: #2980b9;">Phone: 021 216 140</p>
                </td>
            </tr>
        </table>';

    // Construct HTML for booking details table within the container
    $html .= '<table cellspacing="0" cellpadding="4" style="width: 100%; margin-bottom: 20px; border: 1px solid #ccc; border-collapse: collapse;">';
    $html .= '<tr style="background-color: #f0f0f0; font-weight: bold; color: #2c3e50;"><td colspan="2" style="text-align: center;">Booking Details</td></tr>';
    $html .= '<tr><td style="width: 40%;"><strong>Booking ID:</strong></td><td>' . $record['booking_id'] . '</td></tr>';
    $html .= '<tr><td><strong>Check-in/Out ID:</strong></td><td>' . $record['checkin_out_id'] . '</td></tr>';
    $html .= '<tr><td><strong>Room Number:</strong></td><td>' . $record['room_number'] . '</td></tr>';
    $html .= '<tr><td><strong>Check-in Date:</strong></td><td>' . $record['checkin_date'] . '</td></tr>';
    $html .= '<tr><td><strong>Check-out Date:</strong></td><td>' . $record['checkout_date'] . '</td></tr>';
    $html .= '<tr><td><strong>Total Price:</strong></td><td>$' . $record['total_price'] . '</td></tr>';
    $html .= '</table>';

    // Thank you message
    $html .= '<p style="text-align: center; font-size: 12px; color: #2980b9; margin-top: 20px;">Thank you for choosing Hotel Bloom!</p>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('Hotel_Receipt_' . $record['booking_id'] . '.pdf', 'I'); // 'I' option for inline display

    // Close database connection
    mysqli_close($conn);
    exit; // Exit script after generating PDF
} else {
    echo "Booking ID not provided.";
}
?>
