<?php
namespace App\Core;

/**
 * Auth Class
 * 
 * Handles user authentication, session management, and role-based access control.
 */
class Auth {
    private static $instance = null;
    private $user = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $this->loadUser($_SESSION['user_id']);
        }
    }
    
    /**
     * Get singleton instance of Auth
     * 
     * @return Auth
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load user data from database
     * 
     * @param int $userId
     * @return bool
     */
    private function loadUser($userId) {
        $db = Database::getInstance();
        $user = $db->query("SELECT id, name, email, role FROM users WHERE id = :id")
                  ->bind(['id' => $userId])
                  ->single();
        
        if ($user) {
            $this->user = $user;
            return true;
        }
        
        return false;
    }
    
    /**
     * Attempt to log in a user
     * 
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        $db = Database::getInstance();
        $user = $db->query("SELECT id, name, email, password, role FROM users WHERE email = :email")
                  ->bind(['email' => $email])
                  ->single();
        
        if ($user && password_verify($password, $user['password'])) {
            // Store user ID in session
            $_SESSION['user_id'] = $user['id'];
            
            // Load user data
            $this->user = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Log out the current user
     * 
     * @return void
     */
    public function logout() {
        // Unset user session variables
        unset($_SESSION['user_id']);
        
        // Unset user data
        $this->user = null;
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public function isLoggedIn() {
        return $this->user !== null;
    }
    
    /**
     * Check if current user is an admin
     * 
     * @return bool
     */
    public function isAdmin() {
        return $this->isLoggedIn() && $this->user['role'] === 'admin';
    }
    
    /**
     * Get current user data
     * 
     * @return array|null
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public function getUserId() {
        return $this->user ? $this->user['id'] : null;
    }
    
    /**
     * Register a new user
     * 
     * @param array $userData
     * @return bool|int Returns user ID on success, false on failure
     */
    public function register($userData) {
        // Hash the password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Set default role if not provided
        if (!isset($userData['role'])) {
            $userData['role'] = 'customer';
        }
        
        $db = Database::getInstance();
        
        try {
            $db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)")
               ->bind([
                   'name' => $userData['name'],
                   'email' => $userData['email'],
                   'password' => $userData['password'],
                   'role' => $userData['role']
               ])
               ->execute();
            
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            // Email might be duplicate or other database error
            return false;
        }
    }
    
    /**
     * Require user to be logged in
     * Redirects to login page if not logged in
     * 
     * @return bool
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /BATU_E_commerce/login');
            exit;
            return false;
        }
        
        return true;
    }
    
    /**
     * Require user to be an admin
     * Redirects to home page if not admin
     * 
     * @return bool
     */
    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: /BATU_E_commerce/');
            exit;
            return false;
        }
        
        return true;
    }
}