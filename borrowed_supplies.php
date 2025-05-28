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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowed Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 pt-24">
    <?php include "admin_nav.php"; ?>

    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Borrowed Supplies</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-400 shadow-lg rounded-lg">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="border border-gray-500 p-4 text-left">Item Name</th>
                        <th class="border border-gray-500 p-4 text-left">Staff Name</th>
                        <th class="border border-gray-500 p-4 text-left">Quantity</th>
                        <th class="border border-gray-500 p-4 text-left">Borrowed Date</th>
                        <th class="border border-gray-500 p-4 text-left">Return Date</th>
                        <th class="border border-gray-500 p-4 text-left">Status</th>
                        <th class="border border-gray-500 p-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT bs.id, s.item_name, u.username, bs.quantity, bs.borrowed_date, bs.return_date, bs.status
                              FROM borrowed_supplies bs
                              JOIN supplies s ON bs.item_id = s.id
                              JOIN users u ON bs.staff_id = u.id
                              ORDER BY bs.borrowed_date DESC";

                    $stmt = $conn->prepare($query);

                    if (!$stmt) {
                        echo "<tr><td colspan='7' class='border border-gray-500 p-4 text-center text-red-500'>Error preparing query: " . $conn->error . "</td></tr>";
                    } elseif (!$stmt->execute()) {
                        echo "<tr><td colspan='7' class='border border-gray-500 p-4 text-center text-red-500'>Error executing query: " . $stmt->error . "</td></tr>";
                    } else {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='odd:bg-white even:bg-gray-100 hover:bg-gray-200 text-gray-800'>
                                        <td class='border border-gray-400 p-4'>{$row['item_name']}</td>
                                        <td class='border border-gray-400 p-4'>{$row['username']}</td>
                                        <td class='border border-gray-400 p-4'>{$row['quantity']}</td>
                                        <td class='border border-gray-400 p-4'>{$row['borrowed_date']}</td>
                                        <td class='border border-gray-400 p-4'>" . ($row['return_date'] ? $row['return_date'] : 'Not Returned') . "</td>
                                        <td class='border border-gray-400 p-4 text-blue-500 font-bold'>{$row['status']}</td>
                                        <td class='border border-gray-400 p-4 flex gap-4'>
                                            <button onclick='openReturnModal({$row['id']})' class='bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition'>
                                                Mark as Returned
                                            </button>";

                                if (trim(strtolower($row['status'])) === "returned") {
                                    echo "<button onclick='openDeleteModal({$row['id']})' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition'>
                                            Delete
                                          </button>";
                                }

                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='border border-gray-400 p-4 text-center text-gray-600'>No borrowed supplies found.</td></tr>";
                        }
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Popup Modal for Mark as Returned -->
    <div id="returnModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Confirm Return</h2>
            <p class="text-gray-600 text-center">Are you sure you want to mark this item as returned?</p>

            <div class="flex justify-center gap-4 mt-6">
                <button onclick="closeReturnModal()" class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600 transition">Cancel</button>
                <button id="confirmReturnButton" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 transition">Confirm</button>
            </div>
        </div>
    </div>

    <!-- ✅ Popup Modal for Delete -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Confirm Deletion</h2>
            <p class="text-gray-600 text-center">Are you sure you want to delete this returned item?</p>

            <div class="flex justify-center gap-4 mt-6">
                <button onclick="closeDeleteModal()" class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600 transition">Cancel</button>
                <button id="confirmDeleteButton" class="bg-red-500 text-white px-6 py-3 rounded hover:bg-red-600 transition">Delete</button>
            </div>
        </div>
    </div>

    <!-- ✅ Fully Functional JavaScript for Popup Handling -->
    <script>
        let selectedBorrowId = null;

        function openReturnModal(borrowId) {
            selectedBorrowId = borrowId;
            document.getElementById("returnModal").classList.remove("hidden");
        }

        function closeReturnModal() {
            document.getElementById("returnModal").classList.add("hidden");
        }

        function openDeleteModal(borrowId) {
            selectedBorrowId = borrowId;
            document.getElementById("deleteModal").classList.remove("hidden");
        }

        function closeDeleteModal() {
            document.getElementById("deleteModal").classList.add("hidden");
        }

        document.getElementById("confirmReturnButton").addEventListener("click", function() {
            fetch("process_return_borrowed.php", {
                method: "POST",
                body: new URLSearchParams({ borrow_id: selectedBorrowId }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            }).then(response => response.json())
              .then(data => { alert(data.message); if (data.success) location.reload(); });
        });

        document.getElementById("confirmDeleteButton").addEventListener("click", function() {
            fetch("process_delete_borrowed.php", {
                method: "POST",
                body: new URLSearchParams({ borrow_id: selectedBorrowId }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            }).then(response => response.json())
              .then(data => { alert(data.message); if (data.success) location.reload(); });
        });
    </script>
</body>
</html>
