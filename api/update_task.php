<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $task_id = isset($_POST['task_id']) ? $_POST['task_id'] : null;

    if (empty($task_id)) {
        response(400, null, "Missing required field: task_id");
        exit;
    }

    $fieldsToUpdate = [];
    $params = [];

    if (!empty($_POST['title'])) {
        $fieldsToUpdate[] = "title = ?";
        $params[] = $_POST['title'];
    }

    if (!empty($_POST['description'])) {
        $fieldsToUpdate[] = "description = ?";
        $params[] = $_POST['description'];
    }

    if (!empty($_POST['due_date'])) {
        $fieldsToUpdate[] = "due_date = ?";
        $params[] = $_POST['due_date'];
    }

    if (empty($fieldsToUpdate)) {
        response(400, null, "No fields provided to update.");
        exit;
    }

    $query = "UPDATE tasks SET " . implode(", ", $fieldsToUpdate) . " WHERE task_id = ?";
    $params[] = $task_id;

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            response(200, null, "Task updated successfully.");
        } else {
            response(404, null, "Task not found or no changes made.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }
} else {
    response(405, null, "Only POST requests are allowed.");
}
