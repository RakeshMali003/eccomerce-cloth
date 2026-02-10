-- Database Optimization SQL
-- Run this in your phpMyAdmin or MySQL Console

-- 1. Optimize Products Table (Filtering & Sorting)
-- Used in: product-list.php, index.php
ALTER TABLE products ADD INDEX idx_status_created (status, created_at);
ALTER TABLE products ADD INDEX idx_category_status (category_id, status);
ALTER TABLE products ADD INDEX idx_price (price);
ALTER TABLE products ADD INDEX idx_name_search (name);

-- 2. Optimize Orders Table (Dashboard & Reports)
-- Used in: admin/dashboard.php
ALTER TABLE orders ADD INDEX idx_order_date (created_at);
ALTER TABLE orders ADD INDEX idx_status_date (order_status, created_at);
ALTER TABLE orders ADD INDEX idx_user_id (user_id);

-- 3. Optimize Users Table (Login & Search)
-- Used in: login.php, admin user search
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_phone (phone);
ALTER TABLE users ADD INDEX idx_role_status (role, status);

-- 4. Optimize Cart & Wishlist (Header Counts)
-- Used in: includes/functions.php
ALTER TABLE cart ADD INDEX idx_user_cart (user_id);
ALTER TABLE wishlist ADD INDEX idx_user_wishlist (user_id);

-- 5. Fulltext search for products (Better search performance)
ALTER TABLE products ADD FULLTEXT idx_full_search (name, description, sku);
