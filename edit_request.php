<?php
session_start();
require_once "db_connect.php"; // Ensure database connection

// ✅ Ensure staff-only access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    header("Location: unauthorized.php?error=Access denied.");
    exit();
}

// ✅ Validate request ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: my_requests.php?error=Invalid request ID.");
    exit();
}

$request_id = intval($_GET["id"]);

// ✅ Fetch request details securely
$query = "SELECT * FROM requests WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

// ✅ Handle case where request is not found
if (!$request) {
    $error_message = "Request not found.";
} else {
    $error_message = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Request</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 pt-24">
  <?php include "staff_nav.php"; ?>

  <div class="container mx-auto px-6">
    <h2 class="text-3xl font-bold text-center">Edit Request</h2>

    <!-- ✅ Display error if request not found -->
    <?php if (!empty($error_message)): ?>
        <div class="bg-red-500 text-white p-3 rounded-md text-center mb-4">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php else: ?>

        <form action="process_edit_request.php" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
            <input type="hidden" name="request_id" value="<?php echo $request["id"]; ?>">

            <label class="block mb-2 font-medium">Quantity</label>
            <input type="number" name="quantity" class="w-full p-2 border rounded mb-4" value="<?php echo htmlspecialchars($request['quantity']); ?>" required min="1">

            <label class="block mb-2 font-medium">Notes</label>
            <textarea name="notes" class="w-full p-2 border rounded mb-4"><?php echo htmlspecialchars($request['notes']); ?></textarea>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">Update Request</button>
        </form>

    <?php endif; ?>
  </div>
</body>
</html>
