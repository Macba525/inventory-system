<?php
require_once "db_connect.php";
session_start();

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: supplies.php?error=Invalid item ID.");
    exit();
}

$item_id = intval($_GET["id"]);

// Delete the item from the database
$query = "DELETE FROM supplies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    header("Location: supplies.php?success=Item deleted successfully!");
} else {
    header("Location: supplies.php?error=Error deleting item.");
}

$stmt->close();
$conn->close();
exit();
?>
