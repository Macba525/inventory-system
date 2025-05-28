<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = intval($_POST["request_id"]);
    $quantity = intval($_POST["quantity"]);
    $notes = trim($_POST["notes"]);

    $sql = "UPDATE requests SET quantity = ?, notes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $quantity, $notes, $request_id);

    if ($stmt->execute()) {
        header("Location: my_requests.php?success=Request updated!");
    } else {
        header("Location: edit_request.php?id=$request_id&error=Error updating request.");
    }

    $stmt->close();
    $conn->close();
}
?>
