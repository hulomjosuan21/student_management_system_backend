<?php
session_start();
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $verification_code = isset($_POST['verification_code']) ? $_POST['verification_code'] : null;
    
    if (empty($user_id) || empty($verification_code)) {
        response(400, null, "Missing required field");
        exit;
    }

    if ($user_id) {
        $user_id = decrypt_user_id($user_id);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if ((int)$user['is_verified'] === 1) {
                throw new PDOException("User already verified.");
            }

            if ($user['verification_code'] === $verification_code) {
                $updateStmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE user_id = :user_id");
                $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $updateStmt->execute();

                unset($user['password'], $user['verification_code']);

                $_SESSION['user'] = $user;
                
                response(200, $user,"User verified successfully");
            } else {
                response(400, null, "Invalid verification code");
            }
        } else {
            response(404, null, "User not found");
        }
    } catch (PDOException $e) {
        response(500, null, "Database error: " . $e->getMessage());
    }
}
