<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;

/**
 * Admin Controller
 * Handles admin dashboard and operations
 */
class AdminController extends Controller
{
    private $parkingSpaceModel;
    private $vehicleModel;
    private $parkingTicketModel;
    private $userModel;
    
    /**
     * Constructor - initialize models and middleware
     */
    public function __construct()
    {
        // Require admin role for all methods in this controller
        AuthMiddleware::requireAdmin();
        
        // Load models
        $this->parkingSpaceModel = $this->model('ParkingSpace');
        $this->vehicleModel = $this->model('Vehicle');
        $this->parkingTicketModel = $this->model('ParkingTicket');
        $this->userModel = $this->model('User');
    }
    
    /**
     * Default index method - redirects to dashboard
     *
     * @return void
     */
    public function index()
    {
        // Redirect to admin dashboard
        $this->redirect('admin/dashboard');
    }
    
    /**
     * Admin dashboard
     *
     * @return void
     */
    public function dashboard()
    {
        // Get statistics
        $spaceStats = $this->parkingSpaceModel->getStatistics();
        $ticketStats = $this->parkingTicketModel->getStatistics('day');
        
        // Get active tickets
        $activeTickets = $this->parkingTicketModel->getActiveTickets();
        
        $data = [
            'title' => 'Admin Dashboard',
            'spaceStats' => $spaceStats,
            'ticketStats' => $ticketStats,
            'activeTickets' => $activeTickets
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    /**
     * Manage parking spaces
     *
     * @return void
     */
    public function spaces()
    {
        // Get all parking spaces with their type information
        $spaces = $this->parkingSpaceModel->getAllWithType();
        $spaceTypes = $this->parkingSpaceModel->getSpaceTypes();
        
        $data = [
            'title' => 'Manage Parking Spaces',
            'spaces' => $spaces,
            'spaceTypes' => $spaceTypes,
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Check which form was submitted
            if (isset($_POST['add_space'])) {
                // Add new space
                $spaceNumber = trim($_POST['space_number']);
                $typeId = (int)$_POST['type_id'];
                $floor = (int)$_POST['floor'];
                $zone = trim($_POST['zone']);
                
                // Validate space number
                if (empty($spaceNumber)) {
                    $data['errors']['space_number'] = 'Please enter a space number';
                }
                
                // If no errors, add space
                if (empty($data['errors'])) {
                    $spaceData = [
                        'space_number' => $spaceNumber,
                        'type_id' => $typeId,
                        'status' => 'available',
                        'floor' => $floor,
                        'zone' => $zone
                    ];
                    
                    if ($this->parkingSpaceModel->create($spaceData)) {
                        flash('space_success', 'Parking space added successfully');
                        $this->redirect('admin/spaces');
                    } else {
                        die('Something went wrong');
                    }
                }
            } elseif (isset($_POST['update_space'])) {
                // Update space
                $spaceId = (int)$_POST['space_id'];
                $typeId = (int)$_POST['type_id'];
                $floor = (int)$_POST['floor'];
                $zone = trim($_POST['zone']);
                
                $spaceData = [
                    'type_id' => $typeId,
                    'floor' => $floor,
                    'zone' => $zone
                ];
                
                if ($this->parkingSpaceModel->update($spaceId, $spaceData)) {
                    flash('space_success', 'Parking space updated successfully');
                    $this->redirect('admin/spaces');
                } else {
                    die('Something went wrong');
                }
            } elseif (isset($_POST['update_status'])) {
                // Update space status
                $spaceId = (int)$_POST['space_id'];
                $status = trim($_POST['status']);
                
                if ($this->parkingSpaceModel->updateStatus($spaceId, $status)) {
                    flash('space_success', 'Parking space status updated successfully');
                    $this->redirect('admin/spaces');
                } else {
                    die('Something went wrong');
                }
            }
        }
        
        $this->view('admin/spaces', $data);
    }
    
    /**
     * Manage vehicles
     *
     * @return void
     */
    public function vehicles()
    {
        // Get all vehicles with their type information
        $vehicles = $this->vehicleModel->getAllWithType();
        $vehicleTypes = $this->vehicleModel->getVehicleTypes();
        
        $data = [
            'title' => 'Manage Vehicles',
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Check which form was submitted
            if (isset($_POST['add_vehicle'])) {
                // Add new vehicle
                $licensePlate = trim($_POST['license_plate']);
                $typeId = (int)$_POST['type_id'];
                $ownerName = trim($_POST['owner_name']);
                $ownerPhone = trim($_POST['owner_phone']);
                
                // Validate license plate
                if (empty($licensePlate)) {
                    $data['errors']['license_plate'] = 'Please enter a license plate';
                } elseif ($this->vehicleModel->findByLicensePlate($licensePlate)) {
                    $data['errors']['license_plate'] = 'License plate already exists';
                }
                
                // If no errors, add vehicle
                if (empty($data['errors'])) {
                    $vehicleData = [
                        'license_plate' => $licensePlate,
                        'type_id' => $typeId,
                        'owner_name' => $ownerName,
                        'owner_phone' => $ownerPhone
                    ];
                    
                    if ($this->vehicleModel->create($vehicleData)) {
                        flash('vehicle_success', 'Vehicle added successfully');
                        $this->redirect('admin/vehicles');
                    } else {
                        die('Something went wrong');
                    }
                }
            } elseif (isset($_POST['update_vehicle'])) {
                // Update vehicle
                $vehicleId = (int)$_POST['vehicle_id'];
                $typeId = (int)$_POST['type_id'];
                $ownerName = trim($_POST['owner_name']);
                $ownerPhone = trim($_POST['owner_phone']);
                
                $vehicleData = [
                    'type_id' => $typeId,
                    'owner_name' => $ownerName,
                    'owner_phone' => $ownerPhone
                ];
                
                if ($this->vehicleModel->update($vehicleId, $vehicleData)) {
                    flash('vehicle_success', 'Vehicle updated successfully');
                    $this->redirect('admin/vehicles');
                } else {
                    die('Something went wrong');
                }
            } elseif (isset($_POST['delete_vehicle'])) {
                // Delete vehicle
                $vehicleId = (int)$_POST['vehicle_id'];
                
                // Check if vehicle is currently parked
                if ($this->vehicleModel->isParked($vehicleId)) {
                    flash('vehicle_error', 'Cannot delete a vehicle that is currently parked', 'alert alert-danger');
                    $this->redirect('admin/vehicles');
                    return;
                }
                
                // Check if vehicle has active reservation
                if ($this->vehicleModel->hasActiveReservation($vehicleId)) {
                    flash('vehicle_error', 'Cannot delete a vehicle that has an active reservation', 'alert alert-danger');
                    $this->redirect('admin/vehicles');
                    return;
                }
                
                if ($this->vehicleModel->delete($vehicleId)) {
                    flash('vehicle_success', 'Vehicle deleted successfully');
                    $this->redirect('admin/vehicles');
                } else {
                    die('Something went wrong');
                }
            }
        }
        
        $this->view('admin/vehicles', $data);
    }
    
    /**
     * Manage tickets
     *
     * @return void
     */
    public function tickets()
    {
        // Get search criteria
        $criteria = [];
        $limit = 50;
        $offset = 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
            if (!empty($_GET['license_plate'])) {
                $criteria['license_plate'] = trim($_GET['license_plate']);
            }
            
            if (!empty($_GET['status'])) {
                $criteria['status'] = trim($_GET['status']);
            }
            
            if (!empty($_GET['date_from'])) {
                $criteria['date_from'] = trim($_GET['date_from']);
            }
            
            if (!empty($_GET['date_to'])) {
                $criteria['date_to'] = trim($_GET['date_to']);
            }
            
            if (!empty($_GET['vehicle_type'])) {
                $criteria['vehicle_type'] = (int)$_GET['vehicle_type'];
            }
            
            if (!empty($_GET['limit'])) {
                $limit = (int)$_GET['limit'];
            }
            
            if (!empty($_GET['offset'])) {
                $offset = (int)$_GET['offset'];
            }
        }
        
        // Search tickets
        $tickets = $this->parkingTicketModel->searchTickets($criteria, $limit, $offset);
        $vehicleTypes = $this->vehicleModel->getVehicleTypes();
        
        $data = [
            'title' => 'Manage Tickets',
            'tickets' => $tickets,
            'vehicleTypes' => $vehicleTypes,
            'criteria' => $criteria,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        $this->view('admin/tickets', $data);
    }
    
    /**
     * View ticket details
     *
     * @param int $ticketId Ticket ID
     * @return void
     */
    public function ticketDetails($ticketId)
    {
        // Get ticket details
        $ticket = $this->parkingTicketModel->getTicketDetails($ticketId);
        
        if (!$ticket) {
            flash('ticket_error', 'Ticket not found', 'alert alert-danger');
            $this->redirect('admin/tickets');
            return;
        }
        
        $data = [
            'title' => 'Ticket Details',
            'ticket' => $ticket
        ];
        
        $this->view('admin/ticket_details', $data);
    }
    
    /**
     * Close a ticket
     *
     * @param int $ticketId Ticket ID
     * @return void
     */
    public function closeTicket($ticketId)
    {
        // Get ticket details
        $ticket = $this->parkingTicketModel->getTicketDetails($ticketId);
        
        if (!$ticket || $ticket->status !== 'active') {
            flash('ticket_error', 'Invalid ticket or ticket already closed', 'alert alert-danger');
            $this->redirect('admin/tickets');
            return;
        }
        
        $data = [
            'title' => 'Close Ticket',
            'ticket' => $ticket,
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $amountPaid = (float)$_POST['amount_paid'];
            
            // Validate amount
            if ($amountPaid <= 0) {
                $data['errors']['amount_paid'] = 'Please enter a valid amount';
            }
            
            // If no errors, close ticket
            if (empty($data['errors'])) {
                if ($this->parkingTicketModel->closeTicket($ticketId, $amountPaid)) {
                    flash('ticket_success', 'Ticket closed successfully');
                    $this->redirect('admin/tickets');
                } else {
                    die('Something went wrong');
                }
            }
        }
        
        $this->view('admin/close_ticket', $data);
    }
    
    /**
     * Manage users
     *
     * @return void
     */
    public function users()
    {
        // Get all users
        $users = $this->userModel->findAll();
        
        $data = [
            'title' => 'Manage Users',
            'users' => $users,
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Check which form was submitted
            if (isset($_POST['add_user'])) {
                // Add new user
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
                $role = trim($_POST['role']);
                
                // Validate input
                if (empty($name)) {
                    $data['errors']['name'] = 'Please enter a name';
                }
                
                if (empty($email)) {
                    $data['errors']['email'] = 'Please enter an email';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $data['errors']['email'] = 'Please enter a valid email';
                } elseif ($this->userModel->findByEmail($email)) {
                    $data['errors']['email'] = 'Email is already taken';
                }
                
                if (empty($password)) {
                    $data['errors']['password'] = 'Please enter a password';
                } elseif (strlen($password) < 6) {
                    $data['errors']['password'] = 'Password must be at least 6 characters';
                }
                
                // If no errors, add user
                if (empty($data['errors'])) {
                    $userData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'role' => $role
                    ];
                    
                    if ($this->userModel->register($userData)) {
                        flash('user_success', 'User added successfully');
                        $this->redirect('admin/users');
                    } else {
                        die('Something went wrong');
                    }
                }
            } elseif (isset($_POST['update_user'])) {
                // Update user
                $userId = (int)$_POST['user_id'];
                $name = trim($_POST['name']);
                $role = trim($_POST['role']);
                
                // Validate name
                if (empty($name)) {
                    $data['errors']['update_name'] = 'Please enter a name';
                }
                
                // If no errors, update user
                if (empty($data['errors'])) {
                    $userData = [
                        'name' => $name,
                        'role' => $role
                    ];
                    
                    if ($this->userModel->update($userId, $userData)) {
                        flash('user_success', 'User updated successfully');
                        $this->redirect('admin/users');
                    } else {
                        die('Something went wrong');
                    }
                }
            } elseif (isset($_POST['reset_password'])) {
                // Reset user password
                $userId = (int)$_POST['user_id'];
                $password = trim($_POST['password']);
                
                // Validate password
                if (empty($password)) {
                    $data['errors']['reset_password'] = 'Please enter a new password';
                } elseif (strlen($password) < 6) {
                    $data['errors']['reset_password'] = 'Password must be at least 6 characters';
                }
                
                // If no errors, reset password
                if (empty($data['errors'])) {
                    if ($this->userModel->changePassword($userId, $password)) {
                        flash('user_success', 'Password reset successfully');
                        $this->redirect('admin/users');
                    } else {
                        die('Something went wrong');
                    }
                }
            }
        }
        
        $this->view('admin/users', $data);
    }
    
    /**
     * Reports page
     *
     * @return void
     */
    public function reports()
    {
        // Get statistics
        $dayStats = $this->parkingTicketModel->getStatistics('day');
        $weekStats = $this->parkingTicketModel->getStatistics('week');
        $monthStats = $this->parkingTicketModel->getStatistics('month');
        
        $data = [
            'title' => 'Reports',
            'dayStats' => $dayStats,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats
        ];
        
        $this->view('admin/reports', $data);
    }
}
