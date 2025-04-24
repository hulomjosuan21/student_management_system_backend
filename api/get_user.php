<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['user_id'])) {
        response(400, null, "Missing 'user_id' in JSON body.");
    }

     $userId = $data['user_id'];

    if (!$userId) {
        response(400, null, "Missing 'user_id' parameter.");
    }

    $query = "SELECT user_id, first_name, last_name, email, phone_number, gender, course, address, birthdate, profile_url, role, created_at FROM users WHERE user_id = ?";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            response(200, $user, "User fetched successfully.");
        } else {
            response(404, null, "User not found.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }
} else {
    response(405, null, "Only GET requests are allowed.");
}
