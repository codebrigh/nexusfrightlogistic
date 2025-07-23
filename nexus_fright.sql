-- Create the database
CREATE DATABASE IF NOT EXISTS nexus_fright;
USE nexus_fright;

-- Create the tracking table
CREATE TABLE IF NOT EXISTS tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_number VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL,
    last_update VARCHAR(255) NOT NULL,
    estimated_delivery VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);

-- (Optional) Example data
INSERT INTO tracking (tracking_number, status, last_update, estimated_delivery, email) VALUES
('NF123456789', 'In Transit', 'Package arrived at sorting facility - New York', '2024-07-10', 'user1@example.com'),
('NF987654321', 'Delivered', 'Package delivered successfully - Los Angeles', '2024-07-05', 'user2@example.com');

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (email, password, verified) VALUES
('testuser@example.com', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 1);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$wH8QwQwQwQwQwQwQwQwQeOQwQwQwQwQwQwQwQwQwQwQwQwQwq'); 