<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $created_by = isset($data['created_by']) ? $data['created_by'] : null;
        $task_id = isset($data['task_id']) ? $data['task_id'] : null;
        $assign_to = isset($data['assign_to']) ? $data['assign_to'] : null;

        $requiredFields = ['created_by', 'task_id', 'assign_to'];
        foreach ($requiredFields as $field) {
            if (empty($$field)) {
                throw new Exception("Missing required field: $field");
            }
        }
        $query = "INSERT INTO asigned_tasks (created_by, task_id, assign_to) VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$created_by, $task_id, $assign_to]);

        if ($stmt->rowCount() > 0) {
            response(200, null, "User task assigned successfully.");
        } else {
            response(200, null, "User task already exists or nothing was inserted.");
        }
    } catch (Exception $e) {
        response(400, null, $e->getMessage());
    } catch (PDOException $e) {
        response(500, null, "Database error: " . $e->getMessage());
    }
} else {
    response(405, null, "Only POST requests are allowed.");
}
