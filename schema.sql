CREATE DATABASE IF NOT EXISTS `campusfind_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `campusfind_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('student', 'staff', 'admin') DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT IGNORE INTO `categories` (`category_name`) VALUES 
('Electronics'),
('ID Cards'),
('Bags'),
('Books'),
('Clothing'),
('Accessories'),
('Stationery'),
('Keys'),
('Others');

-- 3. Items Table (Lost & Found Reports)
CREATE TABLE IF NOT EXISTS `items` (
    `item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `report_type` ENUM('lost', 'found') NOT NULL,
    `item_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `location` VARCHAR(100) NOT NULL,
    `event_date` DATE NOT NULL,
    `event_time` TIME DEFAULT NULL,
    `image_path` VARCHAR(255) DEFAULT 'default_item.png',
    `contact_email` VARCHAR(150) NOT NULL,
    `status` ENUM('active', 'claimed', 'resolved') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Contact Inquiries Table
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Seed an initial Admin account (Password: Admin@123)
-- Hash generated using PASSWORD_DEFAULT
INSERT IGNORE INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`) VALUES
(1, 'CampusFind Administrator', 'admin@campusfind.rusl.ac.lk', '$2y$10$w85o3s2Fk9eIq.h2Kj4eIuYjI98711Yh93x2y3jkl1234567890ab', 'admin');

-- 6. Seed Sample Items (Matching the wireframes)
INSERT IGNORE INTO `items` (`report_type`, `item_name`, `category`, `description`, `location`, `event_date`, `event_time`, `image_path`, `contact_email`, `status`) VALUES
('lost', 'Mobile Phone', 'Electronics', 'Black Samsung Phone with protective case', 'Cafeteria', '2026-05-10', '10:00:00', 'phone.jpeg', 'student1@rusl.ac.lk', 'active'),
('lost', 'Key Set', 'Keys', '3 Keys with university keychain', 'Parking Area', '2026-05-25', '14:30:00', 'keys.jpg', 'student2@rusl.ac.lk', 'active'),
('lost', 'Umbrella', 'Accessories', 'Black foldable umbrella', 'Cafeteria', '2026-07-25', '12:00:00', 'umbrella.jpeg', 'staff1@rusl.ac.lk', 'active'),
('found', 'Black Backpack', 'Bags', 'Black School Backpack with books inside', 'ICT Building', '2026-07-25', '09:15:00', 'school bag.jpg', 'security@rusl.ac.lk', 'active'),
('found', 'Student ID Card', 'ID Cards', 'Rajarata University Student ID Card', 'Library', '2026-07-24', '11:30:00', 'id card.png', 'library@rusl.ac.lk', 'active'),
('found', 'Water Bottle', 'Others', 'Blue stainless steel water bottle', 'Lecture Hall', '2026-07-23', '14:15:00', 'water.jpg', 'cleaner@rusl.ac.lk', 'active');
