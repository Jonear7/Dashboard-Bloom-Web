<?php
// Database configuration
require_once 'dbconnection.php';
// Function to retrieve all employees or filtered employees
function getAllEmployees($conn, $search = null) {
    $sql = "SELECT * FROM employees";
    
    // Add search condition if provided
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " WHERE name LIKE '%$search%' OR surname LIKE '%$search%' OR job_position LIKE '%$search%'";
    }
    
    $result = $conn->query($sql);
    $employees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    return $employees;
}

// Function to create a new employee
function createEmployee($conn, $name, $surname, $age, $phone, $address, $job_position, $image_path) {
    $name = $conn->real_escape_string($name);
    $surname = $conn->real_escape_string($surname);
    $age = $conn->real_escape_string($age);
    $phone = $conn->real_escape_string($phone);
    $address = $conn->real_escape_string($address);
    $job_position = $conn->real_escape_string($job_position);
    $image_path = $conn->real_escape_string($image_path);

    $sql = "INSERT INTO employees (name, surname, age, phone, address, job_position, image_path) 
            VALUES ('$name', '$surname', '$age', '$phone', '$address', '$job_position', '$image_path')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        return false;
    }
}

// Function to retrieve employee by ID
function getEmployeeById($conn, $id) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM employees WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to update an employee
function updateEmployee($conn, $id, $name, $surname, $age, $phone, $address, $job_position, $image_path) {
    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);
    $surname = $conn->real_escape_string($surname);
    $age = $conn->real_escape_string($age);
    $phone = $conn->real_escape_string($phone);
    $address = $conn->real_escape_string($address);
    $job_position = $conn->real_escape_string($job_position);

    // Check if new image is uploaded
    $image_update_sql = "";
    if ($image_path) {
        $image_path = $conn->real_escape_string($image_path);
        $image_update_sql = ", image_path='$image_path'";
    }
    
    $sql = "UPDATE employees SET name='$name', surname='$surname', age='$age', phone='$phone', 
            address='$address', job_position='$job_position' $image_update_sql WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error updating record: " . $conn->error;
        return false;
    }
}

// Function to delete an employee
function deleteEmployee($conn, $id) {
    $id = $conn->real_escape_string($id);
    $sql = "DELETE FROM employees WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error deleting record: " . $conn->error;
        return false;
    }
}

// Handle file upload and form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_employee"])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $age = $_POST['age'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $job_position = $_POST['job_position'];
        
        // Handle file upload and get image path
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_path = 'uploads/' . $image_name; // Example path, adjust as necessary

            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Call createEmployee function to insert into database
                if (createEmployee($conn, $name, $surname, $age, $phone, $address, $job_position, $image_path)) {
                    echo "<p class='text-green-500'>Employee added successfully.</p>";
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                } else {
                    echo "<p class='text-red-500'>Failed to add employee.</p>";
                }
            } else {
                echo "<p class='text-red-500'>Failed to move uploaded file.</p>";
            }
        } else {
            echo "<p class='text-red-500'>Error uploading file.</p>";
        }
    } elseif (isset($_POST["edit_employee"])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $age = $_POST['age'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $job_position = $_POST['job_position'];
        
        // Handle file upload and get image path
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_path = 'uploads/' . $image_name; // Example path, adjust as necessary

            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Call updateEmployee function to update database
                if (updateEmployee($conn, $id, $name, $surname, $age, $phone, $address, $job_position, $image_path)) {
                    echo "<p class='text-green-500'>Employee updated successfully.</p>";
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                } else {
                    echo "<p class='text-red-500'>Failed to update employee.</p>";
                }
            } else {
                echo "<p class='text-red-500'>Failed to move uploaded file.</p>";
            }
        } else {
            // If no new image uploaded, update without changing image
            if (updateEmployee($conn, $id, $name, $surname, $age, $phone, $address, $job_position, null)) {
                echo "<p class='text-green-500'>Employee updated successfully.</p>";
            } else {
                echo "<p class='text-red-500'>Failed to update employee.</p>";
            }
        }  header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}

// Handle delete action from URL query string
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Call deleteEmployee function to delete from database
    if (deleteEmployee($conn, $id)) {
        echo "<p class='text-green-500'>Employee deleted successfully.</p>";
    } else {
        echo "<p class='text-red-500'>Failed to delete employee.</p>";
    }  header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// HTML output starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-purple-800">
    <?php include 'bar.php'; ?>
    <div class="mx-auto">
        <div class="container mx-auto py-8">
            <h1 class="text-2xl font-bold mb-5 text-white text-center">Employee Management</h1>

            <!-- Search Form -->
            <div class="flex mb-4">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mr-2">
                    <input type="text" name="search" placeholder="Search..." class="bg-gray-200 text-black font-bold py-2 px-4 rounded-full">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Search</button>
                </form>
                <button onclick="openAddModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Add Employee</button>
            </div>

            <!-- Employee Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                    <tr class="bg-gray-200 text-black">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Surname</th>
                            <th class="border px-4 py-2">Age</th>
                            <th class="border px-4 py-2">Phone</th>
                            <th class="border px-4 py-2">Address</th>
                            <th class="border px-4 py-2">Job Position</th>
                            <th class="border px-4 py-2">Image</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        <?php
                        $search = isset($_GET['search']) ? $_GET['search'] : null;
                        $employees = getAllEmployees($conn, $search);
                        foreach ($employees as $employee) {
                            echo "<tr>";
                            echo "<td class='border px-4 py-2'>{$employee['id']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['name']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['surname']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['age']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['phone']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['address']}</td>";
                            echo "<td class='border px-4 py-2'>{$employee['job_position']}</td>";
                            echo "<td class='border px-4 py-2'><img src='{$employee['image_path']}' alt='Image' width='50'></td>";
                            echo "<td class='border px-4 py-2'>
                                    <a href='#' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline' onclick='openEditModal(" . htmlspecialchars(json_encode($employee)) . ")'>Edit</a> 
                                    <a href='?action=delete&id={$employee['id']}' class='bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h2 class="text-2xl font-bold mb-5">Add Employee</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_employee" value="1">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="surname" class="block text-gray-700 text-sm font-bold mb-2">Surname</label>
                    <input type="text" name="surname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="age" class="block text-gray-700 text-sm font-bold mb-2">Age</label>
                    <input type="number" name="age" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="text" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <input type="text" name="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="job_position" class="block text-gray-700 text-sm font-bold mb-2">Job Position</label>
                    <input type="text" name="job_position" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                    <input type="file" name="image" id="add_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="previewImage(event)" required>
                    <img id="add_image_preview" class="mt-2 hidden" width="150" />
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" onclick="closeAddModal()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h2 class="text-2xl font-bold mb-5">Edit Employee</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_employee" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="edit_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_surname" class="block text-gray-700 text-sm font-bold mb-2">Surname</label>
                    <input type="text" name="surname" id="edit_surname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_age" class="block text-gray-700 text-sm font-bold mb-2">Age</label>
                    <input type="number" name="age" id="edit_age" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="text" name="phone" id="edit_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <input type="text" name="address" id="edit_address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_job_position" class="block text-gray-700 text-sm font-bold mb-2">Job Position</label>
                    <input type="text" name="job_position" id="edit_job_position" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="edit_image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                    <input type="file" name="image" id="edit_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="previewEditImage(event)">
                    <img id="edit_image_preview" class="mt-2 hidden" width="150" />
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" onclick="closeEditModal()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Modals -->
    <script>
        
        // Function to open the add employee modal
        function openAddModal() {
            document.getElementById('addEmployeeModal').style.display = 'block';
        }

        // Function to close the add employee modal
        function closeAddModal() {
            document.getElementById('addEmployeeModal').style.display = 'none';
        }

        // Function to open the edit employee modal and populate with employee data
        function openEditModal(employee) {
            document.getElementById('editEmployeeModal').style.display = 'block';
            document.getElementById('edit_id').value = employee.id;
            document.getElementById('edit_name').value = employee.name;
            document.getElementById('edit_surname').value = employee.surname;
            document.getElementById('edit_age').value = employee.age;
            document.getElementById('edit_phone').value = employee.phone;
            document.getElementById('edit_address').value = employee.address;
            document.getElementById('edit_job_position').value = employee.job_position;

            // Display image preview if exists
            const img = document.getElementById('edit_image_preview');
            if (employee.image_path) {
                img.src = employee.image_path;
                img.classList.remove('hidden');
            } else {
                img.src = '';
                img.classList.add('hidden');
            }
        }

        // Function to close the edit employee modal
        function closeEditModal() {
            document.getElementById('editEmployeeModal').style.display = 'none';
        }

        // Function to preview image in add employee modal
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('add_image_preview');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to preview image in edit employee modal
        function previewEditImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('edit_image_preview');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
    </script>

</body>
</html>

