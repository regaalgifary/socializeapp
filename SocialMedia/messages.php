<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_username'] ?? 'User';

$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT username, email, profile_pic, banner_pic, description FROM users WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();
$message_stmt = $mysqli->prepare("
    SELECT m1.sender_id, u.username, m1.message, m1.created_at 
    FROM messages m1
    INNER JOIN (
        SELECT sender_id, MAX(created_at) as latest 
        FROM messages 
        WHERE receiver_id = ? 
        GROUP BY sender_id
    ) m2 ON m1.sender_id = m2.sender_id AND m1.created_at = m2.latest
    INNER JOIN users u ON u.id_user = m1.sender_id
    ORDER BY m1.created_at DESC
");
$message_stmt->bind_param("i", $user_id);
$message_stmt->execute();
$inbox_result = $message_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Messages - Socialize</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    @media (min-width: 768px){
      .sidebar-left {
        position: fixed;
        top: 0;
        bottom: 0;
        overflow-y: auto;
        width: 270px;
        z-index: 1050;
      }
      .content-main {
        margin-left: 270px;
      }
    }
    @media (min-width: 1200px){
      .sidebar-right {
        position: fixed;
        top: 0;
        bottom: 0;
        right: 0;
        overflow-y: auto;
        width: 300px;
      }
      .content-main {
        margin-right: 300px;
      }
    }
    .content-main {
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
      padding: 1rem;
    }
  </style>
</head>
<!-- Modal: New Chat -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newChatModalLabel">Start New Chat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="searchUser" class="form-control mb-3" placeholder="Search by username...">
        <ul id="searchResults" class="list-group small"></ul>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('searchUser').addEventListener('input', function() {
  const keyword = this.value;
  if (keyword.length < 2) {
    document.getElementById('searchResults').innerHTML = '';
    return;
  }

  fetch('search_user.php?keyword=' + encodeURIComponent(keyword))
    .then(response => response.json())
    .then(data => {
      const list = data.map(user =>
        `<li class="list-group-item d-flex justify-content-between align-items-center">
          ${user.username}
          <a href="chat.php?user_id=${user.id_user}" class="btn btn-sm btn-outline-primary">Chat</a>
        </li>`
      ).join('');
      document.getElementById('searchResults').innerHTML = list || '<li class="list-group-item text-muted">No user found.</li>';
    });
});
</script>

<body class="bg-light">
<div class="d-flex">

  <!-- Offcanvas Sidebar (Mobile) -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title text-primary">Socialize</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
      <aside class="bg-white border-end p-3" style="width: 270px; height: 100%;">
        <div class="mb-4 position-relative">
          <form action="search_user.php" method="GET" class="position-relative mb-4">
            <input type="text" name="q" class="form-control ps-5 rounded-pill" placeholder="Search users..." required />
            <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
          </form>
          <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
        </div>
        <nav class="nav flex-column">
          <a href="index.php" class="nav-link text-dark mb-2"><i class="fa fa-home me-2 text-primary"></i>Home</a>
          <a href="profile.php" class="nav-link text-dark mb-2"><i class="fa fa-user me-2 text-primary"></i>Profile</a>
          <a href="notification.php" class="nav-link text-dark mb-2"><i class="fa fa-bell me-2 text-primary"></i>Notifications</a>
          <a href="messages.php" class="nav-link text-dark mb-2"><i class="fa fa-envelope me-2 text-primary"></i>Messages</a>
          <a href="logout.php" class="nav-link text-danger mt-4"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
        </nav>
      </aside>
    </div>
  </div>

  <!-- Sidebar Left (Desktop) -->
  <aside class="bg-white border-end p-3 d-none d-md-block sidebar-left">
    <h1 class="text-primary fs-4 mb-4">Socialize</h1>
    <div class="mb-4 position-relative">
      <form action="search_user.php" method="GET" class="position-relative mb-4">
        <input type="text" name="q" class="form-control ps-5 rounded-pill" placeholder="Search users..." required />
        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
      </form>
      <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
    </div>
    <nav class="nav flex-column">
      <a href="index.php" class="nav-link text-dark mb-2"><i class="fa fa-home me-2 text-primary"></i>Home</a>
      <a href="profile.php" class="nav-link text-dark mb-2"><i class="fa fa-user me-2 text-primary"></i>Profile</a>
      <a href="notification.php" class="nav-link text-dark mb-2"><i class="fa fa-bell me-2 text-primary"></i>Notifications</a>
      <a href="messages.php" class="nav-link text-dark mb-2"><i class="fa fa-envelope me-2 text-primary"></i>Messages</a>
      <a href="logout.php" class="nav-link text-danger mt-4"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-fill content-main">
    <!-- Mobile menu toggle -->
    <div class="d-md-none mb-3">
      <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
        <i class="fa fa-bars"></i> Menu
      </button>
    </div>

    <div class="text-center mb-3">
      <img src="gambar/sosial.png" alt="Socialize Logo" class="img-fluid" style="width: 50px; height: 50px;" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Messages</h1>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
            <i class="fa fa-plus"></i> Chat
        </button>
    </div>
    <p class="text-muted">This is where your conversations will appear. Feature coming soon!</p>
    <div class="bg-white rounded shadow-sm p-3">
    <?php if ($inbox_result->num_rows > 0): ?>
        <?php while ($row = $inbox_result->fetch_assoc()): ?>
        <a href="chat.php?user_id=<?= $row['sender_id'] ?>" class="text-decoration-none text-dark">
            <div class="d-flex justify-content-between align-items-start border-bottom py-2">
            <div>
                <div class="fw-semibold"><?= htmlspecialchars($row['username']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars(mb_strimwidth($row['message'], 0, 40, '...')) ?></div>
            </div>
            <div class="text-muted small"><?= date("H:i", strtotime($row['created_at'])) ?></div>
            </div>
        </a>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center text-muted mb-0">No messages yet.</p>
  <?php endif; ?>
</div>

  </main>

  <!-- Sidebar Right -->
  <aside class="bg-white border-start p-3 d-none d-xl-block sidebar-right">
    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
      <img src="<?= !empty($user_data['profile_pic']) ? htmlspecialchars($user_data['profile_pic']) : 'gambar/profile_pic.jpg' ?>" alt="User Avatar" class="rounded-circle me-2" width="40" height="40" />
      <div>
        <div class="fw-semibold"><?= htmlspecialchars($user_data['username']) ?></div>
        <div class="text-muted small">@<?= htmlspecialchars($user_data['username']) ?></div>
      </div>
    </div>
    <h6>About</h6>
    <p class="small text-muted"><?= nl2br(htmlspecialchars($user_data['description'] ?? 'No description')) ?></p>
    <hr />
    <h6>Followers you know</h6>
    <ul class="list-unstyled small">
      <li><a href="#">Alice</a></li>
      <li><a href="#">Bob</a></li>
      <li><a href="#">Charlie</a></li>
    </ul>
  </aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
