<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;

/**
 * Home Controller
 * 
 * Handles home page operations.
 */
class HomeController extends BaseController {
    private $productModel;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->productModel = new Product();
    }
    
    /**
     * Display home page
     * 
     * @return void
     */
    public function index() {
        // Get featured products (latest products)
        $featuredProducts = $this->productModel->getAll(6);
        
        $this->render('home/index', [
            'title' => 'Welcome to BATU E-Commerce',
            'featuredProducts' => $featuredProducts
        ]);
    }
}