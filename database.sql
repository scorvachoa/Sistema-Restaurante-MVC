-- Database: restaurant_system
CREATE DATABASE IF NOT EXISTS restaurant_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_system;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB;

INSERT INTO roles (name) VALUES ('admin'), ('rider'), ('client');

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE product_extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rider_id INT NULL,
    address_id INT NOT NULL,
    status ENUM('pending','confirmed','assigned','picked_up','on_the_way','delivered','cancelled') DEFAULT 'pending',
    delivery_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (rider_id) REFERENCES users(id),
    FOREIGN KEY (address_id) REFERENCES addresses(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE order_item_extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    extra_id INT NOT NULL,
    extra_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    FOREIGN KEY (extra_id) REFERENCES product_extras(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    method ENUM('cash','yape','plin','bank_transfer') NOT NULL,
    status ENUM('pending','review','approved','rejected') DEFAULT 'pending',
    proof_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB;

CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    rider_id INT NOT NULL,
    status ENUM('assigned','picked_up','on_the_way','delivered','cancelled') DEFAULT 'assigned',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (rider_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Indexes for performance
ALTER TABLE users ADD INDEX idx_users_role (role_id), ADD INDEX idx_users_email (email);
ALTER TABLE addresses ADD INDEX idx_addresses_user (user_id);
ALTER TABLE products ADD INDEX idx_products_category (category_id);
ALTER TABLE product_extras ADD INDEX idx_product_extras_product (product_id);
ALTER TABLE orders ADD INDEX idx_orders_user (user_id), ADD INDEX idx_orders_rider (rider_id), ADD INDEX idx_orders_status (status), ADD INDEX idx_orders_created (created_at);
ALTER TABLE order_items ADD INDEX idx_order_items_order (order_id), ADD INDEX idx_order_items_product (product_id);
ALTER TABLE order_item_extras ADD INDEX idx_order_item_extras_item (order_item_id), ADD INDEX idx_order_item_extras_extra (extra_id);
ALTER TABLE payments ADD INDEX idx_payments_order (order_id), ADD INDEX idx_payments_status (status), ADD INDEX idx_payments_method (method);
ALTER TABLE deliveries ADD INDEX idx_deliveries_order (order_id), ADD INDEX idx_deliveries_rider (rider_id), ADD INDEX idx_deliveries_status (status);
