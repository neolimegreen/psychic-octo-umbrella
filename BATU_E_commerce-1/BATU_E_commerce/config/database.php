<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the E-Commerce platform.
 */

return [
    'host' => 'localhost',
    'dbname' => 'batu_ecommerce',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];