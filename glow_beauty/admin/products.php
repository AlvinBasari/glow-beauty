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
    $image_name = $current_image;

    // --- Upload Gambar ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_type = $_FILES['image']['type'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $error = "Error: Tipe file tidak valid. Hanya JPG, PNG, dan GIF yang diizinkan.";
        } elseif ($file_size > 2097152) {
            $error = "Error: Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $image_name = uniqid('product_', true) . '.' . $file_ext;

            if (move_uploaded_file($file_tmp_name, $upload_dir . $image_name)) {
                if ($current_image !== 'default.jpg' && file_exists($upload_dir . $current_image)) {
                    // unlink($upload_dir . $current_image); // Uncomment jika ingin hapus gambar lama
                }
            } else {
                $error = "Gagal memindahkan file yang diunggah.";
                $image_name = $current_image;
            }
        }
    }

    if (empty($error)) {
        global $conn;

        if (!empty($product_id)) {
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image_name, $product_id);
        } else {
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

if ($action === 'delete' && !empty($product_id)) {
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        nav {
            display: flex;
            gap: 30px;
        }
        
        nav a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        nav a:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            color: white;
            text-align: center;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="a" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23a)"/></svg>');
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 800;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.4rem;
            font-weight: 300;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 40px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .content-wrapper {
            background: #f8fafc;
            min-height: 100vh;
            padding: 40px 0;
        }
        
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Action Bar */
        .action-bar {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        
        .add-product-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .add-product-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
        }
        
        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: linear-gradient(45deg, #f8fafc, #e2e8f0);
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tbody tr {
            transition: all 0.3s ease;
        }
        
        tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.005);
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .product-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .product-price {
            font-weight: 700;
            color: #38a169;
            font-size: 1.1rem;
        }
        
        .stock-badge {
            background: #48bb78;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .stock-badge.low {
            background: #ed8936;
        }
        
        .stock-badge.out {
            background: #e53e3e;
        }
        
        .action-links {
            display: flex;
            gap: 10px;
        }
        
        .action-links a {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .edit-btn {
            background: #4299e1;
            color: white;
        }
        
        .edit-btn:hover {
            background: #3182ce;
            transform: translateY(-1px);
        }
        
        .delete-btn {
            background: #e53e3e;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c53030;
            transform: translateY(-1px);
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        input[type="file"]:focus {
            border-color: #667eea;
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-1px);
        }
        
        .error-message {
            background: linear-gradient(45deg, #feb2b2, #fc8181);
            color: #742a2a;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(254, 178, 178, 0.4);
        }
        
        .current-image {
            margin-top: 15px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            text-align: center;
        }
        
        .current-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 30px;
            }
            
            .action-bar {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            th, td {
                padding: 15px 10px;
            }
            
            .action-links {
                flex-direction: column;
                gap: 5px;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .table-container,
        .form-container,
        .action-bar {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-gem"></i> Glow Beauty Admin
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box"></i> Produk</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                <a href="users.php"><i class="fas fa-users"></i> User</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h1><i class="fas fa-sparkles"></i> Kelola Produk Kecantikan</h1>
            <p>Tingkatkan penjualan dengan manajemen produk yang efektif dan modern</p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($products ?? []); ?></span>
                    <span class="stat-label">Total Produk</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4.9</span>
                    <span class="stat-label">Rating Rata-rata</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">98%</span>
                    <span class="stat-label">Kepuasan Pelanggan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <main>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <div class="action-bar">
                    <h2 class="page-title">Daftar Produk Kecantikan</h2>
                    <a href="products.php?action=add" class="add-product-btn">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </a>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-image"></i> Gambar</th>
                                <th><i class="fas fa-tag"></i> Nama Produk</th>
                                <th><i class="fas fa-dollar-sign"></i> Harga</th>
                                <th><i class="fas fa-cubes"></i> Stok</th>
                                <th><i class="fas fa-cog"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><strong><?php echo $p['product_id']; ?></strong></td>
                                        <td>
                                            <img src="../assets/images/<?php echo htmlspecialchars($p['image']); ?>" 
                                                 class="product-image" 
                                                 alt="<?php echo htmlspecialchars($p['name']); ?>">
                                        </td>
                                        <td>
                                            <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                        </td>
                                        <td>
                                            <span class="product-price">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                                        </td>
                                        <td>
                                            <span class="stock-badge <?php echo $p['stock'] == 0 ? 'out' : ($p['stock'] < 10 ? 'low' : ''); ?>">
                                                <?php echo $p['stock']; ?> unit
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-links">
                                                <a href="products.php?action=edit&id=<?php echo $p['product_id']; ?>" class="edit-btn">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="products.php?action=delete&id=<?php echo $p['product_id']; ?>" 
                                                   class="delete-btn" 
                                                   onclick="return confirm('Anda yakin ingin menghapus produk ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 20px;"></i>
                                        <p style="color: #718096; font-size: 1.1rem;">Belum ada produk yang ditambahkan</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <div class="form-container">
                    <h3 class="form-title">
                        <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?>"></i>
                        <?php echo $action === 'add' ? 'Tambah Produk Baru' : 'Edit Produk'; ?>
                    </h3>
                    
                    <form action="products.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?? ''; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $product['image'] ?? 'default.jpg'; ?>">

                        <div class="form-group">
                            <label for="name"><i class="fas fa-tag"></i> Nama Produk:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Deskripsi:</label>
                            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price"><i class="fas fa-dollar-sign"></i> Harga (Rp):</label>
                            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock"><i class="fas fa-cubes"></i> Stok:</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="image"><i class="fas fa-image"></i> Gambar Produk:</label>
                            <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
                            <?php if ($action === 'edit' && !empty($product['image'])): ?>
                                <div class="current-image">
                                    <p style="margin-bottom: 15px; color: #4a5568; font-weight: 500;">Gambar saat ini:</p>
                                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current product image">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $action === 'add' ? 'Tambah Produk' : 'Update Produk'; ?>
                            </button>
                            <a href="products.php" class="btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat numbers
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const finalValue = stat.textContent;
                if (!isNaN(finalValue)) {
                    let currentValue = 0;
                    const increment = finalValue / 50;
                    const timer = setInterval(() => {
                        currentValue += increment;
                        if (currentValue >= finalValue) {
                            stat.textContent = finalValue;
                            clearInterval(timer);
                        } else {
                            stat.textContent = Math.floor(currentValue);
                        }
                    }, 50);
                }
            });

            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(10px)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>
</html>