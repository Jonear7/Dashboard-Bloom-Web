<?php
require 'dbconnection.php';

if(isset($_POST["submit"])){
    // Retrieve form data
    $type_name = $_POST['type_name']; // Change 'name' to 'type_name'
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Process image files
    $totalFiles = count($_FILES['fileImg']['name']);
    $filesArray = array();

    for($i = 0; $i < $totalFiles; $i++){
        $imageName = $_FILES["fileImg"]["name"][$i];
        $tmpName = $_FILES["fileImg"]["tmp_name"][$i];

        $imageExtension = explode('.', $imageName);
        $imageExtension = strtolower(end($imageExtension));

        $newImageName = uniqid() . '.' . $imageExtension;

        move_uploaded_file($tmpName, 'uploads/' . $newImageName);
        $filesArray[] = $newImageName;
    }

    // Encode image file names as JSON
    $filesArray = json_encode($filesArray);

    // Insert data into the rmtype table
    $query1 = "INSERT INTO rmtype (type_name, description, price, image) VALUES ('$type_name', '$description', '$price', '$filesArray')";
    mysqli_query($conn, $query1);

    // Retrieve the last inserted rmtype_id
    $rmtype_id = mysqli_insert_id($conn);

    // Insert data into the room table with default status 'available'
    $query2 = "INSERT INTO room (rmtype_id, status) VALUES ('$rmtype_id', 'available')";
    mysqli_query($conn, $query2);

    // Redirect to rmtype_view.php after successful insertion
    echo "<script>alert('Successfully Added'); document.location.href = 'rmtype_view.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room Type</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-gray-800 text-white py-4">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">Add a New Room Type</h1>
        <a href="rmtype_view.php" class="text-white hover:underline">View All Room Types</a>
    </div>
</header>
<div class="container mx-auto mt-10 px-6">
    <div class="bg-white p-8 rounded shadow-md">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="type_name" class="block text-gray-700">Type Name:</label>
                <input type="text" id="type_name" name="type_name" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description:</label>
                <input type="text" id="description" name="description" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="fileImg" class="block text-gray-700">Image:</label>
                <input type="file" id="fileImg" name="fileImg[]" accept=".jpg, .jpeg, .png" class="w-full px-3 py-2 border rounded" required multiple>
            </div>
            <button type="submit" name="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
        </form>
    </div>
</div>
</body>
</html>
