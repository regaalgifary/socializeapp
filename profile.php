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

// Ambil data user yang sedang login
$stmt = $mysqli->prepare("SELECT username, email, profile_pic, banner_pic, description FROM users WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();

// Ambil postingan user tersebut
$stmt = $mysqli->prepare("
    SELECT post.*, users.username
    FROM post
    JOIN users ON post.id_user = users.id_user
    WHERE post.id_user = ?
    ORDER BY post.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ambil jumlah like per post (semua post user ini)
$likes_count = [];
if ($posts) {
    $post_ids = array_column($posts, 'id_post');
    $ids_placeholder = implode(',', array_fill(0, count($post_ids), '?'));
    
    $likes_sql = "SELECT id_post, COUNT(*) as like_count FROM post_likes WHERE id_post IN ($ids_placeholder) GROUP BY id_post";
    $likes_stmt = $mysqli->prepare($likes_sql);
    // Bind param dinamis
    $likes_stmt->bind_param(str_repeat('i', count($post_ids)), ...$post_ids);
    $likes_stmt->execute();
    $likes_res = $likes_stmt->get_result();
    while ($row = $likes_res->fetch_assoc()) {
        $likes_count[$row['id_post']] = $row['like_count'];
    }
    $likes_stmt->close();
}

// Cek post yang sudah di-like user ini
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
<title>Profile - Socialize</title>
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
/* Banner responsif */
.profile-banner {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-top-left-radius: .375rem;
  border-top-right-radius: .375rem;
  position: relative;
  z-index: 1; /* Lebih rendah */
}

/* Profile picture di tengah card */
.profile-avatar {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border: 3px solid #fff;
  border-radius: 50%;
  margin-top: -50px;
  position: relative;
  z-index: 2; /* Lebih tinggi */
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
          <input type="text" class="form-control ps-5 rounded-pill" placeholder="Search..." />
          <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
        </div>
        <nav class="nav flex-column">
          <a href="index.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-home me-2 text-primary"></i><span>Home</span></a>
          <a href="profile.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-user me-2 text-primary"></i><span>Profile</span></a>
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span></a>
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span></a>
          <a href="logout.php" class="nav-link text-danger d-flex align-items-center mt-4"><i class="fa fa-sign-out-alt me-2"></i><span>Logout</span></a>
        </nav>
      </aside>
    </div>
  </div>

  <aside class="bg-white border-end p-3 d-none d-md-block sidebar-left" style="width: 270px">
    <h1 class="text-primary fs-4 mb-4">Socialize</h1>
    <div class="mb-4 position-relative">
      <input type="text" class="form-control ps-5 rounded-pill" placeholder="Search..." />
      <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"></i>
    </div>
    <nav class="nav flex-column">
      <a href="index.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-home me-2 text-primary"></i><span>Home</span></a>
      <a href="profile.php" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-user me-2 text-primary"></i><span>Profile</span></a>
      <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span></a>
      <a href="#" class="nav-link text-dark d-flex align-items-center mb-2"><i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span></a>
      <a href="logout.php" class="nav-link text-danger d-flex align-items-center mt-4"><i class="fa fa-sign-out-alt me-2"></i><span>Logout</span></a>
    </nav>
  </aside>

  <main class="flex-fill p-3 content-main" style="max-width: 700px">
  <!-- Mobile menu toggle -->
  <div class="d-md-none mb-3">
    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="fa fa-bars"></i> Menu
    </button>
  </div>

  <!-- Profile Header -->
  <div class="bg-white rounded shadow-sm mb-3">
    <div class="position-relative">
        <img src="<?= !empty($user_data['banner_pic']) ? htmlspecialchars($user_data['banner_pic']) : 'gambar/banner.jpg' ?>" alt="Banner" class="profile-banner">
    </div>      
    <div class="text-center mt-n5">
      <img src="<?= !empty($user_data['profile_pic']) ? htmlspecialchars($user_data['profile_pic']) : 'gambar/profile_pic.jpg
      ' ?>" alt="User Avatar" class="profile-avatar" />
      <h4 class="mt-2 mb-0"><?= htmlspecialchars($user_data['username']) ?></h4>
      <div class="text-muted mb-1">@<?= htmlspecialchars($user_data['username']) ?></div>
      <p class="small mb-2">Investigator in Miami City, Cinema Enthusiast</p>
      <div class="d-flex justify-content-center gap-3 mb-3">
        <span><strong>132</strong> Followers</span>
        <span><strong>121</strong> Following</span>
      </div>
      <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
    </div>
    <div class="border-top d-flex justify-content-around small">
      <a href="#" class="py-2 text-decoration-none border-bottom border-primary text-primary">Posts</a>
      <a href="#" class="py-2 text-decoration-none text-muted">Media</a>
      <a href="#" class="py-2 text-decoration-none text-muted">Likes</a>
      <a href="#" class="py-2 text-decoration-none text-muted">Replies</a>
    </div>
  </div>

  <!-- Posts List -->
  <?php if ($posts): ?>
    <?php foreach ($posts as $post): ?>
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <img src="<?= !empty($user_data['profile_pic']) ? htmlspecialchars($user_data['profile_pic']) : 'gambar/profile_pic.jpg' ?>" alt="User Avatar" class="rounded-circle me-2" width="40" height="40" />
            <div>
              <strong><?= htmlspecialchars($post['username']) ?></strong><br />
              <small class="text-muted"><?= date('d M Y H:i', strtotime($post['created_at'])) ?></small>
            </div>
          </div>
          <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
          <?php if (!empty($post['image'])): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="img-fluid rounded" />
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
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-center text-muted">No posts to show.</p>
  <?php endif; ?>
  </main>

  <aside class="bg-white border-start p-3 d-none d-xl-block sidebar-right" style="width: 300px">
    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
      <img src="<?= !empty($user_data['profile_pic']) ? htmlspecialchars($user_data['profile_pic']) : 'gambar/profile_pic.jpg' ?>" alt="User Avatar" class="rounded-circle me-2" width="40" height="40" />
      <div>
        <div class="fw-semibold"><?= htmlspecialchars($user_data['username']) ?></div>
        <div class="text-muted small">@<?= htmlspecialchars($user_data['username']) ?></div>
      </div>
    </div>
    <!-- Contoh konten sidebar kanan -->
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
<!-- Modal Edit Profile -->
<!-- Modal Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <!-- Username -->
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
          <label for="description" class="form-label">Deskripsi</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($user_data['description'] ?? '') ?></textarea>
        </div>

        <!-- Foto Profil -->
        <div class="mb-3">
          <label for="profile_pic" class="form-label">Foto Profil</label>
          <input class="form-control" type="file" id="profile_pic" name="profile_pic" accept="image/*">
          <small class="text-muted">Biarkan kosong jika tidak ingin mengubah.</small>
        </div>

        <!-- Banner Profil -->
        <div class="mb-3">
          <label for="banner_pic" class="form-label">Banner Profil</label>
          <input class="form-control" type="file" id="banner_pic" name="banner_pic" accept="image/*">
          <small class="text-muted">Biarkan kosong jika tidak ingin mengubah.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
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
</body>
</html>
