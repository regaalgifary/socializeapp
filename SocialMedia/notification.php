<!-- BAGIAN PHP tetap sama seperti sebelumnya -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: logout.php");
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

// Dummy notifications
$notifications = [
    ["text" => "Arif liked your post.", "time" => "2 minutes ago"],
    ["text" => "You have a new follower: Bob.", "time" => "10 minutes ago"],
    ["text" => "Charlie mentioned you in a comment.", "time" => "1 hour ago"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notifications - Socialize</title>
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
          <a href="?logout=1" class="nav-link text-danger mt-4"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
        </nav>
      </aside>
    </div>
  </div>

  <!-- Sidebar Kiri (Desktop) -->
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
      <a href="?logout=1" class="nav-link text-danger mt-4"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </nav>
  </aside>

  <!-- Konten Utama -->
  <main class="flex-fill content-main">
    <!-- Tombol menu di mobile -->
    <div class="d-md-none mb-3">
      <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
        <i class="fa fa-bars"></i> Menu
      </button>
    </div>

    <div class="text-center mb-3">
      <img src="gambar/sosial.png" alt="Socialize Logo" class="img-fluid" style="width: 50px; height: 50px;" />
    </div>

    <h1 class="mb-3">Notifications</h1>

    <?php if (count($notifications) === 0): ?>
      <div class="bg-white rounded shadow-sm p-3">
        <p class="text-center text-muted mb-0">No notifications.</p>
      </div>
    <?php else: ?>
      <?php foreach ($notifications as $notif): ?>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
          <p class="mb-1"><?= htmlspecialchars($notif['text']) ?></p>
          <small class="text-muted"><?= htmlspecialchars($notif['time']) ?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <!-- Sidebar Kanan (Desktop) -->
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
