<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

/**
 * User Model
 * Handles user authentication and management
 */
class User extends Model
{
    protected $table = 'users';
    protected $id;
    protected $name;
    protected $email;
    protected $password;
    protected $role;
    protected $created_at;
    protected $updated_at;
    
    /**
     * Register a new user
     *
     * @param array $data User data
     * @return int|false The user ID or false on failure
     */
    public function register($data)
    {
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create($data);
    }
    
    /**
     * Login a user
     *
     * @param string $email User email
     * @param string $password User password
     * @return object|false User object or false on failure
     */
    public function login($email, $password)
    {
        try {
            // For debugging - add a log file in the app root
            file_put_contents('login_debug.txt', "Login attempt - Email: {$email}\n", FILE_APPEND);
            
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            
            // Debug if user was found
            if ($user) {
                file_put_contents('login_debug.txt', "User found with ID: {$user->id}\n", FILE_APPEND);
                file_put_contents('login_debug.txt', "Stored hash: {$user->password}\n", FILE_APPEND);
                
                // Debug password verification
                $result = password_verify($password, $user->password);
                file_put_contents('login_debug.txt', "Password verification result: " . ($result ? 'true' : 'false') . "\n\n", FILE_APPEND);
                
                if ($result) {
                    return $user;
                }
            } else {
                file_put_contents('login_debug.txt', "No user found with this email\n\n", FILE_APPEND);
            }
            
            return false;
        } catch (\PDOException $e) {
            file_put_contents('login_debug.txt', "PDO Exception: {$e->getMessage()}\n\n", FILE_APPEND);
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Find a user by email
     *
     * @param string $email User email
     * @return object|false User object or false if not found
     */
    public function findByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Log user activity
     *
     * @param int $userId User ID
     * @param string $activity Activity description
     * @param string $ipAddress IP address
     * @return int|false Activity log ID or false on failure
     */
    public function logActivity($userId, $activity, $ipAddress = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, activity, ip_address)
                VALUES (:user_id, :activity, :ip_address)
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':activity', $activity);
            $stmt->bindParam(':ip_address', $ipAddress);
            
            $stmt->execute();
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user activities
     *
     * @param int $userId User ID
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array|false Array of activity logs or false on failure
     */
    public function getActivities($userId, $limit = 10, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM activity_logs
                WHERE user_id = :user_id
                ORDER BY timestamp DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Change user password
     *
     * @param int $userId User ID
     * @param string $newPassword New password
     * @return boolean True on success, false on failure
     */
    public function changePassword($userId, $newPassword)
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET password = :password
                WHERE id = :id
            ");
            
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
