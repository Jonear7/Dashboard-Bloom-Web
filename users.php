<?php
// Include database connection file
require_once 'dbconnection.php';

// Retrieve users data from the database
$query_users = "SELECT * FROM users";
$result_users = mysqli_query($conn, $query_users);
$users = mysqli_fetch_all($result_users, MYSQLI_ASSOC);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Dashboard</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-purple-800 text-white">
    <!-- Include the sidebar -->
    <?php include 'bar.php'; ?>
    
    <!-- Main content -->
    <div class="flex-1 p-8">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center">Users</h1>
            <!-- Display users table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-black">
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $user['user_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $user['username']; ?></td>
                                <td class="border px-4 py-2"><?php echo $user['email']; ?></td>
                                <td class="border px-4 py-2"><?php echo $user['phone']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
