<?php
// Sertakan file koneksi database
require_once 'db.php';

// Fungsi untuk memeriksa apakah user sudah login
function is_user_loggedin()
{
    // Memulai session jika belum dimulai
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Periksa apakah session 'user_id' ada dan valid
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// Fungsi untuk memeriksa apakah user yang login adalah admin
function is_admin()
{
    // Memulai session jika belum dimulai
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Periksa apakah session 'is_admin' ada dan bernilai true;
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Fungsi untuk mengarahkan user ke halaman login jika belum login
function require_user_login()
{
    if (!is_user_loggedin()) {
        // Simpan halaman saat ini sebelum mengarahkan ke login (opsional, untuk mengarahkan kembali setelah login)
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

// Fungsi untuk mengarahkan user ke halaman admin jika bukan admin
function require_admin()
{
    if (!is_admin()) {
        // Arahkan ke halaman user biasa atau tampilkan pesan error
        header('Location: ../index.php'); // Disesuaikan untuk folder admin
        exit();
    }
}

// Fungsi untuk mendapatkan konten dari tabel content
function get_content($page, $section)
{
    global $db;
    $page = mysqli_real_escape_string($db, $page);
    $section = mysqli_real_escape_string($db, $section);
    $query = "SELECT content FROM content WHERE page = '$page' AND section = '$section'";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['content'];
    } else {
        return false;
    }
}

// --- Fungsi-fungsi Produk ---
function get_products()
{
    global $db;
    $query = "SELECT * FROM products";
    $result = mysqli_query($db, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $products;
}

function get_product_detail($product_id)
{
    global $db;
    $product_id = (int)$product_id;
    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

// --- Fungsi-fungsi User ---
function get_user_data($user_id)
{
    global $db;
    $user_id = (int)$user_id;
    $query = "SELECT user_id, username, email, full_name, address, phone_number FROM users WHERE user_id = $user_id";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}
