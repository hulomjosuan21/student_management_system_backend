<?php
session_start();
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if (empty($email) || empty($password)) {
        response(400, null, "Email and password are required.");
    }

    try {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            if (!$user['is_verified']) {
                response(401, null, "Email not verified. Please verify your account.");
            }

            unset($user['password'], $user['verification_code']);

            $_SESSION['user'] = $user;

            response(200, $user, "Login successful!");
        } else {
            response(401, null, "Invalid email or password.");
        }
    } catch (PDOException $e) {
        response(500, null, "Database error: " . $e->getMessage());
    }
} else {
    response(405, null, "Only POST requests are allowed.");
}
