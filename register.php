<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-200">
    <div class="w-96 bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-center">Register</h2>

        <!-- Success/Error Alerts -->
        <div id="alert-container" class="hidden flex items-center justify-between w-full px-3 h-10 rounded-sm mb-4">
            <p id="alert-message" class="text-sm"></p>
            <button type="button" aria-label="close" onclick="document.getElementById('alert-container').classList.add('hidden');">✖</button>
        </div>

        <form id="registerForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label class="block mb-2">Username</label>
            <input type="text" name="username" class="w-full p-2 border rounded mb-3" required>

            <label class="block mb-2">Email</label>
            <input type="email" name="email" class="w-full p-2 border rounded mb-3" required>

            <label class="block mb-2">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded mb-3" required>

            <label class="block mb-2">Role</label>
            <select name="role" class="w-full p-2 border rounded mb-3">
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="staff">Staff</option>
            </select>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
        </form>
    </div>

    <script>
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            console.log("Submitting form...");

            fetch("process_register.php", {
                method: "POST",
                body: formData
            }).then(response => {
                console.log("Fetch response:", response);
                return response.json();
            }).then(data => {
                console.log("Received JSON data:", data);

                const alertContainer = document.getElementById("alert-container");
                const alertMessage = document.getElementById("alert-message");

                alertContainer.classList.remove("hidden");
                alertContainer.style.display = "flex";  // Ensure it's visible

                if (data.success) {
                    alertContainer.classList.add("bg-green-500", "text-white");
                    alertMessage.textContent = "Success! Account created.";
                    setTimeout(() => window.location.href = "login.php", 3000);
                } else {
                    alertContainer.classList.add("bg-red-500", "text-white");
                    alertMessage.textContent = "Error: " + data.message;
                }
            }).catch(error => {
                console.error("Error fetching response:", error);
            });
        });
    </script>
</body>
</html>
