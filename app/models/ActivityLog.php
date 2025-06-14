<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $id;
    protected $user_id;
    protected $activity;
    protected $ip_address;
    protected $timestamp;

    public function log($userId, $activity, $ipAddress = null)
    {
        $data = [
            'user_id' => $userId,
            'activity' => $activity,
            'ip_address' => $ipAddress
        ];
        return $this->create($data);
    }

    /**
     * Log an activity (alias for log method to match controller usage)
     * 
     * @param int $userId User ID
     * @param string $activityType Type of activity
     * @param string $description Description of the activity
     * @param string|null $ipAddress IP address (optional)
     * @return bool Success status
     */
    public function logActivity($userId, $activityType, $description, $ipAddress = null)
    {
        $activity = $activityType . ': ' . $description;
        return $this->log($userId, $activity, $ipAddress);
    }

    public function getByUser($userId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
} 