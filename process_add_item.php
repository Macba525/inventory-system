<?php
require_once "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = trim($_POST["item_name"]);
    $quantity = intval($_POST["quantity"]);
    $description = trim($_POST["description"]);

    // ✅ Validate input
    if (empty($item_name) || $quantity <= 0) {
        echo json_encode(["success" => false, "message" => "Item name and quantity are required."]);
        exit();
    }

    // ✅ Check if item already exists
    $checkQuery = "SELECT id, quantity FROM supplies WHERE item_name = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $item_name);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ Item exists, update quantity
        $existingItem = $result->fetch_assoc();
        $newQuantity = $existingItem["quantity"] + $quantity;

        $updateQuery = "UPDATE supplies SET quantity = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newQuantity, $existingItem["id"]);

        if ($updateStmt->execute()) {
            header("Location: supplies.php?success=Item updated! New quantity: $newQuantity");
            exit();
        } else {
            header("Location: supplies.php?error=Error updating item quantity.");
            exit();
        }

        $updateStmt->close();
    } else {
        // ✅ Item does not exist, insert new
        $insertQuery = "INSERT INTO supplies (item_name, quantity, description) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sis", $item_name, $quantity, $description);

        if ($insertStmt->execute()) {
            header("Location: supplies.php?success=Item added successfully!");
            exit();
        } else {
            header("Location: supplies.php?error=Error adding item.");
            exit();
        }

        $insertStmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
?>
