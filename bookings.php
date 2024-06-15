<?php
// Include database connection file
require_once 'dbconnection.php';

// Function to sanitize user input
function sanitize_input($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
}

// Initialize variables
$search = '';
$success_message = '';
$error_message = '';

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = sanitize_input($conn, $_GET['search']);
}

// Delete Operation and Check-in/Check-out
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_delete"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);
        $query = "DELETE FROM booking WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Booking deleted successfully.";
        } else {
            $error_message = "Error deleting booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    if (isset($_POST["submit_checkin"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);
        $query = "UPDATE booking SET status = 'Checked-in' WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Booking checked-in successfully.";
        } else {
            $error_message = "Error checking in booking: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    if (isset($_POST["submit_checkout"])) {
        $booking_id = sanitize_input($conn, $_POST['booking_id']);
        $checkout_date = date('Y-m-d');

        // Retrieve booking details
        $query_select = "SELECT booking_id, checkin_date, total_price, room.room_number FROM booking 
                        JOIN room ON booking.room_id = room.room_id 
                        WHERE booking.booking_id = ?";
        $stmt_select = mysqli_prepare($conn, $query_select);
        mysqli_stmt_bind_param($stmt_select, 'i', $booking_id);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        $booking_details = mysqli_fetch_assoc($result_select);
        mysqli_stmt_close($stmt_select);

        // Insert into checkin_out table
        $query_insert = "INSERT INTO checkin_out (booking_id, checkin_date, checkout_date, total_price, room_number, status) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);

        // Check if preparation of the statement was successful
        if ($stmt_insert === false) {
            $error_message .= "Error preparing insert statement: " . mysqli_error($conn);
        } else {
            // Bind parameters
            mysqli_stmt_bind_param($stmt_insert, 'issdss', $booking_id, $booking_details['checkin_date'], $checkout_date, $booking_details['total_price'], $booking_details['room_number'], $status);

            // Set status
            $status = 'Checked-out';

            // Execute statement
            if (mysqli_stmt_execute($stmt_insert)) {
                $success_message .= " Booking checked-out successfully.";
            } else {
                $error_message .= " Error checking out booking: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt_insert);

        // Delete from booking table
        $query_delete = "DELETE FROM booking WHERE booking_id = ?";
        $stmt_delete = mysqli_prepare($conn, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, 'i', $booking_id);
        if (mysqli_stmt_execute($stmt_delete)) {
            $success_message .= " Booking record deleted.";
        } else {
            $error_message .= " Error deleting booking record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    }


    if (isset($_POST['submit_add'])) {
        $user_id = sanitize_input($conn, $_POST['user_id']);
        $room_id = sanitize_input($conn, $_POST['room_id']);
        $checkin_date = sanitize_input($conn, $_POST['checkin_date']);
        $checkout_date = sanitize_input($conn, $_POST['checkout_date']);

        // Check room availability
        $query_check_availability = "SELECT COUNT(*) AS count FROM booking 
                                     WHERE room_id = ? 
                                     AND ((checkin_date BETWEEN ? AND ?) OR (checkout_date BETWEEN ? AND ?)) 
                                     AND status != 'Checked-out'";
        $stmt_check_availability = mysqli_prepare($conn, $query_check_availability);
        mysqli_stmt_bind_param($stmt_check_availability, 'issss', $room_id, $checkin_date, $checkout_date, $checkin_date, $checkout_date);
        mysqli_stmt_execute($stmt_check_availability);
        $result_check_availability = mysqli_stmt_get_result($stmt_check_availability);
        $availability = mysqli_fetch_assoc($result_check_availability);
        mysqli_stmt_close($stmt_check_availability);

        session_start(); // Start the session if not already started

        if ($availability['count'] > 0) {
            $_SESSION['error_message'] = "The selected room is not available for the specified dates.";
        } else {
            // Calculate total price based on room type price and duration of stay
            $query_room_price = "SELECT rmtype.price FROM room 
                                 JOIN rmtype ON room.rmtype_id = rmtype.rmtype_id 
                                 WHERE room.room_id = ?";
            $stmt_room_price = mysqli_prepare($conn, $query_room_price);
            mysqli_stmt_bind_param($stmt_room_price, 'i', $room_id);
            mysqli_stmt_execute($stmt_room_price);
            $result_room_price = mysqli_stmt_get_result($stmt_room_price);
            $room_price_row = mysqli_fetch_assoc($result_room_price);
            $room_price = $room_price_row['price'];
            mysqli_stmt_close($stmt_room_price);

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime($checkout_date);
            $interval = $checkin->diff($checkout);
            $total_days = $interval->days;
            $total_price = $total_days * $room_price;

            // Clear any previous error message from session if present
            unset($_SESSION['error_message']);

            // Redirect back to the booking form or wherever you want to display the message

            // Insert booking details
            $query_insert_booking = "INSERT INTO booking (user_id, room_id, checkin_date, checkout_date, total_price, status) 
VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert_booking = mysqli_prepare($conn, $query_insert_booking);

            $user_id = sanitize_input($conn, $_POST['user_id']);
            $room_id = sanitize_input($conn, $_POST['room_id']);
            $checkin_date = sanitize_input($conn, $_POST['checkin_date']);
            $checkout_date = sanitize_input($conn, $_POST['checkout_date']);
            $status = 'Booked'; // Assign 'Booked' to a variable

            mysqli_stmt_bind_param($stmt_insert_booking, 'iissds', $user_id, $room_id, $checkin_date, $checkout_date, $total_price, $status);

            if (mysqli_stmt_execute($stmt_insert_booking)) {
                $success_message = "Booking added successfully.";
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
                // Insert into payment_walk_in table
                $payment_date = date('Y-m-d'); // Assuming payment date is current date
                $payment_total = $total_price; // Assuming payment total is the same as total price

                $query_insert_payment = "INSERT INTO payment_walk_in (payment_date, payment_total) 
    VALUES (?, ?)";
                $stmt_insert_payment = mysqli_prepare($conn, $query_insert_payment);
                mysqli_stmt_bind_param($stmt_insert_payment, 'sd', $payment_date, $payment_total);

                if (mysqli_stmt_execute($stmt_insert_payment)) {
                    $success_message .= " Payment record added successfully.";
                } else {
                    $error_message .= " Error adding payment record: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt_insert_payment);
            } else {
                $error_message = "Error adding booking: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt_insert_booking);
        }
    }
}

// Fetch bookings query
$query = "SELECT booking.booking_id, booking.checkin_date, booking.checkout_date, booking.total_price, booking.room_id, booking.status, users.username, room.room_number AS room_number, rmtype.type_name
          FROM booking 
          JOIN users ON booking.user_id = users.user_id 
          JOIN room ON booking.room_id = room.room_id 
          JOIN rmtype ON room.rmtype_id = rmtype.rmtype_id 
          WHERE 1=1";

if ($search) {
    $query .= " AND (users.username LIKE '%$search%' OR room.room_number LIKE '%$search%' OR booking.booking_id LIKE '%$search%')";
}

$result = mysqli_query($conn, $query);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch rooms query (excluding booked rooms)
$query_rooms = "SELECT room.room_id, room.room_number, rmtype.type_name, rmtype.price FROM room 
                JOIN rmtype ON room.rmtype_id = rmtype.rmtype_id 
                WHERE room.status = 'Available' 
                AND room.room_id NOT IN (
                    SELECT DISTINCT booking.room_id FROM booking 
                    WHERE (booking.checkin_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 YEAR)) 
                    OR (booking.checkout_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 YEAR)) 
                    AND booking.status != 'Checked-out'
                )";
$result_rooms = mysqli_query($conn, $query_rooms);
$rooms = mysqli_fetch_all($result_rooms, MYSQLI_ASSOC);

// Fetch users query
$query_users = "SELECT user_id, username FROM users";
$result_users = mysqli_query($conn, $query_users);
$user_options = '';
while ($row = mysqli_fetch_assoc($result_users)) {
    $user_options .= '<option value="' . $row['user_id'] . '">' . $row['username'] . '</option>';
}

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 1s ease-out;
        }
    </style>
</head>

<body class="bg-purple-800 text-white">
    <?php include 'bar.php'; ?>
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Bookings and Check-ins</h1>
            <?php if (isset($_SESSION['success_message'])) : ?>
                <div id="success-message" class="bg-green-500 text-white p-4 mb-4"><?php echo $_SESSION['success_message'];
                                                                                    unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if ($error_message) : ?>
                <div id="error-message" class="bg-red-500 text-white p-4 mb-4"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="mb-4">
                <div class="flex items-center">
                    <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search..." class="bg-gray-200 text-black font-bold py-2 px-4 rounded">
                    <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded ml-4">Search</button>
                </div>
            </form>

            <!-- Modal Trigger Button -->
            <button id="openModal" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded mb-4">Add Booking</button>

            <!-- Booking List Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full text-left whitespace-no-wrap">
                    <thead>
                        <tr class="bg-white text-black">
                            <th class="py-2 px-4">Booking ID</th>
                            <th class="py-2 px-4">User</th>
                            <th class="py-2 px-4">Room Number</th>
                            <th class="py-2 px-4">Check-in Date</th>
                            <th class="py-2 px-4">Check-out Date</th>
                            <th class="py-2 px-4">Total Price</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $booking['booking_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['username']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['room_number']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkin_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['checkout_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['total_price']; ?></td>
                                <td class="border px-4 py-2"><?php echo $booking['status']; ?></td>
                                <td class="border px-4 py-2">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <?php if ($booking['status'] == 'Booked') : ?>
                                            <button type="submit" name="submit_checkin" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Check-in</button>
                                        <?php elseif ($booking['status'] == 'Checked-in') : ?>
                                            <button type="submit" name="submit_checkout" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Check-out</button>
                                        <?php endif; ?>
                                        <button type="submit" name="submit_delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">Delete</button>

                                        <!-- Add Print Button -->
                                        <a href="generate_invoice.php?booking_id=<?php echo $booking['booking_id']; ?>" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Print Invoice</a>

                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    </div>

    <!-- Add Booking Modal -->
    <div id="addBookingModal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-8 rounded shadow-lg w-1/2 text-black">
            <h2 class="text-2xl font-bold mb-4">Add Booking</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-bold mb-2">User:</label>
                    <select name="user_id" id="user_id" class="bg-gray-200 text-black font-bold py-2 px-4 rounded w-full" required>
                        <?php echo $user_options; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="room_id" class="block text-sm font-bold mb-2">Room:</label>
                    <select name="room_id" id="room_id" class="bg-gray-200 text-black font-bold py-2 px-4 rounded w-full" required onchange="calculateTotalPrice()">
                        <?php foreach ($rooms as $room) : ?>
                            <option value="<?php echo $room['room_id']; ?>" data-price="<?php echo $room['price']; ?>"><?php echo $room['room_number'] . ' (' . $room['type_name'] . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
                <div class="mb-4">
                    <label for="checkin_date" class="block text-sm font-bold mb-2">Check-in Date:</label>
                    <input type="date" name="checkin_date" id="checkin_date" class="bg-gray-200 text-black font-bold py-2 px-4 rounded w-full" required onchange="calculateTotalPrice()">
                </div>
                <div class="mb-4">
                    <label for="checkout_date" class="block text-sm font-bold mb-2">Check-out Date:</label>
                    <input type="date" name="checkout_date" id="checkout_date" class="bg-gray-200 text-black font-bold py-2 px-4 rounded w-full" required onchange="calculateTotalPrice()">
                </div>
                <div class="mb-4">
                    <label for="total_price" class="block text-sm font-bold mb-2">Total Price:</label>
                    <input type="text" name="total_price" id="total_price" class="bg-gray-200 text-black font-bold py-2 px-4 rounded w-full" readonly>
                </div>
                <button type="submit" name="submit_add" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">Add Booking</button>
            </form>
            <button id="closeModal" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mt-4">Close</button>
        </div>
    </div>

    <script>
        function calculateTotalPrice() {
            const roomSelect = document.getElementById('room_id');
            const checkinDate = document.getElementById('checkin_date').value;
            const checkoutDate = document.getElementById('checkout_date').value;
            const totalPriceInput = document.getElementById('total_price');

            if (!checkinDate || !checkoutDate || roomSelect.selectedIndex === -1) {
                totalPriceInput.value = '';
                return;
            }

            const roomPrice = parseFloat(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price'));
            const checkin = new Date(checkinDate);
            const checkout = new Date(checkoutDate);

            if (isNaN(roomPrice) || checkin >= checkout) {
                totalPriceInput.value = '';
                return;
            }

            const duration = (checkout - checkin) / (1000 * 3600 * 24);
            const totalPrice = roomPrice * duration;
            totalPriceInput.value = totalPrice.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');

            if (successMessage || errorMessage) {
                setTimeout(function() {
                    if (successMessage) successMessage.classList.add('fade-out');
                    if (errorMessage) errorMessage.classList.add('fade-out');
                }, 3000);
            }
        });

        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const addBookingModal = document.getElementById('addBookingModal');

        if (openModal && closeModal && addBookingModal) {
            openModal.addEventListener('click', () => addBookingModal.classList.remove('hidden'));
            closeModal.addEventListener('click', () => addBookingModal.classList.add('hidden'));
        }

        const roomSelect = document.getElementById('room_id');
        const checkinDateInput = document.getElementById('checkin_date');
        const checkoutDateInput = document.getElementById('checkout_date');

        if (roomSelect) roomSelect.addEventListener('change', calculateTotalPrice);
        if (checkinDateInput) checkinDateInput.addEventListener('change', calculateTotalPrice);
        if (checkoutDateInput) checkoutDateInput.addEventListener('change', calculateTotalPrice);
    </script>

</body>

</html>