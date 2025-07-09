<?php
require_once 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$product = null;
$product_id = $_GET['id'] ?? 0;

if ($product_id > 0) {
    $product = get_product_detail($product_id);
}

if (!$product) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Glow Beauty - Detail Produk: <?php echo htmlspecialchars($product['name']); ?></title>
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
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Daftar</a></li>
        <?php endif; ?>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin Dashboard</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container mb-5">
  <h2 class="text-center mb-4">Detail Produk</h2>

  <div class="row g-4">
    <div class="col-md-5 text-center">
      <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded shadow-sm">
    </div>

    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
          <p class="card-text"><strong>Harga:</strong> Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
          <p class="card-text"><strong>Stok Tersedia:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
          <p class="card-text"><strong>Deskripsi:</strong><br><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

          <?php if ($product['stock'] > 0): ?>
            <?php if (is_user_loggedin()): ?>
              <form action="cart.php" method="post" class="mt-3">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                  <label for="quantity" class="form-label">Kuantitas:</label>
                  <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
              </form>
            <?php else: ?>
              <div class="alert alert-warning mt-3">Silakan <a href="login.php">login</a> untuk menambahkan ke keranjang.</div>
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-danger mt-3">Stok Habis</div>
          <?php endif; ?>

          <a href="index.php" class="btn btn-outline-secondary mt-3">← Kembali ke Katalog</a>
        </div>
      </div>
    </div>
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
