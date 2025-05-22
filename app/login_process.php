<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Changed from username
    $password = $_POST['password'];

    try {
        // Query changed to use email and select role name from roles table
        $stmt = $db->prepare("SELECT u.id, u.email, u.password_hash, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = :email");
        $stmt->bindParam(':email', $email); // Changed from username
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email']; // Changed from username
            $_SESSION['role'] = $user['role_name']; // Changed from role to role_name

            // Redirect based on role
            if ($user['role_name'] === 'admin') { // Changed from role to role_name
                header("Location: admin_dashboard.php");
            } else if ($user['role_name'] === 'professor') { // Changed from role to role_name
                header("Location: professor_dashboard.php");
            } else {
                // Fallback or error for undefined role
                header("Location: login.php?error=Invalid role");
            }
            exit;
        } else {
            header("Location: login.php?error=Invalid email or password");
            exit;
        }
    } catch (PDOException $e) {
        // Log error or handle more gracefully
        header("Location: login.php?error=Database error");
        exit;
    }
}
?>