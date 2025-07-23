USE nexus_fright;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Store hashed passwords!
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example admin user (username: admin, password: admin123 hashed with bcrypt)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$wH8QwQwQwQwQwQwQwQwQeOQwQwQwQwQwQwQwQwQwQwQwQwQwq'); 