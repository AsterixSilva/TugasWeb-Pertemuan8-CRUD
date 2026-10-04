CREATE DATABASE IF NOT EXISTS inventaris_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE inventaris_db;

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_products_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO categories (name) VALUES
('Laptop'),
('Aksesoris'),
('Komponen'),
('Periferal'),
('Jaringan');

INSERT INTO suppliers (name, phone) VALUES
('PT Teknologi Nusantara', '081234567801'),
('CV Digital Jaya', '081234567802'),
('PT Komputer Mandiri', '081234567803'),
('CV Sumber Data', '081234567804'),
('PT Solusi Informatika', '081234567805');

INSERT INTO products (name, category_id, supplier_id, price, stock) VALUES
('Lenovo ThinkPad L14', 1, 1, 8500000, 8),
('Logitech M331 Mouse', 2, 2, 325000, 15),
('Kingston 16GB DDR4 RAM', 3, 3, 650000, 12),
('Mechanical Keyboard RK61', 4, 4, 575000, 10),
('TP-Link Archer C6', 5, 5, 720000, 7);
