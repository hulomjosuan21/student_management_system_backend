<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_task_id = isset($_POST['user_task_id']) ? $_POST['user_task_id'] : null;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

    if (empty($user_task_id)) {
        response(400, null, "Missing required field: user_task_id");
        exit;
    }

    if (empty($user_id)) {
        response(400, null, "Missing required field: user_id");
        exit;
    }

    $query = "UPDATE user_tasks SET user_id = ? WHERE user_task_id = ?";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $user_task_id]);

        if ($stmt->rowCount() > 0) {
            response(200, null, "User task updated successfully.");
        } else {
            response(404, null, "User task not found or no changes made.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }

} else {
    response(405, null, "Only POST requests are allowed.");
}
