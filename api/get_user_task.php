<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (!isset($_GET['user_id'])) {
            response(400, null, 'Missing required parameter: user_id');
            exit;
        }
        if (!isset($_GET['task_status'])) {
            response(400, null, 'Missing required parameter: task_status');
            exit;
        }

        $user_id = $_GET['user_id'];
        $task_status = $_GET['task_status'];

        $stmt = $pdo->prepare("SELECT a.task_id, b.assigned_task_id, a.title, a.description, a.due_date, b.attachment_url, b.task_status FROM tasks AS a INNER JOIN assigned_tasks as b ON a.task_id = b.task_id WHERE b.assign_to = :user_id AND b.task_status = :task_status ORDER BY b.updated_at DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':task_status', $task_status, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            response(200, $results, 'User tasks fetched successfully!');
        } else {
            response(200, [], 'No tasks found for this user.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
