<?php
session_start();
require_once "db_connect.php";

// ✅ Ensure admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: home.php?error=Access denied.");
    exit();
}

// ✅ Validate item ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: supplies.php?error=Invalid item ID.");
    exit();
}

$item_id = intval($_GET["id"]);

// ✅ Fetch item details for editing
$query = "SELECT item_name, quantity, description FROM supplies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: supplies.php?error=Item not found.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 h-screen flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg w-full">
        <h2 class="text-3xl font-bold mb-6 text-center">Edit Item</h2>

        <form action="process_edit_item.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

            <label class="block mb-2 font-medium">Item Name</label>
            <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" 
                   class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500" required>

            <label class="block mb-2 font-medium">Quantity</label>
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                   class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500" required min="1">

            <label class="block mb-2 font-medium">Description</label>
            <textarea name="description" class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($item['description']); ?></textarea>

            <div class="flex justify-between">
                <a href="supplies.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update Item</button>
            </div>
        </form>
    </div>
</body>
</html>
