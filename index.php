<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 bg-white p-8 rounded-lg shadow-lg text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Inventory System</h2>

        <?php if (isset($_SESSION["user_id"])): ?>
            <p class="text-gray-700">Hello, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!</p>
            <p class="text-gray-600 mb-4">Your role: <strong><?php echo htmlspecialchars($_SESSION["role"]); ?></strong></p>
            <a href="dashboard.php" class="block bg-green-500 text-white font-medium p-2 rounded-lg hover:bg-green-600 transition">Go to Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="block bg-blue-500 text-white font-medium p-2 rounded-lg hover:bg-blue-600 transition">Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
