-- ============================================================
-- SmartHostel Database Schema
-- FOR INFINITYFREE: Import this file AFTER creating the
-- database through the InfinityFree control panel.
-- The CREATE DATABASE and USE lines have been removed.
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    method VARCHAR(50) NOT NULL,
    account_no VARCHAR(50),
    transaction_id VARCHAR(50),
    total VARCHAR(50),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('bf', 'lunch', 'dinner') NOT NULL UNIQUE,
    menu_text VARCHAR(255) NOT NULL,
    price INT NOT NULL
);

-- Insert default menu items
INSERT IGNORE INTO menu (type, menu_text, price) VALUES 
('bf', 'Khichuri & Egg', 45),
('lunch', 'Rice, Fish Curry, Dal', 75),
('dinner', 'Rice, Chicken & Vorta', 85);

CREATE TABLE IF NOT EXISTS gate_passes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    status ENUM('IN', 'OUT') NOT NULL DEFAULT 'IN',
    entered_time VARCHAR(20),
    leaving_time VARCHAR(20),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_email)
);

CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    detail VARCHAR(255),
    qty INT DEFAULT 0,
    cost INT DEFAULT 0,
    time VARCHAR(20),
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sos_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    status ENUM('Active', 'Resolved') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
