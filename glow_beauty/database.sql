-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS glow_beauty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Menggunakan database
USE glow_beauty;

--
-- Struktur Tabel untuk `categories`
--
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data Contoh untuk `categories`
--
INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Skincare', 'Produk untuk perawatan kulit wajah dan tubuh.'),
(2, 'Makeup', 'Produk untuk merias wajah.'),
(3, 'Haircare', 'Produk untuk perawatan rambut.');

--
-- Struktur Tabel untuk `users`
--
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data Contoh untuk `users`
-- (Password untuk kedua user adalah 'password123')
--
INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `address`, `phone_number`, `is_admin`) VALUES
(1, 'admin', '$2y$10$E/f.8z6A1E9j8gH8C1J53.o9y7rG6s.8aC4u2G6t7y.9s0K5B3f2q', 'admin@example.com', 'Administrator', '123 Admin Street', '081234567890', 1),
(2, 'johndoe', '$2y$10$E/f.8z6A1E9j8gH8C1J53.o9y7rG6s.8aC4u2G6t7y.9s0K5B3f2q', 'john.doe@example.com', 'John Doe', '456 User Avenue', '089876543210', 0);


--
-- Struktur Tabel untuk `products`
--
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data Contoh untuk `products`
--
INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `stock`, `image`) VALUES
(1, 1, 'Hydrating Facial Cleanser', 'Pembersih wajah yang lembut dan melembapkan, cocok untuk semua jenis kulit.', 150000.00, 50, 'cleanser.jpg'),
(2, 1, 'Vitamin C Serum', 'Serum pencerah dengan kandungan Vitamin C untuk melawan radikal bebas dan mencerahkan kulit.', 250000.00, 30, 'serum.jpg'),
(3, 2, 'Matte Finish Foundation', 'Foundation dengan hasil akhir matte yang tahan lama sepanjang hari.', 350000.00, 40, 'foundation.jpg');

--
-- Struktur Tabel untuk `content`
--
CREATE TABLE `content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data Contoh untuk `content`
--
INSERT INTO `content` (`content_id`, `page`, `section`, `content`) VALUES
(1, 'home', 'hero', '<h1>Selamat Datang di Glow Beauty</h1><p>Temukan produk kecantikan terbaik untuk kulit Anda.</p>'),
(2, 'about', 'description', '<p>Glow Beauty adalah toko online yang menyediakan berbagai macam produk kecantikan berkualitas.</p>'),
(3, 'contact', 'address', '<p>Jl. Contoh No. 123, Kota ABC</p>'),
(4, 'contact', 'email', '<p>info@glowbeauty.com</p>');
