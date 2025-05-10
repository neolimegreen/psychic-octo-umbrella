<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Order;

/**
 * Order Controller
 * 
 * Handles order-related operations.
 */
class OrderController extends BaseController {
    private $orderModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->orderModel = new Order();
        $this->auth = Auth::getInstance();
        
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to view your orders';
            $this->redirect('login');
        }
    }
    
    /**
     * Display order history
     * 
     * @return void
     */
    public function index() {
        $userId = $this->auth->getUser()['id'];
        $orders = $this->orderModel->getByUserId($userId);
        
        $this->render('orders/index', [
            'title' => 'My Orders',
            'orders' => $orders
        ]);
    }
    
    /**
     * Display order details
     * 
     * @return void
     */
    public function show() {
        $orderId = $this->route_params['id'] ?? null;
        
        if (!$orderId) {
            $_SESSION['flash']['error'] = 'Order ID is required';
            $this->redirect('orders');
        }
        
        $userId = $this->auth->getUser()['id'];
        
        // Get order with security check (must belong to current user)
        $order = $this->orderModel->getById($orderId, $userId);
        
        if (!$order) {
            $_SESSION['flash']['error'] = 'Order not found or access denied';
            $this->redirect('orders');
        }
        
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        $this->render('orders/show', [
            'title' => 'Order #' . $orderId,
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
}