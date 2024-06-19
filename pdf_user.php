<?php
// Include database connection file
require_once('dbconnection.php');

// Include TCPDF library
require_once('tcpdf/tcpdf.php');

// Function to fetch all users
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $users;
}

// Create a new TCPDF object
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('User List');
$pdf->SetSubject('User List');
$pdf->SetKeywords('Users, List');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Content
$html = '<h1 style="text-align: center; margin-bottom: 10px;">User List</h1>';
$html .= '<table border="1" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f0f0f0; text-align: center;">
                    <th style="width: 25%; padding: 4px;">ID</th>
                    <th style="width: 25%; padding: 4px;">Username</th>
                    <th style="width: 25%; padding: 4px;">Email</th>
                    <th style="width: 25%; padding: 4px;">Phone</th>
                </tr>
            </thead>
            <tbody>';

// Fetch users from database
$users = getAllUsers();

foreach ($users as $user) {
    $html .= '<tr style="text-align: center; ">
                <td style="padding: 4px;">'.$user['user_id'].'</td>
                <td style="padding: 4px;">'.$user['username'].'</td>
                <td style="padding: 4px;">'.$user['email'].'</td>
                <td style="padding: 4px;">'.$user['phone'].'</td>
             </tr>';
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('user_list.pdf', 'I'); // I for inline display, D for download
?>
