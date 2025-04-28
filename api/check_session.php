<?php
session_start();
require_once("../utils.php");

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    response(200, $user, "Welcome, " . htmlspecialchars($user['first_name']));
} else {
    response(500, null, "Not logged in.");
}