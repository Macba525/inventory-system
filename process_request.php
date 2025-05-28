<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $staff_id = $_SESSION["user_id"];
    $item_id = intval($_POST["item_id"]);
    $quantity = intval($_POST["quantity"]);
    $notes = trim($_POST["notes"]);

    // Validate stock quantity
    $stockQuery = "SELECT quantity FROM supplies WHERE id = ?";
    $stockStmt = $conn->prepare($stockQuery);
    $stockStmt->bind_param("i", $item_id);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    $item = $stockResult->fetch_assoc();

    if (!$item) {
        header("Location: request_supplies.php?error=Invalid item selected.");
        exit();
    }
    
    if ($item["quantity"] < $quantity) {
        header("Location: request_supplies.php?error=Insufficient stock! Available: " . $item["quantity"]);
        exit();
    }

    // Insert request into the database
    $insertQuery = "INSERT INTO requests (staff_id, item_id, quantity, notes) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iiis", $staff_id, $item_id, $quantity, $notes);

    if ($insertStmt->execute()) {
        header("Location: my_requests.php?success=Request submitted successfully!");
    } else {
        header("Location: request_supplies.php?error=Error submitting request. Please try again.");
    }

    $insertStmt->close();
    $stockStmt->close();
    $conn->close();
}
?>
