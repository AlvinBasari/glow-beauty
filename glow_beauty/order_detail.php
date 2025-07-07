<?php
require_once 'includes/functions.php';

// Memastikan user sudah login
require_user_login();

// Ambil ID pesanan dari parameter URL
$order_id = $_GET['id'] ?? null;

// Ambil ID user dari sesi
$user_id = $_SESSION['user_id'];

if (empty($order_id)) {
 // Jika tidak ada ID pesanan, arahkan kembali ke riwayat pesanan
 header('Location: order_history.php');
 exit();
}

// Ambil detail pesanan menggunakan fungsi yang baru dibuat, memastikan hanya pesanan user ini yang diambil
$order_details = get_user_order_details($order_id, $user_id);

// Jika pesanan tidak ditemukan atau bukan milik user
if (!$order_details) {
 echo "Pesanan tidak ditemukan atau Anda tidak memiliki izin untuk melihat detail pesanan ini.";
 // Anda bisa mengarahkan kembali ke order_history.php atau menampilkan halaman error 404
 exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Detail Pesanan #<?php echo htmlspecialchars($order_details['order_id']); ?></title>
 <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

 <header>
 <h1>Glow Beauty</h1>
 <nav>
 <a href="index.php">Beranda</a>
 <a href="cart.php">Keranjang</a>
 <a href="profile.php">Profil</a>
 <a href="logout.php">Logout</a>
 <?php if (is_admin()): ?>
 <a href="admin/index.php">Admin Dashboard</a>
 <?php endif; ?>
 </nav>
 </header>

 <main>
 <h2>Detail Pesanan #<?php echo htmlspecialchars($order_details['order_id']); ?></h2>

 <div class="order-detail-section">
 <h3>Informasi Pesanan</h3>
 <p><strong>Tanggal Pesanan:</strong> <?php echo date("d M Y H:i", strtotime($order_details['order_date'])); ?></p>
 <p><strong>Total Jumlah:</strong> Rp <?php echo number_format($order_details['total_amount'], 2, ',', '.'); ?></p>
 <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order_details['status'])); ?></p>
 </div>

 <div class="order-detail-section">
 <h3>Informasi Pengiriman</h3>
 <p><strong>Nama:</strong> <?php echo htmlspecialchars($order_details['full_name']); ?></p>
 <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($order_details['address'])); ?></p>
 <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($order_details['phone_number']); ?></p>
 </div>

 <div class="order-detail-section">
 <h3>Item Pesanan</h3>
 <table>
 <thead>
 <tr>
 <th>Gambar</th>
 <th>Nama Produk</th>
 <th>Kuantitas</th>
 <th>Harga Satuan</th>
 <th>Subtotal</th>
 </tr>
 </thead>
 <tbody>
 <?php if (!empty($order_details['items'])): ?>
 <?php foreach ($order_details['items'] as $item): ?>
 <tr>
 <td><img src="assets/images/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" width="50"></td>
 <td><?php echo htmlspecialchars($item['product_name']); ?></td>
 <td><?php echo htmlspecialchars($item['quantity']); ?></td>
 <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
 <td>Rp <?php echo number_format($item['quantity'] * $item['price'], 2, ',', '.'); ?></td>
 </tr>
 <?php endforeach; ?>
 <?php else: ?>
 <tr>
 <td colspan="5">Tidak ada item dalam pesanan ini.</td>
 </tr>
 <?php endif; ?>
 </tbody>
 </table>
 </div>

 <p><a href="order_history.php">Kembali ke Riwayat Pesanan</a></p>

 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="assets/js/script.js"></script>

</body>
</html>
