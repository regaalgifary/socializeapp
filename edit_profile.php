<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

$username = $_POST['username'];
$description = $_POST['description'] ?? null;

$profile_pic = null;
$banner_pic = null;

// Handle upload profile_pic
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $filename = "profile_" . $user_id . "_" . time() . "." . $ext;
    $target = "uploads/" . $filename;
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
        $profile_pic = $target;
    }
}

// Handle upload banner_pic
if (isset($_FILES['banner_pic']) && $_FILES['banner_pic']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['banner_pic']['name'], PATHINFO_EXTENSION);
    $filename = "banner_" . $user_id . "_" . time() . "." . $ext;
    $target = "uploads/" . $filename;
    if (move_uploaded_file($_FILES['banner_pic']['tmp_name'], $target)) {
        $banner_pic = $target;
    }
}

// Build query
$sql = "UPDATE users SET username = ?, description = ?";
$params = [$username, $description];
$types = "ss";

if ($profile_pic) {
    $sql .= ", profile_pic = ?";
    $params[] = $profile_pic;
    $types .= "s";
}
if ($banner_pic) {
    $sql .= ", banner_pic = ?";
    $params[] = $banner_pic;
    $types .= "s";
}

$sql .= " WHERE id_user = ?";
$params[] = $user_id;
$types .= "i";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->close();

header("Location: profile.php");
exit();
?>
