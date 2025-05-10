<?php
/**
 * Main entry point for the BATU E-Commerce application
 * 
 * This file initializes the application, sets up autoloading,
 * and handles routing requests to the appropriate controllers.
 *
 * @author BATU E-Commerce Team
 * @version 1.0
 */

// Define the application root directory
define('ROOT_DIR', __DIR__);

// Set public directory path
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Error reporting settings (enable for debugging, disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader function for class loading
spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    // Fix double backslash issue in Windows paths
    $class = str_replace('\\', '/', $class);
    
    // Define the base directory for classes
    $base_dir = ROOT_DIR . '/';
    
    // Build the file path
    $file = $base_dir . $class . '.php';
    
    // Convert path separators for Windows
    $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    } else {
        // For debugging - uncomment if needed
        // error_log('Could not load class: ' . $class . ' - File not found: ' . $file);
    }
});

// Get the requested URL from query string
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Initialize the router
$router = new \App\Core\Router();

/**
 * Define application routes
 */

// Home routes
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('home', ['controller' => 'HomeController', 'action' => 'index']);

// Authentication routes
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

// Review routes
$router->add('products/{id}/reviews', ['controller' => 'ReviewController', 'action' => 'index']);
$router->add('products/{id}/reviews/add', ['controller' => 'ReviewController', 'action' => 'add']);

// Wishlist routes
$router->add('wishlist', ['controller' => 'WishlistController', 'action' => 'index']);
$router->add('wishlist/add/{id}', ['controller' => 'WishlistController', 'action' => 'add']);
$router->add('wishlist/remove/{id}', ['controller' => 'WishlistController', 'action' => 'remove']);

// Set 404 handler for routes not found
$router->setNotFoundHandler(function() {
    header("HTTP/1.0 404 Not Found");
    include ROOT_DIR . '/app/views/errors/404.php';
});

// Enable more detailed error output for debugging
if (ini_get('display_errors')) {
    // Create error handler function
    function customErrorHandler($errno, $errstr, $errfile, $errline) {
        error_log("Error [$errno] $errstr in $errfile on line $errline");
        return false; // Continue with PHP's internal error handler
    }
    
    // Set custom error handler
    set_error_handler('customErrorHandler');
}

// Dispatch the route and handle exceptions
try {
    // Process the route and execute the appropriate controller action
    $router->dispatch($url);
} catch (\Exception $e) {
    // Log the error for debugging with detailed information
    $errorMessage = 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    error_log($errorMessage);
    
    // For development: show detailed error information
    if (ini_get('display_errors')) {
        echo '<h1>Application Error</h1>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<h2>Stack Trace:</h2>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        exit;
    }
    
    // For production: display user-friendly error page
    header("HTTP/1.0 500 Internal Server Error");
    include ROOT_DIR . '/app/views/errors/500.php';
}
