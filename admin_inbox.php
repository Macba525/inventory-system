<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page
$page_role = "admin"; 

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: unauthorized.php");
    exit();
}

// ✅ Process Forwarding Request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["forward_id"])) {
    $forward_id = intval($_POST["forward_id"]);

    // ✅ Ensure request is marked as forwarded
    $updateQuery = "UPDATE requests SET admin_status = 'processed' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $forward_id);

    if ($stmt->execute()) {
        // ✅ Fetch request details including quantity
        $getRequestQuery = "SELECT staff_id, item_id, quantity FROM requests WHERE id = ?";
        $getRequestStmt = $conn->prepare($getRequestQuery);
        $getRequestStmt->bind_param("i", $forward_id);
        $getRequestStmt->execute();
        $requestData = $getRequestStmt->get_result()->fetch_assoc();
        $getRequestStmt->close();

        if ($requestData) {
            $staff_id = $requestData["staff_id"];
            $item_id = $requestData["item_id"];
            $quantity = $requestData["quantity"];

            // ✅ Insert into Borrowed Supplies with correct quantity
            $insertQuery = "INSERT INTO borrowed_supplies (staff_id, item_id, quantity, borrowed_date, return_date, status) 
                            VALUES (?, ?, ?, NOW(), NULL, 'approved')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $staff_id, $item_id, $quantity);

            if ($insertStmt->execute()) {
                header("Location: admin_inbox.php?success=Request forwarded successfully.");
            } else {
                header("Location: admin_inbox.php?error=Failed to insert into borrowed supplies.");
            }
            $insertStmt->close();
        } else {
            header("Location: admin_inbox.php?error=Request data not found.");
        }
    } else {
        header("Location: admin_inbox.php?error=Failed to forward request.");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approved Requests</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 pt-24">
  <?php include "admin_nav.php"; ?>

  <div class="container mx-auto px-6">
    <h2 class="text-3xl font-bold">Approved Requests</h2>

    <table class="w-full border-collapse border border-gray-300 mt-6">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-3">Request ID</th>
          <th class="border p-3">Staff Name</th>
          <th class="border p-3">Item</th>
          <th class="border p-3">Quantity</th>
          <th class="border p-3">Approval Status</th>
          <th class="border p-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // ✅ Fetch approved requests
        $query = "SELECT r.id, u.username, s.item_name, r.quantity 
                  FROM requests r
                  JOIN users u ON r.staff_id = u.id 
                  JOIN supplies s ON r.item_id = s.id
                  WHERE r.manager_status = 'approved' AND r.admin_status = 'pending'";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("<tr><td colspan='6' class='border p-3 text-center text-red-500'>Error preparing query: " . $conn->error . "</td></tr>");
        }

        if (!$stmt->execute()) {
            die("<tr><td colspan='6' class='border p-3 text-center text-red-500'>Error executing query: " . $stmt->error . "</td></tr>");
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='odd:bg-white even:bg-gray-100'>
                        <td class='border p-3'>{$row['id']}</td>
                        <td class='border p-3'>{$row['username']}</td>
                        <td class='border p-3'>{$row['item_name']}</td>
                        <td class='border p-3'>{$row['quantity']}</td>
                        <td class='border p-3 text-green-500 font-bold'>Approved</td>
                        <td class='border p-3'>
                            <form action='' method='POST'>
                               <input type='hidden' name='forward_id' value='{$row['id']}'>
                               <button type='submit' class='bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition'>
                                   Forward to Borrowed Supplies
                               </button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='border p-3 text-center text-gray-600'>No approved requests found.</td></tr>";
        }

        $stmt->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
