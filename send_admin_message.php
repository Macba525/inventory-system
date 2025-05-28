<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "manager"; // Change this to "staff" or "manager" accordingly

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: unauthorized.php"); // Redirect unauthorized users
    exit();
}
?>


$request_id = intval($_GET["id"]);

// Fetch request details
$getRequestQuery = "SELECT r.staff_id, s.item_name, r.quantity FROM requests r 
                    JOIN supplies s ON r.item_id = s.id 
                    WHERE r.id = ?";
$getRequestStmt = $conn->prepare($getRequestQuery);
$getRequestStmt->bind_param("i", $request_id);
$getRequestStmt->execute();
$requestResult = $getRequestStmt->get_result();
$request = $requestResult->fetch_assoc();

if (!$request) {
    header("Location: pending_requests.php?error=Request not found.");
    exit();
}

$staff_id = $request["staff_id"];
$item_name = $request["item_name"];
$quantity = $request["quantity"];

// Insert notification into admin inbox
$insertMessageQuery = "INSERT INTO admin_messages (staff_id, item_name, quantity, status) VALUES (?, ?, ?, 'unread')";
$insertMessageStmt = $conn->prepare($insertMessageQuery);
$insertMessageStmt->bind_param("iss", $staff_id, $item_name, $quantity);

if ($insertMessageStmt->execute()) {
    header("Location: approved_requests.php?success=Request approved and message sent to admin inbox!");
    exit();
} else {
    header("Location: pending_requests.php?error=Error sending message to admin.");
    exit();
}

$insertMessageStmt->close();
$getRequestStmt->close();
$conn->close();
?>
