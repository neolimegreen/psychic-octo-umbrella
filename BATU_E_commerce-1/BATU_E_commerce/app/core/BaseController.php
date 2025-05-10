<?php
namespace App\Core;

/**
 * Base Controller
 * 
 * Abstract base controller that all other controllers will extend.
 * Provides common functionality like rendering views and redirecting.
 */
abstract class BaseController {
    protected $route_params = [];
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     * @return void
     */
    public function __construct($route_params) {
        $this->route_params = $route_params;
    }
    
    /**
     * Render a view
     * 
     * @param string $view The view file
     * @param array $data Data for the view
     * @return void
     */
    protected function render($view, $data = []) {
        // Extract data to make it available to the view
        if (!empty($data) && is_array($data)) {
            extract($data);
        }
        
        // Get the view file path
        $file = dirname(dirname(__DIR__)) . '/app/views/' . $view . '.php';
        
        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("View $file not found");
        }
    }
    
    /**
     * Redirect to another page
     * 
     * @param string $url The URL to redirect to
     * @return void
     */
    protected function redirect($url) {
        // Check if URL is relative or absolute
        if (strpos($url, 'http') !== 0) {
            $url = '/BATU_E_commerce/' . ltrim($url, '/');
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Get POST data
     * 
     * @param string $key The key to get from POST data
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        
        return isset($_POST[$key]) ? $this->sanitizeInput($_POST[$key]) : $default;
    }
    
    /**
     * Get GET data
     * 
     * @param string $key The key to get from GET data
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return isset($_GET[$key]) ? $this->sanitizeInput($_GET[$key]) : $default;
    }
    
    /**
     * Sanitize input data
     * 
     * @param mixed $input The input to sanitize
     * @return mixed
     */
    protected function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitizeInput($value);
            }
            return $input;
        }
        
        // For strings, apply sanitization
        if (is_string($input)) {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        
        return $input;
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     * 
     * @param string $token The token to verify
     * @return bool
     */
    protected function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Set flash message
     * 
     * @param string $key The key for the message
     * @param string $message The message
     * @return void
     */
    protected function setFlash($key, $message) {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get flash message and remove it
     * 
     * @param string $key The key for the message
     * @return string|null
     */
    protected function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        
        return null;
    }
}