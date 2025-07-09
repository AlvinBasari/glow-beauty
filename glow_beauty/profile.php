<?php
require_once 'includes/functions.php';

require_user_login();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

global $conn;
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);

    if (empty($full_name)) {
        $error = "Nama lengkap tidak boleh kosong.";
    } else {
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

$user = get_user_data($user_id);
if (!$user) {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Glow Beauty - Profil Saya</title>
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
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin Dashboard</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container mb-5">
  <h2 class="text-center mb-4">Profil Saya</h2>
  <p class="text-center text-muted">Kelola informasi akun Anda</p>

  <?php if (!empty($message)): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="profile.php" method="post">
        <div class="mb-3">
          <label class="form-label">Username:</label>
          <p class="form-control-plaintext"><strong><?php echo htmlspecialchars($user['username']); ?></strong> (tidak dapat diubah)</p>
        </div>
        <div class="mb-3">
          <label class="form-label">Email:</label>
          <p class="form-control-plaintext"><strong><?php echo htmlspecialchars($user['email']); ?></strong> (tidak dapat diubah)</p>
        </div>
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap:</label>
          <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Alamat:</label>
          <textarea id="address" name="address" class="form-control" rows="4"><?php echo htmlspecialchars($user['address']); ?></textarea>
        </div>
        <div class="mb-3">
          <label for="phone_number" class="form-label">Nomor Telepon:</label>
          <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-primary">Update Profil</button>
        </div>
      </form>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="order_history.php" class="btn btn-outline-secondary">Lihat Riwayat Pesanan</a>
  </div>
</main>

<!-- Footer -->
<footer class="bg-light text-center py-3 border-top">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
