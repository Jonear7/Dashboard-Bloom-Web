<?php
require_once('tcpdf/tcpdf.php');
require_once('dbconnection.php'); // Adjust the path as per your file structure

if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);

    // Fetch booking details
    $query = "SELECT booking.booking_id, booking.checkin_date, booking.checkout_date, booking.total_price, booking.room_id, booking.status, 
              users.username, users.phone, room.room_number AS room_number, rmtype.type_name
              FROM booking 
              JOIN users ON booking.user_id = users.user_id 
              JOIN room ON booking.room_id = room.room_id 
              JOIN rmtype ON room.rmtype_id = rmtype.rmtype_id 
              WHERE booking.booking_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($booking) {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hotel Bloom');
        $pdf->SetTitle('Hotel Receipt');
        $pdf->SetSubject('Hotel Receipt');

        // Add a page
        $pdf->AddPage();
        // Add datetime at the top left
        $date = date('Y-m-d H:i'); // Current date and time
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(10, 7); // Set the position to top left
        $pdf->Cell(0, 10,  $date, 0, 1, 'L');

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Centered logo
        $logo = '<div style="text-align: center;"><img src="images/logo.jpg" style="width: 150px; height: auto; margin-bottom: 10px;" /></div>';
        $pdf->writeHTML($logo, true, false, true, false, '');

        // Hotel details and address
        $html = '<h1 style="text-align: center; font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Hotel Bloom Receipt</h1>';
        $html .= '<p style="text-align: center; font-size: 12px; color: #2980b9; margin-bottom: 5px;">Wat XiengNgeun Alley, Sethathirath Road,</p>';
        $html .= '<p style="text-align: center; font-size: 12px; color: #2980b9; margin-bottom: 5px;">XiengNgeun Village, Chanthaboury District</p>';
        $html .= '<p style="text-align: center; font-size: 12px; color: #2980b9; margin-bottom: 20px;"><strong>Phone:</strong> 021 216 140</p>';

        // Receipt details
        $html .= '<table cellspacing="0" cellpadding="4" style="width: 100%; margin-bottom: 20px; border: 1px solid #ccc; border-collapse: collapse;">';
        $html .= '<tr style="background-color: #f0f0f0; font-weight: bold; color: #2c3e50;"><td colspan="2" style="text-align: center;">Booking Details</td></tr>';
        $html .= '<tr><td style="width: 40%;"><strong>Booking ID:</strong></td><td>' . $booking['booking_id'] . '</td></tr>';
        $html .= '<tr><td><strong>User:</strong></td><td>' . $booking['username'] . '</td></tr>';
        $html .= '<tr><td><strong>Phone:</strong></td><td>' . $booking['phone'] . '</td></tr>';
        $html .= '<tr><td><strong>Room:</strong></td><td>' . $booking['room_number'] . ' (' . $booking['type_name'] . ')</td></tr>';
        $html .= '<tr><td><strong>Check-in:</strong></td><td>' . $booking['checkin_date'] . '</td></tr>';
        $html .= '<tr><td><strong>Check-out:</strong></td><td>' . $booking['checkout_date'] . '</td></tr>';
        $html .= '<tr><td><strong>Total Price:</strong></td><td>$' . $booking['total_price'] . '</td></tr>';
        $html .= '</table>';

        // Footer with thank you message
        $html .= '<br/>';
        $html .= '<div style="text-align: center; font-size: 12px; color: #2980b9;">Thank you for choosing Hotel Bloom!</div>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $pdf->Output('hotel_receipt.pdf', 'I');
        exit;
    } else {
        die('Booking not found.');
    }
} else {
    die('Booking ID not specified.');
}
