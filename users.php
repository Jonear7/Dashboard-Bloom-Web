<?php
// Include database connection file
require_once 'dbconnection.php';

// Function to insert a new user
function createUser($username, $password, $email, $phone) {
    global $conn;
    
    // Check if the email already exists
    $query_check = "SELECT * FROM users WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        // Email already exists, return false
        mysqli_stmt_close($stmt_check);
        return false;
    }
    
    // Proceed with user creation
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query_insert = "INSERT INTO users (username, password, email, phone) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $hashed_password, $email, $phone);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        mysqli_stmt_close($stmt_insert);
        return true; // User created successfully
    } else {
        mysqli_stmt_close($stmt_insert);
        return false; // Error creating user
    }
}

// Function to fetch all users
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $users;
}

// Function to fetch a user by user_id
function getUserById($user_id) {
    global $conn;
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

// Function to update a user by user_id
function updateUser($user_id, $username, $email, $phone) {
    global $conn;
    $query = "UPDATE users SET username = ?, email = ?, phone = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $phone, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
    mysqli_stmt_close($stmt);
}

// Function to delete a user by user_id
function deleteUser($user_id) {
    global $conn;
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
    mysqli_stmt_close($stmt);
}

// Handle POST requests for CRUD operations
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Add or Edit user
        if ($action == "add" || $action == "edit") {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            
            if ($action == "add") {
                if (createUser($username, $password, $email, $phone)) {
                    $message = "User created successfully!";
                } else {
                    $message = "Error creating user. Email already exists.";
                }
            } elseif ($action == "edit") {
                $user_id = $_POST['user_id'];
                if (updateUser($user_id, $username, $email, $phone)) {
                    $message = "User updated successfully!";
                } else {
                    $message = "Error updating user.";
                }
            }
        }
        
        // Delete user
        elseif ($action == "delete") {
            $user_id = $_POST['user_id'];
            if (deleteUser($user_id)) {
                $message = "User deleted successfully!";
            } else {
                $message = "Error deleting user.";
            }
        }
    }
}

// Fetch all users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles for message */
        .message {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 5px;
            z-index: 9999;
            display: none; /* Initially hidden */
            animation: fadeOut 3s forwards; /* Animation for fade out */
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
    </style>
</head>

<body class="bg-purple-800 text-white">

    <!-- Navigation Bar -->
    <?php include 'bar.php'; ?>

    <div class="container mx-auto p-8">
        

        <h1 class="text-3xl font-bold mb-8 text-center">Users</h1>
        <div class="flex justify-start mb-4"> <!-- Moved Add User button to left side -->
            <button id="addUserBtn"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Add
                User</button>
        </div>
        <!-- Modal for Add/Edit User -->
        <div id="userModal"
            class="fixed top-0 left-0 w-full h-full bg-gray-800 bg-opacity-50 hidden flex items-center justify-center">
            <div class="bg-white p-8 rounded-full shadow-lg w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                <h2 id="modalTitle" class="text-2xl font-bold mb-6 text-center text-black">Add User</h2>
                <form id="userForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                    class="w-full max-w-lg">
                    <input type="hidden" id="action" name="action" value="add">
                    <input type="hidden" id="user_id" name="user_id" value="">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6">
                            <label for="username"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Username</label>
                            <input type="text" id="username" name="username"
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded-full py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                        </div>
                        <div class="w-full px-3 mb-6">
                            <label for="password"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Password</label>
                            <input type="password" id="password" name="password"
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded-full py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                        </div>
                        <div class="w-full px-3 mb-6">
                            <label for="email"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Email</label>
                            <input type="email" id="email" name="email"
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded-full py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                        </div>
                        <div class="w-full px-3 mb-6">
                            <label for="phone"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Phone</label>
                            <input type="text" id="phone" name="phone"
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded-full py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                        </div>
                        <div class="w-full px-3 flex justify-end">
                            <button id="submitBtn" type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Add
                                User</button>
                            <button id="cancelBtn" type="button"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 ml-2 rounded-full focus:outline-none focus:shadow-outline">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Users List</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full text-center">
                    <thead>
                        <tr class="bg-gray-200 text-black">
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Phone</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $user['user_id']; ?></td>
                            <td class="border px-4 py-2"><?php echo $user['username']; ?></td>
                            <td class="border px-4 py-2"><?php echo $user['email']; ?></td>
                            <td class="border px-4 py-2"><?php echo $user['phone']; ?></td>
                            <td class="border px-4 py-2">
                                <button
                                    class="editBtn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline"
                                    data-userid="<?php echo $user['user_id']; ?>">Edit</button>
                                <form class="inline-block"
                                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id"
                                        value="<?php echo $user['user_id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Message Display -->
        <div id="message" class="message">
            <?php echo $message; ?>
        </div>
    </div>

    <!-- Script for Modal Handling and Message Fade -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('userModal');
            const modalTitle = document.getElementById('modalTitle');
            const userForm = document.getElementById('userForm');
            const addUserBtn = document.getElementById('addUserBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const submitBtn = document.getElementById('submitBtn');
            const actionInput = document.getElementById('action');
            const userIdInput = document.getElementById('user_id');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const messageElement = document.getElementById('message');

            // Show modal for adding a user
            addUserBtn.addEventListener('click', function () {
                modalTitle.textContent = 'Add User';
                actionInput.value = 'add';
                userIdInput.value = '';
                usernameInput.value = '';
                passwordInput.value = '';
                emailInput.value = '';
                phoneInput.value = '';
                modal.classList.remove('hidden');
            });

            // Show modal for editing a user
            document.querySelectorAll('.editBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = button.getAttribute('data-userid');
                    modalTitle.textContent = 'Edit User';
                    actionInput.value = 'edit';
                    userIdInput.value = userId;

                    // Fetch user details via AJAX or set from existing table data
                    const user = <?php echo json_encode($users); ?>;
                    const selectedUser = user.find(u => u.user_id == userId);
                    if (selectedUser) {
                        usernameInput.value = selectedUser.username;
                        // passwordInput.value = ''; // You may decide whether to show password field for editing
                        emailInput.value = selectedUser.email;
                        phoneInput.value = selectedUser.phone;
                    }

                    modal.classList.remove('hidden');
                });
            });

            // Cancel button in modal
            cancelBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            // Form submission handling
            userForm.addEventListener('submit', function (event) {
                // Client-side validation if needed
                if (emailInput.value.trim() === '') {
                    alert('Email cannot be empty.');
                    event.preventDefault();
                    return;
                }
                // You can add more validation as needed

                // Message fade animation
                if (messageElement.textContent.trim() !== '') {
                    messageElement.classList.remove('hidden');
                    setTimeout(function () {
                        messageElement.classList.add('hidden');
                    }, 3000); // 3000 milliseconds = 3 seconds
                }
            });

            // Message fade animation for server-side message
            if (messageElement.textContent.trim() !== '') {
                messageElement.classList.remove('hidden');
                setTimeout(function () {
                    messageElement.classList.add('hidden');
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        });
    </script>
</body>

</html>

<?php
// Close database connection
mysqli_close($conn);
?>

  
