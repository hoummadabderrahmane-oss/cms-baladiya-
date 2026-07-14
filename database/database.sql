-- =====================================================
-- SGC v1.0
-- Système de Gestion Communale
-- Database
-- =====================================================

CREATE DATABASE IF NOT EXISTS sgc
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sgc;

-- ==========================================
-- TABLE USERS
-- ==========================================

CREATE TABLE users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    fullname VARCHAR(150) NOT NULL,

    email VARCHAR(150) UNIQUE NOT NULL,

    password VARCHAR(255) NOT NULL,

    role ENUM('admin','agent') DEFAULT 'agent',

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

-- ==========================================
-- TABLE CITOYENS
-- ==========================================

CREATE TABLE citoyens (

    id INT AUTO_INCREMENT PRIMARY KEY,

    cin VARCHAR(20) UNIQUE NOT NULL,

    firstname VARCHAR(100) NOT NULL,

    lastname VARCHAR(100) NOT NULL,

    birth_date DATE,

    gender ENUM('Homme','Femme'),

    address TEXT,

    phone VARCHAR(30),

    email VARCHAR(150),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

-- ==========================================
-- TABLE DOCUMENTS
-- ==========================================

CREATE TABLE documents (

    id INT AUTO_INCREMENT PRIMARY KEY,

    citoyen_id INT NOT NULL,

    document_type VARCHAR(100),

    description TEXT,

    created_by INT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL

);

-- ==========================================
-- TABLE ACTIVITY LOGS
-- ==========================================

CREATE TABLE activity_logs (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT,

    action TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL

);

-- ==========================================
-- TABLE SETTINGS
-- ==========================================

CREATE TABLE settings (

    id INT AUTO_INCREMENT PRIMARY KEY,

    site_name VARCHAR(150),

    commune_name VARCHAR(150),

    language ENUM('fr','ar') DEFAULT 'fr',

    logo VARCHAR(255)

);

INSERT INTO settings(

site_name,
commune_name,
language

)

VALUES(

'SGC',

'Commune',

'fr'

);