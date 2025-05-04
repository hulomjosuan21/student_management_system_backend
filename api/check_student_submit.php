<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (!isset($_GET['task_id'])) {
            response(400, null, 'Missing required parameter: task_id');
            exit;
        }

        $task_id = $_GET['task_id'];

        $stmt = $pdo->prepare("
            SELECT 
                b.assigned_task_id, 
                b.task_id, 
                a.first_name, 
                a.last_name, 
                b.submitted_at, 
                b.task_status, 
                b.attachment_url,
                c.due_date
            FROM users AS a
            INNER JOIN assigned_tasks AS b ON a.user_id = b.assign_to
            INNER JOIN tasks AS c ON b.task_id = c.task_id
            WHERE b.task_id = :task_id
        ");

        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            response(200, $results, 'All assigned students fetched successfully!');
        } else {
            response(200, [], 'No assigned students for this task.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
