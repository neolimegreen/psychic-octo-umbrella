<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Wishlist;
use App\Models\Product;

/**
 * Wishlist Controller
 * 
 * Handles wishlist operations.
 */
class WishlistController extends BaseController {
    private $wishlistModel;
    private $productModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->wishlistModel = new Wishlist();
        $this->productModel = new Product();
        $this->auth = Auth::getInstance();
        
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to access your wishlist';
            $this->redirect('login');
        }
    }
    
    /**
     * Display wishlist items
     * 
     * @return void
     */
    public function index() {
        $userId = $this->auth->getUser()['id'];
        $wishlistItems = $this->wishlistModel->getByUserId($userId);
        
        $this->render('wishlist/index', [
            'title' => 'My Wishlist',
            'wishlistItems' => $wishlistItems
        ]);
    }
    
    /**
     * Add product to wishlist
     * 
     * @return void
     */
    public function add() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $_SESSION['flash']['error'] = 'Product ID is required';
            $this->redirect('products');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $_SESSION['flash']['error'] = 'Product not found';
            $this->redirect('products');
        }
        
        $userId = $this->auth->getUser()['id'];
        
        $result = $this->wishlistModel->add($userId, $productId);
        
        if ($result) {
            $_SESSION['flash']['success'] = 'Product added to wishlist';
        } else {
            $_SESSION['flash']['error'] = 'Failed to add product to wishlist';
        }
        
        // Redirect back to product page or referrer
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
            header('Location: ' . $referer);
            exit;
        } else {
            $this->redirect("products/{$productId}");
        }
    }
    
    /**
     * Remove product from wishlist
     * 
     * @return void
     */
    public function remove() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $_SESSION['flash']['error'] = 'Product ID is required';
            $this->redirect('wishlist');
        }
        
        $userId = $this->auth->getUser()['id'];
        
        $result = $this->wishlistModel->remove($userId, $productId);
        
        if ($result) {
            $_SESSION['flash']['success'] = 'Product removed from wishlist';
        } else {
            $_SESSION['flash']['error'] = 'Failed to remove product from wishlist';
        }
        
        // Redirect back to the referring page or product page
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
            header('Location: ' . $referer);
            exit;
        } else {
            $this->redirect('products/' . $productId);
        }
    }
}