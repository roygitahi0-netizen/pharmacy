-- Create Database
CREATE DATABASE IF NOT EXISTS pharmacy_db;
USE pharmacy_db;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100),
    description TEXT,
    image VARCHAR(255),
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200),
    category_id INT,
    generic_name VARCHAR(200),
    brand VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2),
    quantity INT DEFAULT 0,
    prescription_required TINYINT DEFAULT 0,
    expiry_date DATE,
    image VARCHAR(255),
    images TEXT,
    rating DECIMAL(3,2) DEFAULT 0,
    reviews INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart Table
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE,
    user_id INT,
    subtotal DECIMAL(10,2),
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2),
    payment_method ENUM('cash', 'card', 'mobile') DEFAULT 'cash',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_zip VARCHAR(20),
    prescription_image VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    product_name VARCHAR(200),
    product_price DECIMAL(10,2),
    quantity INT,
    total DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Prescriptions Table
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_id INT,
    prescription_image VARCHAR(255),
    doctor_name VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by INT,
    verified_at TIMESTAMP NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);

-- Insert Admin User
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@pharmacy.com', MD5('admin123'), 'admin');

-- Insert Categories
INSERT INTO categories (name, slug, description) VALUES
('Prescription Drugs', 'prescription-drugs', 'Medications requiring doctor prescription'),
('Over the Counter', 'over-the-counter', 'General medications without prescription'),
('Vitamins & Supplements', 'vitamins-supplements', 'Dietary supplements and vitamins'),
('First Aid', 'first-aid', 'First aid supplies and equipment'),
('Personal Care', 'personal-care', 'Personal hygiene products'),
('Baby Care', 'baby-care', 'Baby products and supplies'),
('Medical Equipment', 'medical-equipment', 'Medical devices and equipment'),
('Herbal Products', 'herbal-products', 'Natural and herbal supplements');

-- Insert Sample Products
INSERT INTO products (name, slug, category_id, generic_name, brand, description, price, compare_price, quantity, prescription_required, rating) VALUES
('Paracetamol 500mg', 'paracetamol-500mg', 2, 'Acetaminophen', 'Tylenol', 'For fever and pain relief. Effective for headaches, toothaches, and body pains.', 5.99, 7.99, 500, 0, 4.5),
('Amoxicillin 500mg', 'amoxicillin-500mg', 1, 'Amoxicillin', 'Generic', 'Antibiotic for bacterial infections. Prescription required.', 15.99, 19.99, 200, 1, 4.8),
('Vitamin C 1000mg', 'vitamin-c-1000mg', 3, 'Ascorbic Acid', 'NatureWay', 'Immune system support with antioxidants.', 12.99, 15.99, 300, 0, 4.6),
('Complete First Aid Kit', 'complete-first-aid-kit', 4, 'First Aid Kit', 'MedicalKit', 'Complete first aid supplies for home and travel.', 29.99, 39.99, 100, 0, 4.7),
('Blood Pressure Monitor', 'blood-pressure-monitor', 7, 'BP Monitor', 'Omron', 'Digital blood pressure device with memory storage.', 89.99, 109.99, 50, 0, 4.9),
('Premium Baby Diapers', 'premium-baby-diapers', 6, 'Diapers', 'Pampers', 'Premium baby diapers size M, pack of 50.', 19.99, 24.99, 200, 0, 4.5),
('Hand Sanitizer Gel', 'hand-sanitizer-gel', 5, 'Sanitizer', 'Purell', 'Alcohol-based hand sanitizer, 500ml.', 4.99, 6.99, 1000, 0, 4.3),
('Garlic Extract Capsules', 'garlic-extract-capsules', 8, 'Garlic Extract', 'Herbalife', 'Natural garlic supplements for heart health.', 18.99, 22.99, 150, 0, 4.4),
('Insulin Pen', 'insulin-pen', 1, 'Insulin', 'Novo Nordisk', 'Fast-acting insulin for diabetes management.', 45.99, 54.99, 80, 1, 4.9),
('N95 Face Mask', 'n95-face-mask', 5, 'Face Mask', '3M', 'N95 respirator mask for protection.', 2.99, 4.99, 2000, 0, 4.6),
('Calcium Tablets', 'calcium-tablets', 3, 'Calcium Carbonate', 'Caltrate', 'Calcium supplement for bone health.', 14.99, 18.99, 250, 0, 4.5),
('Digital Thermometer', 'digital-thermometer', 7, 'Thermometer', 'Braun', 'Fast and accurate digital thermometer.', 24.99, 29.99, 120, 0, 4.7);