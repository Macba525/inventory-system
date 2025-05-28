<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ✅ Define allowed roles per page (change this per file)
$page_role = "manager"; // Change this to "staff" or "manager" accordingly

// ✅ Restrict access if the user’s role doesn’t match
if ($_SESSION["role"] !== $page_role) {
    header("Location: home.php"); // Redirect unauthorized users
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 pt-24">
  <?php include "manager_nav.php"; ?>

  <div class="container mx-auto px-6">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Pending Requests</h2>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-400 shadow-lg rounded-lg">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="border border-gray-500 p-4 text-left">Request ID</th>
            <th class="border border-gray-500 p-4 text-left">Staff Name</th>
            <th class="border border-gray-500 p-4 text-left">Item</th>
            <th class="border border-gray-500 p-4 text-left">Quantity</th>
            <th class="border border-gray-500 p-4 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          require_once "db_connect.php"; // Ensure database connection

          // Use prepared statement to prevent SQL injection
          $query = "SELECT r.id, u.username, s.item_name, r.quantity 
                    FROM requests r 
                    JOIN users u ON r.staff_id = u.id 
                    JOIN supplies s ON r.item_id = s.id 
                    WHERE r.manager_status = 'pending'";

          $stmt = $conn->prepare($query);

          if (!$stmt) {
              echo "<tr><td colspan='5' class='border border-gray-500 p-4 text-center text-red-500'>Error preparing query.</td></tr>";
          } elseif (!$stmt->execute()) {
              echo "<tr><td colspan='5' class='border border-gray-500 p-4 text-center text-red-500'>Error executing query.</td></tr>";
          } else {
              $result = $stmt->get_result();
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr class='odd:bg-white even:bg-gray-100 hover:bg-gray-200 text-gray-800'>
                              <td class='border border-gray-400 p-4'>{$row['id']}</td>
                              <td class='border border-gray-400 p-4'>{$row['username']}</td>
                              <td class='border border-gray-400 p-4'>{$row['item_name']}</td>
                              <td class='border border-gray-400 p-4'>{$row['quantity']}</td>
                              <td class='border border-gray-400 p-4'>
                                <a href='approve_request.php?id={$row['id']}' class='text-green-600 hover:underline'>Approve</a> |
                                <a href='reject_request.php?id={$row['id']}' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure you want to reject this request?\")'>Reject</a>
                              </td>
                            </tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' class='border border-gray-400 p-4 text-center text-gray-600'>No pending requests found.</td></tr>";
              }
          }

          $stmt->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
