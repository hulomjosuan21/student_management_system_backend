<?php
require_once("utils.php");

$server_name = "localhost";
$username = "root";
$password = "";
$database = "final_db_sia";
$users_table = "users";
$tasks_table = "tasks";
$uploadDir = "../profiles/";

try {
    $pdo = new PDO("mysql:host={$server_name};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    response(500, null, $e->getMessage());
}

