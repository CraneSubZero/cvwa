-- FILE: init_db.sql
-- PURPOSE: Initialize database schema and sample data

-- Create database
CREATE DATABASE IF NOT EXISTS cvwa_db;
USE cvwa_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- XSS messages
CREATE TABLE IF NOT EXISTS xss_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample users
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$10$EXAMPLEHASHADMIN', 'admin@cvwa.edu'),
('student', '$2y$10$EXAMPLEHASHSTUDENT', 'student@cvwa.edu'),
('guest', '$2y$10$EXAMPLEHASHGUEST', 'guest@cvwa.edu');

-- Create application user with limited privileges
CREATE USER 'cvwa_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE ON cvwa_db.* TO 'cvwa_user'@'localhost';
FLUSH PRIVILEGES;