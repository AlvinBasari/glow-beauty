<?php

require_once 'includes/functions.php'; // Sertakan file fungsi

// Mulai sesi (jika belum dimulai oleh functions.php)
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Katalog Produk</title>
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
 <a href="logout.php">Logout</a> <!-- Kita perlu membuat file logout.php nanti -->
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
 <h2>Katalog Produk</h2>

 <div class="product-list">
 <?php
 $products = get_products(); // Ambil daftar produk
 // Anda bisa menambahkan logika lain di sini, misalnya filter berdasarkan kategori
 ?>
 <?php if (!empty($products)): ?>
 <?php foreach ($products as $product): ?>
 <div class="product-item">
 <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
 <h3><?php echo htmlspecialchars($product['name']); ?></h3>
 <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p> <!-- Tampilkan deskripsi singkat -->
 <p>Harga: Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
 <a href="product_detail.php?id=<?php echo htmlspecialchars($product['product_id']); ?>">Lihat Detail</a>

 <?php if (is_user_loggedin()): ?>
 <!-- Tombol Tambah ke Keranjang - Akan memproses tambah ke keranjang jika user login -->
 <form action="cart.php" method="post">
 <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
 <input type="hidden" name="action" value="add">
 <button type="submit">Tambah ke Keranjang</button>
 </form>
 <?php else: ?>
 <!-- Arahkan ke halaman login jika user belum login -->
 <p><a href="login.php">Login untuk Tambah ke Keranjang</a></p>
 <?php endif; ?>
 </div>
 <?php endforeach; ?>
 <?php else: ?>
 <p>Belum ada produk tersedia.</p>
 <?php endif; ?>
 </div>
 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="assets/js/script.js"></script> <!-- Tautkan ke file JavaScript -->

</body>
</html>