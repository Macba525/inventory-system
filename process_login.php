<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("Security alert: Invalid CSRF token.");
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            // Store session details
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            // Redirect based on role
            if ($user["role"] === "admin") {
                header("Location: home.php");
            } elseif ($user["role"] === "staff") {
                header("Location: staff_home.php");
            } elseif ($user["role"] === "manager") {
                header("Location: manager_home.php");
            } else {
                header("Location: unauthorized.php");
            }
            exit();
        } else {
            header("Location: login.php?error=Invalid password!");
            exit();
        }
    } else {
        header("Location: login.php?error=User not found!");
        exit();
    }
}
?>
