<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Reservation Model
 * Handles parking space reservations
 */
class Reservation extends Model
{
    protected $table = 'reservations';
    protected $id;
    protected $vehicle_id;
    protected $space_id;
    protected $start_time;
    protected $end_time;
    protected $status;
    protected $created_by;
    protected $created_at;
    protected $updated_at;

    /**
     * Get reservations based on criteria
     *
     * @param array $criteria Search criteria
     * @param int $limit Maximum number of results to return
     * @param int $offset Offset for pagination
     * @return array Reservations matching criteria
     */
    public function getReservations($criteria = [], $limit = 10, $offset = 0)
    {
        try {
            $sql = "
                SELECT r.*, ps.space_number, st.name as space_type, u.name as agent_name, 
                       v.owner_name as customer_name, vt.name as vehicle_type_name
                FROM {$this->table} r
                JOIN parking_spaces ps ON r.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                JOIN users u ON r.created_by = u.id
                JOIN vehicles v ON r.vehicle_id = v.id
                LEFT JOIN vehicle_types vt ON v.type_id = vt.id
                WHERE 1=1
            ";

            $params = [];

            if (!empty($criteria['customer_name'])) {
                $sql .= " AND v.owner_name LIKE :customer_name";
                $params[':customer_name'] = '%' . $criteria['customer_name'] . '%';
            }

            if (!empty($criteria['status'])) {
                $sql .= " AND r.status = :status";
                $params[':status'] = $criteria['status'];
            }

            if (!empty($criteria['date'])) {
                $sql .= " AND (DATE(r.start_time) = :date OR DATE(r.end_time) = :date)";
                $params[':date'] = $criteria['date'];
            }

            if (!empty($criteria['space_id'])) {
                $sql .= " AND r.space_id = :space_id";
                $params[':space_id'] = $criteria['space_id'];
            }

            $sql .= " ORDER BY r.start_time ASC";
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            // Bind pagination parameters
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            // Bind other parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Check if there's a time conflict with existing reservations
     *
     * @param int $spaceId Space ID
     * @param string $startTime Reservation start time
     * @param string $endTime Reservation end time
     * @param int $excludeId Reservation ID to exclude (for updates)
     * @return bool True if conflict exists, false otherwise
     */
    public function hasTimeConflict($spaceId, $startTime, $endTime, $excludeId = null)
    {
        try {
            $sql = "
                SELECT COUNT(*) as conflict_count
                FROM {$this->table}
                WHERE space_id = :space_id
                AND status != 'cancelled'
                AND (
                    (start_time <= :end_time AND end_time >= :start_time)
                )
            ";

            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            $stmt->bindParam(':start_time', $startTime);
            $stmt->bindParam(':end_time', $endTime);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            return $result->conflict_count > 0;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return true; // Assume conflict on error to be safe
        }
    }

    /**
     * Create a new reservation
     *
     * @param int $vehicleId Vehicle ID
     * @param int $spaceId Space ID
     * @param string $startTime Reservation start time
     * @param string $endTime Reservation end time
     * @param int $userId ID of the user creating the reservation
     * @return int|false The reservation ID or false on failure
     */
    // In your Reservation model:
    public function createReservation($data)
    {
        $sql = "INSERT INTO reservations (
        vehicle_id, space_id, start_time, end_time, created_by,
        customer_email, customer_phone, vehicle_type_id, license_plate, notes
    ) VALUES (
        :vehicle_id, :space_id, :start_time, :end_time, :created_by,
        :customer_email, :customer_phone, :vehicle_type_id, :license_plate, :notes
    )";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':vehicle_id',      $data['vehicle_id']);
        $stmt->bindValue(':space_id',        $data['space_id']);
        $stmt->bindValue(':start_time',      $data['start_time']);
        $stmt->bindValue(':end_time',        $data['end_time']);
        $stmt->bindValue(':created_by',      $data['created_by']);
        $stmt->bindValue(':customer_email',  $data['customer_email']);
        $stmt->bindValue(':customer_phone',  $data['customer_phone']);
        $stmt->bindValue(':vehicle_type_id', $data['vehicle_type_id']);
        $stmt->bindValue(':license_plate',   $data['license_plate']);
        $stmt->bindValue(':notes',           $data['notes']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Get a reservation by ID
     *
     * @param int $id Reservation ID
     * @return object|false Reservation object or false if not found
     */
    public function getReservationById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, ps.space_number, st.name as space_type, st.hourly_rate,
                       u.name as agent_name, v.owner_name as customer_name, v.license_plate, vt.name as vehicle_type
                FROM {$this->table} r
                JOIN parking_spaces ps ON r.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                JOIN users u ON r.created_by = u.id
                JOIN vehicles v ON r.vehicle_id = v.id
                LEFT JOIN vehicle_types vt ON v.type_id = vt.id
                WHERE r.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a specific reservation.
     *
     * @param int $id The ID of the reservation to update.
     * @param string $newStatus The new status for the reservation.
     * @return bool True on success, false on failure.
     */
    public function updateReservationStatus($id, $newStatus)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':status', $newStatus, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating reservation status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a reservation
     *
     * @param int $id Reservation ID
     * @param array $data Updated reservation data
     * @return bool True on success, false on failure
     */
    public function updateReservation($id, $data)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET
                    customer_name = :customer_name,
                    customer_email = :customer_email,
                    customer_phone = :customer_phone,
                    vehicle_type_id = :vehicle_type_id,
                    license_plate = :license_plate,
                    start_time = :start_time,
                    end_time = :end_time,
                    notes = :notes,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':customer_name', $data['customer_name']);
            $stmt->bindParam(':customer_email', $data['customer_email']);
            $stmt->bindParam(':customer_phone', $data['customer_phone']);
            $stmt->bindParam(':vehicle_type_id', $data['vehicle_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':license_plate', $data['license_plate']);
            $stmt->bindParam(':start_time', $data['start_time']);
            $stmt->bindParam(':end_time', $data['end_time']);
            $stmt->bindParam(':notes', $data['notes']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Cancel a reservation
     *
     * @param int $id Reservation ID
     * @return bool True on success, false on failure
     */
    public function cancelReservation($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET
                    status = 'cancelled',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Complete a reservation (mark as completed)
     *
     * @param int $id Reservation ID
     * @return bool True on success, false on failure
     */
    public function completeReservation($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET
                    status = 'completed',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Get upcoming reservations for a specific space
     *
     * @param int $spaceId Space ID
     * @return array Upcoming reservations for the space
     */
    public function getUpcomingReservationsForSpace($spaceId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table}
                WHERE space_id = :space_id
                AND status = 'active'
                AND end_time > NOW()
                ORDER BY start_time ASC
            ");

            $stmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
