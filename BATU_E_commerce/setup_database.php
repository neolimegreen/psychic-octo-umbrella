<?php
/**
 * Database Setup Script
 * 
 * This script creates the database and imports the SQL schema
 */

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Setup</h1>";

try {
    // Connect to MySQL server without selecting a database
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>Connected to MySQL server successfully.</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS batu_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>Database 'batu_ecommerce' created or already exists.</p>";
    
    // Select the database
    $pdo = new PDO('mysql:host=localhost;dbname=batu_ecommerce', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>Database 'batu_ecommerce' selected.</p>";
    
    // Create tables directly to ensure they are created correctly
    
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('customer', 'admin') DEFAULT 'customer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>Table 'users' created successfully.</p>";
    
    // Products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>Table 'products' created successfully.</p>";
    
    // Orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            status ENUM('pending', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "<p>Table 'orders' created successfully.</p>";
    
    // Order items table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price_each DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ");
    echo "<p>Table 'order_items' created successfully.</p>";
    
    // Reviews table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "<p>Table 'reviews' created successfully.</p>";
    
    // Wishlist table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            UNIQUE KEY user_product (user_id, product_id)
        )
    ");
    echo "<p>Table 'wishlist' created successfully.</p>";
    
    // Insert admin user (password: admin123)
    $pdo->exec("
        INSERT INTO users (name, email, password, role) 
        VALUES ('Admin User', 'admin@example.com', '$2y$10$8WxhVZkqOhZ1NjJHVJiSxOQFZCbIVXOFkHkwszNqIzJUb6WNgHtry', 'admin')
        ON DUPLICATE KEY UPDATE id=id
    ");
    echo "<p>Admin user created successfully.</p>";
    
    // Insert sample products
    $pdo->exec("
        INSERT INTO products (name, description, price, stock, image_url) VALUES
        ('Smartphone X', 'Latest smartphone with advanced features', 699.99, 50, 'smartphone.jpg'),
        ('Laptop Pro', 'High-performance laptop for professionals', 1299.99, 30, 'laptop.jpg'),
        ('Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 100, 'headphones.jpg'),
        ('Smart Watch', 'Fitness and health tracking smartwatch', 249.99, 75, 'smartwatch.jpg'),
        ('Bluetooth Speaker', 'Portable wireless speaker with deep bass', 89.99, 120, 'speaker.jpg')
    ");
    echo "<p>Sample products inserted successfully.</p>";
    
    echo "<p><strong>Setup completed successfully!</strong></p>";
    echo "<p><a href='/BATU_E_commerce/'>Go to the homepage</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please make sure your MySQL server is running and the credentials are correct.</p>";
}
