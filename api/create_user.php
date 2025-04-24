<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : null;
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : null;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
    $course = isset($_POST['course']) ? $_POST['course'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $role = isset($_POST['role']) ? $_POST['role'] : null;

    $requiredFields = ['first_name', 'last_name', 'email', 'password', 'phone_number', 'gender', 'course', 'address', 'birthdate', 'role'];
    foreach ($requiredFields as $field) {
        if (empty($$field)) {
            throw new Exception("Missing required field: $field");
        }
    }

    $profileUrl = null;

    if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profileUrl = generateProfileImageName();
    }

    $query = "INSERT INTO users (first_name, last_name, email, password, phone_number, gender, course, address, birthdate, profile_url, role) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        
        $stmt->execute([
            $first_name,
            $last_name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $phone_number,
            $gender,
            $course,
            $address,
            $birthdate,
            $profileUrl,
            $role
        ]);

        if ($stmt->rowCount() > 0) {
            if ($profileUrl !== null) {
                saveProfileImage($profileUrl);
            }

            $lastInsertedId = $pdo->lastInsertId();
            $fetchQuery = "SELECT user_id, first_name, last_name, email, phone_number, gender, course, address, birthdate, profile_url, role, created_at FROM users WHERE user_id = ?";
            $fetchStmt = $pdo->prepare($fetchQuery);
            $fetchStmt->execute([$lastInsertedId]);
            $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

            response(200, $user, "User created successfully!");
        } else {
            throw new Exception("Failed to create user.");
        }
    } catch (PDOException $e) {
        response(500, null, $e->getMessage());
    }
}
else {
    response(405, null, "Only POST requests are allowed.");
}