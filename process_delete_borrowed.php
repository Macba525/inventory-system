<?php
session_start();
require_once "db_connect.php";

// ✅ Ensure admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: borrowed_supplies.php?error=Access denied.");
    exit();
}

// ✅ Validate borrow ID
if (!isset($_POST["borrow_id"]) || !is_numeric($_POST["borrow_id"])) {
    header("Location: borrowed_supplies.php?error=Invalid borrow ID.");
    exit();
}

$borrow_id = intval($_POST["borrow_id"]);

// ✅ Delete borrowed item from database
$deleteQuery = "DELETE FROM borrowed_supplies WHERE id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $borrow_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item successfully deleted."]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting item."]);
}

$stmt->close();
$conn->close();
exit();
?>
