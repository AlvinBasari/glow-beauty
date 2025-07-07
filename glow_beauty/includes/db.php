<?php


// Konfigurasi Database
define('DB_SERVER', 'localhost'); // Ganti dengan host database Anda
define('DB_USERNAME', 'root');     // Ganti dengan username database Anda
define('DB_PASSWORD', '');         // Ganti dengan password database Anda
define('DB_NAME', 'glow_beauty');  // Ganti dengan nama database Anda

// Membuat koneksi database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Mengatur charset ke utf8 (opsional, tapi disarankan)
$conn->set_charset("utf8");
?>