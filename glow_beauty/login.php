<?php
require_once '/glow_beauty/includes/functions.php'; // Sertakan file fungsi

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

// Jika user sudah login, arahkan ke halaman utama atau halaman redirect_url
if (is_user_loggedin()) {
 $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/glow_beauty/index.php';
 unset($_SESSION['redirect_url']); // Hapus redirect_url setelah digunakan
 header('Location: ' . $redirect_url);
 exit();
}

$error = ''; // Variabel untuk menyimpan pesan error

// Proses form login jika ada data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $username_email = trim($_POST['username_email']);
 $password = trim($_POST['password']);

 // Validasi input dasar
 if (empty($username_email) || empty($password)) {
 $error = "Username/Email dan password harus diisi.";
 } else {
 // Hubungkan ke database
 global $conn;

 // Siapkan statement SQL untuk mencari user berdasarkan username atau email
 $sql = "SELECT user_id, username, password, is_admin FROM users WHERE username = ? OR email = ?";

 if ($stmt = $conn->prepare($sql)) {
 // Bind parameter ke statement
 $stmt->bind_param("ss", $param_username_email, $param_username_email); // Cari di username atau email

 $param_username_email = $username_email;

 // Jalankan statement
 if ($stmt->execute()) {
 // Simpan hasil
 $stmt->store_result();

 // Periksa apakah username/email ada
 if ($stmt->num_rows == 1) {
 // Bind hasil ke variabel
 $stmt->bind_result($user_id, $username, $hashed_password, $is_admin);
 if ($stmt->fetch()) {
 // Verifikasi password
 if (password_verify($password, $hashed_password)) {
 // Password benar, mulai session baru
 session_regenerate_id(true); // Regenerasi ID sesi untuk keamanan

 // Simpan data user di session
 $_SESSION['user_id'] = $user_id;
 $_SESSION['username'] = $username;
 $_SESSION['is_admin'] = (bool)$is_admin; // Pastikan is_admin adalah boolean

 // Update last_login di database (opsional)
 $update_login_sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
 if ($update_login_stmt = $conn->prepare($update_login_sql)) {
 $update_login_stmt->bind_param("i", $user_id);
 $update_login_stmt->execute();
 $update_login_stmt->close();
 }

 // Arahkan user ke halaman yang diminta sebelumnya atau halaman utama
 $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/glow_beauty/index.php';
 if ($_SESSION['is_admin']) {
 $redirect_url = '/glow_beauty/admin/index.php'; // Arahkan admin ke dashboard admin
 }
 unset($_SESSION['redirect_url']);
 header('Location: ' . $redirect_url);
 exit();

 } else {
 // Password salah
 $error = "Username/Email atau password salah.";
 }
 }
 } else {
 // Username/Email tidak ditemukan
 $error = "Username/Email atau password salah.";
 }
 } else {
 $error = "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
 }

 // Tutup statement
 $stmt->close();
 }
 }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Login</title>
 <link rel="stylesheet" href="/glow_beauty/assets/css/style.css"> <!-- Tautkan ke file CSS -->
</head>
<body>

 <header>
 <h1>Glow Beauty</h1>
 <nav>
 <a href="/glow_beauty/index.php">Beranda</a>
 <a href="/glow_beauty/cart.php">Keranjang</a>
 <?php if (is_user_loggedin()): ?>
 <a href="/glow_beauty/profile.php">Profil</a>
 <a href="/glow_beauty/logout.php">Logout</a>
 <?php else: ?>
 <a href="/glow_beauty/login.php">Login</a>
 <a href="/glow_beauty/register.php">Daftar</a>
 <?php endif; ?>
 <?php if (is_admin()): ?>
 <a href="/glow_beauty/admin/index.php">Admin Dashboard</a>
 <?php endif; ?>
 </nav>
 </header>

 <main>
 <h2>Login User atau Admin</h2>

 <?php if (!empty($error)): ?>
 <div class="error-message"><?php echo $error; ?></div>
 <?php endif; ?>

 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
 <div>
 <label for="username_email">Username atau Email:</label>
 <input type="text" id="username_email" name="username_email" required value="<?php echo isset($username_email) ? htmlspecialchars($username_email) : ''; ?>">
 </div>
 <div>
 <label for="password">Password:</label>
 <input type="password" id="password" name="password" required>
 </div>
 <div>
 <button type="submit">Login</button>
 </div>
 </form>

 <p>Belum punya akun? <a href="/glow_beauty/register.php">Daftar di sini</a>.</p>

 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="/glow_beauty/assets/js/script.js"></script> <!-- Tautkan ke file JavaScript -->

</body>
</html>
<?php

// This is an empty PHP file.

?>