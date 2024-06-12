<?php
// Include database connection file
require_once 'dbconnection.php';

// Initialize variables
$username = $password = $confirm_password = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $username = filter_input(INPUT_POST, 'username', );
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin into the database
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                // Registration successful, redirect to login page
                header("Location: adlogin.php");
                exit();
            } else {
                $error = "Error occurred while registering";
            }
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
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .error-message {
            color: #e53e3e;
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-purple-600">
    <div class="bg-pink-900 p-8 rounded-lg shadow-md max-w-md w-full">
        <h2 class="text-2xl font-semibold mb-4 text-white text-center">Admin Registration</h2>
        <form method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-200">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required
                    class="pl-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-200">Password:</label>
                <input type="password" id="password" name="password" required
                    class="pl-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-200">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="pl-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <?php if(!empty($error)): ?>
                <p class="error-message text-sm text-center"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="mt-4">
                <button type="submit"
                    class="w-full bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
