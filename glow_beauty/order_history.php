<?php
require_once 'includes/functions.php';

// Memastikan user sudah login
require_user_login();

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil ID user dari sesi
$user_id = $_SESSION['user_id'];

// Ambil riwayat pesanan user
$orders = get_user_orders($user_id);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Beauty - Riwayat Pesanan</title>
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
        <h2>Riwayat Pesanan Saya</h2>

        <div class="order-history-container">
            <?php if (!empty($orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo date("d M Y, H:i", strtotime($order['order_date'])); ?></td>
                                <td>Rp <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                                <td><a href="order_detail.php?id=<?php echo $order['order_id']; ?>">Lihat Detail</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Anda belum memiliki riwayat pesanan.</p>
                <p><a href="index.php">Mulai Belanja</a></p>
            <?php endif; ?>
        </div>
        
        <p><a href="profile.php">Kembali ke Profil</a></p>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
    </footer>

    <script src="assets/js/script.js"></script>

</body>
</html>
