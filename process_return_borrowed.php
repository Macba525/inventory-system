<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["borrow_id"])) {
    $borrow_id = intval($_POST["borrow_id"]);

    // ✅ Fetch borrowed item details
    $query = "SELECT item_id, quantity FROM borrowed_supplies WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $borrowedItem = $result->fetch_assoc();
    $stmt->close();

    if (!$borrowedItem) {
        echo json_encode(["success" => false, "message" => "Borrowed item not found."]);
        exit();
    }

    $item_id = $borrowedItem["item_id"];
    $returned_quantity = $borrowedItem["quantity"];

    // ✅ Update borrowed item status
    $updateBorrowQuery = "UPDATE borrowed_supplies SET status = 'returned', return_date = NOW() WHERE id = ?";
    $updateBorrowStmt = $conn->prepare($updateBorrowQuery);
    $updateBorrowStmt->bind_param("i", $borrow_id);
    $updateBorrowStmt->execute();
    $updateBorrowStmt->close();

    // ✅ Restore quantity in supplies
    $updateStockQuery = "UPDATE supplies SET quantity = quantity + ? WHERE id = ?";
    $updateStockStmt = $conn->prepare($updateStockQuery);
    $updateStockStmt->bind_param("ii", $returned_quantity, $item_id);
    $updateStockStmt->execute();
    $updateStockStmt->close();

    echo json_encode(["success" => true, "message" => "Item successfully marked as returned and stock updated."]);
    exit();
}
?>
