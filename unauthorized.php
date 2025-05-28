<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Access Denied</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-500 text-white h-screen flex flex-col justify-center items-center text-center">
  <h1 class="text-4xl font-bold">🚫 Access Denied</h1>
  <p class="mt-4 text-lg">You do not have permission to access this page.</p>
  <p class="mt-4 text-lg">You will be redirected to the Login Page.</p>
  
  <a href="login.php" class="mt-6 bg-white text-red-500 px-6 py-3 rounded-lg hover:bg-gray-200 transition">
    Go Back
  </a>
</body>
</html>
