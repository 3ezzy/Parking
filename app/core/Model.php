<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Base Model class
 * All models will extend this class
 */
class Model
{
    protected $db;
    protected $table;
    
    /**
     * Constructor - connects to the database
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find all records in the model's table
     *
     * @return array|false Array of records or false on failure
     */
    public function findAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Find a record by ID
     *
     * @param int $id The record ID
     * @return object|false The record or false on failure
     */
    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Find records by a specific field
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @return array|false Array of records or false on failure
     */
    public function findBy($field, $value)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value");
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new record
     *
     * @param array $data The record data
     * @return int|false The last insert ID or false on failure
     */
    public function create($data)
    {
        try {
            // Prepare column and value parts
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            // Prepare statement
            $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
            
            // Bind parameters
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            // Execute statement
            $stmt->execute();
            
            // Return last insert ID
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a record
     *
     * @param int $id The record ID
     * @param array $data The record data
     * @return boolean True on success, false on failure
     */
    public function update($id, $data)
    {
        try {
            // Prepare SET part
            $setParts = [];
            foreach (array_keys($data) as $key) {
                $setParts[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setParts);
            
            // Prepare statement
            $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE id = :id");
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            // Execute statement
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     *
     * @param int $id The record ID
     * @return boolean True on success, false on failure
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
