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

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['user_username'] ?? 'User';

// Ambil user yang diajak chat
$chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($chat_user_id === 0 || $chat_user_id == $current_user_id) {
    die("Invalid user");
}

// Ambil data user tujuan
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id_user = ?");
$stmt->bind_param("i", $chat_user_id);
$stmt->execute();
$chat_user_result = $stmt->get_result();
$chat_user = $chat_user_result->fetch_assoc();
$stmt->close();

if (!$chat_user) {
    die("User not found");
}

// Tangani kirim pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $current_user_id, $chat_user_id, $message);
    $stmt->execute();
    $stmt->close();

    // Redirect ulang agar tidak kirim ulang saat refresh
    header("Location: chat.php?user_id=" . $chat_user_id);
    exit();
}

// Ambil semua pesan
$stmt = $mysqli->prepare("
    SELECT sender_id, message, created_at
    FROM messages
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $current_user_id, $chat_user_id, $chat_user_id, $current_user_id);
$stmt->execute();
$messages_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat - <?= htmlspecialchars($chat_user['username']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .chat-bubble {
      padding: 0.5rem 1rem;
      border-radius: 1rem;
      max-width: 75%;
      margin-bottom: 0.5rem;
    }
    .sent {
      background-color: #d1e7dd;
      align-self: end;
    }
    .received {
      background-color: #e2e3e5;
      align-self: start;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Chat with <?= htmlspecialchars($chat_user['username']) ?></h4>
      <a href="messages.php" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div id="chatBox" class="bg-white rounded shadow-sm p-3 mb-3" style="height: 400px; overflow-y: auto; display: flex; flex-direction: column;"></div>


    <form method="POST" class="d-flex gap-2">
      <input type="text" name="message" class="form-control" placeholder="Type a message..." required />
      <button type="submit" class="btn btn-primary">Send</button>
    </form>
    <script>
    const chatBox = document.getElementById('chatBox');
        const chatUserId = <?= json_encode($chat_user_id) ?>;

    function loadMessages() {
        fetch('get_messages.php?user_id=' + chatUserId)
            .then(res => res.text())
            .then(html => {
            const shouldScroll = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50;
            chatBox.innerHTML = html;
            if (shouldScroll) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
    }

// Load pertama kali & auto refresh tiap 5 detik
loadMessages();
setInterval(loadMessages, 5000);
</script>

  </div>
</body>
</html>
