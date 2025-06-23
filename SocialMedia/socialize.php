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
$user_name = $_SESSION['user_username'] ?? 'User';
$user_id = $_SESSION['user_id'];



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

$sql = "
    SELECT post.*, users.username
    FROM post
    JOIN users ON post.id_user = users.id_user
    ORDER BY post.created_at DESC
";
$result = $mysqli->query($sql);
$posts = $result->fetch_all(MYSQLI_ASSOC);
$sql = "
  SELECT 
    p.*, 
    u.username, 
    u.profile_pic
  FROM post p
  JOIN users u ON p.id_user = u.id_user
  ORDER BY p.created_at DESC
";



$result = $mysqli->query($sql);
$posts = $result->fetch_all(MYSQLI_ASSOC);

?>
<?php
// Query jumlah like per post, simpan dalam array keyed by post id
$likes_count = [];
$likes_res = $mysqli->query("SELECT id_post, COUNT(*) as like_count FROM post_likes GROUP BY id_post");
while ($row = $likes_res->fetch_assoc()) {
    $likes_count[$row['id_post']] = $row['like_count'];
}

// Untuk cek apakah user sudah like post, query post_likes untuk user ini
$user_id = $_SESSION['user_id'];
$user_likes = [];
$user_like_res = $mysqli->query("SELECT id_post FROM post_likes WHERE id_user = $user_id");
while ($row = $user_like_res->fetch_assoc()) {
    $user_likes[$row['id_post']] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Socialize</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
@media (min-width: 768px){
  .sidebar-left {
    position: fixed;
    top: 0;
    bottom: 0;
    overflow-y: auto;
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
  }
  .content-main {
    margin-right: 300px;
  }
}
.content-main {
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}
.content-main {
  max-width: calc(100% - 570px); 
  padding: 0 1rem;
}
</style>
</head>
<body class="bg-light">
<div class="d-flex">

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title text-primary" id="offcanvasSidebarLabel">Socialize</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
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
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-home me-2 text-primary"></i><span>Home</span></a>
          <a href="profile.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-user me-2 text-primary"></i><span>Profile</span></a>
          <a href="notification.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span></a>
          <a href="messages.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span></a>
          <a href="?logout=1" class="nav-link text-danger d-flex align-items-center mt-4"><i class="fa fa-sign-out-alt me-2"></i><span>Logout</span></a>
        </nav>
      </aside>
    </div>
  </div>

  <aside class="bg-white border-end p-3 d-none d-md-block sidebar-left" style="width: 270px">
    <h1 class="text-primary fs-4 mb-4">Socialize</h1>
    <div class="mb-4 position-relative">
      <form action="search_user.php" method="GET" class="position-relative mb-4">
        <input type="text" name="q" class="form-control ps-5 rounded-pill" placeholder="Search users..." required />
        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
      </form>
      <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
    </div>
    <nav class="nav flex-column">
      <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-home me-2 text-primary"></i><span>Home</span></a>
      <a href="profile.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-user me-2 text-primary"></i><span>Profile</span></a>
      <a href="notification.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span></a>
      <a href="messages.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span></a>
      <a href="?logout=1" class="nav-link text-danger d-flex align-items-center mt-4"><i class="fa fa-sign-out-alt me-2"></i><span>Logout</span></a>
    </nav>
  </aside>

  <main class="flex-fill p-3 content-main" style="max-width: 700px">
    <div class="d-md-none mb-3">
      <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
        <i class="fa fa-bars"></i> Menu
      </button>
    </div>

    <div class="text-center mb-3">
      <img src="gambar/sosial.png" alt="Socialize Logo" class="img-fluid" style="width: 50px; height: 50px" />
    </div>

    <h1 class="mb-3">Hello, <?= htmlspecialchars($user_name) ?>!</h1>
    <p>Selamat datang di Socialize, platform sosial media oleh kelompok 5 layanan web.</p>

    <?php foreach ($posts as $post): ?>
  <div class="bg-white p-3 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="d-flex align-items-center">
        <img src="<?= !empty($post['profile_pic']) ? htmlspecialchars($post['profile_pic']) : 'gambar/profile_pic.jpg' ?>" alt="User Avatar" class="rounded-circle me-2" width="40" height="40" />
        <div>
          <div class="fw-semibold"><?= htmlspecialchars($post['username']) ?></div>
          <div class="text-muted small">@<?= htmlspecialchars($post['username']) ?></div>
        </div>
      </div>
      <div class="text-secondary">
        <i class="fa fa-ellipsis-h"></i>
      </div>
    </div>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    <?php if (!empty($post['image'])): ?>
      <img src="<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded mb-2" />
    <?php endif; ?>
    <div class="d-flex text-muted small mb-2">
      <div class="me-3">
        <a href="#" class="like-btn" data-postid="<?= $post['id_post'] ?>">
          <i class="fa fa-thumbs-up me-1 <?= isset($user_likes[$post['id_post']]) ? 'text-primary' : '' ?>"></i>
          <span class="like-count"><?= $likes_count[$post['id_post']] ?? 0 ?></span>
        </a>
      </div>
      <div class="me-3"><i class="fa fa-comment me-1"></i>10</div>
      <div><i class="fa fa-share me-1"></i>5</div>
    </div>
    <div class="text-muted small"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></div>
  </div>
  <?php endforeach; ?>

  </main>

  <aside class="bg-white border-start p-3 d-none d-xl-block sidebar-right" style="width: 300px">
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

<button class="btn btn-primary rounded-circle position-fixed" style="width: 45px; height: 45px; bottom: 20px; right: 20px;" data-bs-toggle="modal" data-bs-target="#postModal">
  <i class="fa fa-plus"></i>
</button>

<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="create_post.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="postModalLabel">Create Post</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <textarea name="content" class="form-control mb-3" rows="3" placeholder="What's on your mind?" required></textarea>
          <input type="file" name="image" class="form-control" accept="image/*" />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Post</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
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
        if (icon.classList.contains('text-primary')) {
          icon.classList.remove('text-primary');
        } else {
          icon.classList.add('text-primary');
        }
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(err => alert('Error: ' + err));
  });
});
</script>

</html>
