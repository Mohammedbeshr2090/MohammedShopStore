-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 11:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store1_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 1, 2, 1, '2026-08-23 23:13:32'),
(2, 2, 1, 1, '2026-08-23 23:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `name_ar`, `image`, `status`, `created_at`) VALUES
(1, 'Smartphones', 'هواتف ذكية', 'img/cat_img2.png', 1, '2026-08-23 22:01:12'),
(2, 'Smartwatches', 'ساعات ذكية', 'img/cat_img4.png', 1, '2026-08-23 22:01:12'),
(3, 'Headphones', 'سماعات', 'img/cat_img3.png', 1, '2026-08-23 22:01:12'),
(4, 'Speakers', 'مكبرات صوت', 'img/cat_img7.png', 1, '2026-08-23 22:01:12'),
(5, 'Cameras', 'كاميرات', 'img/cat_img6.png', 1, '2026-08-23 22:01:12'),
(6, 'Televisions', 'تلفزيونات', 'img/cat_img1.png', 1, '2026-08-23 22:01:12'),
(7, 'Games', 'ألعاب', 'img/cat_img5.png', 1, '2026-08-23 22:01:12');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'حمزة', 'ajhj@gmail.com', 'طلب سماعات ايفون', 'هل يوجد لديكم  سماعات ايفون', 0, '2026-08-23 23:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'cod',
  `shipping_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `name_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `hover_image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `name_ar`, `description`, `description_ar`, `price`, `old_price`, `image`, `hover_image`, `stock`, `featured`, `status`, `created_at`) VALUES
(1, 1, 'iPhone 15 Pro Max', 'آيفون 15 برو ماكس', 'Latest iPhone with A17 Pro chip', 'أحدث آيفون بمعالج A17 Pro', 2350.99, 2500.99, 'img/15.jpg', 'img/15 1.jpg', 10, 1, 1, '2026-08-23 22:01:12'),
(2, 1, 'iPhone 14 Pro', 'آيفون 14 برو', 'Powerful iPhone 14 Pro', 'آيفون 14 برو القوي', 1200.99, 1400.99, 'img/iphone 14 ³.jpg', 'img/iphone 14 ¹.jpg', 15, 1, 1, '2026-08-23 22:01:12'),
(3, 1, 'iPhone 13 Pro', 'آيفون 13 برو', 'iPhone 13 Pro with ProMotion', 'آيفون 13 برو مع شاشة ProMotion', 750.99, 899.99, 'img/iphone 13 ¹.jpg', 'img/iphone 13 ².jpg', 20, 1, 1, '2026-08-23 22:01:12'),
(4, 1, 'iPhone 12 Pro Max', 'آيفون 12 برو ماكس', 'iPhone 12 Pro Max with 5G', 'آيفون 12 برو ماكس مع 5G', 640.99, 799.99, 'img/iphone 12 ².jpg', 'img/iphone 12 ¹1.jpg', 12, 1, 1, '2026-08-23 22:01:12'),
(5, 1, 'iPhone 11 Pro', 'آيفون 11 برو', 'iPhone 11 Pro triple camera', 'آيفون 11 برو بثلاث كاميرات', 399.99, 499.99, 'img/iphone 11 ⁰.jpg', 'img/iphone 11 ²1.jpg', 8, 1, 1, '2026-08-23 22:01:12'),
(6, 1, 'iPhone 8', 'آيفون 8', 'Classic iPhone 8', 'آيفون 8 الكلاسيكي', 199.99, 299.99, 'img/iphone 8.jpg', 'img/iphone 8.jpg', 5, 0, 1, '2026-08-23 22:01:12'),
(7, 4, 'New Speakers', 'مكبر صوت جديد', 'Premium wireless speaker', 'مكبر صوت لاسلكي فاخر', 249.99, 399.99, 'img/mr1.jpg', 'img/20.jpg', 25, 1, 1, '2026-08-23 22:01:12'),
(8, 2, 'Smartwatch', 'ساعة ذكية', 'Premium smartwatch with health tracking', 'ساعة ذكية مع تتبع الصحة', 79.99, 99.99, 'img/product-6.jpg', 'img/product-6-hover.jpg', 30, 0, 1, '2026-08-23 22:01:12'),
(9, 3, 'Headphone', 'سماعة رأس', 'Wireless noise-cancelling headphone', 'سماعة لاسلكية بإلغاء الضوضاء', 39.99, 59.99, 'img/سماعات 1.jpg', 'img/سماعة 1.jpg', 50, 0, 1, '2026-08-23 22:01:12'),
(10, 5, 'Camera', 'كاميرا', 'Professional DSLR camera', 'كاميرا DSLR احترافية', 249.99, 399.99, 'img/product-2.jpg', 'img/product-2-hover.jpg', 7, 0, 1, '2026-08-23 22:01:12'),
(11, 6, 'Television', 'تلفزيون', 'Smart 4K TV', 'تلفزيون ذكي 4K', 549.99, 699.99, 'img/product-3.jpg', 'img/product-3-hover.jpg', 10, 0, 1, '2026-08-23 22:01:12'),
(12, 2, 'Smartwatch Pro', 'ساعة ذكية برو', 'Advanced smartwatch', 'ساعة ذكية متقدمة', 149.99, 199.99, 'img/ساعة 1.jpg', 'img/ساعة 2.jpg', 20, 0, 1, '2026-08-23 22:01:12'),
(13, 1, 'samsong S 24 ULTRA', 'سامسونج اس 24 الترا', 'samsong S 24 ULTRA 520 GB 18 RAM', '', 1500.00, 1300.00, 'uploads/product_1787527035_8304.jpg', 'uploads/product_hover_1787527035_7879.jpg', 20, 1, 1, '2026-08-23 23:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'site_name', 'Mohammed Shop'),
(2, 'site_name_ar', 'متجر محمد'),
(3, 'site_email', 'info@mohammedshop.com'),
(4, 'site_phone', '+966 50 000 0000'),
(5, 'site_address', 'Taiz, Yemen'),
(6, 'currency', '$');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `address`, `created_at`) VALUES
(1, 'Admin', 'admin0@gmail.com', '$2y$10$KOS9uBYOIc70cgTGHsXrAemsjKiQT4FgnhVZqmvcNVypBMwpgxAP2', 'admin', NULL, NULL, '2026-08-23 22:01:12'),
(2, 'ali ahmed', 'ali@gmail.com', '$2y$10$WrJMvTbBVuIQ.MSFDZPis.TEZYsUL3zLS5RcAVSN.3iundkZeEJGO', 'user', '774161609', NULL, '2026-08-23 23:21:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
