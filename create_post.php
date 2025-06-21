<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$content = trim($_POST['content']);
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageTmpPath = $_FILES['image']['tmp_name'];
    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . $imageName;

    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($imageTmpPath, $targetFilePath)) {
            $imagePath = $targetFilePath;
        }
    }
}

if (!empty($content)) {
    $stmt = $mysqli->prepare("INSERT INTO post (id_user, content, image, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $_SESSION['user_id'], $content, $imagePath);
    $stmt->execute();
    $stmt->close();
}

$mysqli->close();
header("Location: socialize.php");
exit();
