<?php
require_once '../includes/functions.php';

// Keamanan
require_admin();

// Ambil ID pesanan dari parameter URL
$order_id = $_GET['id'] ?? null;

if (empty($order_id)) {
    // Jika tidak ada ID pesanan, arahkan kembali ke daftar pesanan
    header('Location: orders.php');
    exit();
}

// Ambil detail pesanan menggunakan fungsi yang baru dibuat
$order_details = get_order_details_for_admin($order_id);

// Jika pesanan tidak ditemukan
if (!$order_details) {
    echo "Pesanan tidak ditemukan.";
    // Anda bisa mengarahkan kembali ke orders.php atau menampilkan halaman error 404
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo htmlspecialchars($order_details['order_id']); ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Admin Panel - Glow Beauty</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="products.php">Kelola Produk</a>
            <a href="orders.php">Kelola Pesanan</a>
            <a href="users.php">Kelola User</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Detail Pesanan #<?php echo htmlspecialchars($order_details['order_id']); ?></h2>

        <div class="order-detail-section">
            <h3>Informasi Pesanan</h3>
            <p><strong>Tanggal Pesanan:</strong> <?php echo date("d M Y H:i", strtotime($order_details['order_date'])); ?></p>
            <p><strong>Total Jumlah:</strong> Rp <?php echo number_format($order_details['total_amount'], 2, ',', '.'); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order_details['status'])); ?>
                <!-- Form untuk update status (opsional, bisa juga di halaman orders.php) -->
                <form action="update_order_status.php" method="post" style="display:inline-block; margin-left: 10px;">
                    <input type="hidden" name="order_id" value="<?php echo $order_details['order_id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?php echo $order_details['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order_details['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $order_details['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="completed" <?php echo $order_details['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order_details['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
            </p>
        </div>

        <div class="order-detail-section">
            <h3>Informasi Pelanggan</h3>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($order_details['username']); ?></p>
            <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($order_details['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></p>
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
                                <td><img src="../assets/images/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" width="50"></td>
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

        <p><a href="orders.php">Kembali ke Daftar Pesanan</a></p>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Glow Beauty Admin</p>
    </footer>

</body>
</html>
