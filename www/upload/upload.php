<?php
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']);

    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $filename)) {
        echo "Upload failed";
        exit;
    }

    $target_dir = __DIR__ . '/';

    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Upload failed";
        exit;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        echo "Upload failed";
        exit;
    }

    $mime = mime_content_type($file['tmp_name']);
    $allowed_mime = ['image/jpeg', 'image/png'];

    if (!in_array($mime, $allowed_mime)) {
        echo "Upload failed";
        exit;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $target_file = $target_dir . $filename . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        echo "File uploaded successfully";
    } else {
        echo "Upload failed";
    }
}
?>
