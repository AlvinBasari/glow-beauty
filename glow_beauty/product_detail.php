<?php
require_once 'includes/functions.php'; // Sertakan file fungsi

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$product = null;
$product_id = $_GET['id'] ?? 0; // Ambil ID produk dari parameter URL

// Pastikan ID produk adalah integer positif
if ($product_id > 0) {
    $product = get_product_detail($product_id); // Ambil detail produk
}

// Jika produk tidak ditemukan, arahkan kembali ke halaman utama
if (!$product) {
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Beauty - Detail Produk: <?php echo htmlspecialchars($product['name']); ?></title>
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
        <h2>Detail Produk</h2>

        <div class="product-detail">
            <div class="product-image">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><strong>Harga:</strong> Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                <p><strong>Stok Tersedia:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
                <p><strong>Deskripsi:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <?php if ($product['stock'] > 0): ?>
                    <?php if (is_user_loggedin()): ?>
                        <!-- Tombol Tambah ke Keranjang - Akan memproses tambah ke keranjang jika user login -->
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                            <input type="hidden" name="action" value="add">
                            <label for="quantity">Kuantitas:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>" required>
                            <button type="submit">Tambah ke Keranjang</button>
                        </form>
                    <?php else: ?>
                        <!-- Arahkan ke halaman login jika user belum login -->
                        <p><a href="login.php">Login untuk Tambah ke Keranjang</a></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="out-of-stock">Stok Habis</p>
                <?php endif; ?>

                <p><a href="index.php">Kembali ke Katalog</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
    </footer>

    <script src="assets/js/script.js"></script> <!-- Tautkan ke file JavaScript -->

</body>
</html>
