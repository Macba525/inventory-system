<?php
require_once "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = intval($_POST["item_id"]);
    $item_name = trim($_POST["item_name"]);
    $quantity = intval($_POST["quantity"]);
    $description = trim($_POST["description"]);

    if (empty($item_name) || $quantity < 1) {
        header("Location: edit_item.php?id=$item_id&error=Invalid input.");
        exit();
    }

    // Update item in the database
    $sql = "UPDATE supplies SET item_name = ?, quantity = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $item_name, $quantity, $description, $item_id);

    if ($stmt->execute()) {
        header("Location: supplies.php?success=Item updated successfully!");
        exit();
    } else {
        header("Location: edit_item.php?id=$item_id&error=Error updating item.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
