<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Set default role if not defined
$role = $_SESSION["role"] ?? "user"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <!-- Display Image Banner -->
    <div class="max-w-6xl mx-auto">
        <img src="images/alden.jpeg" alt="Home Banner" class="w-full rounded-lg shadow-md">
    </div>

    <!-- Navigation -->
    <?php
    if ($role === "admin") {
        include 'admin_nav.php';
    } else {
        echo '<p class="text-gray-600 mt-6 text-center">Welcome, user! You do not have admin access.</p>';
    }
    ?>
    
</body>
</html>


