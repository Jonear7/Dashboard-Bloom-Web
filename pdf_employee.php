<?php
require_once 'dbconnection.php'; // Include your database connection script
require_once 'tcpdf/tcpdf.php'; // Include TCPDF library

// Create new TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Employee List');
$pdf->SetSubject('Employee Data');
$pdf->SetKeywords('TCPDF, PDF, employee, data');

// Set default header data
$pdf->SetHeaderData('', 0, 'Employee List', '');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 14);

// Retrieve employees from database
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);

// Check if there are employees
if ($result->num_rows > 0) {
    // Table header
    $html = '<table border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Job Position</th>
                </tr>';

    // Table rows
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['surname'] . '</td>
                    <td>' . $row['age'] . '</td>
                    <td>' . $row['phone'] . '</td>
                    <td>' . $row['address'] . '</td>
                    <td>' . $row['job_position'] . '</td>
                </tr>';
    }

    // Close table
    $html .= '</table>';

    // Print HTML content
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'No employees found.', 0, true, 'C', 0, '', 0, false, 'T', 'M');
}

// Close and output PDF document
$pdf->Output('employee_list.pdf', 'I');

// Close connection
$conn->close();
?>
