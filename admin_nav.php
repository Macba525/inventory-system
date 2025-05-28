<?php
// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: unauthorized.php");
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

<nav class="fixed top-0 left-0 w-full h-16 bg-indigo-500 flex items-center justify-between px-8 shadow-md z-50">
    <!-- Logo & Admin Title -->
    <a href="home.php" class="flex items-center gap-3">
        <img src="images/settings.png" alt="logo" class="h-10">
        <span class="text-white text-xl font-bold">Admin Panel</span>
    </a>

    <!-- Navigation Links -->
    <div class="flex items-center gap-8 relative">
        <a href="home.php" class="text-white text-lg hover:underline">Home</a>
        <a href="borrowed_supplies.php" class="text-white text-lg hover:underline">Borrowed Supplies</a>
        <a href="supplies.php" class="text-white text-lg hover:underline">Supplies</a>
        <a href="admin_inbox.php" class="text-white text-lg hover:underline">Approved Inbox</a>
    </div>

    <button id="logoutButton" class="bg-red-500 text-white px-6 py-2 text-lg rounded hover:bg-red-600 transition">
        Logout
    </button>
</nav>

<script>
document.getElementById("logoutButton").addEventListener("click", function() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "logout.php";
    }
});
</script>
