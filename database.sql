-- Elo Marketplace Database Schema
-- Database: elo_marketplace

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `elo_marketplace` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `elo_marketplace`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Admin User', 'admin@elo-marketplace.com', '$2y$10$LI8hOGXuAnTLqrXq5MDYBeDGSM2oYU.vRxVVa/AUvZmtJZ7jweHha', 'admin', 1);

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO `categories` (`name`, `description`, `image`) VALUES
('Electronics & Gadgets', 'Latest smartphones, laptops, smart devices and consumer electronics', NULL),
('Mobile Accessories', 'Phone cases, chargers, cables, screen protectors and more', NULL),
('Home & Kitchen', 'Smart home devices, appliances, cookware and storage solutions', NULL),
('Clothing & Apparel', 'Men\'s and women\'s fashion, casual wear and seasonal clothing', NULL),
('Sports & Outdoors', 'Fitness equipment, outdoor gear, cycling and camping essentials', NULL),
('Beauty & Personal Care', 'Skincare, grooming tools, hair care and wellness products', NULL),
('Toys & Hobbies', 'RC toys, model kits, puzzles, board games and hobby supplies', NULL),
('Tools & Hardware', 'Power tools, hand tools, workshop supplies and DIY accessories', NULL);

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample products
INSERT INTO `products` (`name`, `description`, `price`, `discount_percentage`, `stock`, `category_id`, `sku`, `status`) VALUES
-- Electronics & Gadgets (category 1)
('Wireless Bluetooth Earbuds Pro', 'True wireless stereo earbuds with active noise cancellation, 30hr battery life, IPX5 waterproof, compatible with iOS and Android', 2499.00, 10.00, 120, 1, 'ELC-EAR-001', 1),
('Smart LED TV 43" 4K UHD', '4K Ultra HD smart television with built-in Wi-Fi, Android OS, HDR support and multiple HDMI/USB ports', 18999.00, 15.00, 30, 1, 'ELC-TV-43', 1),
('Mechanical Gaming Keyboard RGB', 'Compact TKL mechanical keyboard with RGB backlight, blue switches, anti-ghosting and USB-C connection', 1899.00, 5.00, 75, 1, 'ELC-KB-RGB', 1),
('Portable Power Bank 20000mAh', 'Fast-charge 20000mAh power bank with dual USB-A + USB-C output, LED indicator and slim design', 1199.00, 0.00, 200, 1, 'ELC-PB-20K', 1),
-- Mobile Accessories (category 2)
('USB-C Fast Charger 65W GaN', 'GaN technology 65W USB-C wall charger, foldable plug, compatible with laptops, phones and tablets', 899.00, 0.00, 150, 2, 'MOB-CHG-65W', 1),
('Magnetic Phone Mount for Car', '360° adjustable magnetic dashboard phone holder, universal fit, strong neodymium magnet, no-scratch base', 349.00, 0.00, 300, 2, 'MOB-MNT-CAR', 1),
('Tempered Glass Screen Protector 5-Pack', '9H hardness ultra-clear tempered glass, bubble-free installation, compatible with most smartphone models', 199.00, 0.00, 500, 2, 'MOB-SCR-5PK', 1),
-- Home & Kitchen (category 3)
('Smart RGB LED Strip Light 5m', 'Wi-Fi enabled 5-metre RGB LED strip, app controlled, works with Alexa and Google Home, music sync mode', 699.00, 10.00, 180, 3, 'HOM-LED-5M', 1),
('Stainless Steel Cookware Set 7pc', '7-piece non-stick stainless steel cookware set including pots, pans and glass lids, dishwasher safe', 3499.00, 20.00, 40, 3, 'HOM-CKW-7PC', 1),
('Digital Air Fryer 5L', '5-litre digital air fryer with touch control panel, 8 preset cooking modes, 360° rapid air circulation', 4299.00, 12.00, 55, 3, 'HOM-AFR-5L', 1),
-- Clothing & Apparel (category 4)
('Men\'s Oversized Graphic Hoodie', 'Premium cotton-blend fleece hoodie, oversized streetwear fit, kangaroo pocket, available in multiple colours', 1299.00, 0.00, 90, 4, 'CLO-HOD-MEN', 1),
('Women\'s High-Waist Yoga Leggings', 'Four-way stretch moisture-wicking leggings with hidden waistband pocket, squat-proof fabric', 899.00, 5.00, 110, 4, 'CLO-LEG-WMN', 1),
-- Sports & Outdoors (category 5)
('Resistance Bands Set 5pc', 'Set of 5 premium latex resistance bands (10–50 lb), includes carry bag and exercise guide', 649.00, 0.00, 160, 5, 'SPT-RES-5PC', 1),
('Foldable Trekking Poles Pair', 'Lightweight aluminium collapsible hiking poles with cork grip, wrist straps and interchangeable tips', 1499.00, 8.00, 60, 5, 'SPT-TRK-PLR', 1),
-- Beauty & Personal Care (category 6)
('Electric Facial Cleansing Brush', 'Sonic vibration cleansing brush with 3 speed settings, waterproof silicone head, USB rechargeable', 799.00, 10.00, 130, 6, 'BEA-CLN-FCL', 1),
-- Toys & Hobbies (category 7)
('RC Drift Car 4WD 1:16 Scale', 'High-speed 4WD remote control drift car, 2.4GHz controller, LED headlights, 25km/h top speed', 1799.00, 0.00, 70, 7, 'TOY-RC-CAR', 1),
-- Tools & Hardware (category 8)
('Cordless Drill Set 20V', '20V lithium-ion cordless drill/driver with 2 batteries, charger, 25-piece bit set and carry case', 3299.00, 15.00, 45, 8, 'TLS-DRL-20V', 1);

-- --------------------------------------------------------
-- Table structure for table `product_images`
-- --------------------------------------------------------

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `wishlist`
-- --------------------------------------------------------

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comment` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `messages`
-- --------------------------------------------------------

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `site_settings`
-- --------------------------------------------------------

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('home_banner_image', NULL),
('about_us_image', NULL),
('default_category_logo', NULL);

COMMIT;
