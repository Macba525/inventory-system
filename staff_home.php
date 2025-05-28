<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "staff"; // Change this to "staff" or "manager" accordingly

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 pt-24">
  <?php include "staff_nav.php"; ?>

  <div class="container mx-auto px-6">
    <h2 class="text-3xl font-bold text-center">Welcome to the Staff Home Page</h2>
    <p class="mt-4 text-lg text-center">This page is only accessible to staff.</p>
  </div>
</body>
</html>
