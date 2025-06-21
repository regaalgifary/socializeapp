<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "socialize_db");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

$search = trim($_GET['q'] ?? '');
$results = [];

if ($search !== '') {
    $stmt = $mysqli->prepare("SELECT id_user, username, profile_pic FROM users WHERE (username LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%')) AND id_user != ?");
    $stmt->bind_param("ssi", $search, $search, $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $results = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Search Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Search results for "<?= htmlspecialchars($search) ?>"</h3>
  <?php if (count($results) === 0): ?>
    <p>No users found.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($results as $user): ?>
        <a href="profile_view.php?id=<?= $user['id_user'] ?>" class="list-group-item d-flex align-items-center">
          <img src="<?= !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'gambar/profile_pic.jpg' ?>" class="rounded-circle me-3" width="40" height="40" />
          <strong><?= htmlspecialchars($user['username']) ?></strong>
        </a>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
</body>
</html>
