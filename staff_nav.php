<?php
// ✅ Start session only if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php"; // Ensure database connection

// ✅ Restrict access to staff only
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    header("Location: unauthorized.php?error=Access denied.");
    exit();
}
?>

<nav class="fixed top-0 left-0 w-full h-16 bg-blue-500 flex items-center justify-between px-8 shadow-md z-50">
    <!-- Logo & Staff Panel Title -->
    <a href="staff_home.php" class="flex items-center gap-3" aria-label="Staff Home">
        <img src="images/management.png" alt="Staff Panel Logo" class="h-10">
        <span class="text-white text-xl font-bold">Staff Panel</span>
    </a>

    <!-- Centered Navigation Links -->
    <div class="flex-1 flex justify-center gap-8">
        <a href="staff_home.php" class="text-white text-lg hover:underline">Home</a>
        <a href="request_supplies.php" class="text-white text-lg hover:underline">Request Supplies</a>
        <a href="my_requests.php" class="text-white text-lg hover:underline">My Requests</a>
    </div>

    <!-- Secure Logout Form -->
    <form action="logout.php" method="POST">
        <button type="submit" class="bg-red-500 text-white px-6 py-2 text-lg rounded hover:bg-red-600 transition">
            Logout
        </button>
    </form>
</nav>
