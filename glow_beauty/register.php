<?php
require_once '/workspace/glow_beauty/includes/functions.php'; // Sertakan file fungsi

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

// Jika user sudah login, arahkan ke halaman utama
if (is_user_loggedin()) {
 header('Location: /workspace/glow_beauty/index.php');
 exit();
}

$username = $email = $full_name = '';
$username_err = $email_err = $password_err = $confirm_password_err = '';
$success_message = '';
$error = ''; // Tambahkan variabel error umum

// Proses form registrasi jika ada data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // Validasi Username
 if (empty(trim($_POST["username"]))) {
 $username_err = "Mohon masukkan username.";
 } else {
 // Periksa apakah username sudah ada di database
 global $conn;
 $sql = "SELECT user_id FROM users WHERE username = ?";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("s", $param_username);
 $param_username = trim($_POST["username"]);
 if ($stmt->execute()) {
 $stmt->store_result();
 if ($stmt->num_rows == 1) {
 $username_err = "Username ini sudah digunakan.";
 } else {
 $username = trim($_POST["username"]);
 }
 } else {
                $error = "Oops! Terjadi kesalahan saat memeriksa username. Silakan coba lagi nanti.";
 }
 $stmt->close();
 } else {
            $error = "Oops! Terjadi kesalahan database saat menyiapkan statement username.";
 }
 }

 // Validasi Email
 if (empty(trim($_POST["email"]))) {
 $email_err = "Mohon masukkan email.";
 } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
 $email_err = "Format email tidak valid.";
 }
 else {
 // Periksa apakah email sudah ada di database
 global $conn;
 $sql = "SELECT user_id FROM users WHERE email = ?";
 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("s", $param_email);
 $param_email = trim($_POST["email"]);
 if ($stmt->execute()) {
 $stmt->store_result();
 if ($stmt->num_rows == 1) {
 $email_err = "Email ini sudah terdaftar.";
 } else {
 $email = trim($_POST["email"]);
 }
 } else {
                $error = "Oops! Terjadi kesalahan saat memeriksa email. Silakan coba lagi nanti.";
 }
 $stmt->close();
 } else {
            $error = "Oops! Terjadi kesalahan database saat menyiapkan statement email.";
 }
 }

 // Validasi Password
 if (empty(trim($_POST["password"]))) {
 $password_err = "Mohon masukkan password.";
 } elseif (strlen(trim($_POST["password"])) < 6) { // Contoh: minimal 6 karakter
 $password_err = "Password minimal harus 6 karakter.";
 } else {
 $password = trim($_POST["password"]);
 }

 // Validasi Konfirmasi Password
 if (empty(trim($_POST["confirm_password"]))) {
 $confirm_password_err = "Mohon konfirmasi password.";
 } else {
 $confirm_password = trim($_POST["confirm_password"]);
 if (empty($password_err) && ($password != $confirm_password)) {
 $confirm_password_err = "Password tidak cocok.";
 }
 }

 // Validasi Nama Lengkap (Opsional, tergantung kebutuhan)
    // Tidak perlu validasi kosong jika opsional
 $full_name = trim($_POST["full_name"]);


 // Jika tidak ada error validasi, masukkan user baru ke database
 if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($error)) {
 global $conn;
 $sql = "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)";

 if ($stmt = $conn->prepare($sql)) {
 $stmt->bind_param("ssss", $param_username, $param_email, $param_password, $param_full_name);

 $param_username = $username;
 $param_email = $email;
 $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash password
 $param_full_name = $full_name;

 if ($stmt->execute()) {
 // Registrasi berhasil, bisa langsung login atau arahkan ke halaman login
 $success_message = "Registrasi berhasil! Anda sekarang bisa login.";
 // Opsional: Langsung login user setelah registrasi
 // $_SESSION['user_id'] = $conn->insert_id;
 // $_SESSION['username'] = $username;
 // $_SESSION['is_admin'] = false;
 // header('Location: /workspace/glow_beauty/index.php');
 // exit();

 // Reset form fields
 $username = $email = $full_name = '';

 } else {
 if ($conn->errno == 1062) { // MySQL error code for duplicate entry
 $error = "Username atau email sudah terdaftar.";
 } else {
 $error = "Terjadi kesalahan saat menyimpan data user.";
 }
 }

 $stmt->close();
 } else {
        $error = "Oops! Terjadi kesalahan database saat menyiapkan statement insert user.";
 }
 }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Glow Beauty - Registrasi</title>
 <link rel="stylesheet" href="/workspace/glow_beauty/assets/css/style.css"> <!-- Tautkan ke file CSS -->
</head>
<body>

 <header>
 <h1>Glow Beauty</h1>
 <nav>
 <a href="/workspace/glow_beauty/index.php">Beranda</a>
 <a href="/workspace/glow_beauty/cart.php">Keranjang</a>
 <?php if (is_user_loggedin()): ?>
 <a href="/workspace/glow_beauty/profile.php">Profil</a>
 <a href="/workspace/glow_beauty/logout.php">Logout</a>
 <?php else: ?>
 <a href="/workspace/glow_beauty/login.php">Login</a>
 <a href="/workspace/glow_beauty/register.php">Daftar</a>
 <?php endif; ?>
 <?php if (is_admin()): ?>
 <a href="/workspace/glow_beauty/admin/index.php">Admin Dashboard</a>
 <?php endif; ?>
 </nav>
 </header>

 <main>
 <h2>Registrasi Akun Baru</h2>

 <?php if (!empty($success_message)): ?>
 <div class="success-message"><?php echo $success_message; ?></div>
 <?php endif; ?>
 <?php if (!empty($error)): ?>
 <div class="error-message"><?php echo $error; ?></div>
 <?php endif; ?>


 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
 <div>
 <label for="username">Username:</label>
 <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
 <span class="error-message"><?php echo $username_err; ?></span>
 </div>
 <div>
 <label for="full_name">Nama Lengkap:</label>
 <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
 </div>
 <div>
 <label for="email">Email:</label>
 <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
 <span class="error-message"><?php echo $email_err; ?></span>
 </div>
 <div>
 <label for="password">Password:</label>
 <input type="password" id="password" name="password" required>
 <span class="error-message"><?php echo $password_err; ?></span>
 </div>
 <div>
 <label for="confirm_password">Konfirmasi Password:</label>
 <input type="password" id="confirm_password" name="confirm_password" required>
 <span class="error-message"><?php echo $confirm_password_err; ?></span>
 </div>
 <div>
 <button type="submit">Daftar</button>
 </div>
 </form>

 <p>Sudah punya akun? <a href="/workspace/glow_beauty/login.php">Login di sini</a>.</p>

 </main>

 <footer>
 <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
 </footer>

 <script src="/workspace/glow_beauty/assets/js/script.js"></script> <!-- Tautkan ke file JavaScript -->

</body>
</html>
<?php

?>