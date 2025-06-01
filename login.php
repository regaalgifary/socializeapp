<?php
session_start();
include 'database.php'; // Koneksi ke database

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cek apakah request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak diizinkan.");
}

// Ambil data dari form
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Email dan password wajib diisi.";
    $_SESSION['login_email'] = $email;
    header("Location: index.php");
    exit();
}

// Hash password dengan md5
$password_hashed = md5($password);

// Persiapkan query untuk mencari user berdasarkan email dan password
$query = "SELECT id_user, username, email FROM users WHERE email = ? AND password = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare statement gagal: " . $conn->error);
}

$stmt->bind_param("ss", $email, $password_hashed);
$stmt->execute();
$result = $stmt->get_result();

// Verifikasi hasil query
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Login sukses: set session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_email'] = $user['email'];

    // Bersihkan error sebelumnya
    unset($_SESSION['login_error'], $_SESSION['login_email']);

    // Arahkan ke halaman utama setelah login
    header("Location: socialize.php");
    exit();
} else {
    // Email atau password salah
    $_SESSION['login_error'] = "Email atau password salah.";
    $_SESSION['login_email'] = $email;
    header("Location: index.php");
    exit();
}
?>
