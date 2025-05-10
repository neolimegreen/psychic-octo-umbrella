<?php
/**
 * Main entry point for the E-Commerce application
 * 
 * This file initializes the application, sets up autoloading,
 * and handles routing requests to the appropriate controllers.
 */

// Define the application root directory
define('ROOT_DIR', dirname(__DIR__));

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Autoloader function
spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Define the base directory for classes
    $base_dir = ROOT_DIR . DIRECTORY_SEPARATOR;
    
    // Build the file path
    $file = $base_dir . $class . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Get the requested URL
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Initialize the router
$router = new \App\Core\Router();

// Define routes
// Home routes
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('home', ['controller' => 'HomeController', 'action' => 'index']);

// Auth routes
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);

// Product routes
$router->add('products', ['controller' => 'ProductController', 'action' => 'index']);
$router->add('products/{id}', ['controller' => 'ProductController', 'action' => 'show']);

// Admin routes
$router->add('admin', ['controller' => 'AdminController', 'action' => 'index']);
$router->add('admin/products', ['controller' => 'AdminController', 'action' => 'products']);
$router->add('admin/products/add', ['controller' => 'AdminController', 'action' => 'addProduct']);
$router->add('admin/products/edit/{id}', ['controller' => 'AdminController', 'action' => 'editProduct']);
$router->add('admin/products/delete/{id}', ['controller' => 'AdminController', 'action' => 'deleteProduct']);
$router->add('admin/orders', ['controller' => 'AdminController', 'action' => 'orders']);
$router->add('admin/orders/{id}', ['controller' => 'AdminController', 'action' => 'showOrder']);

// Cart routes
$router->add('cart', ['controller' => 'CartController', 'action' => 'index']);
$router->add('cart/add/{id}', ['controller' => 'CartController', 'action' => 'add']);
$router->add('cart/remove/{id}', ['controller' => 'CartController', 'action' => 'remove']);
$router->add('cart/update/{id}', ['controller' => 'CartController', 'action' => 'update']);
$router->add('checkout', ['controller' => 'CartController', 'action' => 'checkout']);
$router->add('confirm-order', ['controller' => 'CartController', 'action' => 'confirmOrder']);

// Order routes
$router->add('orders', ['controller' => 'OrderController', 'action' => 'index']);
$router->add('orders/{id}', ['controller' => 'OrderController', 'action' => 'show']);

// Review routes (bonus feature)
$router->add('products/{id}/reviews', ['controller' => 'ReviewController', 'action' => 'index']);
$router->add('products/{id}/reviews/add', ['controller' => 'ReviewController', 'action' => 'add']);

// Wishlist routes (bonus feature)
$router->add('wishlist', ['controller' => 'WishlistController', 'action' => 'index']);
$router->add('wishlist/add/{id}', ['controller' => 'WishlistController', 'action' => 'add']);
$router->add('wishlist/remove/{id}', ['controller' => 'WishlistController', 'action' => 'remove']);

// Set 404 handler
$router->setNotFoundHandler(function() {
    header("HTTP/1.0 404 Not Found");
    include ROOT_DIR . '/app/views/errors/404.php';
});

// Dispatch the route
try {
    $router->dispatch($url);
} catch (Exception $e) {
    // Log the error
    error_log($e->getMessage());
    
    // Display error page
    header("HTTP/1.0 500 Internal Server Error");
    include ROOT_DIR . '/app/views/errors/500.php';
}