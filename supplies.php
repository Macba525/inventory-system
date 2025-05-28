<?php
session_start();
require_once "db_connect.php";

// ✅ Ensure admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: home.php?error=Access denied.");
    exit();
}

// ✅ Handle search input safely
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplies Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 pt-24">
    <?php include "admin_nav.php"; ?> 

    <div class="container mx-auto px-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Supplies Inventory</h2>
            <div class="flex gap-4">
                <form method="GET" class="flex">
                    <input type="text" name="search" id="searchInput" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>"
                           class="border border-gray-400 p-2 rounded-l-lg focus:outline-none">
                    <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700 transition">Search</button>
                    <button type="button" id="clearSearch" class="bg-gray-500 text-white px-4 rounded-lg hover:bg-gray-600 transition ml-2">
                        Clear
                    </button>
                </form>
                <button onclick="openModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add Item</button>
                <a href="export_supplies.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Export to CSV</a>
            </div>
        </div>

        <!-- ✅ Smooth Transition Modal -->
        <div id="addItemModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl transform scale-95 transition-all duration-300">
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Add New Item</h2>

                <form action="process_add_item.php" method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700">Item Name</label>
                            <input type="text" name="item_name" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700">Quantity</label>
                            <input type="number" name="quantity" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500" required min="1">
                        </div>
                    </div>

                    <label class="block mb-2 font-semibold text-gray-700">Description</label>
                    <textarea name="description" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Add Item</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ✅ JavaScript for Modal & Search Reset -->
        <script>
            function openModal() { document.getElementById("addItemModal").classList.remove("hidden"); }
            function closeModal() { document.getElementById("addItemModal").classList.add("hidden"); }

            document.getElementById("clearSearch").addEventListener("click", function() {
                document.getElementById("searchInput").value = ""; // ✅ Clears input field
                window.location.href = "supplies.php"; // ✅ Reloads page, showing all items
            });
        </script>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-400 shadow-lg rounded-lg">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="border border-gray-500 p-4 text-left">Item ID</th>
                        <th class="border border-gray-500 p-4 text-left">Item Name</th>
                        <th class="border border-gray-500 p-4 text-left">Quantity</th>
                        <th class="border border-gray-500 p-4 text-left">Description</th>
                        <th class="border border-gray-500 p-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM supplies WHERE item_name LIKE ? ORDER BY created_at DESC";
                    $stmt = $conn->prepare($query);
                    $searchTerm = "%{$search}%";
                    $stmt->bind_param("s", $searchTerm);

                    if (!$stmt->execute()) {
                        echo "<tr><td colspan='5' class='border border-gray-500 p-4 text-center text-red-600'>Error retrieving data.</td></tr>";
                    } else {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='odd:bg-white even:bg-gray-100 hover:bg-gray-200 text-gray-800'>
                                        <td class='border border-gray-400 p-4'>{$row['id']}</td>
                                        <td class='border border-gray-400 p-4'>{$row['item_name']}</td>
                                        <td class='border border-gray-400 p-4'>{$row['quantity']}</td>
                                        <td class='border border-gray-400 p-4'>" . (!empty($row['description']) ? $row['description'] : 'No Description') . "</td>
                                        <td class='border border-gray-400 p-4'>
                                            <a href='edit_item.php?id={$row['id']}' class='text-blue-600 hover:underline'>Edit</a> |
                                            <a href='delete_item.php?id={$row['id']}' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='border border-gray-400 p-4 text-center text-gray-600'>No items found.</td></tr>";
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
