<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "admin"; // Change this to "staff" or "manager" accordingly

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: home.php"); // Redirect unauthorized users
    exit();
}
?>


$request_id = intval($_GET["id"]);

// Delete approved request from the database
$sql = "DELETE FROM requests WHERE id = ? AND manager_status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    header("Location: approved_requests.php?success=Approved request deleted successfully!");
} else {
    header("Location: approved_requests.php?error=Error deleting approved request.");
}

$stmt->close();
$conn->close();
?>
