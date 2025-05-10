<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Order;

/**
 * Cart Controller
 * 
 * Handles shopping cart operations.
 */
class CartController extends BaseController {
    private $productModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->productModel = new Product();
        $this->auth = Auth::getInstance();
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Display cart contents
     * 
     * @return void
     */
    public function index() {
        $cart = [];
        $total = 0;
        
        // Get product details for each item in cart
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                $subtotal = $product['price'] * $quantity;
                $cart[] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'image_url' => $product['image_url']
                ];
                
                $total += $subtotal;
            }
        }
        
        $this->render('cart/index', [
            'title' => 'Shopping Cart',
            'cart' => $cart,
            'total' => $total
        ]);
    }
    
    /**
     * Add product to cart
     * 
     * @return void
     */
    public function add() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $this->redirect('products');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $_SESSION['flash']['error'] = 'Product not found';
            $this->redirect('products');
        }
        
        // Get quantity from POST or default to 1
        $quantity = (int) ($this->getPost('quantity') ?? 1);
        
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Check if product is already in cart
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        $_SESSION['flash']['success'] = 'Product added to cart';
        
        // Redirect back to product page or referrer
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
            $this->redirect($referer);
        } else {
            $this->redirect("products/{$productId}");
        }
    }
    
    /**
     * Remove product from cart
     * 
     * @return void
     */
    public function remove() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $this->redirect('cart');
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['flash']['success'] = 'Product removed from cart';
        } else {
            $_SESSION['flash']['error'] = 'Product not found in cart';
        }
        $this->redirect('cart');
    }
    
    /**
     * Update product quantity in cart
     * 
     * @return void
     */
    public function update() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $this->redirect('cart');
        }
        
        $quantity = (int) $this->getPost('quantity');
        
        if ($quantity < 1) {
            // Remove product if quantity is less than 1
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                $_SESSION['flash']['success'] = 'Product removed from cart';
            }
        } else {
            // Update quantity
            $_SESSION['cart'][$productId] = $quantity;
            $_SESSION['flash']['success'] = 'Quantity updated successfully';
        }
        
        $this->redirect('cart');
    }
    
    /**
     * Display checkout page
     * 
     * @return void
     */
    public function checkout() {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to checkout';
            $this->redirect('login');
        }
        
        // Check if cart is empty
        if (empty($_SESSION['cart'])) {
            $_SESSION['flash']['error'] = 'Your cart is empty';
            $this->redirect('cart');
        }
        
        $cart = [];
        $total = 0;
        $hasStockIssue = false;
        
        // Get product details and check stock
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                // Check if enough stock
                if ($product['stock'] < $quantity) {
                    $hasStockIssue = true;
                    $_SESSION['cart'][$productId] = $product['stock']; // Adjust quantity to available stock
                    $quantity = $product['stock'];
                    
                    if ($quantity <= 0) {
                        unset($_SESSION['cart'][$productId]); // Remove if no stock
                        continue;
                    }
                    
                    $_SESSION['flash']['warning'] = 'Some items in your cart have been adjusted due to stock availability';
                }
                
                $subtotal = $product['price'] * $quantity;
                $cart[] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'image_url' => $product['image_url']
                ];
                
                $total += $subtotal;
            }
        }
        
        // If cart became empty due to stock issues
        if (empty($cart)) {
            $_SESSION['flash']['error'] = 'Your cart is empty';
            $this->redirect('cart');
        }
        
        // If there were stock issues, redirect back to cart to show the updated quantities
        if ($hasStockIssue) {
            $this->redirect('cart');
        }
        
        $this->render('cart/checkout', [
            'title' => 'Checkout',
            'cart' => $cart,
            'total' => $total,
            'user' => $this->auth->getUser()
        ]);
    }
    
    /**
     * Process order confirmation
     * 
     * @return void
     */
    public function confirmOrder() {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to place an order';
            $this->redirect('login');
        }
        
        // Check if cart is empty
        if (empty($_SESSION['cart'])) {
            $_SESSION['flash']['error'] = 'Your cart is empty';
            $this->redirect('cart');
        }
        
        $cart = [];
        $total = 0;
        $stockValid = true;
        
        // Validate stock and calculate total
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                if ($product['stock'] < $quantity) {
                    $stockValid = false;
                    break;
                }
                
                $subtotal = $product['price'] * $quantity;
                $cart[] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                
                $total += $subtotal;
            }
        }
        
        // If stock validation failed
        if (!$stockValid) {
            $_SESSION['flash']['error'] = 'Some items in your cart are no longer available in the requested quantity';
            $this->redirect('checkout');
        }
        
        // Start database transaction
        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();

        try {
            // Create order
            $orderModel = new Order();
            $userId = $this->auth->getUser()['id'];
            
            $orderData = [
                'user_id' => $userId,
                'total_price' => $total,
                'status' => 'pending'
            ];
            
            $orderId = $orderModel->create($orderData);
            
            if (!$orderId) {
                throw new \Exception('Failed to create order');
            }
        
        // Add order items and update product stock
        $success = true;
        
        foreach ($cart as $item) {
            $orderItemData = [
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price_each' => $item['price']
            ];
            
            if (!$orderModel->addOrderItem($orderItemData)) {
                $success = false;
                break;
            }
            
            // Update product stock with quantity check
            if (!$this->productModel->updateStock($item['id'], $item['quantity'])) {
                throw new \Exception('Insufficient stock for product: ' . $item['name']);
            }
        }
        
        if (!$success) {
            throw new \Exception('Failed to add order items');
        }

        // Commit transaction if all operations succeeded
            $db->commit();
        } catch (\Exception $e) {
            // Rollback transaction on any error
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Failed to process order: ' . $e->getMessage();
            $this->redirect('checkout');
        }
        
        // Clear cart after successful order
        $_SESSION['cart'] = [];
        
        $_SESSION['flash']['success'] = 'Your order has been placed successfully!';
        $this->redirect("orders/{$orderId}");
    }
}