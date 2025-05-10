<?php
namespace App\Models;

use App\Core\Database;

/**
 * Wishlist Model
 * 
 * Handles wishlist-related database operations.
 */
class Wishlist {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get wishlist items by user ID
     * 
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId) {
        return $this->db->query("
            SELECT w.*, p.name, p.description, p.price, p.stock, p.image_url 
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            WHERE w.user_id = :user_id
            ORDER BY w.id DESC
        ")->bind(['user_id' => $userId])->all();
    }
    
    /**
     * Add product to wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return int|false The new wishlist item ID or false on failure
     */
    public function add($userId, $productId) {
        try {
            // Check if already in wishlist
            if ($this->exists($userId, $productId)) {
                return true; // Already exists, consider it a success
            }
            
            $this->db->query("INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)")
                   ->bind([
                       'user_id' => $userId,
                       'product_id' => $productId
                   ])
                   ->execute();
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Remove product from wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function remove($userId, $productId) {
        try {
            return $this->db->query("DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id")
                          ->bind([
                              'user_id' => $userId,
                              'product_id' => $productId
                          ])
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if product exists in user's wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function exists($userId, $productId) {
        $result = $this->db->query("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id")
                         ->bind([
                             'user_id' => $userId,
                             'product_id' => $productId
                         ])
                         ->single();
        
        return $result !== false;
    }
    
    /**
     * Get count of items in user's wishlist
     * 
     * @param int $userId
     * @return int
     */
    public function getCount($userId) {
        $result = $this->db->query("SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id")
                         ->bind(['user_id' => $userId])
                         ->single();
        
        return $result ? (int)$result['count'] : 0;
    }
}