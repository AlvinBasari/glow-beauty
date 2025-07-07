<?php
require_once '../includes/functions.php';

// Keamanan
require_admin();

$action = $_GET['action'] ?? 'list';
$product_id = $_GET['id'] ?? null;
$error = '';

// Handle form submission untuk tambah/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $current_image = $_POST['current_image'] ?? 'default.jpg';
    $image_name = $current_image; // Default ke gambar yang ada

    // --- Logika Unggah Gambar ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_type = $_FILES['image']['type'];

        // Validasi tipe file (misalnya, hanya izinkan jpg, png)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $error = "Error: Tipe file tidak valid. Hanya JPG, PNG, dan GIF yang diizinkan.";
        }
        // Validasi ukuran file (misalnya, maks 2MB)
        elseif ($file_size > 2097152) {
            $error = "Error: Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            // Buat nama file unik untuk mencegah tumpang tindih
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $image_name = uniqid('product_', true) . '.' . $file_ext;
            
            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($file_tmp_name, $upload_dir . $image_name)) {
                // Hapus gambar lama jika ada dan bukan gambar default
                if ($current_image !== 'default.jpg' && file_exists($upload_dir . $current_image)) {
                    // unlink($upload_dir . $current_image);
                }
            } else {
                $error = "Gagal memindahkan file yang diunggah.";
                $image_name = $current_image; // Jika gagal, kembalikan ke gambar lama
            }
        }
    }
    // --- Akhir Logika Unggah Gambar ---

    if (empty($error)) {
        global $conn;

        if (!empty($product_id)) {
            // Update produk
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image_name, $product_id);
        } else {
            // Tambah produk baru
            $sql = "INSERT INTO products (name, description, price, stock, image, category_id) VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image_name);
        }

        if ($stmt->execute()) {
            header('Location: products.php');
            exit();
        } else {
            $error = "Gagal menyimpan produk ke database.";
        }
    }
}

// Handle aksi hapus
if ($action === 'delete' && !empty($product_id)) {
    // (opsional) tambahkan logika untuk menghapus file gambar saat produk dihapus
    global $conn;
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        header('Location: products.php');
        exit();
    } else {
        $error = "Gagal menghapus produk.";
    }
}


// Ambil data untuk ditampilkan
if ($action === 'edit' && !empty($product_id)) {
    $product = get_product_detail($product_id);
} elseif ($action === 'add') {
    $product = null;
} else {
    $products = get_products();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
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
        <h2>Kelola Produk</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <a href="products.php?action=add">Tambah Produk Baru</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?php echo $p['product_id']; ?></td>
                            <td><img src="../assets/images/<?php echo htmlspecialchars($p['image']); ?>" alt="" width="50"></td>
                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo number_format($p['price']); ?></td>
                            <td><?php echo $p['stock']; ?></td>
                            <td>
                                <a href="products.php?action=edit&id=<?php echo $p['product_id']; ?>">Edit</a>
                                <a href="products.php?action=delete&id=<?php echo $p['product_id']; ?>" onclick="return confirm('Anda yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <h3><?php echo $action === 'add' ? 'Tambah Produk Baru' : 'Edit Produk'; ?></h3>
            <form action="products.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?? ''; ?>">
                <input type="hidden" name="current_image" value="<?php echo $product['image'] ?? 'default.jpg'; ?>">
                
                <div>
                    <label for="name">Nama Produk:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label for="price">Harga:</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="stock">Stok:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($stock ?? ''); ?>" required>
                </div>
                <div>
                    <label for="image">Gambar Produk:</label>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
                    <?php if ($action === 'edit' && !empty($product['image'])): ?>
                        <p>Gambar saat ini: <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" width="100"></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit"><?php echo $action === 'add' ? 'Tambah Produk' : 'Update Produk'; ?></button>
                <a href="products.php">Batal</a>
            </form>
        <?php endif; ?>

    </main>
</body>
</html>
