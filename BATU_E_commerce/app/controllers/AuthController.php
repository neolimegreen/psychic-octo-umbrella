<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\User;

/**
 * Auth Controller
 * 
 * Handles user authentication operations.
 */
class AuthController extends BaseController {
    private $userModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->userModel = new User();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Display login form
     * 
     * @return void
     */
    public function login() {
        // If user is already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
        }
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            
            // Validate input
            $errors = [];
            
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }
            
            // If no errors, attempt login
            if (empty($errors)) {
                if ($this->auth->login($email, $password)) {
                    // Redirect to intended page or home
                    $redirect = $_SESSION['redirect_after_login'] ?? '';
                    unset($_SESSION['redirect_after_login']);
                    
                    $this->redirect($redirect);
                } else {
                    $errors['login'] = 'Invalid email or password';
                }
            }
            
            // If we got here, there were errors or login failed
            $this->render('auth/login', [
                'title' => 'Login',
                'errors' => $errors,
                'email' => $email
            ]);
        } else {
            // Display the login form
            $this->render('auth/login', [
                'title' => 'Login'
            ]);
        }
    }
    
    /**
     * Display registration form
     * 
     * @return void
     */
    public function register() {
        // If user is already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
        }
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->getPost('name');
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirm_password');
            
            // Validate input
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Name is required';
            }
            
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Email already in use';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            // If no errors, create user
            if (empty($errors)) {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role' => 'customer'
                ];
                
                $userId = $this->userModel->create($userData);
                
                if ($userId) {
                    // Auto login after registration
                    $this->auth->login($email, $password);
                    
                    $_SESSION['flash']['success'] = 'Registration successful! Welcome to BATU E-Commerce.';
                    $this->redirect('');
                } else {
                    $_SESSION['flash']['error'] = 'Registration failed. Please try again.';
                }
            }
            
            // If we got here, there were errors or registration failed
            $this->render('auth/register', [
                'title' => 'Register',
                'errors' => $errors,
                'name' => $name,
                'email' => $email
            ]);
        } else {
            // Display the registration form
            $this->render('auth/register', [
                'title' => 'Register'
            ]);
        }
    }
    
    /**
     * Log out the current user
     * 
     * @return void
     */
    public function logout() {
        $this->auth->logout();
        $_SESSION['flash']['success'] = 'You have been logged out';
        $this->redirect('');
    }
}