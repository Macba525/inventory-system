<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Ensure admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_inbox.php?error=Access denied.");
    exit();
}

// ✅ Validate request ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: admin_inbox.php?error=Invalid request ID.");
    exit();
}
$request_id = intval($_GET["id"]);

// ✅ Fetch request details, including quantity
$query = "SELECT staff_id, item_id, quantity FROM requests WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    header("Location: admin_inbox.php?error=Request not found.");
    exit();
}

$staff_id = $request["staff_id"];
$item_id = $request["item_id"];
$request_quantity = $request["quantity"];

// ✅ Forward request
$forwardQuery = "UPDATE requests SET admin_status = 'processed' WHERE id = ?";
$forwardStmt = $conn->prepare($forwardQuery);
$forwardStmt->bind_param("i", $request_id);
$forwardStmt->execute();
$forwardStmt->close();

// ✅ Insert into Borrowed Supplies with correct quantity
$borrowQuery = "INSERT INTO borrowed_supplies (staff_id, item_id, quantity, borrowed_date, return_date, status) 
                VALUES (?, ?, ?, NOW(), NULL, 'forwarded')";
$borrowStmt = $conn->prepare($borrowQuery);
$borrowStmt->bind_param("iii", $staff_id, $item_id, $request_quantity);
$borrowStmt->execute();
$borrowStmt->close();

// ✅ Success response
header("Location: borrowed_supplies.php?success=Request forwarded successfully!");
exit();

// ✅ Main Execution Flow
checkAdminAccess();
$request_id = validateRequestID();
$request = getRequestDetails($request_id, $conn);

$staff_id = $request["staff_id"];
$item_id = $request["item_id"];
$request_quantity = $request["quantity"];

forwardRequest($request_id, $conn);
addToBorrowedSupplies($staff_id, $item_id, $request_quantity, $conn);

// ✅ Success response
header("Location: borrowed_supplies.php?success=Request forwarded successfully!");
exit();
?>
