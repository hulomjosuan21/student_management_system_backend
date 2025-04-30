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

        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE created_by = :created_by");
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
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
?>
