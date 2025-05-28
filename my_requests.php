<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "staff"; // Change this to "staff" or "manager" accordingly

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: unauthorized.php"); // Redirect unauthorized users
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Requests</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 pt-24">
  <?php include "staff_nav.php"; ?>

  <div class="container mx-auto px-6">
    <h2 class="text-3xl font-bold">My Requests</h2>

    <table class="w-full border-collapse border border-gray-300 mt-6">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-3">Request ID</th>
          <th class="border p-3">Item</th>
          <th class="border p-3">Quantity</th>
          <th class="border p-3">Status</th>
          <th class="border p-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $staff_id = $_SESSION["user_id"];
        $query = "SELECT r.id, s.item_name, r.quantity, r.manager_status FROM requests r 
                  JOIN supplies s ON r.item_id = s.id 
                  WHERE r.staff_id = ? ORDER BY r.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "<tr class='odd:bg-white even:bg-gray-100'>
                    <td class='border p-3'>{$row['id']}</td>
                    <td class='border p-3'>{$row['item_name']}</td>
                    <td class='border p-3'>{$row['quantity']}</td>
                    <td class='border p-3'>{$row['manager_status']}</td>
                    <td class='border p-3'>
                      <a href='edit_request.php?id={$row['id']}' class='text-blue-500 hover:underline'>Edit</a> |
                      <a href='delete_request.php?id={$row['id']}' class='text-red-500 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }

        $stmt->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
