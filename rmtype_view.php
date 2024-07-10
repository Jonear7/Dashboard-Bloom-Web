<?php
require 'dbconnection.php';
require 'tcpdf/tcpdf.php';

// Fetch room type data
$conn = new mysqli("localhost", "root", "", "bloom");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_pdf'])) {
    // Handle PDF generation
    $result = $conn->query("SELECT rmtype.*, room.room_number FROM rmtype INNER JOIN room ON rmtype.rmtype_id = room.rmtype_id");

    // Create a new TCPDF object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Room Type Information');
    $pdf->SetSubject('Room Type List');
    $pdf->SetKeywords('Rooms, Room Types');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Content
    $html = '<h1 style="text-align: center; margin-bottom: 10px;">Room Type Information</h1>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f0f0f0; text-align: center;">
                        <th style="width: 20%; padding: 4px;">ID</th>
                        <th style="width: 20%; padding: 4px;">Name</th>
                        <th style="width: 20%; padding: 4px;">Description</th>
                        <th style="width: 20%; padding: 4px;">Price</th>
                        <th style="width: 20%; padding: 4px;">Room Number</th>
                    </tr>
                </thead>
                <tbody>';

    // Add data rows
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr style="text-align: center;">
                    <td style="padding: 4px;">'.$i++.'</td>
                    <td style="padding: 4px;">'.$row['type_name'].'</td>
                    <td style="padding: 4px;">'.$row['description'].'</td>
                    <td style="padding: 4px;">'.$row['price'].'</td>
                    <td style="padding: 4px;">'.$row['room_number'].'</td>
                 </tr>';
    }

    $html .= '</tbody></table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('room_type_information.pdf', 'I'); // I for inline display, D for download
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room Type Information</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Include Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Include Fancybox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include Fancybox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
</head>
<body class="bg-gradient-to-br from-purple-700 to-indigo-900 text-white font-sans">
<?php include 'bar.php'; ?>
<h1 class="text-3xl text-center py-8">Room Type Information</h1>

<div class="overflow-x-auto mx-4">
  <form method="post" action="">
    <div class="flex justify-between items-center mb-4">
      <button type="submit" name="generate_pdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Print PDF</button>
      <a href="upload.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Room</a>
    </div>
  </form>
  <table class="w-full table-auto">
    <thead>
      <tr class="bg-gray-200 text-black">
        <th class="px-4 py-2">ID</th>
        <th class="px-4 py-2">Name</th>
        <th class="px-4 py-2">Description</th>
        <th class="px-4 py-2">Price</th>
        <th class="px-4 py-2">Room Number</th>
        <th class="px-4 py-2">Image</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 1;
      $result = $conn->query("SELECT rmtype.*, room.room_number FROM rmtype INNER JOIN room ON rmtype.rmtype_id = room.rmtype_id");

      if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['generate_pdf'])) {
        $rmtypeId = $_POST['rmtypeId'];
        $price = $_POST['price'];

        $sql = "UPDATE rmtype SET price = ? WHERE rmtype_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $price, $rmtypeId);

        if ($stmt->execute()) {
          echo "<div class='alert alert-success'> Room Type ID $rmtypeId updated successfully!</div>";
        } else {
          echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
      }

      while ($row = $result->fetch_assoc()) : ?>
      <tr>
        <td class="border px-4 py-2"><?php echo $i++; ?></td>
        <td class="border px-4 py-2"><?php echo $row["type_name"]; ?></td>
        <td class="border px-4 py-2"><?php echo $row["description"]; ?></td>
        <td class="border px-4 py-2"><?php echo $row["price"]; ?></td>
        <td class="border px-4 py-2"><?php echo $row["room_number"]; ?></td>
        <td class="border px-4 py-2">
          <?php foreach (json_decode($row["image"]) as $image) : ?>
            <a href="uploads/<?php echo $image; ?>" data-fancybox="gallery-<?php echo $row['rmtype_id']; ?>">
              <img class="popup-image w-24 h-24 object-cover rounded" src="uploads/<?php echo $image; ?>" alt="<?php echo $row["type_name"]; ?>">
            </a>
          <?php endforeach; ?>
        </td>
        <td class="border px-4 py-2">
          <button type="button" class="btn btn-primary update-btn" data-bs-toggle="modal" data-bs-target="#updateModal" data-rmtype-id="<?php echo $row['rmtype_id']; ?>" data-rmtype-price="<?php echo $row['price']; ?>">
            Update
          </button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">Update Room Type Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" method="post" action="">
          <input type="hidden" id="rmtypeId" name="rmtypeId">
          <div class="mb-3">
            <label for="rmtypeIdDisplay" class="form-label">Room Type ID</label>
            <input type="text" class="form-control" id="rmtypeIdDisplay" name="rmtypeIdDisplay" readonly>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" class="form-control" id="price" name="price">
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.update-btn').on('click', function() {
    var rmtypeId = $(this).data('rmtype-id');
    var rmtypePrice = $(this).data('rmtype-price');

    $('#rmtypeId').val(rmtypeId);
    $('#rmtypeIdDisplay').val(rmtypeId);  // Display the room type ID in the modal
    $('#price').val(rmtypePrice);
  });

  $('[data-fancybox]').fancybox({
    buttons: [
      "slideShow",
      "thumbs",
      "zoom",
      "fullScreen",
      "close"
    ]
  });

  // Function to hide the alert message after 3 seconds
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  }, 3000);
});
</script>

</body>
</html>
