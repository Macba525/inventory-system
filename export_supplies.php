<?php
require_once "db_connect.php"; // Connect to the database

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=supplies_inventory.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Item ID', 'Item Name', 'Quantity', 'Description']); // CSV Headers

$query = "SELECT id, item_name, quantity, description FROM supplies ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error: Could not retrieve data.");
}

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row); // Write each row to CSV
}

fclose($output);
exit();
?>
