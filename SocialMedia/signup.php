<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username  = trim($_POST['username']); // ✅ diperbaiki
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak cocok!'); window.history.back();</script>";
        exit();
    }

    $hashed_password = md5($password);

    $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Akun berhasil dibuat! Silakan login.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mendaftar. Coba lagi.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>