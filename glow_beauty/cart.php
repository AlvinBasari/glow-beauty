<?php
require_once 'includes/functions.php'; // Sertakan file fungsi

// --- Memastikan user sudah login ---
require_user_login(); // Fungsi ini akan mengarahkan ke login.php jika belum login

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

// Inisialisasi keranjang di session jika belum ada
if (!isset($_SESSION['cart'])) {
 $_SESSION['cart'] = array();
}

$message = ''; // Variabel untuk pesan sukses/error

// --- Menangani Aksi Keranjang (Tambah, Update, Hapus) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $action = $_POST['action'] ?? ''; // Ambil aksi dari POST
 $product_id = $_POST['product_id'] ?? 0;
 $quantity = $_POST['quantity'] ?? 1;

 // Validasi product_id dan quantity (minimal 1)
 $product_id = (int)$product_id;
 $quantity = (int)$quantity;
 if ($product_id <= 0) {
 // Redirect atau tampilkan error jika product_id tidak valid
 header('Location: cart.php'); // Arahkan kembali ke halaman keranjang
 exit();
 }

 // Ambil detail produk untuk memeriksa ketersediaan stok dan harga
 $product = get_product_detail($product_id);
 if (!$product) {
 // Produk tidak ditemukan, arahkan kembali atau tampilkan error
 header('Location: cart.php');
 exit();
 }


 switch ($action) {
 case 'add':
 // Tambah produk ke keranjang
 if (isset($_SESSION['cart'][$product_id])) {
 // Jika produk sudah ada, tambahkan kuantitasnya
 $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
 // Batasi kuantitas maksimum sesuai stok
 if ($new_quantity > $product['stock']) {
 $message = "Stok produk "". htmlspecialchars($product['name']) ."" hanya tersedia ". $product['stock'] .".";
 $new_quantity = $product['stock']; // Set kuantitas ke stok maksimum
 }
 $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;

 } else {
 // Jika produk belum ada, tambahkan item baru
 if ($quantity > $product['stock']) {
 $message = "Stok produk "". htmlspecialchars($product['name']) ."" hanya tersedia ". $product['stock'] .".";
 $quantity = $product['stock']; // Set kuantitas ke stok maksimum
 }
 if ($quantity > 0) {
 $_SESSION['cart'][$product_id] = [
 'product_id' => $product_id,
 'name' => $product['name'],
 'price' => $product['price'],
 'quantity' => $quantity,
 'image' => $product['image'] // Tambahkan info gambar jika perlu
 ];
 } else {
 $message = "Kuantitas harus minimal 1 untuk menambahkan ke keranjang.";
 }
 }
 // Redirect untuk mencegah pengiriman form ganda
 header('Location: cart.php');
 exit();
 break;

 case 'update':
 // Update kuantitas item di keranjang
 if (isset($_SESSION['cart'][$product_id])) {
 if ($quantity > $product['stock']) {
 $message = "Stok produk "". htmlspecialchars($_SESSION['cart'][$product_id]['name']) ."" hanya tersedia ". $product['stock'] .".";
 $quantity = $product['stock']; // Set kuantitas ke stok maksimum
 }
 if ($quantity > 0) {
 $_SESSION['cart'][$product_id]['quantity'] = $quantity;
 } else {
 // Jika kuantitas 0 atau kurang, hapus item
 unset($_SESSION['cart'][$product_id]);
 $message = "Produk "". htmlspecialchars($product['name']) ."" dihapus dari keranjang.";
 }
 }
 // Redirect untuk mencegah pengiriman form ganda
 header('Location: cart.php');
 exit();
 break;

 case 'remove':
 // Hapus item dari keranjang
 if (isset($_SESSION['cart'][$product_id])) {
 unset($_SESSION['cart'][$product_id]);
 $message = "Produk "". htmlspecialchars($product['name']) ."" dihapus dari keranjang.";
 }
 // Redirect untuk mencegah pengiriman form ganda
 header('Location: cart.php');
 exit();
 break;

 default:
 // Aksi tidak dikenali
 header('Location: cart.php');
 exit();
 break;
 }
}

// --- Ambil Detail Produk untuk item di Keranjang (Jika diperlukan) ---
// Jika Anda hanya menyimpan product_id dan quantity di sesi,
// Anda mungkin perlu mengambil detail nama, harga, gambar dari database di sini
// untuk ditampilkan. Contoh di atas sudah menyimpan nama, harga, gambar di sesi
// saat menambahkan produk, jadi langkah ini opsional jika data tsb sudah ada.
// Jika Anda ingin memastikan data produk di keranjang selalu up-to-date dengan database,
// Anda bisa mengambil detail produk di sini menggunakan ID produk dari sesi.

$cart_items_details = [];
$cart_total = 0;

if (!empty($_SESSION['cart'])) {
 foreach ($_SESSION['cart'] as $item) {
 // Dalam contoh ini, kita sudah menyimpan detail di sesi.
 // Jika Anda hanya menyimpan ID dan kuantitas, lakukan query database di sini.
 $cart_items_details[] = $item;
 $cart_total += $item['price'] * $item['quantity'];
 }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Keranjang Belanja</title>
 <link rel="stylesheet" href="assets/css/style.css"> <!-- Tautkan ke file CSS -->
</head>
<body>

 <header>
 <h1>Glow Beauty</h1>
 <nav>
 <a href="index.php">Beranda</a>
 <a href="cart.php">Keranjang</a>
 <?php if (is_user_loggedin()): ?>
 <a href="profile.php">Profil</a>
 <a href="logout.php">Logout</a>
 <?php else: ?>
 <a href="login.php">Login</a>
 <a href="register.php">Daftar</a>
 <?php endif; ?>
 <?php if (is_admin()): ?>
 <a href="admin/index.php">Admin Dashboard</a>
 <?php endif; ?>
 </nav>
 </header>

 <main>
 <h2>Keranjang Belanja Anda</h2>

 <?php if (!empty($message)): ?>
 <div class="info-message"><?php echo $message; ?></div>
 <?php endif; ?>

 <?php if (!empty($cart_items_details)): ?>
 <div class="cart-items">
 <?php foreach ($cart_items_details as $item): ?>
 <div class="cart-item">
 <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
 <h3><?php echo htmlspecialchars($item['name']); ?></h3>
 <p>Harga Satuan: Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></p>

 <form action="cart.php" method="post" style="display: inline-block;">
 <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
 <input type="hidden" name="action" value="update">
 <label for="quantity-<?php echo htmlspecialchars($item['product_id']); ?>">Kuantitas:</label>
 <input type="number" id="quantity-<?php echo htmlspecialchars($item['product_id']); ?>" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0" required>
 <button type="submit">Update</button>
 </form>

 <form action="cart.php" method="post" style="display: inline-block;">
 <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
 <input type="hidden" name="action" value="remove">
 <button type="submit">Hapus</button>
 </form>

 <p>Subtotal: Rp <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></p>
 </div>
 <?php endforeach; ?>
 </div>

 <div class="cart-summary">
 <h3>Total Keranjang: Rp <?php echo number_format($cart_total, 2, ',', '.'); ?></h3>
 <p><a href="checkout.php">Lanjutkan ke Checkout</a></p> <!-- Tautan ke halaman checkout -->
 </div>

 <?php else: ?>
 <p>Keranjang belanja Anda kosong.</p>
 <p><a href="index.php">Lanjutkan Belanja</a></p>
 <?php endif; ?>

 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="assets/js/script.js"></script> <!-- Tautkan ke file JavaScript -->

</body>
</html>
