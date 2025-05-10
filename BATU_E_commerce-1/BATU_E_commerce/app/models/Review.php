<?php
namespace App\Models;

use App\Core\Database;

/**
 * Review Model
 * 
 * Handles product review-related database operations.
 */
class Review {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get reviews by product ID
     * 
     * @param int $productId
     * @return array
     */
    public function getByProductId($productId) {
        return $this->db->query("
            SELECT r.*, u.name as user_name 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = :product_id
            ORDER BY r.created_at DESC
        ")->bind(['product_id' => $productId])->all();
    }
    
    /**
     * Add a new review
     * 
     * @param array $data Review data (product_id, user_id, rating, comment)
     * @return int|false The new review ID or false on failure
     */
    public function add($data) {
        try {
            $this->db->query("
                INSERT INTO reviews (product_id, user_id, rating, comment) 
                VALUES (:product_id, :user_id, :rating, :comment)
            ")->bind([
                'product_id' => $data['product_id'],
                'user_id' => $data['user_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment']
            ])->execute();
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update an existing review
     * 
     * @param int $id Review ID
     * @param array $data Review data to update
     * @return bool
     */
    public function update($id, $data) {
        try {
            $this->db->query("
                UPDATE reviews 
                SET rating = :rating, comment = :comment 
                WHERE id = :id AND user_id = :user_id
            ")->bind([
                'id' => $id,
                'user_id' => $data['user_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment']
            ])->execute();
            
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete a review
     * 
     * @param int $id Review ID
     * @param int $userId User ID (for security check)
     * @return bool
     */
    public function delete($id, $userId) {
        try {
            return $this->db->query("DELETE FROM reviews WHERE id = :id AND user_id = :user_id")
                          ->bind(['id' => $id, 'user_id' => $userId])
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if user has already reviewed a product
     * 
     * @param int $productId
     * @param int $userId
     * @return array|false The review data or false if not found
     */
    public function getUserReview($productId, $userId) {
        return $this->db->query("
            SELECT * FROM reviews 
            WHERE product_id = :product_id AND user_id = :user_id
        ")->bind([
            'product_id' => $productId,
            'user_id' => $userId
        ])->single();
    }
    
    /**
     * Get average rating for a product
     * 
     * @param int $productId
     * @return float|null
     */
    public function getAverageRating($productId) {
        $result = $this->db->query("
            SELECT AVG(rating) as avg_rating 
            FROM reviews 
            WHERE product_id = :product_id
        ")->bind(['product_id' => $productId])->single();
        
        return $result ? round($result['avg_rating'], 1) : null;
    }
    
    /**
     * Get review count for a product
     * 
     * @param int $productId
     * @return int
     */
    public function getCount($productId) {
        $result = $this->db->query("
            SELECT COUNT(*) as count 
            FROM reviews 
            WHERE product_id = :product_id
        ")->bind(['product_id' => $productId])->single();
        
        return $result ? (int)$result['count'] : 0;
    }
}