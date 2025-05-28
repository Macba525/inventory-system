<?php
session_start();
require_once "db_connect.php";

// Ensure admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_inbox.php?error=Access denied.");
    exit();
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: admin_inbox.php?error=Invalid request ID.");
    exit();
}

$request_id = intval($_GET["id"]);

// Get request details
$getRequestQuery = "SELECT item_id, quantity FROM requests WHERE id = ?";
$getRequestStmt = $conn->prepare($getRequestQuery);
$getRequestStmt->bind_param("i", $request_id);
$getRequestStmt->execute();
$requestResult = $getRequestStmt->get_result();
$request = $requestResult->fetch_assoc();

if (!$request) {
    header("Location: admin_inbox.php?error=Request not found.");
    exit();
}

$item_id = $request["item_id"];
$request_quantity = $request["quantity"];

// Deduct stock from supplies
$updateSupplyQuery = "UPDATE supplies SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
$updateSupplyStmt = $conn->prepare($updateSupplyQuery);
$updateSupplyStmt->bind_param("iii", $request_quantity, $item_id, $request_quantity);

if ($updateSupplyStmt->execute() && $updateSupplyStmt->affected_rows > 0) {
    // Update request status to processed
    $finalApproveQuery = "UPDATE requests SET admin_status = 'processed' WHERE id = ?";
    $finalApproveStmt = $conn->prepare($finalApproveQuery);
    $finalApproveStmt->bind_param("i", $request_id);

    if ($finalApproveStmt->execute()) {
        header("Location: admin_inbox.php?success=Request fully approved! Stock updated.");
        exit();
    } else {
        header("Location: admin_inbox.php?error=Error approving request.");
        exit();
    }

    $finalApproveStmt->close();
} else {
    header("Location: admin_inbox.php?error=Insufficient stock for final approval.");
    exit();
}

$updateSupplyStmt->close();
$getRequestStmt->close();
$conn->close();
?>
