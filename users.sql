-- Use the same database as your tracking table
USE nexus_fright;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example user (password is 'password123' hashed with bcrypt)
INSERT INTO users (email, password, verified) VALUES
('testuser@example.com', '$2y$10$eImiTXuWVxfM37uY4JANjQ==', 1); 