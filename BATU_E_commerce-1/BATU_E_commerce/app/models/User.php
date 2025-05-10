<?php
namespace App\Models;

use App\Core\Database;

/**
 * User Model
 * 
 * Handles user-related database operations.
 */
class User {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        return $this->db->query("SELECT id, name, email, role, created_at FROM users WHERE id = :id")
                      ->bind(['id' => $id])
                      ->single();
    }
    
    /**
     * Get user by email
     * 
     * @param string $email
     * @return array|false
     */
    public function getByEmail($email) {
        return $this->db->query("SELECT id, name, email, password, role, created_at FROM users WHERE email = :email")
                      ->bind(['email' => $email])
                      ->single();
    }
    
    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return int|false The new user ID or false on failure
     */
    public function create($data) {
        try {
            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->query("
                INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)
            ")->bind([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $passwordHash,
                'role' => $data['role'] ?? 'customer'
            ])->execute();
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update user information
     * 
     * @param int $id User ID
     * @param array $data User data to update
     * @return bool
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE users SET ";
            $params = ['id' => $id];
            $updates = [];
            
            // Only update provided fields
            if (isset($data['name'])) {
                $updates[] = "name = :name";
                $params['name'] = $data['name'];
            }
            
            if (isset($data['email'])) {
                $updates[] = "email = :email";
                $params['email'] = $data['email'];
            }
            
            if (isset($data['password'])) {
                $updates[] = "password = :password";
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['role'])) {
                $updates[] = "role = :role";
                $params['role'] = $data['role'];
            }
            
            if (empty($updates)) {
                return true; // Nothing to update
            }
            
            $sql .= implode(", ", $updates) . " WHERE id = :id";
            
            return $this->db->query($sql)
                          ->bind($params)
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete a user
     * 
     * @param int $id User ID
     * @return bool
     */
    public function delete($id) {
        try {
            return $this->db->query("DELETE FROM users WHERE id = :id")
                          ->bind(['id' => $id])
                          ->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get all users (for admin)
     * 
     * @return array
     */
    public function getAll() {
        return $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->all();
    }
    
    /**
     * Check if email already exists
     * 
     * @param string $email
     * @param int $excludeId Optional user ID to exclude from check (for updates)
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $result = $this->db->query($sql)->bind($params)->single();
        return $result !== false;
    }
    
    /**
     * Verify password for a user
     * 
     * @param int $userId
     * @param string $password
     * @return bool
     */
    public function verifyPassword($userId, $password) {
        $user = $this->db->query("SELECT password FROM users WHERE id = :id")
                        ->bind(['id' => $userId])
                        ->single();
        
        if ($user) {
            return password_verify($password, $user['password']);
        }
        
        return false;
    }
}