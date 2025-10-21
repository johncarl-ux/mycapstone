-- OPMDC database schema (users table)
-- Import with: mysql -u root -p < opmdc_db.sql

CREATE DATABASE IF NOT EXISTS opmdc DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE opmdc;

-- users table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  role ENUM('Barangay Official','OPMDC Staff','OPMDC Head') NOT NULL DEFAULT 'OPMDC Staff',
  barangayName VARCHAR(255) DEFAULT NULL,
  status ENUM('pending','approved','active','disabled') NOT NULL DEFAULT 'approved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example accounts (password: password)
INSERT INTO users (username, email, password, name, role, barangayName, status) VALUES
('admin','admin@example.com','$2y$10$KbQiC0dJZ2g0Y5sQqzQeUuW9b5nQe9y5Wv0dK1u6k9P0F7ZVgX9Wy','OPMDC Admin','OPMDC Head',NULL,'approved'),
('staff','staff@example.com','$2y$10$KbQiC0dJZ2g0Y5sQqzQeUuW9b5nQe9y5Wv0dK1u6k9P0F7ZVgX9Wy','OPMDC Staff','OPMDC Staff',NULL,'approved'),
('brgy1','brgy1@example.com','$2y$10$KbQiC0dJZ2g0Y5sQqzQeUuW9b5nQe9y5Wv0dK1u6k9P0F7ZVgX9Wy','Barangay Official 1','Barangay Official','Barangay 1','approved');

-- The hash above corresponds to the plaintext password: password

-- requests table: stores requests submitted by barangays for staff/head review
DROP TABLE IF EXISTS requests;
CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barangay VARCHAR(255) NOT NULL,
  request_type VARCHAR(255) NOT NULL,
  urgency VARCHAR(50) DEFAULT 'Medium',
  location VARCHAR(255) DEFAULT NULL,
  description TEXT,
  email VARCHAR(255) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  attachment VARCHAR(255) DEFAULT NULL,
  status ENUM('Pending','Approved','Declined') NOT NULL DEFAULT 'Pending',
  history JSON DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

