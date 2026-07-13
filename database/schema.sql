-- CMS Baladiya Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS cms_baladiya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cms_baladiya;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    department VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Citizens table
CREATE TABLE citizens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    id_number VARCHAR(50) UNIQUE,
    date_of_birth DATE,
    gender ENUM('male', 'female') DEFAULT 'male',
    phone VARCHAR(20),
    filenum VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Documents table
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT,
    title VARCHAR(200) NOT NULL,
    doc_type VARCHAR(50) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Requests table
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    request_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_by INT,
    approved_by INT,
    approved_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role, department, status) VALUES 
('admin', 'admin123', 'System Administrator', 'admin@baladiya.local', 'admin', 'IT', 'active');



-- Insert sample documents
INSERT INTO documents (citizen_id, title, doc_type, description, created_by) VALUES
(1, 'National ID Card', 'ID Card', 'Copy of national identification card', 1),
(1, 'Birth Certificate', 'Birth Certificate', 'Official birth certificate', 1),
(2, 'Residence Proof', 'Residence Proof', 'Utility bill as proof of residence', 1);

-- Insert sample requests
INSERT INTO requests (citizen_id, request_type, description, status, created_by) VALUES
(1, 'Residence Certificate', 'Requesting official residence certificate for visa application', 'pending', 1),
(2, 'Birth Certificate', 'Need certified copy of birth certificate for passport renewal', 'approved', 1),
(3, 'Permit Request', 'Requesting construction permit for residential building', 'pending', 1);