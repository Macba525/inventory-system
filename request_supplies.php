<?php
session_start();
require_once "db_connect.php";

// Ensure only staff can access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    header("Location: unauthorized.php?error=Access denied.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Supplies</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 h-screen flex items-center justify-center">
  <?php include "staff_nav.php"; ?>

  <div class="bg-white p-6 rounded-lg shadow-md max-w-md w-full text-center">
    <h2 class="text-3xl font-bold">Request Supplies</h2>
    <p class="mt-4">Fill in the form below to request supplies.</p>

    <form action="process_request.php" method="POST" class="mt-6" onsubmit="return validateRequest()">
      <label class="block mb-2 font-medium">Select Item</label>
      <select name="item_id" id="itemSelect" class="w-full p-2 border rounded mb-4" required onchange="updateSupplies()">
        <option value="" disabled selected>Select an item</option>
        <?php
        $query = "SELECT id, item_name, quantity FROM supplies";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['id']}' data-supplies='{$row['quantity']}'>
                      {$row['item_name']} (Available: {$row['quantity']} supplies)
                  </option>";
        }
        ?>
      </select>

      <input type="hidden" id="availableSupplies" value="0">

      <label class="block mb-2 font-medium">Quantity</label>
      <input type="number" name="quantity" id="quantityInput" class="w-full p-2 border rounded mb-4" required min="1">

      <label class="block mb-2 font-medium">Reason / Notes</label>
      <textarea name="notes" class="w-full p-2 border rounded mb-4"></textarea>

      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">Submit Request</button>
    </form>
  </div>

  <script>
    function updateSupplies() {
      const selectedItem = document.getElementById("itemSelect");
      const selectedOption = selectedItem.options[selectedItem.selectedIndex];
      const availableSupplies = selectedOption.getAttribute("data-supplies");

      if (availableSupplies) {
          document.getElementById("availableSupplies").value = parseInt(availableSupplies, 10);
      } else {
          document.getElementById("availableSupplies").value = 0;
      }
    }

    function validateRequest() {
      const quantity = parseInt(document.getElementById("quantityInput").value, 10);
      const availableSupplies = parseInt(document.getElementById("availableSupplies").value, 10);

      if (isNaN(quantity) || isNaN(availableSupplies)) {
          alert("Error: Unable to determine available supplies. Try again.");
          return false;
      }

      if (quantity > availableSupplies) {
          alert("You cannot request more than the available supplies (" + availableSupplies + ").");
          return false;
      }
      return true;
    }

    document.getElementById("itemSelect").addEventListener("change", updateSupplies);
  </script>
</body>
</html>
