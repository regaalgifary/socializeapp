<?php
// Konfigurasi koneksi database
$host     = "localhost";      // biasanya localhost
$username = "root";           // username MySQL kamu
$password = "";               // password MySQL kamu (kosong jika default di XAMPP)
$database = "socialize_db";   // nama database kamu

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika berhasil, kamu bisa lanjut menggunakan $conn di file lain
?>
