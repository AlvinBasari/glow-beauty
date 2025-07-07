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
-- Struktur Tabel untuk `orders`
--
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur Tabel untuk `order_items`
--
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
