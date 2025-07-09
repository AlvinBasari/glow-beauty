<?php
session_start();
require_once 'includes/functions.php'; // Sesuaikan path

// Inisialisasi variabel form
$username = $full_name = $email = '';
$username_err = $email_err = $password_err = $confirm_password_err = '';
$success_message = $error = '';

// Handle submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validasi input
    if (empty($username)) {
        $username_err = "Username wajib diisi.";
    }

    if (empty($email)) {
        $email_err = "Email wajib diisi.";
    }

    if (empty($password)) {
        $password_err = "Password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password minimal 6 karakter.";
    }

    if ($password !== $confirm_password) {
        $confirm_password_err = "Konfirmasi password tidak cocok.";
    }

    // Simpan jika tidak ada error
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        global $conn;

        $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username atau email sudah digunakan.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("ssss", $username, $full_name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $success_message = "Registrasi berhasil! Silakan login.";
                $username = $full_name = $email = '';
            } else {
                $error = "Terjadi kesalahan saat menyimpan data.";
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Glow Beauty - Registrasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">Glow Beauty</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Keranjang</a></li>
        <?php if (is_user_loggedin()): ?>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link active" href="register.php">Daftar</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container">
  <h2 class="mb-4 text-center">Registrasi Akun Baru</h2>

  <?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="card shadow-sm p-4">
    <div class="mb-3">
      <label for="username" class="form-label">Username<span class="text-danger">*</span></label>
      <input type="text" id="username" name="username" class="form-control <?php echo !empty($username_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" required>
      <div class="invalid-feedback"><?php echo $username_err; ?></div>
    </div>

    <div class="mb-3">
      <label for="full_name" class="form-label">Nama Lengkap</label>
      <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
      <input type="email" id="email" name="email" class="form-control <?php echo !empty($email_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required>
      <div class="invalid-feedback"><?php echo $email_err; ?></div>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
      <input type="password" id="password" name="password" class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>" required>
      <div class="invalid-feedback"><?php echo $password_err; ?></div>
    </div>

    <div class="mb-3">
      <label for="confirm_password" class="form-label">Konfirmasi Password<span class="text-danger">*</span></label>
      <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo !empty($confirm_password_err) ? 'is-invalid' : ''; ?>" required>
      <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Daftar</button>
    </div>
  </form>

  <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
</main>

<!-- Footer -->
<footer class="bg-light text-center py-3 mt-5 border-top">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
