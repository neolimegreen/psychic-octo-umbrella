<?php
namespace App\Core;

/**
 * Database Class
 * 
 * Handles database connections and queries using PDO.
 */
class Database {
    private static $instance = null;
    private $connection;
    private $statement;
    
    /**
     * Private constructor to prevent direct instantiation
     * Establishes database connection using config settings
     */
    private function __construct() {
        $config = require_once dirname(dirname(__DIR__)) . '/config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        
        try {
            $this->connection = new \PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: {$e->getMessage()}");
        }
    }
    
    /**
     * Get singleton instance of Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Prepare a SQL statement
     * 
     * @param string $sql
     * @return Database
     */
    public function query($sql) {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }
    
    /**
     * Bind values to prepared statement
     * 
     * @param array $params
     * @return Database
     */
    public function bind($params) {
        if (!empty($params) && is_array($params)) {
            foreach ($params as $param => $value) {
                $type = null;
                
                if (is_int($value)) {
                    $type = \PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $type = \PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $type = \PDO::PARAM_NULL;
                } else {
                    $type = \PDO::PARAM_STR;
                }
                
                if (is_numeric($param)) {
                    $this->statement->bindValue($param + 1, $value, $type);
                } else {
                    $this->statement->bindValue(":$param", $value, $type);
                }
            }
        }
        return $this;
    }
    
    /**
     * Execute the prepared statement
     * 
     * @return bool
     */
    public function execute() {
        return $this->statement->execute();
    }
    
    /**
     * Get a single record
     * 
     * @return mixed
     */
    public function single() {
        $this->execute();
        return $this->statement->fetch();
    }
    
    /**
     * Get all records
     * 
     * @return array
     */
    public function all() {
        $this->execute();
        return $this->statement->fetchAll();
    }
    
    /**
     * Get row count
     * 
     * @return int
     */
    public function rowCount() {
        return $this->statement->rowCount();
    }
    
    /**
     * Get last inserted ID
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
}