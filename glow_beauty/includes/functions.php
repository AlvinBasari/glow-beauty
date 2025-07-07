<?php
// Sertakan file koneksi database
require_once 'db.php';

// Fungsi untuk memeriksa apakah user sudah login
function is_user_loggedin() {
 // Memulai session jika belum dimulai
 if (session_status() == PHP_SESSION_NONE) {
 session_start();
 }
 // Periksa apakah session 'user_id' ada dan valid
 return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// Fungsi untuk memeriksa apakah user yang login adalah admin
function is_admin() {
 // Memulai session jika belum dimulai
 if (session_status() == PHP_SESSION_NONE) {
 session_start();
 }
 // Periksa apakah session 'is_admin' ada dan bernilai true;
 return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Fungsi untuk mengarahkan user ke halaman login jika belum login
function require_user_login() {
 if (!is_user_loggedin()) {
 // Simpan halaman saat ini sebelum mengarahkan ke login (opsional, untuk mengarahkan kembali setelah login)
 $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
 header('Location: login.php');
 exit();
 }
}

// Fungsi untuk mengarahkan user ke halaman admin jika bukan admin
function require_admin() {
 if (!is_admin()) {
 // Arahkan ke halaman user biasa atau tampilkan pesan error
 header('Location: ../index.php'); // Disesuaikan untuk folder admin
 exit();
 }
}

// --- Fungsi-fungsi Produk ---
function get_products() {
 global $conn;
 $sql = "SELECT * FROM products ORDER BY product_id DESC";
 $result = $conn->query($sql);
 $products = [];
 if ($result->num_rows > 0) {
 while($row = $result->fetch_assoc()) {
 $products[] = $row;
 }
 }
 return $products;
}

function get_product_detail($product_id) {
 global $conn;
 $product_id = (int)$product_id;
 $sql = "SELECT * FROM products WHERE product_id = ?";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("i", $product_id);
 if ($stmt->execute()) {
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
 return $result->fetch_assoc();
 }
 }
 $stmt->close();
 }
 return null;
}

// --- Fungsi-fungsi User ---
function get_user_data($user_id) {
 global $conn;
 $user_id = (int)$user_id;
 $sql = "SELECT user_id, username, email, full_name, address, phone_number FROM users WHERE user_id = ?";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("i", $user_id);
 if ($stmt->execute()) {
 $result = $stmt->get_result();
 if ($result->num_rows == 1) {
 return $result->fetch_assoc();
 }
 }
 $stmt->close();
 }
 return null;
}

// --- Fungsi-fungsi Pesanan ---
function create_order($user_id, $cart_items, $total_amount) {
 global $conn;
 $conn->begin_transaction();
 try {
 $sql_order = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
 $stmt_order = $conn->prepare($sql_order);
 $stmt_order->bind_param("id", $user_id, $total_amount);
 $stmt_order->execute();
 $order_id = $conn->insert_id;

 $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
 $stmt_item = $conn->prepare($sql_item);
 $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
 $stmt_update_stock = $conn->prepare($sql_update_stock);

 foreach ($cart_items as $item) {
 $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
 $stmt_item->execute();
 $stmt_update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
 $stmt_update_stock->execute();
 }
 
 $conn->commit();
 $stmt_order->close();
 $stmt_item->close();
 $stmt_update_stock->close();
 return $order_id;
 } catch (Exception $e) {
 $conn->rollback();
 return false;
 }
}

function get_user_orders($user_id) {
 global $conn;
 $user_id = (int)$user_id;
 $orders = [];
 $sql = "SELECT order_id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("i", $user_id);
 if ($stmt->execute()) {
 $result = $stmt->get_result();
 while ($row = $result->fetch_assoc()) {
 $orders[] = $row;
 }
 }
 $stmt->close();
 }
 return $orders;
}

// Fungsi untuk mendapatkan semua pesanan (untuk admin)
function get_all_orders() {
 global $conn;
 $orders = [];
 // Menggabungkan (JOIN) tabel orders dan users untuk mendapatkan nama pelanggan
 $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status, u.username, u.full_name
 FROM orders o
 JOIN users u ON o.user_id = u.user_id
 ORDER BY o.order_date DESC";
 $result = $conn->query($sql);
 if ($result && $result->num_rows > 0) {
 while ($row = $result->fetch_assoc()) {
 $orders[] = $row;
 }
 }
 return $orders;
}

// Fungsi untuk mendapatkan detail pesanan lengkap untuk admin
function get_order_details_for_admin($order_id) {
 global $conn;
 $order_id = (int)$order_id;
 $order_details = null;

 // 1. Ambil detail pesanan dan info user
 $sql_order = "SELECT o.*, u.username, u.full_name, u.email, u.address, u.phone_number
 FROM orders o
 JOIN users u ON o.user_id = u.user_id
 WHERE o.order_id = ?";
 if ($stmt_order = $conn->prepare($sql_order)) {
 $stmt_order->bind_param("i", $order_id);
 if ($stmt_order->execute()) {
 $result_order = $stmt_order->get_result();
 if ($result_order->num_rows == 1) {
 $order_details = $result_order->fetch_assoc();
 }
 }
 $stmt_order->close();
 }

 // Jika pesanan tidak ditemukan, kembalikan null
 if (!$order_details) {
 return null;
 }

 // 2. Ambil item-item dalam pesanan tersebut
 $sql_items = "SELECT oi.*, p.name AS product_name, p.image AS product_image
 FROM order_items oi
 JOIN products p ON oi.product_id = p.product_id
 WHERE oi.order_id = ?";
 $order_details['items'] = [];
 if ($stmt_items = $conn->prepare($sql_items)) {
 $stmt_items->bind_param("i", $order_id);
 if ($stmt_items->execute()) {
 $result_items = $stmt_items->get_result();
 while ($row_item = $result_items->fetch_assoc()) {
 $order_details['items'][] = $row_item;
 }
 }
 $stmt_items->close();
 }

 return $order_details;
}
