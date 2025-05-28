<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "admin"; // Change this to "staff" or "manager" accordingly

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: unauthorized.php"); // Redirect unauthorized users
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Item</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-200">
    <?php include "admin_nav.php"; ?> 

    <div class="w-96 bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-center">Add Item</h2>

        <form action="process_add_item.php" method="POST">
            <label class="block mb-2">Item Name</label>
            <input type="text" name="item_name" class="w-full p-2 border rounded mb-3" required>

            <label class="block mb-2">Quantity</label>
            <input type="number" name="quantity" class="w-full p-2 border rounded mb-3" required>

            <label class="block mb-2">Description</label>
            <textarea name="description" class="w-full p-2 border rounded mb-3"></textarea>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">Add Item</button>
        </form>
    </div>
</body>
</html>
