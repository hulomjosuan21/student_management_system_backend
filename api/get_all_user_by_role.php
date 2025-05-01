<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try{
        $role = isset($_GET['role']) ? $_GET['role'] : 'user';

        $query = "SELECT user_id, first_name, last_name FROM users WHERE role = ? AND is_verified = 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$role]);
        $users = $stmt->fetchAll();

        if ($stmt->rowCount() > 0) {
            response(200, $users, "Users fetch successfully!.");
        } else {
            response(200, [], "No users found with the specified role.");
        }
    }catch (Exception $e) {
        response(400, null, $e->getMessage());
    } catch (PDOException $e) {
        response(500, null, "Database error: " . $e->getMessage());
    }
}