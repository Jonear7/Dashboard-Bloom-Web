<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Include Font Awesome script -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Styling for dropdown */
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #1a202c; /* Dark gray background */
            border-radius: 0.25rem;
            z-index: 10; /* Ensure dropdown appears above other content */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* Style for dropdown links */
        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #cbd5e0; /* Light gray text */
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #2d3748; /* Darken background on hover */
            color: #ffffff; /* White text on hover */
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <h1 class="text-white text-lg font-bold">Admin Dashboard</h1>
                </div>
                <!-- Navigation Links -->
                <div class="flex items-center justify-center">
                    <a href="dashboard.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <a href="employee.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Employees</a>
                    <a href="users.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Users</a>
                    <a href="bookings.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Bookings</a>
                    <a href="checkout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">CheckOuts</a>
                    <!-- Dropdown for Payments -->
                    <div class="dropdown relative">
                        <button class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium focus:outline-none">
                            Payments <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <div class="dropdown-menu hidden">
                            <a href="payments_online.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm">Online Payments</a>
                            <a href="payments_walkin.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm">Walk-in Payments</a>
                        </div>
                    </div>
                    <!-- End Dropdown for Payments -->
                    <a href="room_view.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Rooms</a>
                    <!-- Logout Link with icon -->
                    <a href="logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="p-8">
        <!-- Content from other pages will be loaded here using PHP includes or another method -->
    </div>

    <!-- Include JavaScript for dropdown functionality -->
    <script>
        // JavaScript for dropdown menu
        document.addEventListener('DOMContentLoaded', function() {
            var dropdowns = document.querySelectorAll('.dropdown');

            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('mouseenter', function() {
                    this.querySelector('.dropdown-menu').classList.remove('hidden');
                });

                dropdown.addEventListener('mouseleave', function() {
                    this.querySelector('.dropdown-menu').classList.add('hidden');
                });
            });
        });
    </script>
</body>

</html>
