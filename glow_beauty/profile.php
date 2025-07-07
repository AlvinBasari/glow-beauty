<?php
require_once 'includes/functions.php';

// Memastikan user sudah login
require_user_login();

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

global $conn;
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submission untuk update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // Ambil dan bersihkan input
 $full_name = trim($_POST['full_name']);
 $address = trim($_POST['address']);
 $phone_number = trim($_POST['phone_number']);

 // Validasi dasar
 if (empty($full_name)) {
 $error = "Nama lengkap tidak boleh kosong.";
 } else {
 // Update data di database
 $sql = "UPDATE users SET full_name = ?, address = ?, phone_number = ? WHERE user_id = ?";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("sssi", $full_name, $address, $phone_number, $user_id);

 if ($stmt->execute()) {
 $message = "Profil berhasil diperbarui.";
 } else {
 $error = "Gagal memperbarui profil. Silakan coba lagi.";
 }
 $stmt->close();
 } else {
 $error = "Terjadi kesalahan. Silakan coba lagi nanti.";
 }
 }
}

// Ambil data user terbaru untuk ditampilkan di form
$user = get_user_data($user_id);

// Jika user tidak ditemukan (misalnya, session tidak valid), logout
if (!$user) {
 header("Location: logout.php");
 exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Profil Saya</title>
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
 <h2>Profil Saya</h2>
 <p>Kelola informasi akun Anda.</p>

 <?php if (!empty($message)): ?>
 <div class="success-message"><?php echo $message; ?></div>
 <?php endif; ?>
 <?php if (!empty($error)): ?>
 <div class="error-message"><?php echo $error; ?></div>
 <?php endif; ?>

 <div class="profile-form">
 <form action="profile.php" method="post">
 <div>
 <label>Username:</label>
 <p><strong><?php echo htmlspecialchars($user['username']); ?></strong> (tidak dapat diubah)</p>
 </div>
 <div>
 <label>Email:</label>
 <p><strong><?php echo htmlspecialchars($user['email']); ?></strong> (tidak dapat diubah)</p>
 </div>
 <div>
 <label for="full_name">Nama Lengkap:</label>
 <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
 </div>
 <div>
 <label for="address">Alamat:</label>
 <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user['address']); ?></textarea>
 </div>
 <div>
 <label for="phone_number">Nomor Telepon:</label>
 <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
 </div>
 <div>
 <button type="submit">Update Profil</button>
 </div>
 </form>
 </div>
 
 <!-- Di sini bisa ditambahkan link ke riwayat pesanan -->
 <div class="order-history-link">
 <p><a href="order_history.php">Lihat Riwayat Pesanan</a></p>
 </div>
 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="assets/js/script.js"></script>

</body>
</html>
