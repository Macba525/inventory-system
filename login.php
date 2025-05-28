<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen w-full bg-gray-100">

    <!-- Left Side Image -->
    <div class="hidden md:flex w-1/2 h-full">
        <img class="object-cover w-full h-full rounded-r-lg" src="images/gojo.jpeg" alt="Login Image">
    </div>

    <!-- Login Form -->
    <div class="w-full md:w-1/2 flex flex-col items-center justify-center px-6">

        <form class="w-full max-w-md flex flex-col bg-white p-8 rounded-lg shadow-lg" action="process_login.php" method="POST">
            <h2 class="text-3xl font-semibold text-gray-900 text-center">Login</h2>
            <p class="text-sm text-gray-500 text-center mt-2">Welcome back! Please log in to continue.</p>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Username Input -->
            <div class="mt-6">
                <label class="block text-gray-600 text-sm font-medium">Username</label>
                <input type="text" name="username" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Password Input -->
            <div class="mt-4">
                <label class="block text-gray-600 text-sm font-medium">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center gap-2">
                    <input class="h-5 w-5" type="checkbox" id="remember">
                    <label class="text-sm text-gray-600" for="remember">Remember me</label>
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" class="mt-6 w-full py-2 rounded-lg text-white bg-indigo-500 hover:bg-indigo-600 transition">
                Login
            </button>

            <!-- Sign Up Option -->
            <p class="text-gray-600 text-sm text-center mt-4">Don’t have an account? <a class="text-indigo-500 hover:underline" href="register.php">Register</a></p>
        </form>
    </div>

</body>
</html>

