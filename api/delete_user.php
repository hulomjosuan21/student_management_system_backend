<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    response(405, null, "Only DELETE requests are allowed.");
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id'])) {
    response(400, null, "Missing 'user_id' for deletion.");
}

$userId = $data['user_id'];

$deleteQuery = "DELETE FROM users WHERE user_id = ?";

try {
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute([$userId]);

    if ($stmt->rowCount() > 0) {
        response(200, null, "User deleted successfully.");
    } else {
        response(404, null, "User not found.");
    }
} catch (PDOException $e) {
    response(500, null, $e->getMessage());
}
