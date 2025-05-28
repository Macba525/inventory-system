<?php
$host = "localhost"; // Use "127.0.0.1" if "localhost" doesn't work
$dbname = "inventory_system"; // Update your database name here
$username = "root"; // Change if using a different username
$password = ""; // Add your database password if set

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
