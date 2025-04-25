<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    try {
        // Require necessary parameters
        $inputData = json_decode(file_get_contents('php://input'), true);

        if (!isset($inputData['user_id']) || !isset($inputData['task_id']) || !isset($inputData['task_status'])) {
            response(400, null, 'Missing required parameters: user_id, task_id, task_status');
            exit;
        }

        $userId = $inputData['user_id'];
        $taskId = $inputData['task_id'];
        $taskStatus = $inputData['task_status'];

        // Validate task status
        if (!in_array($taskStatus, ['complete', 'pending'])) {
            response(400, null, 'Invalid task status. Allowed values are "complete" or "pending".');
            exit;
        }

        // Prepare query to update task status
        $query = "
            UPDATE user_tasks
            SET task_status = ?, updated_at = NOW()
            WHERE user_id = ? AND task_id = ?
        ";

        // Prepare and execute the query
        $statement = $pdo->prepare($query);
        $statement->execute([$taskStatus, $userId, $taskId]);

        // Check if any row was updated
        if ($statement->rowCount() > 0) {
            response(200, null, 'Task status updated successfully!');
        } else {
            response(404, null, 'No such task found for this user.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only PUT requests are allowed.');
}
?>
