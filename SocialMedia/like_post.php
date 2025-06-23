<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id'] ?? 0);
if ($post_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection error']);
    exit();
}

// Cek apakah user sudah like post
$stmt = $mysqli->prepare("SELECT id_like FROM post_likes WHERE id_user = ? AND id_post = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Sudah like, hapus (unlike)
    $stmt->close();
    $del = $mysqli->prepare("DELETE FROM post_likes WHERE id_user = ? AND id_post = ?");
    $del->bind_param("ii", $user_id, $post_id);
    $del->execute();
    $del->close();
} else {
    // Belum like, insert
    $stmt->close();
    $ins = $mysqli->prepare("INSERT INTO post_likes (id_post, id_user) VALUES (?, ?)");
    $ins->bind_param("ii", $post_id, $user_id);
    $ins->execute();
    $ins->close();
}

// Hitung jumlah like terbaru
$res = $mysqli->prepare("SELECT COUNT(*) FROM post_likes WHERE id_post = ?");
$res->bind_param("i", $post_id);
$res->execute();
$res->bind_result($like_count);
$res->fetch();
$res->close();

echo json_encode(['status' => 'success', 'like_count' => $like_count]);
?>
