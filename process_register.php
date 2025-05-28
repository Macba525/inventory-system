<?php
require_once "db_connect.php";
session_start();
header("Content-Type: application/json");

// ✅ Ensure POST method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

// ✅ Validate CSRF Token
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
    echo json_encode(["success" => false, "message" => "Invalid CSRF token."]);
    exit();
}

// ✅ Sanitize inputs
$username = trim($_POST["username"]);
$email = trim($_POST["email"]);
$password = trim($_POST["password"]);
$role = isset($_POST["role"]) ? trim($_POST["role"]) : "staff"; // Default role

// ✅ Input validation
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit();
}
if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long."]);
    exit();
}

// ✅ Check for existing username/email
$checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$checkStmt->bind_param("ss", $username, $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username or email already exists."]);
    exit();
}
$checkStmt->close();

// ✅ Secure password hashing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ✅ Insert new user securely
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful! Redirecting..."]);
} else {
    echo json_encode(["success" => false, "message" => "Database Error: " . mysqli_error($conn)]);
}

$stmt->close();
$conn->close();
?>
