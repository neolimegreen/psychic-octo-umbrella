<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

/**
 * Admin Controller
 * 
 * Handles admin-related operations.
 */
class AdminController extends BaseController {
    private $productModel;
    private $orderModel;
    private $userModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->auth = Auth::getInstance();
        
        // Check if user is admin
        if (!$this->auth->isAdmin()) {
            $_SESSION['flash']['error'] = 'Access denied. Admin privileges required.';
            $this->redirect('');
        }
    }
    
    /**
     * Display admin dashboard
     * 
     * @return void
     */
    public function index() {
        // Get counts for dashboard
        $productCount = $this->productModel->getCount();
        $orderCount = $this->orderModel->getCount();
        $userCount = $this->userModel->getCount();
        
        // Get recent orders
        $recentOrders = $this->orderModel->getAll(5);
        
        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'productCount' => $productCount,
            'orderCount' => $orderCount,
            'userCount' => $userCount,
            'recentOrders' => $recentOrders
        ]);
    }
    
    /**
     * Display product management page
     * 
     * @return void
     */
    public function products() {
        $products = $this->productModel->getAll();
        
        $this->render('admin/products', [
            'title' => 'Manage Products',
            'products' => $products
        ]);
    }
    
    /**
     * Display add product form
     * 
     * @return void
     */
    public function addProduct() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->getPost('name');
            $description = $this->getPost('description');
            $price = $this->getPost('price');
            $stock = $this->getPost('stock');
            $imageUrl = $this->getPost('image_url');
            
            // Validate input
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Product name is required';
            }
            
            if (empty($description)) {
                $errors['description'] = 'Description is required';
            }
            
            if (empty($price) || !is_numeric($price) || $price <= 0 || $price > 999999.99) {
                $errors['price'] = 'Price must be a positive number between 0 and 999,999.99';
            } else {
                // Format price to 2 decimal places
                $price = number_format((float)$price, 2, '.', '');
            }
            
            if (!is_numeric($stock) || $stock < 0 || $stock > 999999) {
                $errors['stock'] = 'Stock must be a non-negative number less than 1,000,000';
            } else {
                // Ensure stock is an integer
                $stock = (int)$stock;
            }
            
            // Handle file upload if present
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Validate file size (max 5MB)
                $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                if ($_FILES['image']['size'] > $maxFileSize) {
                    $errors['image'] = 'Image size must not exceed 5MB';
                } else {
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                    finfo_close($finfo);
                    
                    if (!in_array($mimeType, $allowedTypes)) {
                        $errors['image'] = 'Only JPG, PNG and GIF images are allowed';
                    } else {
                        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', basename($_FILES['image']['name']));
                        $uploadFile = $uploadDir . $fileName;
                        
                        // Check if it's an image and get dimensions
                        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
                        if ($imageInfo === false) {
                            $errors['image'] = 'Uploaded file is not a valid image';
                        } else {
                            // Check image dimensions
                            list($width, $height) = $imageInfo;
                            if ($width > 2000 || $height > 2000) {
                                $errors['image'] = 'Image dimensions must not exceed 2000x2000 pixels';
                            } else {
                                // Move the uploaded file
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                                    $imageUrl = '/BATU_E_commerce/public/uploads/' . $fileName;
                                } else {
                                    $errors['image'] = 'Failed to upload image. Please try again.';
                                }
                            }
                        }
                    }
                }
            }
            
            // If no errors, create product
            if (empty($errors)) {
                $productData = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ];
                
                $productId = $this->productModel->create($productData);
                
                if ($productId) {
                    $_SESSION['flash']['success'] = 'Product added successfully';
                    $this->redirect('admin/products');
                } else {
                    $_SESSION['flash']['error'] = 'Failed to add product';
                }
            }
            
            // If we got here, there were errors or product creation failed
            $this->render('admin/add_product', [
                'title' => 'Add Product',
                'errors' => $errors,
                'product' => [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ]
            ]);
        } else {
            // Display the form
            $this->render('admin/add_product', [
                'title' => 'Add Product',
                'errors' => [],
                'product' => [
                    'name' => '',
                    'description' => '',
                    'price' => '',
                    'stock' => '',
                    'image_url' => ''
                ]
            ]);
        }
    }
    
    /**
     * Display edit product form
     * 
     * @return void
     */
    public function editProduct() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $_SESSION['flash']['error'] = 'Product ID is required';
            $this->redirect('admin/products');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $_SESSION['flash']['error'] = 'Product not found';
            $this->redirect('admin/products');
        }
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->getPost('name');
            $description = $this->getPost('description');
            $price = $this->getPost('price');
            $stock = $this->getPost('stock');
            $imageUrl = $this->getPost('image_url') ?? $product['image_url'];
            
            // Validate input
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Product name is required';
            }
            
            if (empty($description)) {
                $errors['description'] = 'Description is required';
            }
            
            if (empty($price) || !is_numeric($price) || $price <= 0 || $price > 999999.99) {
                $errors['price'] = 'Price must be a positive number between 0 and 999,999.99';
            } else {
                // Format price to 2 decimal places
                $price = number_format((float)$price, 2, '.', '');
            }
            
            if (!is_numeric($stock) || $stock < 0 || $stock > 999999) {
                $errors['stock'] = 'Stock must be a non-negative number less than 1,000,000';
            } else {
                // Ensure stock is an integer
                $stock = (int)$stock;
            }
            
            // Handle file upload if present
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Validate file size (max 5MB)
                $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                if ($_FILES['image']['size'] > $maxFileSize) {
                    $errors['image'] = 'Image size must not exceed 5MB';
                } else {
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                    finfo_close($finfo);
                    
                    if (!in_array($mimeType, $allowedTypes)) {
                        $errors['image'] = 'Only JPG, PNG and GIF images are allowed';
                    } else {
                        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', basename($_FILES['image']['name']));
                        $uploadFile = $uploadDir . $fileName;
                        
                        // Check if it's an image and get dimensions
                        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
                        if ($imageInfo === false) {
                            $errors['image'] = 'Uploaded file is not a valid image';
                        } else {
                            // Check image dimensions
                            list($width, $height) = $imageInfo;
                            if ($width > 2000 || $height > 2000) {
                                $errors['image'] = 'Image dimensions must not exceed 2000x2000 pixels';
                            } else {
                                // Move the uploaded file
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                                    $imageUrl = '/BATU_E_commerce/public/uploads/' . $fileName;
                                } else {
                                    $errors['image'] = 'Failed to upload image. Please try again.';
                                }
                            }
                        }
                    }
                }
            }
            
            // If no errors, update product
            if (empty($errors)) {
                $productData = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ];
                
                $success = $this->productModel->update($productId, $productData);
                
                if ($success) {
                    $_SESSION['flash']['success'] = 'Product updated successfully';
                    $this->redirect('admin/products');
                } else {
                    $_SESSION['flash']['error'] = 'Failed to update product';
                }
            }
            
            // If we got here, there were errors or product update failed
            $this->render('admin/edit_product', [
                'title' => 'Edit Product',
                'errors' => $errors,
                'product' => [
                    'id' => $productId,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $imageUrl
                ]
            ]);
        } else {
            // Display the form with product data
            $this->render('admin/edit_product', [
                'title' => 'Edit Product',
                'product' => $product,
                'errors' => []
            ]);
        }
    }
    
    /**
     * Delete a product
     * 
     * @return void
     */
    public function deleteProduct() {
        $productId = $this->route_params['id'] ?? null;
        
        if (!$productId) {
            $_SESSION['flash']['error'] = 'Product ID is required';
            $this->redirect('admin/products');
        }
        
        $success = $this->productModel->delete($productId);
        
        if ($success) {
            $_SESSION['flash']['success'] = 'Product deleted successfully';
        } else {
            $_SESSION['flash']['error'] = 'Failed to delete product';
        }
        
        $this->redirect('admin/products');
    }
    
    /**
     * Display order management page
     * 
     * @return void
     */
    public function orders() {
        $orders = $this->orderModel->getAll();
        
        $this->render('admin/orders', [
            'title' => 'Manage Orders',
            'orders' => $orders
        ]);
    }
    
    /**
     * Display order details
     * 
     * @return void
     */
    public function showOrder() {
        $orderId = $this->route_params['id'] ?? null;
        
        if (!$orderId) {
            $_SESSION['flash']['error'] = 'Order ID is required';
            $this->redirect('admin/orders');
        }
        
        $order = $this->orderModel->getById($orderId);
        
        if (!$order) {
            $_SESSION['flash']['error'] = 'Order not found';
            $this->redirect('admin/orders');
        }
        
        $orderItems = $this->orderModel->getOrderItems($orderId);
        $user = $this->userModel->getById($order['user_id']);
        
        // Check if form was submitted (status update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $this->getPost('status');
            
            if ($status && in_array($status, ['pending', 'shipped', 'delivered', 'cancelled'])) {
                $success = $this->orderModel->updateStatus($orderId, $status);
                
                if ($success) {
                    $_SESSION['flash']['success'] = 'Order status updated successfully';
                    // Refresh order data
                    $order = $this->orderModel->getById($orderId);
                } else {
                    $_SESSION['flash']['error'] = 'Failed to update order status';
                }
            }
        }
        
        $this->render('admin/order_details', [
            'title' => 'Order Details',
            'order' => $order,
            'orderItems' => $orderItems,
            'user' => $user
        ]);
    }
}