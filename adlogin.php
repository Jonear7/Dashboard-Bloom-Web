<?php
session_start();

// Redirect logged-in admins to dashboard
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'dbconnection.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate username and password
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        // Retrieve admin data from the database
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                // Correct password, set session variables including admin_id
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['username'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "Admin not found";
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .error-message {
            color: white;
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-purple-600">
    <div class="bg-pink-900 p-8 rounded-lg shadow-md max-w-md w-full">
        <img class="object-cover rounded-full w-32 h-32 mx-auto mb-4" src="images/bloom.jpg" alt="Description">
        <h2 class="text-2xl font-semibold mb-4 text-white text-center">Admin Login</h2>
        <form method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-200">Username:</label>
                <input type="text" id="username" name="username" required
                    class="pl-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-200">Password:</label>
                <input type="password" id="password" name="password" required
                    class="pl-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <?php if(isset($error) && !empty($error)): ?>
                <p class="error-message text-sm text-center"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="mt-4">
                <button type="submit"
                    class="w-full bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
