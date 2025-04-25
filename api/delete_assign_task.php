<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    try {
        if (!isset($_GET['user_id'])) {
            response(400, null, 'Missing required parameter: user_id');
            exit;
        }

        $userId = $_GET['user_id'];

        $query = "DELETE FROM user_tasks WHERE user_id = ?";
        $statement = $pdo->prepare($query);
        $statement->execute([$userId]);

        if ($statement->rowCount() > 0) {
            response(200, null, 'All task assignments deleted for this user.');
        } else {
            response(404, null, 'No task assignments found for this user.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only DELETE requests are allowed.');
}
?>
