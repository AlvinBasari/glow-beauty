<?php
require_once 'includes/functions.php';

// --- Memastikan user sudah login ---
require_user_login();

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

global $conn;
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// --- Proses Pembuatan Pesanan ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan keranjang tidak kosong sebelum memproses
    if (!empty($_SESSION['cart'])) {
        $cart_items = $_SESSION['cart'];
        $cart_total = 0;
        foreach ($cart_items as $item) {
            $cart_total += $item['price'] * $item['quantity'];
        }

        // Panggil fungsi create_order
        $order_id = create_order($user_id, $cart_items, $cart_total);
        
        if ($order_id) {
            // Pesanan berhasil dibuat
            unset($_SESSION['cart']); // Kosongkan keranjang
            $message = "Pesanan Anda dengan ID #" . $order_id . " telah berhasil dibuat! Terima kasih telah berbelanja.";
        } else {
            $error = "Gagal membuat pesanan. Stok produk mungkin tidak mencukupi atau terjadi kesalahan internal. Silakan coba lagi.";
        }
    } else {
        $error = "Keranjang Anda kosong.";
    }
}


// Ambil data user untuk informasi pengiriman (jika pesanan belum dibuat)
if (empty($message)) {
    // Periksa apakah keranjang ada dan tidak kosong
    if (empty($_SESSION['cart'])) {
        // Jika keranjang kosong, arahkan ke halaman utama atau keranjang
        header('Location: index.php');
        exit();
    }
    
    $user = get_user_data($user_id);
    if (!$user) {
        // Jika data user tidak ditemukan, ada masalah serius. Logout untuk keamanan.
        header('Location: logout.php');
        exit();
    }

    // Ambil item keranjang dari sesi
    $cart_items = $_SESSION['cart'];
    $cart_total = 0;
    foreach ($cart_items as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Beauty - Checkout</title>
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
        <h2>Checkout</h2>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <h3>Terima Kasih!</h3>
                <p><?php echo $message; ?></p>
            </div>
            <p><a href="index.php">Kembali ke Beranda</a></p>
            <p><a href="order_history.php">Lihat Riwayat Pesanan Anda</a></p>

        <?php else: ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-summary">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item-summary">
                                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                                <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="cart-total-summary">
                        <strong>Total Pesanan:</strong>
                        <strong>Rp <?php echo number_format($cart_total, 2, ',', '.'); ?></strong>
                    </div>
                </div>

                <div class="shipping-info">
                    <h3>Informasi Pengiriman</h3>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                    <p><a href="profile.php">Ubah informasi pengiriman di profil Anda</a></p>
                </div>
            </div>

            <?php if (empty($user['address']) || empty($user['phone_number'])): ?>
                <div class="error-message">
                    Mohon lengkapi alamat dan nomor telepon di profil Anda sebelum melanjutkan.
                </div>
            <?php else: ?>
                <div class="place-order">
                    <form action="checkout.php" method="post">
                        <button type="submit">Buat Pesanan Sekarang</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
    </footer>

    <script src="assets/js/script.js"></script>

</body>
</html>
