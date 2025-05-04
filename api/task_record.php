<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (!isset($_GET['created_by'])) {
            response(400, null, 'Missing required parameter: created_by');
            exit;
        }

        $created_by = $_GET['created_by'];

        $stmt = $pdo->prepare("
            SELECT
                a.title,
                a.description,
                a.due_date,
                b.task_status,
                b.submitted_at,
                c.first_name,
                c.last_name 
            FROM tasks AS a
            INNER JOIN assigned_tasks AS b ON a.task_id = b.task_id
            INNER JOIN users AS c ON b.assign_to = c.user_id
            WHERE b.created_by = :created_by
            AND b.task_status IN ('complete', 'late') 
            ORDER BY b.updated_at DESC
        ");

        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            response(200, $results, 'User record tasks fetched successfully!');
        } else {
            response(200, [], 'No tasks found for this user.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
