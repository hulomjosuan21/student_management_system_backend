<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : null;
        $task_id = isset($_POST['task_id']) ? trim($_POST['task_id']) : null;

        $requiredFields = ['user_id', 'task_id'];
        foreach ($requiredFields as $field) {
            if (empty($$field)) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Use INSERT IGNORE to avoid duplicate (based on unique constraint)
        $query = "INSERT IGNORE INTO user_tasks (user_id, task_id) VALUES (?, ?)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $task_id]);

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
?>
