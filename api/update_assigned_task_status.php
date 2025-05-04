<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $assigned_task_id = isset($_POST['assigned_task_id']) ? $_POST['assigned_task_id'] : null;
        $task_status = isset($_POST['task_status']) ? $_POST['task_status'] : null;

        $requiredFields = ['assigned_task_id', 'task_status'];
        foreach ($requiredFields as $field) {
            if (empty($$field)) {
                throw new Exception("Missing required field: $field");
            }
        }

        $attachmentFileNameOrUrl = null;

        if (isset($_FILES['attachment_url']) && $_FILES['attachment_url']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = realpath(__DIR__ . '/../attachment');
            if (!$uploadDir) {
                throw new Exception("Upload directory does not exist.");
            }

            $originalName = basename($_FILES['attachment_url']['name']);
            $safeName = time() . "_" . preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $originalName);
            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

            if (!move_uploaded_file($_FILES['attachment_url']['tmp_name'], $targetPath)) {
                throw new Exception("Failed to upload file.");
            }

            $attachmentFileNameOrUrl = $safeName;
        } elseif (!empty($_POST['attachment_url'])) {
            $url = trim($_POST['attachment_url']);
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid URL format.");
            }
            $attachmentFileNameOrUrl = $url;
        }

        $query = "UPDATE assigned_tasks SET task_status = ?";
        $params = [$task_status];

        if ($task_status === 'submitted') {
            $query .= ", submitted_at = NOW()";
        }

        if ($attachmentFileNameOrUrl !== null) {
            $query .= ", attachment_url = ?";
            $params[] = $attachmentFileNameOrUrl;
        }

        $query .= " WHERE assigned_task_id = ?";
        $params[] = $assigned_task_id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            response(200, $assigned_task_id, "Assigned task updated successfully.");
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
