<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS user_count FROM users");
        $stmt->execute();
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) AS verified_user_count FROM users WHERE is_verified = 1");
        $stmt->execute();
        $verifiedUserCount = $stmt->fetch(PDO::FETCH_ASSOC)['verified_user_count'];

        $sessionDir = ini_get("session.save_path");
        if (!$sessionDir) {
            $sessionDir = sys_get_temp_dir();
        }

        $sessionFiles = glob($sessionDir . '/sess_*');
        $sessionCount = is_array($sessionFiles) ? count($sessionFiles) : 0;

        $systemHealth = [
            'database_connection' => 'OK',
            'disk_usage' => 'Normal',
            'uptime' => '24 hours'
        ];

        $payload = [
            'user_count' => $userCount,
            'verified_user_count' => $verifiedUserCount,
            'session_count' => $sessionCount,
            'system_health' => $systemHealth
        ];

        if ($stmt->rowCount() > 0) {
            response(200, $payload, 'User tasks fetched successfully!');
        } else {
            response(200, [], 'Error fetching dashboard analytics.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
