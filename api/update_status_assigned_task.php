<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $assigned_task_id = isset($data['assigned_task_id']) ? $data['assigned_task_id'] : null;
        $task_status = isset($data['task_status']) ? $data['task_status'] : null;

        $requiredFields = ['assigned_task_id', 'task_status'];
        foreach ($requiredFields as $field) {
            if (empty($$field)) {
                throw new Exception("Missing required field: $field");
            }
        }

        $query = "UPDATE assigned_tasks SET task_status = ? WHERE assigned_task_id = ?";
        $params = [$task_status, $assigned_task_id];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            response(200, $assigned_task_id, "Task status updated to '$task_status' successfully.");
        } else {
            response(200, $assigned_task_id, "No changes made to the task.");
        }
    } catch (Exception $e) {
        response(400, null, $e->getMessage());
    } catch (PDOException $e) {
        response(500, null, "Database error: " . $e->getMessage());
    }
} else {
    response(405, null, "Only POST requests are allowed.");
}
