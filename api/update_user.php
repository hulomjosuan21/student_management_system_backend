<?php
session_start(); // REQUIRED to access $_SESSION

require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, null, "Only POST requests are allowed.");
}

$data = $_POST;
$userId = $data['user_id'] ?? null;

if (!$userId) {
    response(400, null, "Missing 'user_id' for update.");
}

// Fields we allow updating (excluding profile_url - handled separately)
$fieldsToUpdate = [
    'first_name', 'last_name', 'email', 'password', 'phone_number',
    'gender', 'course', 'address', 'birthdate', 'role'
];

$setParts = [];
$params = [];

// Collect non-empty fields to update
foreach ($fieldsToUpdate as $field) {
    if (isset($data[$field]) && $data[$field] !== '') {
        $setParts[] = "$field = ?";
        $params[] = ($field === 'password')
            ? password_hash($data[$field], PASSWORD_DEFAULT)
            : $data[$field];
    }
}

// Handle profile image upload
$profileUrl = null;
if (!empty($_FILES['profile_url']) && $_FILES['profile_url']['error'] === UPLOAD_ERR_OK) {
    $profileUrl = generateProfileImageName();
    $setParts[] = "profile_url = ?";
    $params[] = $profileUrl;
}

if (empty($setParts)) {
    response(400, null, "No valid fields provided for update.");
}

$params[] = $userId;
$updateQuery = "UPDATE users SET " . implode(", ", $setParts) . " WHERE user_id = ?";

try {
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        if ($profileUrl !== null) {
            saveProfileImage($profileUrl);
        }

        $fetchQuery = "SELECT * FROM users WHERE user_id = ?";
        $fetchStmt = $pdo->prepare($fetchQuery);
        $fetchStmt->execute([$userId]);
        $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        unset($user['password'], $user['verification_code']);

        $_SESSION['user'] = $user;

        response(200, $user, "User updated successfully.");
    } else {
        response(404, null, "User not found or no changes made.");
    }
} catch (PDOException $e) {
    response(500, null, $e->getMessage());
}
