<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Review;

/**
 * Product Controller
 * 
 * Handles product-related operations.
 */
class ProductController extends BaseController {
    private $productModel;
    private $reviewModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->productModel = new Product();
        $this->reviewModel = new Review();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Display product listing
     * 
     * @return void
     */
    public function index() {
        // Pagination
        $page = (int) ($this->getQuery('page') ?? 1);
        $perPage = 12; // Products per page
        $offset = ($page - 1) * $perPage;
        
        // Search functionality
        $search = $this->getQuery('search');
        
        if ($search) {
            $products = $this->productModel->search($search);
            $totalProducts = count($products);
        } else {
            $products = $this->productModel->getAll($perPage, $offset);
            $totalProducts = $this->productModel->getCount();
        }
        
        $totalPages = ceil($totalProducts / $perPage);
        
        $this->render('products/index', [
            'title' => 'Products',
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }
    
    /**
     * Display product details
     * 
     * @return void
     */
    public function show() {
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
        
        // Get reviews for this product
        $reviews = $this->reviewModel->getByProductId($productId);
        $averageRating = $this->reviewModel->getAverageRating($productId);
        $reviewCount = $this->reviewModel->getCount($productId);
        
        // Check if product is in user's wishlist
        $inWishlist = false;
        if ($this->auth->isLoggedIn()) {
            $userId = $this->auth->getUser()['id'];
            $wishlistModel = new \App\Models\Wishlist();
            $inWishlist = $wishlistModel->exists($userId, $productId);
        }
        
        $this->render('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'inWishlist' => $inWishlist
        ]);
    }
}