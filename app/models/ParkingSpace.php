<?php
namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * ParkingSpace Model
 * Handles parking space data and operations
 */
class ParkingSpace extends Model
{
    protected $table = 'parking_spaces';
    protected $id;
    protected $space_number;
    protected $type;
    protected $type_id;
    protected $status;
    protected $floor;
    protected $zone;
    protected $created_at;
    protected $updated_at;
    
    /**
     * Get all parking spaces with their type information
     *
     * @return array|false Array of parking spaces or false on failure
     */
    public function getAllWithType()
    {
        try {
            $query = "
                SELECT ps.*, st.name as type_name, st.hourly_rate
                FROM {$this->table} ps
                JOIN space_types st ON ps.type_id = st.id
                ORDER BY ps.space_number
            ";
            
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available parking spaces by type
     *
     * @param int $typeId Optional type ID filter
     * @return array|false Array of available parking spaces or false on failure
     */
    public function getAvailableSpaces($typeId = null)
    {
        try {
            $query = "
                SELECT ps.*, st.name as type_name, st.hourly_rate
                FROM {$this->table} ps
                JOIN space_types st ON ps.type_id = st.id
                WHERE ps.status = 'available'
            ";
            
            if ($typeId !== null) {
                $query .= " AND ps.type_id = :type_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':type_id', $typeId, PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare($query);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get parking spaces by status
     *
     * @param string $status Status to filter by
     * @return array|false Array of parking spaces or false on failure
     */
    public function getSpacesByStatus($status)
    {
        try {
            $query = "
                SELECT ps.*, st.name as type_name, st.hourly_rate
                FROM {$this->table} ps
                JOIN space_types st ON ps.type_id = st.id
                WHERE ps.status = :status
                ORDER BY ps.space_number
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get space with details including active ticket or reservation
     *
     * @param int $spaceId Space ID
     * @return object|false Space details or false on failure
     */
    public function getSpaceDetails($spaceId)
    {
        try {
            $query = "
                SELECT ps.*, st.name as type_name, st.hourly_rate
                FROM {$this->table} ps
                JOIN space_types st ON ps.type_id = st.id
                WHERE ps.id = :space_id
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            $stmt->execute();
            
            $space = $stmt->fetch(PDO::FETCH_OBJ);
            
            if (!$space) {
                return false;
            }
            
            // If space is occupied, get the active ticket
            if ($space->status === 'occupied') {
                $ticketQuery = "
                    SELECT pt.*, v.license_plate, v.type_id, vt.name as vehicle_type
                    FROM parking_tickets pt
                    JOIN vehicles v ON pt.vehicle_id = v.id
                    JOIN vehicle_types vt ON v.type_id = vt.id
                    WHERE pt.space_id = :space_id AND pt.status = 'active'
                    LIMIT 1
                ";
                
                $ticketStmt = $this->db->prepare($ticketQuery);
                $ticketStmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
                $ticketStmt->execute();
                
                $space->active_ticket = $ticketStmt->fetch(PDO::FETCH_OBJ);
            }
            
            // If space is reserved, get the active reservation
            if ($space->status === 'reserved') {
                $reservationQuery = "
                    SELECT r.*, v.license_plate, v.type_id, vt.name as vehicle_type
                    FROM reservations r
                    JOIN vehicles v ON r.vehicle_id = v.id
                    JOIN vehicle_types vt ON v.type_id = vt.id
                    WHERE r.space_id = :space_id 
                    AND r.status IN ('confirmed', 'pending')
                    AND r.start_time <= NOW() AND r.end_time >= NOW()
                    LIMIT 1
                ";
                
                $reservationStmt = $this->db->prepare($reservationQuery);
                $reservationStmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
                $reservationStmt->execute();
                
                $space->active_reservation = $reservationStmt->fetch(PDO::FETCH_OBJ);
            }
            
            return $space;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update space status
     *
     * @param int $spaceId Space ID
     * @param string $status New status
     * @return boolean True on success, false on failure
     */
    public function updateStatus($spaceId, $status)
    {
        try {
            $query = "
                UPDATE {$this->table}
                SET status = :status
                WHERE id = :space_id
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all space types
     *
     * @return array|false Array of space types or false on failure
     */
    public function getSpaceTypes()
    {
        try {
            $query = "SELECT * FROM space_types ORDER BY id";
            $stmt = $this->db->query($query);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Find the next available space for a vehicle type
     *
     * @param int $vehicleTypeId The vehicle type ID
     * @return object|false The available space or false if none found
     */
    public function findAvailableSpaceForVehicleType($vehicleTypeId)
    {
        try {
            // Get the vehicle type to check if it requires a special space
            $vehicleTypeQuery = "
                SELECT * FROM vehicle_types
                WHERE id = :vehicle_type_id
            ";
            
            $vehicleTypeStmt = $this->db->prepare($vehicleTypeQuery);
            $vehicleTypeStmt->bindParam(':vehicle_type_id', $vehicleTypeId, PDO::PARAM_INT);
            $vehicleTypeStmt->execute();
            
            $vehicleType = $vehicleTypeStmt->fetch(PDO::FETCH_OBJ);
            
            if (!$vehicleType) {
                return false;
            }
            
            // If vehicle requires special space, filter by compatible space types
            $query = "
                SELECT ps.*, st.name as type_name, st.hourly_rate
                FROM {$this->table} ps
                JOIN space_types st ON ps.type_id = st.id
            ";
            
            // Add conditions based on vehicle type
            if ($vehicleType->requires_special_space) {
                // For vehicles requiring special spaces (like trucks, trailers)
                // We might have specific space types for them
                $query .= " WHERE ps.status = 'available' AND ps.type_id IN (3)"; // Assuming type_id 3 is for larger vehicles
            } else if ($vehicleType->name === 'Motorcycle') {
                // Motorcycles can park in any available space
                $query .= " WHERE ps.status = 'available'";
            } else {
                // Regular vehicles can park in standard or VIP spaces
                $query .= " WHERE ps.status = 'available' AND ps.type_id IN (1, 3)"; // Assuming 1=Standard, 3=VIP
            }
            
            $query .= " ORDER BY ps.space_number LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get space statistics
     *
     * @return object|false Object with statistics or false on failure
     */
    public function getStatistics()
    {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total_spaces,
                    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_spaces,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_spaces,
                    SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved_spaces,
                    SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_spaces
                FROM {$this->table}
            ";
            
            $stmt = $this->db->query($query);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
