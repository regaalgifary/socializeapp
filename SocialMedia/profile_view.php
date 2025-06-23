<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || $_GET['id'] == $logged_in_user_id) {
    header("Location: profile.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$view_id = (int)$_GET['id'];

// Ambil data user yang dilihat
$stmt = $mysqli->prepare("SELECT username, email, profile_pic, banner_pic, description FROM users WHERE id_user = ?");
$stmt->bind_param("i", $view_id);
$stmt->execute();
$user_result = $stmt->get_result();
$view_user = $user_result->fetch_assoc();
$stmt->close();

if (!$view_user) {
    echo "User not found.";
    exit();
}

// Ambil post pengguna ini
$stmt = $mysqli->prepare("
    SELECT p.*, u.username, u.profile_pic 
    FROM post p 
    JOIN users u ON p.id_user = u.id_user 
    WHERE u.id_user = ? 
    ORDER BY p.created_at DESC
");
$stmt->bind_param("i", $view_id);
$stmt->execute();
$posts_res = $stmt->get_result();
$posts = $posts_res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Cek apakah user sudah follow
$follow_stmt = $mysqli->prepare("SELECT * FROM followers WHERE id_user = ? AND id_follower = ?");
$follow_stmt->bind_param("ii", $view_id, $logged_in_user_id);
$follow_stmt->execute();
$is_following = $follow_stmt->get_result()->num_rows > 0;
$follow_stmt->close();

// Jumlah followers & following
$followers = $mysqli->query("SELECT COUNT(*) as count FROM followers WHERE id_user = $view_id")->fetch_assoc()['count'];
$following = $mysqli->query("SELECT COUNT(*) as count FROM followers WHERE id_follower = $view_id")->fetch_assoc()['count'];

// Ambil like info
$likes_count = [];
$likes_res = $mysqli->query("SELECT id_post, COUNT(*) as like_count FROM post_likes GROUP BY id_post");
while ($row = $likes_res->fetch_assoc()) {
    $likes_count[$row['id_post']] = $row['like_count'];
}
$user_likes = [];
$user_like_res = $mysqli->query("SELECT id_post FROM post_likes WHERE id_user = $logged_in_user_id");
while ($row = $user_like_res->fetch_assoc()) {
    $user_likes[$row['id_post']] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($view_user['username']) ?> - Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
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
      padding: 1rem;
      max-width: calc(100% - 570px);
      margin-left: auto;
      margin-right: auto;
    }
    .profile-banner {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-top-left-radius: .375rem;
      border-top-right-radius: .375rem;
    }
    .profile-avatar {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid white;
      margin-top: -50px;
      position: relative;
      z-index: 2;
    }
  </style>
</head>
<body class="bg-light">
<div class="d-flex">

  <!-- Sidebar Kiri -->
  <aside class="bg-white border-end p-3 d-none d-md-block sidebar-left">
    <h1 class="text-primary fs-4 mb-4">Socialize</h1>
    <div class="mb-4 position-relative">
      <input type="text" class="form-control ps-5 rounded-pill" placeholder="Search..." />
      <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
    </div>
    <nav class="nav flex-column">
      <a href="index.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-home me-2 text-primary"></i><span>Home</span></a>
      <a href="profile.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-user me-2 text-primary"></i><span>Profile</span></a>
      <a href="notification.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span></a>
      <a href="messages.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span></a>
      <a href="logout.php" class="nav-link text-danger d-flex align-items-center mt-4"><i class="fa fa-sign-out-alt me-2"></i><span>Logout</span></a>
    </nav>
  </aside>

  <!-- Konten Utama -->
  <main class="flex-fill p-3 content-main" style="max-width: 700px">

    <!-- Banner dan Info Profil -->
    <div class="bg-white rounded shadow-sm mb-3">
      <div class="position-relative">
        <img src="<?= !empty($view_user['banner_pic']) ? htmlspecialchars($view_user['banner_pic']) : 'gambar/banner.jpg' ?>" class="profile-banner" alt="Banner">
      </div>
      <div class="text-center mt-n5">
        <img src="<?= !empty($view_user['profile_pic']) ? htmlspecialchars($view_user['profile_pic']) : 'gambar/profile_pic.jpg' ?>" class="profile-avatar" alt="Avatar" />
        <h4 class="mt-2 mb-0"><?= htmlspecialchars($view_user['username']) ?></h4>
        <div class="text-muted mb-1">@<?= htmlspecialchars($view_user['username']) ?></div>
        <p class="small mb-2"><?= htmlspecialchars($view_user['description'] ?? 'No description') ?></p>
        <div class="d-flex justify-content-center gap-3 mb-3">
          <span><strong><?= $followers ?></strong> Followers</span>
          <span><strong><?= $following ?></strong> Following</span>
        </div>
        <form method="POST" action="follow_action.php">
          <input type="hidden" name="target_id" value="<?= $view_id ?>">
          <button type="submit" class="btn btn-<?= $is_following ? 'outline-secondary' : 'outline-primary' ?> btn-sm mb-3">
            <?= $is_following ? 'Unfollow' : 'Follow' ?>
          </button>
        </form>
      </div>
      <div class="border-top d-flex justify-content-around small">
        <a href="#" class="py-2 text-decoration-none border-bottom border-primary text-primary">Posts</a>
        <a href="#" class="py-2 text-decoration-none text-muted">Media</a>
        <a href="#" class="py-2 text-decoration-none text-muted">Likes</a>
        <a href="#" class="py-2 text-decoration-none text-muted">Replies</a>
      </div>
    </div>

    <!-- Daftar Postingan -->
    <?php if ($posts): ?>
      <?php foreach ($posts as $post): ?>
        <div class="d-flex justify-content-center">
          <div class="card mb-3 w-100" style="max-width: 650px;">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <img src="<?= !empty($post['profile_pic']) ? htmlspecialchars($post['profile_pic']) : 'gambar/profile_pic.jpg' ?>" class="rounded-circle me-2" width="40" height="40" />
                <div>
                  <strong><?= htmlspecialchars($post['username']) ?></strong><br />
                  <small class="text-muted"><?= date('d M Y H:i', strtotime($post['created_at'])) ?></small>
                </div>
              </div>
              <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
              <?php if (!empty($post['image'])): ?>
                <img src="<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded" />
              <?php endif; ?>
              <div class="d-flex text-muted small mb-2 mt-2">
                <div class="me-3">
                  <a href="#" class="like-btn" data-postid="<?= $post['id_post'] ?>">
                    <i class="fa fa-thumbs-up me-1 <?= isset($user_likes[$post['id_post']]) ? 'text-primary' : '' ?>"></i>
                    <span class="like-count"><?= $likes_count[$post['id_post']] ?? 0 ?></span>
                  </a>
                </div>
                <div class="me-3"><i class="fa fa-comment me-1"></i>0</div>
                <div><i class="fa fa-share me-1"></i>0</div>
              </div>
              <div class="text-muted small"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">No posts to show.</p>
    <?php endif; ?>
  </main>

  <!-- Sidebar Kanan -->
  <aside class="bg-white border-start p-3 d-none d-xl-block sidebar-right">
    <h6>About</h6>
    <p class="small text-muted"><?= nl2br(htmlspecialchars($view_user['description'] ?? 'No description')) ?></p>
  </aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const postId = this.dataset.postid;
    fetch('like_post.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'post_id=' + postId
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const icon = this.querySelector('i.fa-thumbs-up');
        const countSpan = this.querySelector('.like-count');
        countSpan.textContent = data.like_count;
        icon.classList.toggle('text-primary');
      }
    });
  });
});
</script>
</body>
</html>
