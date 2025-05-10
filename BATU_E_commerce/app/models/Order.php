<?php
namespace App\Models;

use App\Core\Database;

/**
 * Order Model
 * 
 * Handles order-related database operations.
 */
class Order {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all orders for a user
     * 
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId) {
        return $this->db->query("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ")->bind(['user_id' => $userId])->all();
    }
    
    /**
     * Get order by ID
     * 
     * @param int $id
     * @param int $userId Optional user ID for security check
     * @return array|false
     */
    public function getById($id, $userId = null) {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $params = ['id' => $id];
        
        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        return $this->db->query($sql)->bind($params)->single();
    }
    
    /**
     * Get order items by order ID
     * 
     * @param int $orderId
     * @return array
     */
    public function getOrderItems($orderId) {
        return $this->db->query("
            SELECT oi.*, p.name, p.image_url 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ")->bind(['order_id' => $orderId])->all();
    }
    
    /**
     * Create a new order
     * 
     * @param array $data Order data
     * @return int|false The new order ID or false on failure
     */
    public function create($data) {
        try {
            $this->db->query("
                INSERT INTO orders (user_id, total_price, status) 
                VALUES (:user_id, :total_price, :status)
            ")->bind([
                'user_id' => $data['user_id'],
                'total_price' => $data['total_price'],
                'status' => $data['status'] ?? 'pending'
            ])->execute();
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Add an order item
     * 
     * @param array $data Order item data
     * @return bool
     */
    public function addOrderItem($data) {
        try {
            return $this->db->query("
                INSERT INTO order_items (order_id, product_id, quantity, price_each) 
                VALUES (:order_id, :product_id, :quantity, :price_each)
            ")->bind([
                'order_id' => $data['order_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price_each' => $data['price_each']
            ])->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update order status
     * 
     * @param int $id Order ID
     * @param string $status New status
     * @return bool
     */
    public function updateStatus($id, $status) {
        try {
            return $this->db->query("
                UPDATE orders 
                SET status = :status 
                WHERE id = :id
            ")->bind([
                'id' => $id,
                'status' => $status
            ])->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete an order
     * 
     * @param int $id Order ID
     * @return bool
     */
    public function delete($id) {
        try {
            return $this->db->query("DELETE FROM orders WHERE id = :id")
                          ->bind(['id' => $id])
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get all orders (for admin)
     * 
     * @return array
     */
    public function getAll() {
        return $this->db->query("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
        ")->all();
    }
    
    /**
     * Get order count for a user
     * 
     * @param int $userId
     * @return int
     */
    public function getCount($userId = null) {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $params = [];
        
        if ($userId !== null) {
            $sql .= " WHERE user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $result = $this->db->query($sql)->bind($params)->single();
        return $result ? (int)$result['count'] : 0;
    }
}