<?php
session_start();
require_once "db_connect.php";

// Ensure manager access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "manager") {
    header("Location: pending_requests.php?error=Access denied.");
    exit();
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: pending_requests.php?error=Invalid request ID.");
    exit();
}

$request_id = intval($_GET["id"]);

// Update request status to "rejected"
$sql = "UPDATE requests SET manager_status = 'rejected' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    header("Location: pending_requests.php?success=Request rejected!");
} else {
    header("Location: pending_requests.php?error=Error rejecting request.");
}

$stmt->close();
$conn->close();
?>
