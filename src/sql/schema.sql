-- Disable foreign key checks to allow table drops/creation in any order
SET
    FOREIGN_KEY_CHECKS = 0;

-- =========================================
-- COMPANIES TABLE 
-- Represents different Pharmacies or Clinics
-- =========================================
DROP TABLE IF EXISTS companies;

CREATE TABLE
    companies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        registration_no VARCHAR(50) NOT NULL,
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB;

-- =========================================
-- USERS TABLE
-- Stores Registered Members and Administrators.
-- Admins are linked to a Company.
-- Public users are handled via session until they register.
-- =========================================
DROP TABLE IF EXISTS users;

CREATE TABLE
    users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT, -- Link user to a specific company (Only required for Admins!!)
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL, -- Stores HASHED password
        role ENUM ('member', 'admin') DEFAULT 'member',
        phone_number VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL
    ) ENGINE = InnoDB;

-- =========================================
-- CATEGORIES TABLE
-- Allows grouping products (e.g., Medicine, Vitamins)
-- Global categories shared across system for standardization
-- =========================================
DROP TABLE IF EXISTS categories;

CREATE TABLE
    categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    ) ENGINE = InnoDB;

-- =========================================
-- PRODUCTS TABLE
-- The inventory items managed by the Admin.
-- Scoped to a specific Company.
-- =========================================
DROP TABLE IF EXISTS products;

CREATE TABLE
    products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        category_id INT,
        name VARCHAR(150) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        stock_quantity INT NOT NULL DEFAULT 0,
        image_path VARCHAR(255), -- Path to image in /public/assets/images/
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
    ) ENGINE = InnoDB;

-- =========================================
-- ORDERS TABLE (Transaction Header)
-- Stores the summary of a purchase. Used for Reports.
-- =========================================
DROP TABLE IF EXISTS orders;

CREATE TABLE
    orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        payment_status ENUM ('pending', 'paid', 'failed') DEFAULT 'pending',
        payment_method VARCHAR(50) DEFAULT 'Credit Card',
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- =========================================
-- ORDER_ITEMS TABLE (Transaction Details)
-- Links Orders to Products. Stores the price at the moment of purchase.
-- =========================================
DROP TABLE IF EXISTS order_items;

CREATE TABLE
    order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10, 2) NOT NULL,
        subtotal DECIMAL(10, 2) GENERATED ALWAYS AS (quantity * price_at_purchase) STORED,
        FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
    ) ENGINE = InnoDB;

-- Re-enable foreign key checks
SET
    FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- SEED DATA (Default Login & Inventory)
-- =========================================
-- Create Companies
INSERT INTO
    companies (name, address)
VALUES
    ('SatayTech Central Pharmacy', '123 Main St, Kuching'),
    ('City Clinic & Dispensary', '456 North Rd, Kuching');

-- Create Categories
INSERT INTO
    categories (name, description)
VALUES
    ('Medicine', 'General pharmaceuticals'),
    ('Vitamins', 'Supplements and health boosters'),
    ('First Aid', 'Bandages and emergency kits');

-- Create Users
-- Admin Password: 'Pass123!' 
-- Admin assigned to Company 1 (SatayTech Central)
INSERT INTO
    users (company_id, full_name, email, password, role, phone_number)
VALUES
    (
        1,
        'Yong The Final Boss',
        'admin@sys.com',
        '$2a$12$mOfJjFxDPc.I4llrv/FO3elTEJ7sBYdukjHL.onUFYvUZorRXWB06',
        'admin',
        '012-3456789'
    );

-- Member Password: 'User123!'
-- Members are currently not bound to a company (global customers)
INSERT INTO
    users (company_id, full_name, email, password, role, phone_number)
VALUES
    (
        NULL,
        'Jerry Anak My',
        'jeremy@gmail.com',
        '$2a$12$H3uimer1oUAORAmxNqE.1.pcdwGs0cz2YSTY5yiHcZqWDy0f1nMri',
        'member',
        '019-8765432'
    );

-- 4. Create Products (Assigned to Company 1)
INSERT INTO
    products (
        company_id,
        category_id,
        name,
        description,
        price,
        stock_quantity
    )
VALUES
    (
        1,
        1,
        'Paracetamol 500mg',
        'Effective for fever and mild pain',
        12.00,
        100
    ),
    (
        1,
        1,
        'Cough Syrup',
        'Soothing syrup for dry cough',
        18.50,
        50
    ),
    (
        1,
        2,
        'Vitamin C 1000mg',
        'Immune system booster',
        30.00,
        75
    ),
    (
        1,
        3,
        'Bandage Roll',
        'Elastic bandage for injuries',
        5.50,
        200
    );

-- Create Products
INSERT INTO
    products (
        company_id,
        category_id,
        name,
        description,
        price,
        stock_quantity
    )
VALUES
    (
        2,
        1,
        'Paracetamol 500mg',
        'Generic brand',
        10.50,
        500
    ),
    (
        2,
        3,
        'Premium Bandages',
        'Waterproof',
        8.00,
        100
    );