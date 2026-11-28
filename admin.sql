-- Member 4 tables: Administrator Management

CREATE DATABASE salary_tracker;

USE salary_tracker;

CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- View all registered users
SELECT * FROM Users;

-- Search users by name
SELECT * FROM Users WHERE name LIKE '%Shova%';

-- Count total users
SELECT COUNT(*) AS total_users FROM Users;

-- View administrators
SELECT * FROM Users WHERE role = 'admin';

-- Update user information
UPDATE Users SET name = 'Updated Name' WHERE user_id = 1;

-- Change user role
UPDATE Users SET role = 'admin' WHERE user_id = 1;

-- Deactivate user account
UPDATE Users SET is_active = 0 WHERE user_id = 1;