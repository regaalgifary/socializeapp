<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_POST['target_id'])) {
    header("Location: login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "socialize_db");

$current_user = $_SESSION['user_id'];
$target_user = (int)$_POST['target_id'];

// Cek apakah sudah follow
$check = $mysqli->prepare("SELECT * FROM followers WHERE id_user = ? AND id_follower = ?");
$check->bind_param("ii", $target_user, $current_user);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Unfollow
    $stmt = $mysqli->prepare("DELETE FROM followers WHERE id_user = ? AND id_follower = ?");
} else {
    // Follow
    $stmt = $mysqli->prepare("INSERT INTO followers (id_user, id_follower) VALUES (?, ?)");
}
$stmt->bind_param("ii", $target_user, $current_user);
$stmt->execute();

header("Location: profile_view.php?id=$target_user");
exit();
