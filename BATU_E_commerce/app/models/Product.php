<?php
namespace App\Models;

use App\Core\Database;

/**
 * Product Model
 * 
 * Handles product-related database operations.
 */
class Product {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all products
     * 
     * @param int $limit Optional limit
     * @param int $offset Optional offset for pagination
     * @return array
     */
    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $query = $this->db->query($sql);
        
        if ($limit !== null) {
            $query->bind(['limit' => $limit]);
            if ($offset !== null) {
                $query->bind(['offset' => $offset]);
            }
        }
        
        return $query->all();
    }
    
    /**
     * Get product by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        return $this->db->query("SELECT * FROM products WHERE id = :id")
                      ->bind(['id' => $id])
                      ->single();
    }
    
    /**
     * Search products by name or description
     * 
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $keyword = "%$keyword%";
        return $this->db->query("
            SELECT * FROM products 
            WHERE name LIKE :keyword OR description LIKE :keyword 
            ORDER BY created_at DESC
        ")->bind(['keyword' => $keyword])->all();
    }
    
    /**
     * Create a new product
     * 
     * @param array $data Product data
     * @return int|false The new product ID or false on failure
     */
    public function create($data) {
        try {
            $this->db->query("
                INSERT INTO products (name, description, price, stock, image_url) 
                VALUES (:name, :description, :price, :stock, :image_url)
            ")->bind([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image_url' => $data['image_url'] ?? null
            ])->execute();
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update an existing product
     * 
     * @param int $id Product ID
     * @param array $data Product data to update
     * @return bool
     */
    public function update($id, $data) {
        try {
            $this->db->query("
                UPDATE products 
                SET name = :name, description = :description, price = :price, 
                    stock = :stock, image_url = :image_url 
                WHERE id = :id
            ")->bind([
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image_url' => $data['image_url'] ?? null
            ])->execute();
            
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete a product
     * 
     * @param int $id Product ID
     * @return bool
     */
    public function delete($id) {
        try {
            return $this->db->query("DELETE FROM products WHERE id = :id")
                          ->bind(['id' => $id])
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update product stock
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity to reduce from stock
     * @return bool
     */
    public function updateStock($id, $quantity) {
        try {
            return $this->db->query("
                UPDATE products 
                SET stock = stock - :quantity 
                WHERE id = :id AND stock >= :quantity
            ")->bind([
                'id' => $id,
                'quantity' => $quantity
            ])->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get total number of products
     * 
     * @return int
     */
    public function getCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM products")->single();
        return $result ? (int)$result['count'] : 0;
    }
}