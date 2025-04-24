<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    $requiredFields = ['user_id', 'title', 'description'];
    foreach ($requiredFields as $field) {
        if (empty($$field)) {
            throw new Exception("Missing required field: $field");
        }
    }

    $query = "INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        
        $stmt->execute([
            $user_id,
            $title,
            $description
        ]);

        if ($stmt->rowCount() > 0) {
            response(200, null, "Task created successfully!");
        } else {
            throw new Exception("Failed to create task.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }
}
else {
    response(405, null, "Only POST requests are allowed.");
}