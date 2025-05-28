<?php
session_start();
require_once "db_connect.php";

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: my_requests.php?error=Invalid ID.");
    exit();
}

$request_id = intval($_GET["id"]);

$sql = "DELETE FROM requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    header("Location: my_requests.php?success=Request deleted successfully!");
} else {
    header("Location: my_requests.php?error=Error deleting request.");
}

$stmt->close();
$conn->close();
exit();
?>
