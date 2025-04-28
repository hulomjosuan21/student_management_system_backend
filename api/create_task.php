<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $created_by = isset($_POST['created_by']) ? $_POST['created_by'] : null; // admin user_id
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    $requiredFields = ['created_by', 'title', 'description'];
    foreach ($requiredFields as $field) {
        if (empty($$field)) {
            response(400, null, "Missing required field: $field");
            exit;
        }
    }

    // // Optional: Check if the creator is an admin
    // $checkRole = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    // $checkRole->execute([$created_by]);
    // $user = $checkRole->fetch();
    
    // if (!$user || $user['role'] !== 'admin') {
    //     response(403, null, "Only admins can create tasks.");
    //     exit;
    // }

    $query = "INSERT INTO tasks (created_by, title, description) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$created_by, $title, $description]);

        if ($stmt->rowCount() > 0) {
            $task_id = $pdo->lastInsertId();
            response(200, ['task_id' => $task_id], "Task created successfully!");
        } else {
            throw new Exception("Failed to create task.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }
} else {
    response(405, null, "Only POST requests are allowed.");
}
