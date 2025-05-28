<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php?error=Please log in.");
    exit();
}

// Show navbar based on user role
if ($_SESSION['role'] === "admin") {
    include "admin_nav.php";
} elseif ($_SESSION['role'] === "staff") {
    include "staff_nav.php";
} else {
    echo "<p class='text-red-500 text-center p-4'>Access Denied</p>";
}
?>
