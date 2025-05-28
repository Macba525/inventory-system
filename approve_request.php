<?php
session_start();
require_once "db_connect.php";

// ✅ Ensure manager access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "manager") {
    header("Location: pending_requests.php?error=Access denied.");
    exit();
}

// ✅ Validate request ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: pending_requests.php?error=Invalid request ID.");
    exit();
}

$request_id = intval($_GET["id"]);

// ✅ Begin transaction for data integrity
$conn->begin_transaction();

// ✅ Fetch request details
$getRequestQuery = "SELECT item_id, quantity FROM requests WHERE id = ?";
$getRequestStmt = $conn->prepare($getRequestQuery);
$getRequestStmt->bind_param("i", $request_id);
$getRequestStmt->execute();
$requestResult = $getRequestStmt->get_result();
$request = $requestResult->fetch_assoc();
$getRequestStmt->close();

if (!$request) {
    $conn->rollback();
    header("Location: pending_requests.php?error=Request not found.");
    exit();
}

$item_id = $request["item_id"];
$request_quantity = $request["quantity"];

// ✅ Check stock availability before deduction
$checkStockQuery = "SELECT quantity FROM supplies WHERE id = ?";
$checkStockStmt = $conn->prepare($checkStockQuery);
$checkStockStmt->bind_param("i", $item_id);
$checkStockStmt->execute();
$stockResult = $checkStockStmt->get_result();
$stock = $stockResult->fetch_assoc();
$checkStockStmt->close();

if (!$stock || $stock["quantity"] < $request_quantity) {
    $conn->rollback();
    header("Location: pending_requests.php?error=Insufficient stock.");
    exit();
}

// ✅ Deduct stock from supplies
$updateSupplyQuery = "UPDATE supplies SET quantity = quantity - ? WHERE id = ?";
$updateSupplyStmt = $conn->prepare($updateSupplyQuery);
$updateSupplyStmt->bind_param("ii", $request_quantity, $item_id);

if (!$updateSupplyStmt->execute()) {
    $conn->rollback();
    header("Location: pending_requests.php?error=Error updating stock.");
    exit();
}
$updateSupplyStmt->close();

// ✅ Approve the request
$approveQuery = "UPDATE requests SET manager_status = 'approved', admin_status = 'pending' WHERE id = ?";
$approveStmt = $conn->prepare($approveQuery);
$approveStmt->bind_param("i", $request_id);

if (!$approveStmt->execute()) {
    $conn->rollback();
    header("Location: pending_requests.php?error=Error approving request.");
    exit();
}
$approveStmt->close();

$conn->commit(); // ✅ Commit transaction if everything succeeds
$conn->close();

// ✅ Success response
header("Location: pending_requests.php?success=Request approved! Stock updated.");
exit();
?>
