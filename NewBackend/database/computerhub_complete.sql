-- Complete ComputerHub Database Setup with ALL Products
-- Import this file into phpMyAdmin or MySQL to set up the entire database

-- Create database
CREATE DATABASE IF NOT EXISTS computerhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE computerhub;

-- Drop existing tables if they exist (to avoid conflicts)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_price (price)
);

-- Create cart_items table
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);

-- Insert ALL products from your HTML files
INSERT INTO products (name, description, price, category, stock_quantity) VALUES

-- Processors
('Intel Core i9 12th Gen Processor', '12th Generation Intel Core i9 Processor with advanced performance', 147500.00, 'Processors', 15),
('Intel Core i9 13th Gen Processor', '13th Generation Intel Core i9 Processor with enhanced features', 257295.00, 'Processors', 12),
('Intel Core i9 14th Gen Processor', '14th Generation Intel Core i9 Processor with latest technology', 166920.00, 'Processors', 10),
('Intel Core Ultra 7 265KF', 'Intel Core Ultra 7 265KF Processor for high-end computing', 124000.00, 'Processors', 8),
('Intel Core Ultra 9 285K', 'Intel Core Ultra 9 285K Processor for extreme performance', 194000.00, 'Processors', 5),
('AMD Ryzen 9 5990X', 'AMD Ryzen 9 5990X 16-Core 32-Thread Processor', 116900.00, 'Processors', 8),
('AMD Ryzen 9 7950X', 'AMD Ryzen 9 7950X 16-Core 32-Thread Processor', 162500.00, 'Processors', 10),
('AMD Ryzen Threadripper 3990X', 'AMD Ryzen Threadripper 3990X 64-Core Processor', 1150000.00, 'Processors', 3),
('AMD Ryzen Threadripper 3995WX', 'AMD Ryzen Threadripper 3995WX 64-Core Workstation Processor', 1450000.00, 'Processors', 2),

-- Motherboards
('ROG Crosshair X670E Hero', 'ASUS ROG Crosshair X670E Hero Gaming Motherboard', 275000.00, 'Motherboards', 8),
('MSI MEG Z790 GODLIKE', 'MSI MEG Z790 GODLIKE Gaming Motherboard', 127312.00, 'Motherboards', 5),
('MSI Z790 Carbon WiFi', 'MSI Z790 Carbon WiFi Gaming Motherboard', 162500.00, 'Motherboards', 10),
('Gigabyte Z790 AORUS Master X', 'Gigabyte Z790 AORUS Master X Gaming Motherboard', 52926.00, 'Motherboards', 12),
('Gigabyte B550 AORUS Elite V2', 'Gigabyte B550 AORUS Elite V2 Gaming Motherboard', 189.99, 'Motherboards', 15),

-- Memory (RAM)
('Vengance DDR5 32GB', 'Corsair Vengeance DDR5 32GB Memory Kit', 18500.00, 'Memory', 20),
('Vengance DDR5 64GB', 'Corsair Vengeance DDR5 64GB Memory Kit', 8500.00, 'Memory', 15),
('G-Skill DDR5 16GB', 'G-Skill DDR5 16GB Memory Kit', 21500.00, 'Memory', 25),
('ADATA DDR5 8GB', 'ADATA DDR5 8GB Memory Module', 18500.00, 'Memory', 30),
('ADATA DDR5 16GB', 'ADATA DDR5 16GB Memory Module', 21500.00, 'Memory', 20),

-- Graphic Cards
('AORUS MASTER GEFORCE RTX 5090 32GB', 'Gigabyte AORUS MASTER GeForce RTX 5090 32GB Graphics Card', 1095000.00, 'Graphic Cards', 3),
('MSI GEFORCE RTX 4090 24GB', 'MSI GeForce RTX 4090 24GB Gaming Graphics Card', 205000.00, 'Graphic Cards', 5),
('NVIDIA GEFORCE RTX 4080 Ti 16GB GDDR6X', 'NVIDIA GeForce RTX 4080 Ti 16GB GDDR6X Graphics Card', 425000.00, 'Graphic Cards', 8),
('MSI RTX 4080 SUPER 16GB VENTUS 3X OC', 'MSI RTX 4080 SUPER 16GB VENTUS 3X OC Graphics Card', 542000.00, 'Graphic Cards', 6),
('GIGABYTE RADEON RX 7900 XTX 24GB GAMING OC', 'Gigabyte Radeon RX 7900 XTX 24GB Gaming OC Graphics Card', 293874.13, 'Graphic Cards', 4),

-- Power Supply Units
('Apevia ATX-PR1000W Prestige', 'Apevia ATX-PR1000W Prestige Power Supply', 55000.00, 'PSU', 12),
('ASUS ROG Loki 1000W Platinum', 'ASUS ROG Loki 1000W Platinum Power Supply', 306672.00, 'PSU', 8),
('ASUS TUF GAMING 1000W GOLD', 'ASUS TUF GAMING 1000W Gold Power Supply', 102000.00, 'PSU', 15),
('EVGA Supernova 1000 G7', 'EVGA Supernova 1000 G7 Power Supply', 119605.00, 'PSU', 10),
('Thermaltake Toughpower GF3 1000W', 'Thermaltake Toughpower GF3 1000W Power Supply', 129.99, 'PSU', 12),

-- Monitors
('Acer Predator X32 FP', 'Acer Predator X32 FP Gaming Monitor', 561000.00, 'Monitors', 5),
('Alienware AW2524H', 'Alienware AW2524H Gaming Monitor', 429473.00, 'Monitors', 8),
('ASUS ROG Swift OLED PG27AQDM', 'ASUS ROG Swift OLED PG27AQDM Gaming Monitor', 399000.00, 'Monitors', 6),
('Corsair Xeneon 27QHD240', 'Corsair Xeneon 27QHD240 Gaming Monitor', 385000.00, 'Monitors', 7),
('Lenovo Thinkvision P32p-30', 'Lenovo Thinkvision P32p-30 Professional Monitor', 1184117.00, 'Monitors', 3),

-- Storage
('Samsung 990 Pro 1TB', 'Samsung 990 Pro 1TB PCIe 4.0 NVMe M.2 SSD', 54000.00, 'Storage', 30),
('Crucial T700 2TB', 'Crucial T700 2TB PCIe 5.0 NVMe M.2 SSD', 115000.00, 'Storage', 25),
('WD Black SN850X 2TB', 'WD Black SN850X 2TB Gaming Layout NVMe SSD', 76000.00, 'Storage', 20),
('Samsung T7 Shield 2TB Portable', 'Samsung T7 Shield 2TB Portable External SSD', 65000.00, 'Storage', 15),
('Crucial X10 Pro 2TB Portable', 'Crucial X10 Pro 2TB Portable External SSD', 58000.00, 'Storage', 18),

-- Keyboard and Mouse
('Logitec K780', 'Logitech K780 Multi-Device Wireless Keyboard', 27900.00, 'Keyboard and Mouse', 20),
('Ducky ONE 3 Classic TKL', 'Ducky ONE 3 Classic TKL Mechanical Keyboard', 49560.00, 'Keyboard and Mouse', 15),
('Razer Basilisk V3 Pro', 'Razer Basilisk V3 Pro Wireless Gaming Mouse', 54400.00, 'Keyboard and Mouse', 18),
('Logitech G502 X Lightspeed', 'Logitech G502 X Lightspeed Wireless Gaming Mouse', 34000.00, 'Keyboard and Mouse', 22),
('Razer DeathAdder V2', 'Razer DeathAdder V2 Gaming Mouse', 31200.00, 'Keyboard and Mouse', 25),

-- Speakers and Headphones
('ASUS Tuf Gaming H1', 'ASUS Tuf Gaming H1 Gaming Headset', 13500.00, 'Speakers and Headphones', 30),
('Fantech ALTO MH91', 'Fantech ALTO MH91 Gaming Headset', 8750.00, 'Speakers and Headphones', 35),
('Harman Kardon SoundSticks 4', 'Harman Kardon SoundSticks 4 Speaker System', 122850.00, 'Speakers and Headphones', 8),
('Fantech SONAR GS202', 'Fantech SONAR GS202 Gaming Speaker', 15000.00, 'Speakers and Headphones', 20),
('Logitech Z623', 'Logitech Z623 THX-Certified 2.1 Speaker System', 25000.00, 'Speakers and Headphones', 15),

-- Cooling
('MasterLiquid 360 Atmos', 'Cooler Master MasterLiquid 360 Atmos Liquid Cooler', 149.99, 'Cooling', 12),
('MasterLiquid 360 Ion', 'Cooler Master MasterLiquid 360 Ion Liquid Cooler', 149.99, 'Cooling', 10),
('DeepCool AK500', 'DeepCool AK500 Air Cooler', 149.99, 'Cooling', 20),
('DeepCool Assassin IV', 'DeepCool Assassin IV Air Cooler', 149.99, 'Cooling', 15),
('Lial Li Galahad II Trinity SL-INF 240mmmod', 'Lian Li Galahad II Trinity SL-INF 240mm Liquid Cooler', 149.99, 'Cooling', 8),

-- Casings
('ASUS TUF Gaming GT501', 'ASUS TUF Gaming GT501 Mid-Tower Case', 47600.00, 'Casings', 10),
('Coolermaster_mb520', 'Cooler Master MB520 Mid-Tower Case', 32900.00, 'Casings', 15),
('MSI MEG PANO M100R', 'MSI MEG PANO M100R Mid-Tower Case', 33000.00, 'Casings', 8),
('MSI MAG Forge 320R', 'MSI MAG Forge 320R Mid-Tower Case', 23000.00, 'Casings', 12),
('NZXT H5 ELITE', 'NZXT H5 ELITE Mid-Tower Case', 32500.00, 'Casings', 18),

-- Cables and Connections
('ASUS ROG Rapture GT-AX11000', 'ASUS ROG Rapture GT-AX11000 WiFi Router', 85000.00, 'Cables and Connections', 5),
('MSI RadoX AXE6600', 'MSI RadoX AXE6600 WiFi Router', 15000.00, 'Cables and Connections', 8),
('DVI Cable', 'DVI to DVI Cable', 1500.00, 'Cables and Connections', 20),
('HDMI 2.1', 'HDMI 2.1 High-Speed Cable', 2500.00, 'Cables and Connections', 50),
('Display Port Cable', 'DisplayPort 1.4 High-Speed Cable', 3500.00, 'Cables and Connections', 45),

-- Laptops - Dell
('Dell Alienware m17 R5', 'Dell Alienware m17 R5 Gaming Laptop', 627000.00, 'Laptops', 5),
('Dell Precision 5740', 'Dell Precision 5740 Workstation Laptop', 891000.00, 'Laptops', 3),
('Dell Latitude 9430', 'Dell Latitude 9430 Business Laptop', 636900.00, 'Laptops', 8),
('Dell Inspiron 16 Plus', 'Dell Inspiron 16 Plus Laptop', 395670.00, 'Laptops', 12),
('Dell G15', 'Dell G15 Gaming Laptop', 395670.00, 'Laptops', 10),

-- Laptops - HP
('HP Spectre x360 13', 'HP Spectre x360 13 Convertible Laptop', 247500.00, 'Laptops', 8),
('HP Spectre x360 15', 'HP Spectre x360 15 Convertible Laptop', 462000.00, 'Laptops', 6),
('HP Envy 13', 'HP Envy 13 Laptop', 249143.00, 'Laptops', 10),
('HP Envy 14', 'HP Envy 14 Laptop', 290000.00, 'Laptops', 7),
('HP Omen 16', 'HP Omen 16 Gaming Laptop', 449500.00, 'Laptops', 5),

-- Laptops - Lenovo
('Lenovo ThinkPad X1 Carbon', 'Lenovo ThinkPad X1 Carbon Business Laptop', 527670.00, 'Laptops', 8),
('Lenovo ThinkPad T14s Gen 6', 'Lenovo ThinkPad T14s Gen 6 Business Laptop', 395670.00, 'Laptops', 10),
('Lenovo ThinkPad X1 Yoga', 'Lenovo ThinkPad X1 Yoga Convertible Laptop', 363000.00, 'Laptops', 6),
('Lenovo Yoga 9i', 'Lenovo Yoga 9i Convertible Laptop', 395670.00, 'Laptops', 7),
('Lenovo Yoga Slim 7i', 'Lenovo Yoga Slim 7i Laptop', 326700.00, 'Laptops', 12),

-- Laptops - Mac
('MacBook Air M1 (2020)', 'MacBook Air M1 (2020) Laptop', 310000.00, 'Laptops', 15),
('MacBook Air M2 (2022)', 'MacBook Air M2 (2022) Laptop', 420000.00, 'Laptops', 12),
('MacBook Air M3 (2024)', 'MacBook Air M3 (2024) Laptop', 495000.00, 'Laptops', 10),
('MacBook Pro (13 inch, M1 2020)', 'MacBook Pro (13 inch, M1 2020) Laptop', 355000.00, 'Laptops', 8),
('MacBook Pro (14 inch, M2 2022)', 'MacBook Pro (14 inch, M2 2022) Laptop', 690000.00, 'Laptops', 6),

-- Laptops - MSI
('MSI GS66 Stealth', 'MSI GS66 Stealth Gaming Laptop', 248000.00, 'Laptops', 8),
('MSI GS75 Stealth', 'MSI GS75 Stealth Gaming Laptop', 973060.00, 'Laptops', 5),
('MSI Titan 18 HX', 'MSI Titan 18 HX Gaming Laptop', 2350000.00, 'Laptops', 2),
('MSI Titan GT76', 'MSI Titan GT76 Gaming Laptop', 2697255.00, 'Laptops', 1),
('MSI Titan GT77', 'MSI Titan GT77 Gaming Laptop', 2550000.00, 'Laptops', 1),

-- Laptops - ASUS (AZUZ)
('AZUZ Chromebook CX9', 'ASUS Chromebook CX9 Laptop', 219950.00, 'Laptops', 15),
('ASUS Zenbook 14x OLED', 'ASUS Zenbook 14x OLED Laptop', 228000.00, 'Laptops', 10),
('ASUS Zenbook 14 OLED', 'ASUS Zenbook 14 OLED Laptop', 399950.00, 'Laptops', 8),
('ASUS ExpertBook B5 Flip', 'ASUS ExpertBook B5 Flip Convertible Laptop', 199950.00, 'Laptops', 12),
('ASUS Chromebook Flip CX5', 'ASUS Chromebook Flip CX5 Convertible Laptop', 119950.00, 'Laptops', 20),

-- Laptops - Acer
('Acer Swift Go 14', 'Acer Swift Go 14 Laptop', 365000.00, 'Laptops', 10),
('Acer Swift Edge 16', 'Acer Swift Edge 16 Laptop', 365000.00, 'Laptops', 10),
('Acer Predator Helios 16', 'Acer Predator Helios 16 Laptop', 494670.00, 'Laptops', 8),
('Acer Predator Triton 17 X', 'Acer Predator Triton 17 X Laptop', 1253670.00, 'Laptops', 5),
('Acer Chromebook Spin 714', 'Acer Chromebook Spin 714 Laptop', 158400.00, 'Laptops', 12);

-- Insert Admin User (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@computerhub.com', '$2y$10$E.M9.p0L3e./VcySizgmGOsMvWTRgIuNF1Yp2tG3HvD52sJLC8hNy', 'admin');

-- Insert Regular User (password: user123)
INSERT INTO users (name, email, password, role) VALUES
('Regular User', 'user@example.com', '$2y$10$wT8vI8e.F2iXmBLiXyOqUe5nS7u2/jV9A5k6b.xG9b9O2m/C6tK/O', 'user');

-- Insert existing Test User with explicit user role (password: test123)
INSERT INTO users (name, email, password, role) VALUES
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Show success message
SELECT 'ComputerHub database setup completed successfully with ALL products and users!' AS message;

-- Show all users and their roles
SELECT id, name, email, role FROM users ORDER BY id; 