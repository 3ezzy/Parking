<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use DateTime;

/**
 * ParkingTicket Model
 * Handles parking ticket data and operations
 */
class ParkingTicket extends Model
{
    protected $table = 'parking_tickets';
    
    /**
     * Create a new parking ticket
     *
     * @param int $vehicleId Vehicle ID
     * @param int $spaceId Space ID
     * @param int $createdBy User ID who created the ticket
     * @return int|false The ticket ID or false on failure
     */
    public function createTicket($vehicleId, $spaceId, $createdBy)
    {
        try {
            $this->db->beginTransaction();
            
            // Create ticket
            $query = "
                INSERT INTO {$this->table} (vehicle_id, space_id, entry_time, status, created_by)
                VALUES (:vehicle_id, :space_id, NOW(), 'active', :created_by)
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':vehicle_id', $vehicleId, PDO::PARAM_INT);
            $stmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $createdBy, PDO::PARAM_INT);
            $stmt->execute();
            
            $ticketId = $this->db->lastInsertId();
            
            // Update space status
            $updateSpaceQuery = "
                UPDATE parking_spaces
                SET status = 'occupied'
                WHERE id = :space_id
            ";
            
            $updateSpaceStmt = $this->db->prepare($updateSpaceQuery);
            $updateSpaceStmt->bindParam(':space_id', $spaceId, PDO::PARAM_INT);
            $updateSpaceStmt->execute();
            
            $this->db->commit();
            
            return $ticketId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Close a parking ticket
     *
     * @param int $ticketId Ticket ID
     * @param float $amountPaid Amount paid
     * @return boolean True on success, false on failure
     */
    public function closeTicket($ticketId, $amountPaid)
    {
        try {
            $this->db->beginTransaction();
            
            // Get ticket and space information
            $getTicketQuery = "
                SELECT * FROM {$this->table}
                WHERE id = :ticket_id AND status = 'active'
            ";
            
            $getTicketStmt = $this->db->prepare($getTicketQuery);
            $getTicketStmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $getTicketStmt->execute();
            
            $ticket = $getTicketStmt->fetch(PDO::FETCH_OBJ);
            
            if (!$ticket) {
                return false;
            }
            
            // Close ticket
            $closeTicketQuery = "
                UPDATE {$this->table}
                SET exit_time = NOW(), amount_paid = :amount_paid, status = 'completed'
                WHERE id = :ticket_id
            ";
            
            $closeTicketStmt = $this->db->prepare($closeTicketQuery);
            $closeTicketStmt->bindParam(':amount_paid', $amountPaid);
            $closeTicketStmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $closeTicketStmt->execute();
            
            // Update space status
            $updateSpaceQuery = "
                UPDATE parking_spaces
                SET status = 'available'
                WHERE id = :space_id
            ";
            
            $updateSpaceStmt = $this->db->prepare($updateSpaceQuery);
            $updateSpaceStmt->bindParam(':space_id', $ticket->space_id, PDO::PARAM_INT);
            $updateSpaceStmt->execute();
            
            $this->db->commit();
            
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ticket details with vehicle and space information
     *
     * @param int $ticketId Ticket ID
     * @return object|false Ticket details or false on failure
     */
    public function getTicketDetails($ticketId)
    {
        try {
            $query = "
                SELECT pt.*, 
                    v.license_plate, v.owner_name, v.owner_phone, vt.name as vehicle_type,
                    ps.space_number, st.name as space_type, st.hourly_rate,
                    u.name as created_by_name
                FROM {$this->table} pt
                JOIN vehicles v ON pt.vehicle_id = v.id
                JOIN vehicle_types vt ON v.type_id = vt.id
                JOIN parking_spaces ps ON pt.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                JOIN users u ON pt.created_by = u.id
                WHERE pt.id = :ticket_id
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
            
            $ticket = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($ticket) {
                // Calculate duration and amount if not yet paid
                if ($ticket->status === 'active') {
                    $entryTime = new DateTime($ticket->entry_time);
                    $currentTime = new DateTime();
                    $duration = $entryTime->diff($currentTime);
                    
                    $hours = $duration->h + ($duration->i / 60);
                    $days = $duration->d;
                    
                    $totalHours = $days * 24 + $hours;
                    
                    $ticket->duration_hours = round($totalHours, 2);
                    $ticket->calculated_amount = round($totalHours * $ticket->hourly_rate, 2);
                } else {
                    // For completed tickets, calculate duration from entry to exit time
                    $entryTime = new DateTime($ticket->entry_time);
                    $exitTime = new DateTime($ticket->exit_time);
                    $duration = $entryTime->diff($exitTime);
                    
                    $hours = $duration->h + ($duration->i / 60);
                    $days = $duration->d;
                    
                    $totalHours = $days * 24 + $hours;
                    
                    $ticket->duration_hours = round($totalHours, 2);
                }
            }
            
            return $ticket;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get active tickets
     *
     * @return array|false Array of active tickets or false on failure
     */
    public function getActiveTickets()
    {
        try {
            $query = "
                SELECT pt.*, 
                    v.license_plate, vt.name as vehicle_type,
                    ps.space_number, st.name as space_type
                FROM {$this->table} pt
                JOIN vehicles v ON pt.vehicle_id = v.id
                JOIN vehicle_types vt ON v.type_id = vt.id
                JOIN parking_spaces ps ON pt.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                WHERE pt.status = 'active'
                ORDER BY pt.entry_time
            ";
            
            $stmt = $this->db->query($query);
            
            $tickets = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Calculate duration and amount for each ticket
            foreach ($tickets as &$ticket) {
                $entryTime = new DateTime($ticket->entry_time);
                $currentTime = new DateTime();
                $duration = $entryTime->diff($currentTime);
                
                $hours = $duration->h + ($duration->i / 60);
                $days = $duration->d;
                
                $totalHours = $days * 24 + $hours;
                
                $ticket->duration_hours = round($totalHours, 2);
            }
            
            return $tickets;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Search tickets by various criteria
     *
     * @param array $criteria Search criteria
     * @param int $limit Result limit
     * @param int $offset Result offset
     * @return array|false Array of tickets or false on failure
     */
    public function searchTickets($criteria = [], $limit = 50, $offset = 0)
    {
        try {
            $query = "
                SELECT pt.*, 
                    v.license_plate, vt.name as vehicle_type,
                    ps.space_number, st.name as space_type,
                    u.name as created_by_name
                FROM {$this->table} pt
                JOIN vehicles v ON pt.vehicle_id = v.id
                JOIN vehicle_types vt ON v.type_id = vt.id
                JOIN parking_spaces ps ON pt.space_id = ps.id
                JOIN space_types st ON ps.type_id = st.id
                JOIN users u ON pt.created_by = u.id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Add search criteria
            if (!empty($criteria['license_plate'])) {
                $query .= " AND v.license_plate LIKE :license_plate";
                $params[':license_plate'] = '%' . $criteria['license_plate'] . '%';
            }
            
            if (!empty($criteria['status'])) {
                $query .= " AND pt.status = :status";
                $params[':status'] = $criteria['status'];
            }
            
            if (!empty($criteria['date_from'])) {
                $query .= " AND pt.entry_time >= :date_from";
                $params[':date_from'] = $criteria['date_from'];
            }
            
            if (!empty($criteria['date_to'])) {
                $query .= " AND pt.entry_time <= :date_to";
                $params[':date_to'] = $criteria['date_to'];
            }
            
            if (!empty($criteria['vehicle_type'])) {
                $query .= " AND v.type_id = :vehicle_type";
                $params[':vehicle_type'] = $criteria['vehicle_type'];
            }
            
            // Add ordering
            $query .= " ORDER BY pt.entry_time DESC";
            
            // Add limit and offset
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                if ($key === ':limit' || $key === ':offset' || $key === ':vehicle_type') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ticket statistics
     *
     * @param string $period Period to get statistics for ('day', 'week', 'month')
     * @return object|false Statistics object or false on failure
     */
    public function getStatistics($period = 'day')
    {
        try {
            $dateCondition = '';
            
            switch ($period) {
                case 'day':
                    $dateCondition = "AND (pt.entry_time >= CURDATE() OR (pt.status = 'active' AND pt.entry_time < CURDATE()))";
                    break;
                case 'week':
                    $dateCondition = "AND (pt.entry_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) OR (pt.status = 'active' AND pt.entry_time < DATE_SUB(CURDATE(), INTERVAL 7 DAY)))";
                    break;
                case 'month':
                    $dateCondition = "AND (pt.entry_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) OR (pt.status = 'active' AND pt.entry_time < DATE_SUB(CURDATE(), INTERVAL 30 DAY)))";
                    break;
                default:
                    $dateCondition = '';
            }
            
            $query = "
                SELECT 
                    COUNT(*) as total_tickets,
                    SUM(CASE WHEN pt.status = 'active' THEN 1 ELSE 0 END) as active_tickets,
                    SUM(CASE WHEN pt.status = 'completed' THEN 1 ELSE 0 END) as completed_tickets,
                    SUM(CASE WHEN pt.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_tickets,
                    SUM(CASE WHEN pt.status = 'completed' THEN pt.amount_paid ELSE 0 END) as total_revenue
                FROM {$this->table} pt
                WHERE 1=1 {$dateCondition}
            ";
            
            $stmt = $this->db->query($query);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
