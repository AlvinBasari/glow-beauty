<?php
require_once '../includes/functions.php'; // Path disesuaikan karena berada di dalam subfolder

// --- Keamanan ---
// Memastikan user adalah admin yang sudah login
require_admin();

// --- Logika Dashboard (bisa ditambahkan nanti) ---
// Contoh: Mengambil statistik dasar
// $total_products = count_products();
// $new_orders = count_new_orders();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glow Beauty</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Path disesuaikan -->
</head>
<body>

    <header>
        <h1>Admin Panel - Glow Beauty</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="products.php">Kelola Produk</a>
            <a href="orders.php">Kelola Pesanan</a>
            <a href="users.php">Kelola User</a>
            <a href="../index.php" target="_blank">Lihat Situs</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Ini adalah halaman utama dashboard admin. Gunakan navigasi di atas untuk mengelola situs.</p>

        <div class="dashboard-stats">
            <div class="stat-item">
                <h3>Total Produk</h3>
                <p>N/A</p> <!-- Placeholder, akan diisi dengan data nanti -->
            </div>
            <div class="stat-item">
                <h3>Pesanan Baru</h3>
                <p>N/A</p> <!-- Placeholder, akan diisi dengan data nanti -->
            </div>
             <div class="stat-item">
                <h3>Total User</h3>
                <p>N/A</p> <!-- Placeholder, akan diisi dengan data nanti -->
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Glow Beauty Admin</p>
    </footer>

</body>
</html>
