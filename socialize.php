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
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Socialize</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
  </head>
  <body class="bg-light">
    <div class="d-flex min-vh-100">

      <!-- Sidebar Offcanvas untuk mobile -->
      <div
        class="offcanvas offcanvas-start"
        tabindex="-1"
        id="offcanvasSidebar"
        aria-labelledby="offcanvasSidebarLabel"
      >
        <div class="offcanvas-header">
          <h5 class="offcanvas-title text-primary" id="offcanvasSidebarLabel">Socialize</h5>
          <button
            type="button"
            class="btn-close text-reset"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
          ></button>
        </div>
        <div class="offcanvas-body p-0">
          <aside class="bg-white border-end p-3" style="width: 270px; height: 100%;">
            <!-- Search box -->
            <div class="mb-4 position-relative">
              <input
                type="text"
                class="form-control ps-5 rounded-pill"
                placeholder="Search..."
                aria-label="Search"
              />
              <i
                class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"
                aria-hidden="true"
              ></i>
            </div>

            <!-- Navigation -->
            <nav class="nav flex-column">
              <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
                <i class="fa fa-home me-2 text-primary"></i><span>Home</span>
              </a>
              <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
                <i class="fa fa-user me-2 text-primary"></i><span>Profile</span>
              </a>
              <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
                <i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span>
              </a>
              <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
                <i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span>
              </a>
              <a href="?logout=1" class="nav-link text-danger d-flex align-items-center mt-4">
                <i class="fa fa-sign-out-alt me-2"></i><span>Logout</span>
              </a>
            </nav>
          </aside>
        </div>
      </div>

      <!-- Sidebar kiri (hanya tampil di md ke atas) -->
      <aside class="bg-white border-end p-3 d-none d-md-block" style="width: 270px">
        <h1 class="text-primary fs-4 mb-4">Socialize</h1>

        <div class="mb-4 position-relative">
          <input
            type="text"
            class="form-control ps-5 rounded-pill"
            placeholder="Search..."
            aria-label="Search"
          />
          <i
            class="fa fa-search position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"
            aria-hidden="true"
          ></i>
        </div>

        <nav class="nav flex-column">
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
            <i class="fa fa-home me-2 text-primary"></i><span>Home</span>
          </a>
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
            <i class="fa fa-user me-2 text-primary"></i><span>Profile</span>
          </a>
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
            <i class="fa fa-bell me-2 text-primary"></i><span>Notifications</span>
          </a>
          <a href="#" class="nav-link text-dark d-flex align-items-center mb-2">
            <i class="fa fa-envelope me-2 text-primary"></i><span>Messages</span>
          </a>
          <a href="?logout=1" class="nav-link text-danger d-flex align-items-center mt-4">
            <i class="fa fa-sign-out-alt me-2"></i><span>Logout</span>
          </a>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="flex-fill p-3 mx-auto" style="max-width: 700px">
        <!-- Tombol toggle sidebar hanya di md ke bawah -->
        <div class="d-md-none mb-3">
          <button
            class="btn btn-primary"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasSidebar"
            aria-controls="offcanvasSidebar"
          >
            <i class="fa fa-bars"></i> Menu
          </button>
        </div>

        <div class="text-center mb-3">
          <img
            src="gambar/sosial.png"
            alt="Socialize Logo"
            class="img-fluid"
            style="width: 50px; height: 50px"
          />
        </div>

        <h1 class="mb-3">Hello, <?= htmlspecialchars($user_name) ?>!</h1>
        <p>Selamat datang di Socialize, platform sosial media sederhana Anda.</p>

        <!-- Create Post -->
        <div class="bg-white p-3 rounded shadow-sm mb-3">
          <div class="d-flex align-items-center mb-2">
            <img
              src="avatar.jpg"
              alt="User Avatar"
              class="rounded-circle me-2"
              width="40"
              height="40"
            />
            <input
              type="text"
              class="form-control rounded-pill"
              placeholder="What's on your mind?"
              aria-label="Create a post"
            />
          </div>
          <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-primary btn-sm">Post</button>
            <div class="d-flex gap-3 text-secondary small">
              <span><i class="fa fa-image me-1"></i>Photo</span>
              <span><i class="fa fa-video me-1"></i>Video</span>
              <span><i class="fa fa-calendar me-1"></i>Event</span>
            </div>
          </div>
        </div>

        <!-- Sample Post -->
        <div class="bg-white p-3 rounded shadow-sm mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
              <img
                src="avatar.jpg"
                alt="User Avatar"
                class="rounded-circle me-2"
                width="40"
                height="40"
              />
              <div>
                <div class="fw-semibold">John Doe</div>
                <div class="text-muted small">@johndoe</div>
              </div>
            </div>
            <div class="text-secondary" style="cursor: pointer">
              <i class="fa fa-ellipsis-h" aria-label="Post options"></i>
            </div>
          </div>
          <p>
            Lorem ipsum dolor sit amet
            <span class="text-primary" style="cursor: pointer">#socialize</span>
          </p>
          <img
            src="post.jpg"
            alt="Post content image"
            class="img-fluid rounded mb-2"
          />
          <div class="d-flex text-muted small mb-2">
            <div class="me-3" style="cursor: pointer">
              <i class="fa fa-thumbs-up me-1"></i>24
            </div>
            <div class="me-3" style="cursor: pointer">
              <i class="fa fa-comment me-1"></i>10
            </div>
            <div style="cursor: pointer"><i class="fa fa-share me-1"></i>5</div>
          </div>
          <div class="text-muted small">2 hours ago</div>
        </div>
      </main>

      <!-- Right Sidebar (hidden on smaller than XL screens) -->
      <aside
        class="bg-white border-start p-3 d-none d-xl-block"
        style="width: 300px"
      >
        <!-- Profile Card -->
        <div class="d-flex align-items-center mb-3 border-bottom pb-3">
          <img
            src="avatar.jpg"
            alt="User Avatar"
            class="rounded-circle me-2"
            width="40"
            height="40"
          />
          <div>
            <div class="fw-semibold">Jane Smith</div>
            <div class="text-muted small">@janesmith</div>
          </div>
        </div>

        <!-- Recommendations -->
        <div class="mb-4">
          <h6>Who to follow</h6>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
              <img
                src="avatar.jpg"
                alt="User Avatar"
                class="rounded-circle me-2"
                width="30"
                height="30"
              />
              <div>
                <div class="fw-semibold small">Alice</div>
                <div class="text-muted small">@alice</div>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary">Follow</button>
          </div>
          <div
            class="text-primary small text-center"
            style="cursor: pointer"
            role="button"
            tabindex="0"
          >
            Show more
          </div>
        </div>

        <!-- Trending Section -->
        <div>
          <h6>Trending</h6>
          <div class="mb-3" style="cursor: pointer">
            <div class="fw-semibold small">#Technology</div>
            <div class="text-muted small">120K Tweets</div>
          </div>
          <div class="mb-3" style="cursor: pointer">
            <div class="fw-semibold small">#Sports</div>
            <div class="text-muted small">90K Tweets</div>
          </div>
          <div style="cursor: pointer">
            <div class="fw-semibold small">#Music</div>
            <div class="text-muted small">50K Tweets</div>
          </div>
        </div>
      </aside>
    </div>

    <!-- Floating Action Button -->
    <a
      href="#"
      class="btn btn-primary rounded-circle position-fixed"
      style="
        width: 45px;
        height: 45px;
        bottom: 20px;
        right: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
      "
      aria-label="Create new post"
    >
      <i class="fa fa-plus"></i>
    </a>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
