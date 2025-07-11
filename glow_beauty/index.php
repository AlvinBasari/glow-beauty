<?php
require_once 'includes/functions.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Glow Beauty</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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

        /* Custom Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
            border: none;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-nav .nav-link {
            color: #4a5568 !important;
            font-weight: 500;
            margin: 0 10px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #667eea !important;
            background: rgba(102, 126, 234, 0.1);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="a" width="30" height="30" patternUnits="userSpaceOnUse"><circle cx="15" cy="15" r="2" fill="white" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23a)"/></svg>');
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            font-weight: 300;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .hero-features {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 50px;
            flex-wrap: wrap;
        }

        .feature-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #ffd700;
        }

        .feature-text {
            font-size: 1rem;
            font-weight: 500;
        }

        /* Main Content */
        .content-wrapper {
            background: #f8fafc;
            min-height: 100vh;
            padding: 50px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 50px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #718096;
            margin-bottom: 60px;
        }

        /* Product Cards */
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 25px;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .product-description {
            color: #718096;
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #38a169;
            margin-bottom: 20px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            flex-direction: column;
        }

        .btn-detail {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-detail:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 30px;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 15px;
        }

        .empty-text {
            color: #718096;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #ffd700;
        }

        .footer-section p,
        .footer-section a {
            color: #cbd5e0;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #ffd700;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #a0aec0;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #ffd700;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-features {
                flex-direction: column;
                gap: 20px;
            }

            .product-actions {
                flex-direction: column;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
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

        .product-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
        .product-card:nth-child(5) { animation-delay: 0.5s; }
        .product-card:nth-child(6) { animation-delay: 0.6s; }

        /* Loading Animation */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Custom Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-gem"></i> Glow Beauty
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="product.php">Product</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link" href="admin/index.php"><i class="fas fa-cog"></i> Admin</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <?php
            $home_hero_content = get_content('home', 'hero');
            if ($home_hero_content) {
                echo $home_hero_content;
            } else {
                echo '<h1><i class="fas fa-sparkles"></i> Welcome to Glow Beauty</h1>
                      <p>Discover the best beauty products for your skin.</p>';
            }
            ?>

            <div class="hero-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="feature-text">100% Natural</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="feature-text">Premium Quality</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="feature-text">Fast Delivery</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="feature-text">Dermatologist Tested</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="container">

        <!-- Main Content -->
        <main>
            <h2 class="section-title">Beauty Product Catalog</h2>
            <p class="section-subtitle">Choose the best products for your beauty routine</p>

            <div class="row g-4" id="productGrid">
                <?php
                $products = get_products();
                if (!empty($products)):
                    foreach ($products as $product):
                        ?>
                        <div class="col-lg-4 col-md-6 product-item">
                            <div class="card product-card">
                                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                     class="card-img-top product-image"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body product-info">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <div class="product-price">
                                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </div>

                                    <div class="product-actions">
                                        <a href="product_detail.php?id=<?php echo htmlspecialchars($product['product_id']); ?>"
                                           class="btn btn-detail">
                                            <i class="fas fa-eye"></i> View Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    endforeach;
                else:
                    ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3 class="empty-title">No Products Yet</h3>
                            <p class="empty-text">Amazing beauty products are being prepared. Please come back later!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4><i class="fas fa-gem"></i> Glow Beauty</h4>
                <p>A trusted beauty store with high-quality products for all your beauty care needs.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Services</h4>
                <a href="#">Beauty Consultation</a>
                <a href="#">Free Shipping</a>
                <a href="#">Return & Exchange</a>
                <a href="#">Customer Support</a>
            </div>
            <div class="footer-section">
                <h4>Information</h4>
                <a href="#">About Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">FAQ</a>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Kecantikan No. 123, Jakarta</p>
                <p><i class="fas fa-phone"></i> +62 21 1234 5678</p>
                <p><i class="fas fa-envelope"></i> info@glowbeauty.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved. Made with <i class="fas fa-heart text-danger"></i> for beautiful you.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    // Smooth scroll for navbar links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero');
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
</script>
</body>
</html>
<?php
function get_content($page, $section)
{
    global $db;
    $page = mysqli_real_escape_string($db, $page);
    $section = mysqli_real_escape_string($db, $section);
    $query = "SELECT content FROM content WHERE page = '$page' AND section = '$section'";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['content'];
    } else {
        return false;
    }
}

function get_products()
{
    global $db;
    $query = "SELECT * FROM products";
    $result = mysqli_query($db, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $products;
}
?>