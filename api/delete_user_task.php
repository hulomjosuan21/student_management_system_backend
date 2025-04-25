<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    response(405, null, "Only DELETE requests are allowed.");
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || (!isset($data['user_task_id']) && !isset($data['user_id']))) {
    response(400, null, "Missing 'user_task_id' or 'user_id' for deletion.");
}

$whereConditions = [];
$params = [];

if (isset($data['user_task_id'])) {
    $whereConditions[] = "user_task_id = ?";
    $params[] = $data['user_task_id'];
}

if (isset($data['user_id'])) {
    $whereConditions[] = "user_id = ?";
    $params[] = $data['user_id'];
}

$deleteQuery = "DELETE FROM user_tasks WHERE " . implode(' OR ', $whereConditions);

try {
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        response(200, null, "User Task deleted successfully.");
    } else {
        response(404, null, "User Task not found.");
    }
} catch (PDOException $e) {
    response(500, null, $e->getMessage());
}
