<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    http_response_code(500);
    exit("Database error");
}

$current_user_id = $_SESSION['user_id'];
$chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$stmt = $mysqli->prepare("
    SELECT sender_id, message, created_at
    FROM messages
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $current_user_id, $chat_user_id, $chat_user_id, $current_user_id);
$stmt->execute();
$res = $stmt->get_result();

while ($msg = $res->fetch_assoc()) {
    $isSender = $msg['sender_id'] == $current_user_id;
    $class = $isSender ? 'sent ms-auto' : 'received me-auto';
    $time = date("H:i, d M Y", strtotime($msg['created_at']));
    echo "<div class='chat-bubble $class'>";
    echo htmlspecialchars($msg['message']);
    echo "<div class='text-muted text-end mt-1' style='font-size: 0.75rem;'>$time</div>";
    echo "</div>";
}
