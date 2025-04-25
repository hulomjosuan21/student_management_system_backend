<?php
require_once('db.php');

function response($status, $payload, $message, $redirect = false)
{
    header('Content-Type: application/json');
    http_response_code($status);

    $response = [
        'status' => $status,
        'message' => $message
    ];

    if (!is_null($payload)) {
        $response['payload'] = $payload;
    }

    if ($redirect !== false) {
        $response['redirect'] = $redirect;
    }

    echo json_encode($response);
    exit;
}

function saveProfileImage($fileName)
{
    global $uploadDir;

    $fileTmpPath = $_FILES['profile_url']['tmp_name'];
    $destination = $uploadDir . $fileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        throw new Exception("Failed to save uploaded image to destination path.");
    }
}

function generateProfileImageName()
{
    if (isset($_FILES['profile_url']) && $_FILES['profile_url']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['profile_url']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file format. Allowed formats: JPG, JPEG, PNG, GIF.");
        }

        return uniqid() . "." . $fileExtension;
    }

    return null;
}

define('ENCRYPTION_KEY', 'josuan');
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function encrypt_user_id($user_id) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    $encrypted = openssl_encrypt($user_id, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_user_id($encrypted_user_id) {
    $data = base64_decode($encrypted_user_id);
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}
