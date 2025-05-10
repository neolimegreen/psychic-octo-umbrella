<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Review;
use App\Models\Product;

/**
 * Review Controller
 * 
 * Handles product review operations.
 */
class ReviewController extends BaseController {
    private $reviewModel;
    private $productModel;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param array $route_params Parameters from the route
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->reviewModel = new Review();
        $this->productModel = new Product();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Display reviews for a product
     * 
     * @return void
     */
    public function index() {
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
        
        $reviews = $this->reviewModel->getByProductId($productId);
        $averageRating = $this->reviewModel->getAverageRating($productId);
        
        // Check if user has already reviewed this product
        $userReview = null;
        if ($this->auth->isLoggedIn()) {
            $userId = $this->auth->getUser()['id'];
            $userReview = $this->reviewModel->getUserReview($productId, $userId);
        }
        
        $this->render('products/reviews', [
            'title' => 'Reviews for ' . $product['name'],
            'product' => $product,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'userReview' => $userReview
        ]);
    }
    
    /**
     * Add a new review
     * 
     * @return void
     */
    public function add() {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to add a review';
            $this->redirect('login');
        }
        
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
        
        // Check if user has already reviewed this product
        $existingReview = $this->reviewModel->getUserReview($productId, $userId);
        
        if ($existingReview) {
            $_SESSION['flash']['error'] = 'You have already reviewed this product';
            $this->redirect("products/{$productId}/reviews");
        }
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = (int) $this->getPost('rating');
            $comment = $this->getPost('comment');
            
            // Validate input
            $errors = [];
            
            if ($rating < 1 || $rating > 5) {
                $errors['rating'] = 'Rating must be between 1 and 5';
            }
            
            if (empty($comment)) {
                $errors['comment'] = 'Comment is required';
            }
            
            // If no errors, add review
            if (empty($errors)) {
                $reviewData = [
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'rating' => $rating,
                    'comment' => $comment
                ];
                
                $reviewId = $this->reviewModel->add($reviewData);
                
                if ($reviewId) {
                    $_SESSION['flash']['success'] = 'Review added successfully';
                    $this->redirect("products/{$productId}/reviews");
                } else {
                    $_SESSION['flash']['error'] = 'Failed to add review';
                }
            }
            
            // If we got here, there were errors or review creation failed
            $this->render('products/add_review', [
                'title' => 'Add Review for ' . $product['name'],
                'product' => $product,
                'errors' => $errors,
                'review' => [
                    'rating' => $rating,
                    'comment' => $comment
                ]
            ]);
        } else {
            // Display the form
            $this->render('products/add_review', [
                'title' => 'Add Review for ' . $product['name'],
                'product' => $product
            ]);
        }
    }
    
    /**
     * Update an existing review
     * 
     * @return void
     */
    public function update() {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to update a review';
            $this->redirect('login');
        }
        
        $reviewId = $this->route_params['id'] ?? null;
        
        if (!$reviewId) {
            $_SESSION['flash']['error'] = 'Review ID is required';
            $this->redirect('products');
        }
        
        $userId = $this->auth->getUser()['id'];
        
        // Get the review
        $review = $this->reviewModel->getById($reviewId);
        
        if (!$review) {
            $_SESSION['flash']['error'] = 'Review not found';
            $this->redirect('products');
        }
        
        // Check if the review belongs to the user
        if ($review['user_id'] != $userId) {
            $_SESSION['flash']['error'] = 'You can only update your own reviews';
            $this->redirect("products/{$review['product_id']}/reviews");
        }
        
        $product = $this->productModel->getById($review['product_id']);
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = (int) $this->getPost('rating');
            $comment = $this->getPost('comment');
            
            // Validate input
            $errors = [];
            
            if ($rating < 1 || $rating > 5) {
                $errors['rating'] = 'Rating must be between 1 and 5';
            }
            
            if (empty($comment)) {
                $errors['comment'] = 'Comment is required';
            }
            
            // If no errors, update review
            if (empty($errors)) {
                $reviewData = [
                    'user_id' => $userId,
                    'rating' => $rating,
                    'comment' => $comment
                ];
                
                $success = $this->reviewModel->update($reviewId, $reviewData);
                
                if ($success) {
                    $_SESSION['flash']['success'] = 'Review updated successfully';
                    $this->redirect("products/{$review['product_id']}/reviews");
                } else {
                    $_SESSION['flash']['error'] = 'Failed to update review';
                }
            }
            
            // If we got here, there were errors or review update failed
            $this->render('products/edit_review', [
                'title' => 'Edit Review for ' . $product['name'],
                'product' => $product,
                'errors' => $errors,
                'review' => [
                    'id' => $reviewId,
                    'rating' => $rating,
                    'comment' => $comment
                ]
            ]);
        } else {
            // Display the form with review data
            $this->render('products/edit_review', [
                'title' => 'Edit Review for ' . $product['name'],
                'product' => $product,
                'review' => $review
            ]);
        }
    }
    
    /**
     * Delete a review
     * 
     * @return void
     */
    public function delete() {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            $_SESSION['flash']['error'] = 'Please login to delete a review';
            $this->redirect('login');
        }
        
        $reviewId = $this->route_params['id'] ?? null;
        
        if (!$reviewId) {
            $_SESSION['flash']['error'] = 'Review ID is required';
            $this->redirect('products');
        }
        
        $userId = $this->auth->getUser()['id'];
        
        // Get the review
        $review = $this->reviewModel->getById($reviewId);
        
        if (!$review) {
            $_SESSION['flash']['error'] = 'Review not found';
            $this->redirect('products');
        }
        
        // Check if the review belongs to the user or if user is admin
        if ($review['user_id'] != $userId && !$this->auth->isAdmin()) {
            $_SESSION['flash']['error'] = 'You can only delete your own reviews';
            $this->redirect("products/{$review['product_id']}/reviews");
        }
        
        $success = $this->reviewModel->delete($reviewId, $userId);
        
        if ($success) {
            $_SESSION['flash']['success'] = 'Review deleted successfully';
        } else {
            $_SESSION['flash']['error'] = 'Failed to delete review';
        }
        
        $this->redirect("products/{$review['product_id']}/reviews");
    }
}