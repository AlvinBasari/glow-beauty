php
<?php
session_start(); // Memulai sesi

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi. Ini juga akan menghapus cookie sesi.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Akhiri sesi
session_destroy();

// Arahkan user ke halaman utama atau halaman login
header('Location: index.php'); // Bisa juga diubah ke 'login.php'
exit();
?>