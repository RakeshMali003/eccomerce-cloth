-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 07:25 AM
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
-- Database: `ecommerce_clothing_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','manager','accountant','delivery') DEFAULT 'manager',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@gurukrupa.com', '$2y$10$4pg2HH96NZipQfHxa66KzOmgfi0qrBQ8ne5V5h6WWpkrN3qZ.aaGG', 'super_admin', 'active', '2025-12-27 23:12:11', '2025-12-27 23:12:11');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `purchase_type` enum('retail','wholesale') DEFAULT 'retail',
  `quantity` int(11) DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'General', NULL, '2025-12-30 12:08:41', '2025-12-30 12:08:41'),
(2, 'silk sarre', '', '2025-12-30 17:51:19', '2025-12-30 17:51:19'),
(4, 'cotton shirt', '', '2025-12-30 17:53:05', '2025-12-30 17:53:05'),
(5, 'cotton pant', '', '2025-12-30 17:54:51', '2025-12-30 17:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

CREATE TABLE `delivery_partners` (
  `courier_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `usage_limit` int(11) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_usage`
--

CREATE TABLE `discount_usage` (
  `usage_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `used_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `transaction_type` enum('purchase','sale','return','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `balance_qty` int(11) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `reference_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `variant_id`, `transaction_type`, `quantity`, `balance_qty`, `transaction_date`, `reference_id`) VALUES
(1, 7, 1, 'sale', 10, 50, '2025-12-30 23:09:02', 1),
(2, 7, 1, 'sale', 10, 90, '2025-12-30 23:14:01', 1),
(3, 7, 1, 'sale', 10, 80, '2026-01-14 11:35:11', 1),
(4, 7, 1, 'sale', 10, 70, '2026-01-14 11:35:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` datetime DEFAULT current_timestamp(),
  `gst_total` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `order_id`, `invoice_number`, `invoice_date`, `gst_total`, `subtotal`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-2025-0001', '2025-12-30 20:20:55', 90.00, 500.00, 590.00, '2025-12-30 20:20:55', '2025-12-30 20:20:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` enum('order','promotion','system') DEFAULT 'system',
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_type` enum('retail','wholesale') DEFAULT 'retail',
  `order_status` enum('pending','confirmed','packed','shipped','delivered','cancelled','returned') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` enum('card','upi','wallet','cod') DEFAULT 'cod',
  `total_amount` decimal(10,2) NOT NULL,
  `gst_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_type`, `order_status`, `payment_status`, `payment_method`, `total_amount`, `gst_amount`, `discount_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'wholesale', 'returned', 'paid', 'upi', 590.00, 90.00, 0.00, '2025-12-30 20:20:55', '2026-01-14 11:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `gst_percent` decimal(5,2) DEFAULT 0.00,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `variant_id`, `quantity`, `unit_price`, `gst_percent`, `discount_percent`, `total_price`) VALUES
(1, 1, 7, 1, 10, 50.00, 18.00, 0.00, 590.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_shipments`
--

CREATE TABLE `order_shipments` (
  `shipment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `status` enum('pending','shipped','in_transit','delivered','returned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_shipments`
--

INSERT INTO `order_shipments` (`shipment_id`, `order_id`, `courier_id`, `tracking_number`, `shipped_at`, `delivered_at`, `status`) VALUES
(49, 1, NULL, NULL, NULL, NULL, 'returned');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `preferred_supplier_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `wholesale_price` decimal(10,2) NOT NULL,
  `min_wholesale_qty` int(11) DEFAULT 10,
  `gst_percent` decimal(5,2) DEFAULT 0.00,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 10,
  `main_image` varchar(255) DEFAULT NULL,
  `image_1` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `image_3` varchar(255) DEFAULT NULL,
  `image_4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `supplier_id`, `category_id`, `preferred_supplier_id`, `name`, `sku`, `description`, `price`, `wholesale_price`, `min_wholesale_qty`, `gst_percent`, `discount_percent`, `status`, `created_at`, `updated_at`, `stock`, `min_stock_level`, `main_image`, `image_1`, `image_2`, `image_3`, `image_4`) VALUES
(7, 17, 1, 17, 'rakesh mali lt', 'AIRPODSPRO-001', 'rstytjytrewreytyewrs s afdasdf', 50.00, 40.00, 10, 18.00, 0.00, 'active', '2025-12-30 13:18:18', '2026-01-14 11:37:14', 5, 5, '69538421245c7_1767080993.jpeg', '695383c2156f4_1767080898.png', '695383c21604b_1767080898.avif', NULL, NULL),
(8, 17, 1, 17, 'rakesh mali', 'AIRPODSPRO-000', 'aesrtytuyjttreewrrt', 54.00, 64.00, 10, 18.00, 0.00, 'active', '2025-12-30 13:19:19', '2025-12-30 23:08:16', 150, 10, '6953894713a07_1767082311.jpg', '695383ff198d4_1767080959.png', '695383ff19bc6_1767080959.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `material` varchar(50) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `size`, `color`, `material`, `stock_qty`, `created_at`, `updated_at`) VALUES
(1, 7, 'free', 'Green', NULL, 0, '2025-12-30 13:30:29', '2025-12-30 13:30:29'),
(2, 8, 'xl', 'red', NULL, 50, '2025-12-30 13:31:02', '2025-12-30 13:31:02'),
(3, 7, 'sm', 'red', NULL, 25, '2025-12-30 13:33:08', '2025-12-30 13:33:08'),
(4, 7, 'free', 'Green', NULL, 0, '2025-12-30 13:39:26', '2025-12-30 13:39:26'),
(5, 8, '', '', NULL, 100, '2025-12-30 23:08:16', '2025-12-30 23:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `purchase_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`purchase_item_id`, `po_id`, `product_id`, `quantity`, `cost_price`, `total`) VALUES
(1, 2, 0, 15, 450.00, 6750.00),
(2, 3, 0, 1, 52.00, 52.00),
(3, 3, 0, 1, 520.00, 520.00),
(4, 3, 0, 251, 100.00, 25100.00),
(5, 4, 0, 51, 5100.00, 260100.00),
(6, 5, 0, 511, 51.00, 26061.00),
(7, 5, 0, 51, 51.00, 2601.00),
(8, 6, 0, 51, 5120.00, 261120.00),
(9, 7, 0, 51, 210.00, 10710.00),
(10, 7, 0, 102, 500.00, 51000.00),
(11, 7, 0, 102, 1000.00, 102000.00),
(12, 8, 0, 1, 500.00, 500.00),
(20, 21, 7, 50, 1000.00, 50000.00),
(21, 21, 7, 110, 250.00, 27500.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `expected_delivery` datetime DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','received','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `supplier_id`, `po_number`, `order_date`, `expected_delivery`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(2, 16, '1545102', '2025-12-29 00:00:00', '2025-12-16 00:00:00', 6750.00, 'received', '2025-12-29 18:45:33', '2025-12-29 18:45:33'),
(3, 16, '1545102', '2025-12-29 00:00:00', '2025-12-30 00:00:00', 25672.00, 'received', '2025-12-29 18:46:05', '2025-12-29 18:46:05'),
(4, 16, '1545102', '2025-12-29 00:00:00', '2025-12-31 00:00:00', 260100.00, 'received', '2025-12-29 19:57:57', '2025-12-29 19:57:57'),
(5, 17, '1545102', '2025-12-22 00:00:00', '2026-01-08 00:00:00', 28662.00, 'received', '2025-12-29 19:58:32', '2025-12-29 19:58:32'),
(6, 17, '1545102', '2025-12-23 00:00:00', '2025-12-31 00:00:00', 261120.00, 'received', '2025-12-29 19:58:51', '2025-12-29 19:58:51'),
(7, 17, '1545102', '2025-12-29 00:00:00', '2026-01-09 00:00:00', 163710.00, 'received', '2025-12-29 20:01:45', '2025-12-29 20:01:45'),
(8, 17, 'PO-1767020345', '2025-12-29 15:59:20', '2026-01-03 00:00:00', 500.00, 'pending', '2025-12-29 20:29:20', '2025-12-29 20:29:20'),
(9, 17, 'PO-1767020365', '2025-12-29 15:59:39', '2025-12-16 00:00:00', 5500.00, 'received', '2025-12-29 20:29:39', '2025-12-29 20:30:08'),
(10, 16, '1545102', '2025-12-30 00:00:00', '2026-01-02 00:00:00', 5250.00, 'received', '2025-12-30 11:52:28', '2025-12-30 11:52:28'),
(11, 17, '1545102', '2025-12-31 00:00:00', '2026-01-10 00:00:00', 5200.00, 'received', '2025-12-30 11:53:07', '2025-12-30 11:53:07'),
(12, 16, '1545102', '2025-12-30 00:00:00', '2026-01-02 00:00:00', 105220.00, 'received', '2025-12-30 12:02:35', '2025-12-30 12:02:35'),
(13, 16, '1545102', '2025-12-30 00:00:00', '2026-01-08 00:00:00', 26100.00, 'received', '2025-12-30 12:05:51', '2025-12-30 12:05:51'),
(14, 16, '1545102', '2025-12-30 00:00:00', '0000-00-00 00:00:00', 94870.00, 'received', '2025-12-30 12:09:51', '2025-12-30 12:09:51'),
(15, 17, '1545102', '2025-12-30 00:00:00', '2026-01-08 00:00:00', 533740.00, 'received', '2025-12-30 12:15:10', '2025-12-30 12:15:10'),
(16, 16, '1545102', '2025-12-30 00:00:00', '2026-01-10 00:00:00', 55650.00, 'received', '2025-12-30 12:23:12', '2025-12-30 12:23:12'),
(17, 19, 'PO-1767116088', '2025-12-30 18:35:35', '2026-01-10 00:00:00', 36350.00, 'received', '2025-12-30 23:05:35', '2025-12-30 23:06:27'),
(18, 20, 'PO-1767709406', '2026-01-06 15:24:24', '2026-01-17 00:00:00', 76975.00, 'received', '2026-01-06 19:54:24', '2026-01-06 19:55:20'),
(19, 16, '3874', '2026-01-14 00:00:00', '2026-01-21 00:00:00', 31500.00, 'received', '2026-01-14 11:40:00', '2026-01-14 11:40:00'),
(20, 16, '1545102', '2026-01-14 00:00:00', '2026-01-15 00:00:00', 6250.00, 'received', '2026-01-14 12:43:59', '2026-01-14 12:43:59'),
(21, 19, 'PO-1768395539', '2026-01-14 14:02:17', '2026-01-21 00:00:00', 77500.00, 'pending', '2026-01-14 18:32:17', '2026-01-14 18:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `old_qty` int(11) NOT NULL,
  `adjustment` int(11) NOT NULL,
  `new_qty` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT 'Manual Adjustment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`log_id`, `product_id`, `old_qty`, `adjustment`, `new_qty`, `reason`, `created_at`) VALUES
(1, 8, 15, 50, 65, 'Manual Adjustment', '2025-12-30 12:31:29'),
(2, 8, 65, 60, 125, 'Manual Adjustment', '2025-12-30 12:31:41'),
(3, 7, 50, 50, 100, 'Manual Adjustment', '2025-12-30 17:39:37'),
(4, 7, 70, 5, 75, 'Manual Adjustment', '2026-01-14 06:07:06'),
(5, 7, 75, -70, 5, 'Manual Adjustment', '2026-01-14 06:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gstin` varchar(15) DEFAULT NULL,
  `payment_terms` enum('Advance','COD','Net 30','Net 60') DEFAULT 'COD',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `contact_person`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `gstin`, `payment_terms`, `status`, `created_at`, `updated_at`) VALUES
(16, 'rakesh mali', 'shrinathji school', 'rakeshmali46519@gmail.com', '89898801505', 'SAI KRIPA SOCIETY\r\nAIRPORT ROAD', '', '', '', 'JHUGYFDFGHJK', 'COD', 'active', '2025-12-29 17:32:23', '2025-12-29 17:32:33'),
(17, 'Primeadmin', 'rakesh mali', 'rakeshmali46519@gmail.com', '07600404831', 'Daman\r\nmc', '', '', '', 'JHUGYFDFGHJK', 'COD', 'active', '2025-12-29 19:42:50', '2025-12-29 19:42:50'),
(18, 'rakesh mali', 'shrinathji school', 'rakeshmali46519@gmail.com', '07600404831', 'SAI KRIPA SOCIETY\r\nAIRPORT ROAD', '', '', '', 'JHUGYFDFGHJK', 'COD', 'active', '2025-12-29 20:00:59', '2025-12-29 20:00:59'),
(19, 'mukesh', 'mukesh', 'rakeshmali46519@gmail.com', '07600404831', 'Daman\r\nmc dfnje', '', '', '', 'JHUGYFDFGHJK', 'COD', 'active', '2025-12-30 23:04:38', '2025-12-30 23:04:38'),
(20, 'durga', 'haku', 'rakeshmali46519@gmail.com', '07600404831', 'Daman\r\nmc', '', '', '', 'JHUGYFDFGHJK', 'COD', 'active', '2026-01-06 19:53:20', '2026-01-06 19:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_bills`
--

CREATE TABLE `supplier_bills` (
  `bill_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `bill_number` varchar(100) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `balance_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('Pending','Partial','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_bills`
--

INSERT INTO `supplier_bills` (`bill_id`, `supplier_id`, `bill_number`, `bill_date`, `due_date`, `total_amount`, `paid_amount`, `balance_amount`, `payment_status`, `created_at`) VALUES
(2, 16, '1545102', '2025-12-29', '2025-12-16', 6750.00, 18000.00, -26250.00, 'Paid', '2025-12-29 13:15:33'),
(3, 16, '1545102', '2025-12-29', '2025-12-30', 25672.00, 25000.00, -24328.00, 'Paid', '2025-12-29 13:16:05'),
(4, 16, '1545102', '2025-12-29', '2025-12-31', 260100.00, 310100.00, -310100.00, 'Paid', '2025-12-29 14:27:57'),
(5, 17, '1545102', '2025-12-22', '2026-01-08', 28662.00, 20000.00, -2676.00, 'Paid', '2025-12-29 14:28:32'),
(6, 17, '1545102', '2025-12-23', '2025-12-31', 261120.00, 150000.00, -38880.00, 'Paid', '2025-12-29 14:28:51'),
(7, 17, '1545102', '2025-12-29', '2026-01-09', 163710.00, 646160.00, -1016190.00, 'Paid', '2025-12-29 14:31:45'),
(8, 16, '1545102', '2025-12-29', '2026-01-09', 5500.00, 15000.00, -24500.00, 'Paid', '2025-12-29 15:00:08'),
(9, 16, '1545102', '2025-12-30', '2026-01-02', 5250.00, 5000.00, -4750.00, 'Paid', '2025-12-30 06:22:28'),
(10, 17, '1545102', '2025-12-31', '2026-01-10', 5200.00, 5200.00, -5200.00, 'Paid', '2025-12-30 06:23:07'),
(11, 16, '1545102', '2025-12-30', '2026-01-02', 105220.00, 105220.00, -105220.00, 'Paid', '2025-12-30 06:32:35'),
(12, 16, '1545102', '2025-12-30', '2026-01-08', 26100.00, 26000.00, -25900.00, 'Paid', '2025-12-30 06:35:51'),
(13, 16, '1545102', '2025-12-30', '0000-00-00', 94870.00, 94870.00, -94870.00, 'Paid', '2025-12-30 06:39:51'),
(14, 17, '1545102', '2025-12-30', '2026-01-08', 533740.00, 533740.00, -533740.00, 'Paid', '2025-12-30 06:45:10'),
(15, 16, '1545102', '2025-12-30', '2026-01-10', 55650.00, 50650.00, -15650.00, 'Paid', '2025-12-30 06:53:12'),
(16, 19, '1545102', '2025-12-30', '2026-01-09', 36350.00, 35650.00, -33250.00, 'Paid', '2025-12-30 17:36:27'),
(17, 20, '1545102', '2026-01-06', '2026-01-31', 76975.00, 56975.00, -16975.00, 'Paid', '2026-01-06 14:25:20'),
(18, 16, '3874', '2026-01-14', '2026-01-21', 31500.00, 26000.00, -19500.00, 'Paid', '2026-01-14 06:10:00'),
(19, 16, '1545102', '2026-01-14', '2026-01-15', 6250.00, 6250.00, -6250.00, 'Paid', '2026-01-14 07:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `payment_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_mode` enum('Cash','Online','Cheque') DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `cheque_number` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Cleared') DEFAULT 'Cleared',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`payment_id`, `supplier_id`, `bill_id`, `payment_date`, `amount`, `payment_mode`, `transaction_id`, `cheque_number`, `bank_name`, `status`, `created_at`) VALUES
(1, 16, 2, '2025-12-29', 3000.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-29 14:09:22'),
(2, 16, 3, '2025-12-29', 2500.00, 'Cheque', '', NULL, NULL, 'Pending', '2025-12-29 14:09:40'),
(3, 16, 3, '2025-12-29', 25000.00, 'Online', '', NULL, NULL, 'Cleared', '2025-12-29 14:10:05'),
(4, 16, 2, '2025-12-29', 15000.00, 'Online', '', NULL, NULL, 'Cleared', '2025-12-29 14:10:18'),
(5, 16, 4, '2025-12-29', 50000.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-29 14:32:57'),
(6, 17, 5, '2025-12-30', 8662.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-30 06:24:22'),
(7, 17, 10, '2025-12-30', 5200.00, 'Online', '12', NULL, NULL, 'Cleared', '2025-12-30 13:32:15'),
(8, 17, 5, '2025-12-30', 11338.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-30 13:32:32'),
(9, 16, 8, '2025-12-30', 15000.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-30 13:33:18'),
(10, 16, 13, '2025-12-30', 94870.00, 'Cash', '', NULL, NULL, 'Cleared', '2025-12-30 13:34:38'),
(11, 17, 7, '2025-12-30', 5000.00, 'Online', '', NULL, NULL, 'Cleared', '2025-12-30 17:40:35'),
(12, 20, 17, '2026-01-06', 20000.00, 'Online', '12', NULL, NULL, 'Cleared', '2026-01-06 14:30:18'),
(13, 20, 17, '2026-01-06', 36975.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-06 14:30:44'),
(14, 16, 9, '2026-01-06', 5000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-06 14:32:57'),
(15, 16, 12, '2026-01-06', 26000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-06 14:34:55'),
(16, 16, 4, '2026-01-06', 260100.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-06 14:35:15'),
(17, 16, 15, '2026-01-06', 25000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-06 14:35:32'),
(18, 17, 6, '2026-01-14', 150000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:23:06'),
(19, 17, 7, '2026-01-14', 53710.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:23:36'),
(20, 17, 7, '2026-01-14', 51290.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:24:02'),
(21, 17, 7, '2026-01-14', 2420.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:24:19'),
(22, 16, 11, '2026-01-14', 105220.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:24:36'),
(23, 16, 15, '2026-01-14', 5000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:24:51'),
(24, 16, 19, '2026-01-14', 6250.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:25:03'),
(25, 16, 15, '2026-01-14', 20650.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:25:20'),
(26, 16, 18, '2026-01-14', 500.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:25:29'),
(27, 16, 18, '2026-01-14', 500.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:25:41'),
(28, 16, 18, '2026-01-14', 25000.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:25:52'),
(29, 19, 16, '2026-01-14', 350.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:26:17'),
(30, 19, 16, '2026-01-14', 650.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:26:34'),
(31, 19, 16, '2026-01-14', 700.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:26:50'),
(32, 19, 16, '2026-01-14', 33950.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:27:02'),
(33, 17, 7, '2026-01-14', 533740.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:27:18'),
(34, 17, 14, '2026-01-14', 533740.00, 'Cash', '', NULL, NULL, 'Cleared', '2026-01-14 14:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gst_number` varchar(15) DEFAULT NULL,
  `business_name` varchar(150) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `is_verified_wholesale` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `google_id`, `phone`, `address`, `city`, `state`, `country`, `pincode`, `gst_number`, `business_name`, `role`, `status`, `is_verified_wholesale`, `created_at`, `updated_at`) VALUES
(1, 'rakesh mali', 'rakeshmali46519@gmail.com', '$2y$10$YACDKxRYb/87saVwZYNMgONvV0nSZvy1D2DD35ZCXfS3dc6C.YzAm', NULL, '7600404831', 'SAI KRIPA SOCIETY\r\nAIRPORT ROAD', 'Nani daman', NULL, NULL, '396210', NULL, NULL, '', 'active', 0, '2025-12-27 18:32:15', '2025-12-27 19:58:36'),
(2, 'primebank', 'subham@gmail.com', '$2y$10$cRbpJNAnHe6xghWcRjNq1e2Y.wA2PWzjhR0jVkW4TtSHF75cSykOq', NULL, '07600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'active', 0, '2025-12-27 19:04:08', '2025-12-27 19:04:08'),
(4, 'shrinathji school', 'rk@gmail.com', '$2y$10$IxRbVvgaP6lF3DDY78l5uucPdCn6U7L83NW51Rn763aLLppVspvNq', NULL, '7600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'active', 0, '2025-12-27 19:09:23', '2025-12-27 19:09:23'),
(6, 'rakesh mali', 'admin@gmail.com', '$2y$10$4pg2HH96NZipQfHxa66KzOmgfi0qrBQ8ne5V5h6WWpkrN3qZ.aaGG', NULL, '7600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'active', 0, '2025-12-27 23:10:09', '2025-12-27 23:10:09'),
(8, 'rakesh mali', 'rakeshma46519@gmail.com', '$2y$10$sBdN3GEyotNaTq0K2p96R.L76mFdIP/SR45Gn6j9o5xkdhpHZutEi', NULL, '07600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-28 11:56:25', '2025-12-28 11:56:25'),
(10, 'rakesh mali', 'rakhmali46519@gmail.com', '$2y$10$OjLrIFeOHIaF2T1rCFrB7O7YVZn/He0/gg15023IwkZ3EyTK8xu/u', NULL, '07600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-28 12:01:20', '2025-12-28 12:01:20'),
(11, 'rakesh mali', 'rakeshmali6519@gmail.com', '$2y$10$MK9goMK1D5CQpJy.AANAFOjvVEZ6dtrvDCPOQsPpftRgVJVsW7gq2', NULL, '07600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-28 12:15:20', '2025-12-28 12:15:20'),
(15, 'rakesh mali', 'rakeshm=ali46519@gmail.com', '$2y$10$b.pemFwRJRddh8F7cOeNveOuQDbahbbKdkYenvgM0Y3EQMu28OCD2', NULL, '07600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-28 12:28:09', '2025-12-28 12:28:09'),
(16, 'rakesh mali', 'ssk2025@gmail.com', '$2y$10$lRckvABTVK4viqXmwheFu.rp4yUYbCk1ceBwU8tlRgy39noYP1GWG', NULL, '7600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-31 11:56:30', '2025-12-31 11:56:30'),
(18, 'rakesh mali', 'rakesli46519@gmail.com', '$2y$10$pTjeqTMD8LAWYIwkQ05pAeaDjI/WBW7Oa/qFHPu57yRKVfWX/MGqm', NULL, '7600404831', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', 'active', 0, '2025-12-31 12:14:09', '2025-12-31 12:14:09');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD PRIMARY KEY (`courier_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `discount_usage`
--
ALTER TABLE `discount_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `order_shipments`
--
ALTER TABLE `order_shipments`
  ADD PRIMARY KEY (`shipment_id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `courier_id` (`courier_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`purchase_item_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`po_item_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `supplier_bills`
--
ALTER TABLE `supplier_bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  MODIFY `courier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_usage`
--
ALTER TABLE `discount_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_shipments`
--
ALTER TABLE `order_shipments`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `purchase_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `po_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `supplier_bills`
--
ALTER TABLE `supplier_bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `discount_usage`
--
ALTER TABLE `discount_usage`
  ADD CONSTRAINT `discount_usage_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`),
  ADD CONSTRAINT `discount_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `discount_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_shipments`
--
ALTER TABLE `order_shipments`
  ADD CONSTRAINT `order_shipments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_shipments_ibfk_2` FOREIGN KEY (`courier_id`) REFERENCES `delivery_partners` (`courier_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_bills`
--
ALTER TABLE `supplier_bills`
  ADD CONSTRAINT `supplier_bills_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`bill_id`) REFERENCES `supplier_bills` (`bill_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
