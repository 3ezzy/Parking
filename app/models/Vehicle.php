<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use \PDOException;

/**
 * Vehicle Model
 * Handles vehicle data and operations
 */
class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $id;
    protected $license_plate;
    protected $type;
    protected $type_id;
    protected $owner_name;
    protected $owner_phone;
    protected $created_at;
    protected $updated_at;
    
    /**
     * Get all vehicles with their type information
     *
     * @return array|false Array of vehicles or false on failure
     */
    public function getAllWithType()
    {
        try {
            $query = "
                SELECT v.*, vt.name as type_name
                FROM {$this->table} v
                JOIN vehicle_types vt ON v.type_id = vt.id
                ORDER BY v.id DESC
            ";
            
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Find a vehicle by license plate
     *
     * @param string $licensePlate The license plate to search for
     * @return object|false The vehicle or false if not found
     */
    public function findByLicensePlate($licensePlate)
    {
        try {
            $query = "
                SELECT v.*, vt.name as type_name
                FROM {$this->table} v
                JOIN vehicle_types vt ON v.type_id = vt.id
                WHERE v.license_plate = :license_plate
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':license_plate', $licensePlate);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all vehicle types
     *
     * @return array|false Array of vehicle types or false on failure
     */
    public function getVehicleTypes()
    {
        try {
            $query = "SELECT * FROM vehicle_types ORDER BY id";
            $stmt = $this->db->query($query);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get vehicle with active parking details
     *
     * @param int $vehicleId Vehicle ID
     * @return object|false Vehicle with parking details or false on failure
     */
    public function getWithActiveParking($vehicleId)
    {
        try {
            $query = "
                SELECT v.*, vt.name as type_name,
                    pt.id as ticket_id, pt.entry_time, pt.status as ticket_status,
                    ps.id as space_id, ps.space_number, ps.status as space_status,
                    st.name as space_type_name
                FROM {$this->table} v
                JOIN vehicle_types vt ON v.type_id = vt.id
                LEFT JOIN parking_tickets pt ON v.id = pt.vehicle_id AND pt.status = 'active'
                LEFT JOIN parking_spaces ps ON pt.space_id = ps.id
                LEFT JOIN space_types st ON ps.type_id = st.id
                WHERE v.id = :vehicle_id
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':vehicle_id', $vehicleId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a vehicle is currently parked
     *
     * @param int $vehicleId Vehicle ID
     * @return boolean True if parked, false otherwise
     */
    public function isParked($vehicleId)
    {
        try {
            $query = "
                SELECT COUNT(*) as count
                FROM parking_tickets
                WHERE vehicle_id = :vehicle_id AND status = 'active'
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':vehicle_id', $vehicleId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            return $result->count > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a vehicle has a future reservation
     *
     * @param int $vehicleId Vehicle ID
     * @return object|false Reservation details or false if none
     */
    public function hasActiveReservation($vehicleId)
    {
        try {
            $query = "
                SELECT r.*, ps.space_number
                FROM reservations r
                JOIN parking_spaces ps ON r.space_id = ps.id
                WHERE r.vehicle_id = :vehicle_id
                AND r.status IN ('pending', 'confirmed')
                AND r.end_time >= NOW()
                ORDER BY r.start_time ASC
                LIMIT 1
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':vehicle_id', $vehicleId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get vehicle parking history
     *
     * @param int $vehicleId Vehicle ID
     * @param int $limit Result limit
     * @param int $offset Result offset
     * @return array|false Array of parking history or false on failure
     */
    public function getParkingHistory($vehicleId, $limit = 10, $offset = 0)
    {
        try {
            $query = "
                SELECT pt.*, ps.space_number, st.name as space_type
                FROM parking_tickets pt
                JOIN parking_spaces ps ON pt.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                WHERE pt.vehicle_id = :vehicle_id
                ORDER BY pt.entry_time DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':vehicle_id', $vehicleId, PDO::PARAM_INT);
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
     * Get a vehicle by license plate
     *
     * @param string $licensePlate The license plate to search for
     * @return object|false The vehicle or false if not found
     */
    public function getVehicleByLicensePlate($licensePlate)
    {
        return $this->findByLicensePlate($licensePlate);
    }
    
    /**
     * Create a new vehicle
     *
     * @param array $data Vehicle data
     * @return int|false The new vehicle ID or false on failure
     */
    public function createVehicle($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (
                    license_plate, type_id, owner_name, owner_phone
                ) VALUES (
                    :license_plate, :type_id, :owner_name, :owner_phone
                )
            ");
            
            $stmt->bindParam(':license_plate', $data['license_plate']);
            $stmt->bindParam(':type_id', $data['type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':owner_name', $data['owner_name']);
            $stmt->bindParam(':owner_phone', $data['owner_phone']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
