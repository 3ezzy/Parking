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
     * Add settings method to load the settings page
     *
     * @return void
     */
    public function settings()
    {
        // Fetch settings data if needed (replace with actual logic)
        $settings = [];
        $paymentMethods = [];
        $data = [
            'title' => 'Settings',
            'settings' => $settings,
            'paymentMethods' => $paymentMethods
        ];
        $this->view('admin/settings', $data);
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
        $data = [
            'title' => 'Manage Users',
            'users' => [],
            'errors' => [],
            'user' => (object)['name' => '', 'email' => '', 'role' => 'agent'] // Default for add form
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // This POST is for adding a new user (form action is admin/users)
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                flash('user_error', 'CSRF token mismatch. Action aborted.', 'alert alert-danger');
                $this->redirect('admin/users');
                return;
            }

            // 'add_user' might be a specific submit button name, or just implied by POSTing to admin/users
            // For now, assume any POST to this method is an attempt to add a user.
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? 'agent'); // Default role if not provided

            $data['user'] = (object)['name' => $name, 'email' => $email, 'role' => $role]; // Repopulate form

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
            // Add role validation if necessary, e.g., ensure it's one of the allowed roles.

            if (empty($data['errors'])) {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password, // Remember to hash this in the model's register method
                    'role' => $role
                ];
                if ($role === 'admin' || $role === 'agent') {
                    $userData['status'] = 'active';
                }

                if ($this->userModel->register($userData)) {
                    flash('user_success', 'User added successfully');
                    $this->redirect('admin/users');
                    return;
                } else {
                    flash('user_error', 'Failed to add user. Please try again.', 'alert alert-danger');
                    // Errors will be displayed on the form, $data already prepared
                }
            }
        }

        // For GET request, or if POST had errors and didn't redirect:
        $data['users'] = $this->userModel->findAll(); // Get all users for the list
        $this->view('admin/users', $data);
    }
    
    /**
     * Reports page
     *
     * @return void
     */
    /**
     * Delete a user
     *
     * @param int $userId User ID
     * @return void
     */
    public function editUser($id)
    {
        // Ensure $id is an integer
        $id = (int)$id;
        if ($id <= 0) {
            flash('user_error', 'Invalid user ID.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        $userToEdit = $this->userModel->findById($id);
        if (!$userToEdit) {
            flash('user_error', 'User not found.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        $data = [
            'title' => 'Edit User',
            'users' => $this->userModel->findAll(), // For the list in the background
            'user' => $userToEdit, // Pre-fill form for editing
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                flash('user_error', 'CSRF token mismatch. Action aborted.', 'alert alert-danger');
                // $this->view('admin/users', $data); // Re-show edit form with error
                $this->redirect('admin/editUser/' . $id); // Or redirect back to GET edit page
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $role = trim($_POST['role'] ?? '');
            // Update $data['user'] with submitted values for form repopulation on error
            $data['user']->name = $name;
            $data['user']->role = $role;

            if (empty($name)) {
                $data['errors']['name'] = 'Please enter a name';
            }
            // Add role validation if necessary
            if (empty($role) || !in_array($role, ['admin', 'agent', 'customer'])) { // Assuming 'customer' is another role
                 // $data['errors']['role'] = 'Please select a valid role';
                 // For now, we only ensure admin/agent get active status. Other roles are permitted.
            }

            // Prevent changing role of primary admin (ID 1) or self to non-admin
            if ($id === 1 && $role !== 'admin') {
                 $data['errors']['role'] = 'Primary admin role cannot be changed.';
            }
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id && $userToEdit->role === 'admin' && $role !== 'admin'){
                $data['errors']['role'] = 'You cannot change your own role from admin.';
            }


            if (empty($data['errors'])) {
                $updateData = [
                    'name' => $name,
                    'role' => $role
                ];

                // Logic for 'status' based on role
                if ($role === 'admin' || $role === 'agent') {
                    $updateData['status'] = 'active';
                } elseif (isset($_POST['status'])) { // If status field is part of edit form for other roles
                    $updateData['status'] = trim($_POST['status']);
                } else {
                    // If status is not in form, and role is not admin/agent, status remains unchanged by this logic.
                    // If you want to ensure it's explicitly set, you might need to fetch current status.
                    // For now, if 'status' is not in $updateData, model->update should not change it.
                }
                
                // Prevent primary admin (ID 1) from having status other than active if they are admin
                if ($id === 1 && $updateData['role'] === 'admin') {
                    $updateData['status'] = 'active';
                }

                if ($this->userModel->update($id, $updateData)) {
                    flash('user_success', 'User updated successfully');
                    $this->redirect('admin/users');
                    return;
                } else {
                    flash('user_error', 'Failed to update user. Please try again.', 'alert alert-danger');
                    // $data['errors'] already populated, $data['user'] updated for repopulation
                }
            }
        }
        // For GET request, or if POST had errors:
        $this->view('admin/users', $data);
    }

    public function resetPassword($id)
    {
        $id = (int)$id;
        $userToReset = $this->userModel->findById($id);

        if (!$userToReset) {
            flash('user_error', 'User not found.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        $data = [
            'title' => 'Reset Password for ' . htmlspecialchars($userToReset->name),
            'user_to_reset' => $userToReset,
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                flash('user_error', 'CSRF token mismatch. Action aborted.', 'alert alert-danger');
                $this->redirect('admin/resetPassword/' . $id);
                return;
            }

            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($password)) {
                $data['errors']['password'] = 'Please enter a new password';
            } elseif (strlen($password) < 6) {
                $data['errors']['password'] = 'Password must be at least 6 characters';
            }
            if ($password !== $confirmPassword) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }

            if (empty($data['errors'])) {
                if ($this->userModel->changePassword($id, $password)) {
                    flash('user_success', 'Password for ' . htmlspecialchars($userToReset->name) . ' reset successfully.');
                    $this->redirect('admin/users');
                    return;
                } else {
                    flash('user_error', 'Failed to reset password. Please try again.', 'alert alert-danger');
                }
            }
        }
        // This view needs to be created or adapted.
        // For now, let's assume a view 'admin/reset_password_form.php'
        // $this->view('admin/reset_password_form', $data);
        // As a temporary measure if the view doesn't exist, to avoid errors:
        flash('user_info', 'Password reset page for ' . htmlspecialchars($userToReset->name) . '. Form needs to be implemented.', 'alert alert-info');
        $this->view('admin/users', ['users' => $this->userModel->findAll(), 'title'=>'Manage Users', 'errors'=>[], 'user'=>(object)[]]); // Redirect to users list with info

    }

    public function deleteUser($userId)
    {
        // Ensure CSRF token is valid if you were using POST, but this is GET from a link
        // For GET, ensure the user has rights and it's not a malicious attempt.
        // The AuthMiddleware::requireAdmin() already protects this whole controller.

        if (empty($userId)) {
            flash('user_error', 'Invalid user ID.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            flash('user_error', 'User not found.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        // Prevent deletion of admin users or the primary admin (ID 1 often)
        // The view already disables button for ID 1, this is backend reinforcement.
        if ($user->role === 'admin' || $user->id == 1) { // Assuming ID 1 is a super admin or first admin
            flash('user_error', 'Administrators cannot be deleted.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }
        
        // Prevent logged-in user from deleting themselves via this route (though UI might prevent button)
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            flash('user_error', 'You cannot delete your own account.', 'alert alert-danger');
            $this->redirect('admin/users');
            return;
        }

        if ($this->userModel->delete($userId)) {
            flash('user_success', 'User deleted successfully.');
        } else {
            flash('user_error', 'Failed to delete user. Please try again.', 'alert alert-danger');
        }
        $this->redirect('admin/users');
    }

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
