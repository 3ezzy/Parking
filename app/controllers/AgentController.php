<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;

/**
 * Agent Controller
 * Handles agent operations for parking management
 */
class AgentController extends Controller
{
    private $parkingSpaceModel;
    private $vehicleModel;
    private $parkingTicketModel;
    private $reservationModel;
    private $activityLogModel;
    
    /**
     * Constructor - initialize models and middleware
     */
    public function __construct()
    {
        // Require agent role for all methods in this controller
        // Load models
        $this->parkingSpaceModel = $this->model('ParkingSpace');
        $this->vehicleModel = $this->model('Vehicle');
        $this->parkingTicketModel = $this->model('ParkingTicket');
        $this->reservationModel = $this->model('Reservation');
        $this->activityLogModel = $this->model('ActivityLog');
        $this->vehicleModel = $this->model('Vehicle');
        $this->parkingTicketModel = $this->model('ParkingTicket');
    }
    
    /**
     * Default index method - redirects to dashboard
     *
     * @return void
     */
    public function index()
    {
        // Redirect to agent dashboard
        $this->redirect('agent/dashboard');
    }

    /**
     * Agent dashboard
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
            'title' => 'Agent Dashboard',
            'spaceStats' => $spaceStats,
            'ticketStats' => $ticketStats,
            'activeTickets' => $activeTickets
        ];
        
        $this->view('agent/dashboard', $data);
    }
    
    /**
     * Vehicle entry
     *
     * @return void
     */
    public function vehicleEntry()
    {
        $data = [
            'title' => 'Vehicle Entry',
            'formData' => [],
            'errors' => []
        ];
        
        // Get vehicle types
        $vehicleTypeModel = $this->model('Vehicle');
        $data['vehicleTypes'] = $vehicleTypeModel->getVehicleTypes();
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $data['formData'] = [
                'license_plate' => trim($_POST['license_plate']),
                'vehicle_type_id' => (int)$_POST['vehicle_type_id'],
                'owner_name' => !empty($_POST['owner_name']) ? trim($_POST['owner_name']) : null,
                'owner_phone' => !empty($_POST['owner_phone']) ? trim($_POST['owner_phone']) : null,
                'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null
            ];
            
            // Validate license plate
            if (empty($data['formData']['license_plate'])) {
                $data['errors']['license_plate'] = 'Please enter a license plate number';
            }
            
            // Validate vehicle type
            if (empty($data['formData']['vehicle_type_id'])) {
                $data['errors']['vehicle_type_id'] = 'Please select a vehicle type';
            }
            
            // If no errors, process vehicle entry
            if (empty($data['errors'])) {
                $licensePlate = $data['formData']['license_plate'];
                $typeId = $data['formData']['vehicle_type_id'];
                
                // Check if vehicle exists
                $vehicle = $this->vehicleModel->findByLicensePlate($licensePlate);
                
                // Create new vehicle if it doesn't exist
                if (!$vehicle) {
                    $vehicleData = [
                        'license_plate' => $licensePlate,
                        'type_id' => $typeId,
                        'owner_name' => $data['formData']['owner_name'],
                        'owner_phone' => $data['formData']['owner_phone']
                    ];
                    
                    $vehicleId = $this->vehicleModel->create($vehicleData);
                } else {
                    $vehicleId = $vehicle->id;
                    
                    // Check if vehicle is already parked
                    if ($this->vehicleModel->isParked($vehicleId)) {
                        $data['errors']['license_plate'] = 'This vehicle is already parked';
                        $this->view('agent/vehicle_entry', $data);
                        return;
                    }
                }
                
                // Find available space for vehicle type
                $space = $this->parkingSpaceModel->findAvailableSpaceForVehicleType($typeId);
                
                if (!$space) {
                    $data['errors']['space'] = 'No available parking space for this vehicle type';
                    $this->view('agent/vehicle_entry', $data);
                    return;
                }
                
                // Create parking ticket
                $ticketId = $this->parkingTicketModel->createTicket($vehicleId, $space->id, $_SESSION['user_id']);
                
                if ($ticketId) {
                    flash('ticket_success', 'Vehicle entry recorded successfully');
                    $this->redirect('agent/ticketDetails/' . $ticketId);
                } else {
                    die('Error creating ticket');
                }
            }
        }
        
        $this->view('agent/vehicle_entry', $data);
    }
    
    /**
     * Vehicle exit
     *
     * @return void
     */
    public function vehicleExit()
    {
        $data = [
            'title' => 'Vehicle Exit',
            'formData' => [],
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $data['formData'] = [
                'license_plate' => trim($_POST['license_plate'])
            ];
            
            // Validate license plate
            if (empty($data['formData']['license_plate'])) {
                $data['errors']['license_plate'] = 'Please enter a license plate number';
            }
            
            // If no errors, find vehicle
            if (empty($data['errors'])) {
                $licensePlate = $data['formData']['license_plate'];
                
                // Check if vehicle exists and is parked
                $vehicle = $this->vehicleModel->findByLicensePlate($licensePlate);
                if ($vehicle) {
                    $vehicleWithParking = $this->vehicleModel->getWithActiveParking($vehicle->id);
                } else {
                    $vehicleWithParking = null;
                }
                
                if (!$vehicleWithParking || !isset($vehicleWithParking->ticket_id)) {
                    $data['errors']['license_plate'] = 'Vehicle not found or not currently parked';
                } else {
                    // Redirect to process exit page
                    $this->redirect('agent/processExit/' . $vehicleWithParking->ticket_id);
                    return;
                }
            }
        }
        
        $this->view('agent/vehicle_exit', $data);
    }
    
    /**
     * Manage reservations
     *
     * @return void
     */
    public function reservations()
    {
        // Initialize data array
        $data = [
            'title' => 'Manage Reservations',
            'criteria' => [],
            'reservations' => [],
            'offset' => 0,
            'limit' => 10 // Show 10 results per page by default
        ];
        
        // Handle pagination
        if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
            $data['offset'] = max(0, (int)$_GET['offset']);
        }
        
        if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
            $data['limit'] = max(1, min(50, (int)$_GET['limit'])); // Limit between 1 and 50
        }
        
        // Handle search/filter criteria
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
            // Filter criteria
            if (!empty($_GET['customer_name'])) {
                $data['criteria']['customer_name'] = trim($_GET['customer_name']);
            }
            
            if (!empty($_GET['status'])) {
                $data['criteria']['status'] = trim($_GET['status']);
            }
            
            if (!empty($_GET['date'])) {
                $data['criteria']['date'] = trim($_GET['date']);
            }
            
            if (!empty($_GET['space_id'])) {
                $data['criteria']['space_id'] = (int)$_GET['space_id'];
            }
            
            // Get filtered reservations
            $reservationModel = $this->model('Reservation');
            $data['reservations'] = $reservationModel->getReservations($data['criteria'], $data['limit'], $data['offset']);
        } else {
            // Get all active reservations by default
            $reservationModel = $this->model('Reservation');
            $data['reservations'] = $reservationModel->getReservations(['status' => 'active'], $data['limit'], $data['offset']);
        }
        
        $this->view('agent/reservations', $data);
    }
    
    /**
     * Create a new reservation
     *
     * @return void
     */
    public function createReservation()
{
    // Initialize data array
    $data = [
        'title' => 'Create New Reservation',
        'formData' => [],
        'errors' => [],
        'spaces' => [],
        'vehicleTypes' => [],
    ];

    // Load available spaces and vehicle types
    $data['spaces'] = $this->parkingSpaceModel->getAvailableSpaces();
    $vehicleTypeModel = $this->model('Vehicle');
    $data['vehicleTypes'] = $vehicleTypeModel->getVehicleTypes();

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Get form data
        $data['formData'] = [
            'customer_name'    => trim($_POST['customer_name']),
            'customer_email'   => trim($_POST['customer_email']),
            'customer_phone'   => trim($_POST['customer_phone']),
            'space_id'         => (int)$_POST['space_id'],
            'vehicle_type_id'  => !empty($_POST['vehicle_type_id']) ? (int)$_POST['vehicle_type_id'] : null,
            'license_plate'    => !empty($_POST['license_plate']) ? trim($_POST['license_plate']) : null,
            'start_time'       => trim($_POST['start_time']),
            'end_time'         => trim($_POST['end_time']),
            'notes'            => !empty($_POST['notes']) ? trim($_POST['notes']) : null
        ];

        // Validate data
        if (empty($data['formData']['customer_name'])) {
            $data['errors']['customer_name'] = 'Customer name is required';
        }
        if (empty($data['formData']['customer_email'])) {
            $data['errors']['customer_email'] = 'Customer email is required';
        } elseif (!filter_var($data['formData']['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $data['errors']['customer_email'] = 'Invalid email format';
        }
        if (empty($data['formData']['customer_phone'])) {
            $data['errors']['customer_phone'] = 'Customer phone is required';
        }
        if (empty($data['formData']['space_id'])) {
            $data['errors']['space_id'] = 'Please select a parking space';
        }
        if (empty($data['formData']['vehicle_type_id'])) {
            $data['errors']['vehicle_type_id'] = 'Please select vehicle type';
        }
        if (empty($data['formData']['start_time'])) {
            $data['errors']['start_time'] = 'Start time is required';
        }
        if (empty($data['formData']['end_time'])) {
            $data['errors']['end_time'] = 'End time is required';
        }

        // Validate times if provided
        $startTime = strtotime($data['formData']['start_time']);
        $endTime = strtotime($data['formData']['end_time']);
        if ($startTime && $endTime) {
            if ($startTime >= $endTime) {
                $data['errors']['end_time'] = 'End time must be after start time';
            }
            if ($startTime < time()) {
                $data['errors']['start_time'] = 'Start time cannot be in the past';
            }
        }

        // If no errors, create reservation
        if (empty($data['errors'])) {
            $reservationModel = $this->model('Reservation');

            // Check for time conflicts
            if ($reservationModel->hasTimeConflict(
                $data['formData']['space_id'],
                $data['formData']['start_time'],
                $data['formData']['end_time']
            )) {
                $data['errors']['time_conflict'] = 'There is a time conflict with another reservation for this space';
                $this->view('agent/create_reservation', $data);
                return;
            }

            // Handle vehicle first - vehicle_id is required in the database
            $vehicleModel = $this->model('Vehicle');
            $vehicleId = null;

            // If license plate is provided, find or create the vehicle
            if (!empty($data['formData']['license_plate'])) {
                // Try to find existing vehicle
                $vehicle = $vehicleModel->getVehicleByLicensePlate($data['formData']['license_plate']);

                if ($vehicle) {
                    $vehicleId = $vehicle->id;
                } else {
                    // Create new vehicle
                    $vehicleData = [
                        'license_plate' => $data['formData']['license_plate'],
                        'type_id' => $data['formData']['vehicle_type_id'],
                        'owner_name' => $data['formData']['customer_name'],
                        'owner_phone' => $data['formData']['customer_phone']
                    ];
                    $vehicleId = $vehicleModel->createVehicle($vehicleData);
                }
            } else {
                // No license plate provided, create a temporary vehicle with a placeholder license plate
                $placeholderLicense = 'TEMP-' . time() . '-' . rand(1000, 9999);
                $vehicleData = [
                    'license_plate' => $placeholderLicense,
                    'type_id' => $data['formData']['vehicle_type_id'],
                    'owner_name' => $data['formData']['customer_name'],
                    'owner_phone' => $data['formData']['customer_phone']
                ];
                $vehicleId = $vehicleModel->createVehicle($vehicleData);
            }

            // If we couldn't create or find a vehicle, show an error
            if (!$vehicleId) {
                $data['errors']['general'] = 'Failed to create or find vehicle';
                $this->view('agent/create_reservation', $data);
                return;
            }

            // Assemble reservation data for insertion
            $reservationData = [
                'vehicle_id'      => $vehicleId,
                'space_id'        => $data['formData']['space_id'],
                'start_time'      => $data['formData']['start_time'],
                'end_time'        => $data['formData']['end_time'],
                'created_by'      => $_SESSION['user_id'],
                'customer_email'  => $data['formData']['customer_email'],
                'customer_phone'  => $data['formData']['customer_phone'],
                'vehicle_type_id' => $data['formData']['vehicle_type_id'],
                'license_plate'   => $data['formData']['license_plate'],
                'notes'           => $data['formData']['notes']
            ];

            $reservationId = $reservationModel->createReservation($reservationData);

            if ($reservationId) {
                flash('reservation_success', 'Reservation created successfully');
                $this->redirect('agent/viewReservation/' . $reservationId);
                return;
            } else {
                $data['errors']['general'] = 'Failed to create reservation';
            }
        }
    }

    $this->view('agent/create_reservation', $data);
}
    
    /**
     * View reservation details
     *
     * @param int $id Reservation ID
     * @return void
     */
    public function viewReservation($id)
    {
        // Get reservation details
        $reservationModel = $this->model('Reservation');
        $reservation = $reservationModel->getReservationById($id);
        
        if (!$reservation) {
            flash('reservation_error', 'Reservation not found', 'alert alert-danger');
            $this->redirect('agent/reservations');
            return;
        }
        
        // Get space details
        $spaceModel = $this->model('ParkingSpace');
        $space = $spaceModel->getSpaceDetails($reservation->space_id);
        
        // Get vehicle details
        $vehicleModel = $this->model('Vehicle');
        $vehicle = $vehicleModel->getWithActiveParking($reservation->vehicle_id);
        
        // Get existing reservations for this space (for timeline display)
        $existingReservations = $reservationModel->getUpcomingReservationsForSpace($reservation->space_id);
        
        // Prepare data for view
        $data = [
            'title' => 'Reservation Details',
            'reservation' => $reservation,
            'space' => $space,
            'vehicle' => $vehicle,
            'existingReservations' => $existingReservations
        ];
        
        $this->view('agent/view_reservation', $data);
    }

    /**
     * Edit an existing reservation
     *
     * @param int $id Reservation ID
     * @return void
     */
    public function editReservation($id)
    {
        $reservationModel = $this->model('Reservation');
        $reservation = $reservationModel->getReservationById($id);

        if (!$reservation) {
            flash('reservation_error', 'Reservation not found.', 'alert alert-danger');
            $this->redirect('agent/reservations');
            return;
        }

        // Check if reservation status allows editing (e.g., not completed or cancelled)
        if (in_array($reservation->status, ['completed', 'cancelled', 'no_show'])) {
            flash('reservation_error', 'This reservation cannot be edited because it is ' . $reservation->status . '.', 'alert alert-warning');
            $this->redirect('agent/viewReservation/' . $id);
            return;
        }

        // Load data for forms (spaces, vehicle types)
        $data['spaces'] = $this->parkingSpaceModel->getAllSpaces(); // Or getAvailableSpaces if preferred
        $vehicleTypeModel = $this->model('Vehicle'); // Vehicle model is already a property $this->vehicleModel
        $data['vehicleTypes'] = $this->vehicleModel->getVehicleTypes();

        // Prepare form data, pre-filled with existing reservation details
        // Note: The ReservationModel::updateReservation method updates specific fields.
        // We should align the form with those fields.
        // Current fields in updateReservation: customer_name, customer_email, customer_phone, vehicle_type_id, license_plate, start_time, end_time, notes
        // It does NOT update space_id. If space_id needs to be editable, the model's updateReservation needs adjustment.

        $data['formData'] = [
            'customer_name' => $reservation->customer_name,
            'customer_email' => $reservation->customer_email ?? '',
            'customer_phone' => $reservation->customer_phone ?? '',
            'vehicle_id' => $reservation->vehicle_id, // Needed to fetch vehicle details
            'license_plate' => $reservation->license_plate ?? '',
            'vehicle_type_id' => null, // Will be fetched from vehicle details if available
            'space_id' => $reservation->space_id,
            'start_time' => date('Y-m-d\TH:i', strtotime($reservation->start_time)),
            'end_time' => date('Y-m-d\TH:i', strtotime($reservation->end_time)),
            'notes' => $reservation->notes ?? ''
        ];
        
        // Fetch vehicle details to get current vehicle_type_id if vehicle exists
        if ($reservation->vehicle_id) {
            $vehicle = $this->vehicleModel->getVehicleById($reservation->vehicle_id);
            if ($vehicle) {
                $data['formData']['vehicle_type_id'] = $vehicle->type_id;
            }
        }

        $data = [
            'title' => 'Edit Reservation (ID: ' . $id . ')',
            'reservation_id' => $id,
            'formData' => $data['formData'],
            'spaces' => $data['spaces'],
            'vehicleTypes' => $data['vehicleTypes'],
            'errors' => [] // Initialize empty errors array for the form
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                flash('reservation_error', 'Invalid request. Please try again.', 'alert alert-danger');
                $this->redirect('agent/editReservation/' . $id);
                return;
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $submittedData = [
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'customer_email' => trim($_POST['customer_email'] ?? ''),
                'customer_phone' => trim($_POST['customer_phone'] ?? ''),
                'license_plate' => strtoupper(trim($_POST['license_plate'] ?? '')),
                'vehicle_type_id' => !empty($_POST['vehicle_type_id']) ? (int)$_POST['vehicle_type_id'] : null,
                'start_time' => trim($_POST['start_time'] ?? ''),
                'end_time' => trim($_POST['end_time'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'space_id' => $reservation->space_id // Space ID is not editable, use original
            ];

            $errors = [];

            // Validate form data
            if (empty($submittedData['customer_name'])) {
                $errors['customer_name'] = 'Customer name is required.';
            }
            if (!empty($submittedData['customer_email']) && !filter_var($submittedData['customer_email'], FILTER_VALIDATE_EMAIL)) {
                $errors['customer_email'] = 'Invalid email format.';
            }
            if (empty($submittedData['start_time'])) {
                $errors['start_time'] = 'Start time is required.';
            }
            if (empty($submittedData['end_time'])) {
                $errors['end_time'] = 'End time is required.';
            }

            $startTimeTimestamp = strtotime($submittedData['start_time']);
            $endTimeTimestamp = strtotime($submittedData['end_time']);

            if ($startTimeTimestamp && $endTimeTimestamp) {
                if ($endTimeTimestamp <= $startTimeTimestamp) {
                    $errors['end_time'] = 'End time must be after start time.';
                }
                // Optional: Check if start time is in the past (if editing active/future reservations)
                // if ($startTimeTimestamp < time() && $reservation->status !== 'active') { // Allow editing start time of active reservations if needed
                //     $errors['start_time'] = 'Start time cannot be in the past for new edits unless reservation is already active.';
                // }
            } else {
                if (empty($submittedData['start_time'])) $errors['start_time'] = 'Invalid start time format.';
                if (empty($submittedData['end_time'])) $errors['end_time'] = 'Invalid end time format.';
            }
            
            // Check for time conflicts, excluding the current reservation
            if (empty($errors) && $this->reservationModel->hasTimeConflict($submittedData['space_id'], $submittedData['start_time'], $submittedData['end_time'], $id)) {
                $errors['time_conflict'] = 'The selected time slot conflicts with an existing reservation for this space.';
            }

            if (!empty($errors)) {
                // Re-populate data for the view with submitted values and errors
                $data['formData'] = $submittedData; // Use submitted data to refill form
                $data['errors'] = $errors;
                flash('reservation_error', 'Please correct the errors below.', 'alert alert-danger');
                $this->view('agent/edit_reservation', $data);
            } else {
                // Prepare data for ReservationModel::updateReservation
                $updateResult = $this->reservationModel->updateReservation(
                    $id, // reservation_id
                    $submittedData['customer_name'],
                    $submittedData['start_time'],
                    $submittedData['end_time'],
                    $submittedData['notes'],
                    $_SESSION['user_id'], // updated_by
                    $submittedData['customer_email'],
                    $submittedData['customer_phone'],
                    $submittedData['license_plate'],
                    $submittedData['vehicle_type_id']
                );

                if ($updateResult) {
                    $this->activityLogModel->logActivity($_SESSION['user_id'], 'reservation_updated', "Reservation ID: {$id} updated by agent.");
                    flash('reservation_success', 'Reservation updated successfully!');
                    $this->redirect('agent/viewReservation/' . $id);
                } else {
                    $data['formData'] = $submittedData;
                    $data['errors']['general'] = 'Failed to update reservation. Please try again.';
                    flash('reservation_error', 'Failed to update reservation. An unexpected error occurred.', 'alert alert-danger');
                    $this->view('agent/edit_reservation', $data);
                }
            }
        } else {
            // Display the form (GET request)
            $this->view('agent/edit_reservation', $data);
        }
    }

    /**
     * Handle POST request to cancel a reservation.
     *
     * @param int $id Reservation ID
     * @return void
     */
    public function cancelReservationPost($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('reservation_error', 'Invalid request method.', 'alert alert-danger');
            $this->redirect('agent/reservations');
            return;
        }

        // Verify CSRF token
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            flash('reservation_error', 'Invalid request. Please try again.', 'alert alert-danger');
            $this->redirect('agent/viewReservation/' . $id);
            return;
        }

        $reservationModel = $this->model('Reservation');
        $reservation = $reservationModel->getReservationById($id);

        if (!$reservation) {
            flash('reservation_error', 'Reservation not found.', 'alert alert-danger');
            $this->redirect('agent/reservations');
            return;
        }

        // Check if reservation can be cancelled
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            flash('reservation_info', 'This reservation is already ' . $reservation->status . ' and cannot be cancelled again.', 'alert alert-info');
            $this->redirect('agent/viewReservation/' . $id);
            return;
        }
        
        // Potentially add a check here if only 'pending' or 'confirmed' or 'active' reservations can be cancelled.
        // For example, if 'active' reservations that have already started should not be cancelled via this simple flow.
        // Current ReservationModel::cancelReservation just sets status to 'cancelled'.

        if ($reservationModel->cancelReservation($id)) {
            $this->activityLogModel->logActivity($_SESSION['user_id'], 'reservation_cancelled', "Reservation ID: {$id} cancelled by agent.");
            flash('reservation_success', 'Reservation (ID: ' . $id . ') has been successfully cancelled.');
            $this->redirect('agent/viewReservation/' . $id); // Or redirect to agent/reservations
        } else {
            flash('reservation_error', 'Failed to cancel reservation. Please try again.', 'alert alert-danger');
            $this->redirect('agent/viewReservation/' . $id);
        }
    }
    
    /**
     * This was a duplicate viewReservation method - removed to fix syntax error
     */
    /*public function duplicateViewReservation($id)
    {
        $reservationModel = $this->model('Reservation');
        $reservation = $reservationModel->getReservationById($id);
        
        if (!$reservation) {
            flash('reservation_error', 'Reservation not found', 'alert-danger');
            $this->redirect('agent/reservations');
            return;
        }
        
        // Get space details
        $space = $this->parkingSpaceModel->getSpaceById($reservation->space_id);
        
        // Get existing reservations for this space
        $existingReservations = $reservationModel->getUpcomingReservationsForSpace($reservation->space_id);
        
        $data = [
            'title' => 'Reservation Details',
            'reservation' => $reservation,
            'space' => $space,
            'existingReservations' => $existingReservations
        ];
        
        $this->view('agent/view_reservation', $data);
    }*/
    
    /**
     * Parking map view
     *
     * @return void
     */
    public function parkingMap()
    {
        // Get all spaces with their current status
        $spaces = $this->parkingSpaceModel->getAllWithType();
        $data = [
            'title' => 'Parking Map',
            'spaces' => $spaces
        ];
        $this->view('agent/parking_map', $data);
    }
    
    /**
     * Process vehicle exit
     *
     * @param int $ticketId Ticket ID
     * @return void
     */
    public function processExit($ticketId)
    {
        // Get ticket details
        $ticket = $this->parkingTicketModel->getTicketDetails($ticketId);
        
        if (!$ticket || $ticket->exit_time) {
            flash('ticket_error', 'Invalid ticket or already processed', 'alert-danger');
            $this->redirect('agent/vehicleExit');
            return;
        }
        
        $data = [
            'title' => 'Process Vehicle Exit',
            'ticket' => $ticket,
            'formData' => [],
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $data['formData'] = [
                'payment_method' => trim($_POST['payment_method']),
                'amount_paid' => (float)$_POST['amount_paid']
            ];
            
            // Validate payment method
            if (empty($data['formData']['payment_method'])) {
                $data['errors']['payment_method'] = 'Please select a payment method';
            }
            
            // Validate amount paid
            if (empty($data['formData']['amount_paid'])) {
                $data['errors']['amount_paid'] = 'Please enter the amount paid';
            } elseif ($data['formData']['amount_paid'] < $ticket->amount_due) {
                $data['errors']['amount_paid'] = 'Amount paid must be at least the amount due';
            }
            
            // If no errors, process exit
            if (empty($data['errors'])) {
                $amountPaid = $data['formData']['amount_paid'];
                $paymentMethod = $data['formData']['payment_method'];
                
                // Process exit
                $success = $this->parkingTicketModel->closeTicket($ticketId, $amountPaid, $paymentMethod);
                
                if ($success) {
                    flash('ticket_success', 'Vehicle exit processed successfully');
                    $this->redirect('agent/dashboard');
                    return;
                } else {
                    $data['errors']['general'] = 'Something went wrong';
                }
            }
        }
        
        $this->view('agent/process_exit', $data);
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
            flash('ticket_error', 'Ticket not found', 'alert-danger');
            $this->redirect('agent/dashboard');
            return;
        }
        $data = [
            'title' => 'Ticket Details',
            'ticket' => $ticket
        ];
        $this->view('agent/ticket_details', $data);
    }
    
    /**
     * Search tickets
     *
     * @return void
     */
    public function searchTickets()
    {
        $limit = 10;
        $offset = 0;
        
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int)$_GET['page'];
            $offset = ($page - 1) * $limit;
        }
        
        $criteria = [];
        
        // Handle search criteria
        if (!empty($_GET['license_plate'])) {
            $criteria['license_plate'] = trim($_GET['license_plate']);
        }
        
        if (!empty($_GET['status'])) {
            $criteria['status'] = trim($_GET['status']);
        }
        
        if (!empty($_GET['date'])) {
            $criteria['date'] = trim($_GET['date']);
        }
        
        // Get tickets based on criteria
        $tickets = $this->parkingTicketModel->searchTickets($criteria, $limit, $offset);
        
        $data = [
            'title' => 'Search Tickets',
            'tickets' => $tickets,
            'criteria' => $criteria,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        $this->view('agent/search_tickets', $data);
    }

    /**
     * Get space details for AJAX request
     *
     * @param int $spaceId Space ID
     * @return void
     */
    public function getSpaceDetails($spaceId)
    {
        // Get space details
        $space = $this->parkingSpaceModel->getSpaceById($spaceId);
        
        if (!$space) {
            http_response_code(404);
            echo json_encode(['error' => 'Space not found']);
            return;
        }
        
        // Get any active ticket for this space
        $ticket = $this->parkingTicketModel->getActiveTicketBySpaceId($spaceId);
        $vehicle = null;
        
        if ($ticket) {
            $vehicle = $this->vehicleModel->getVehicleById($ticket->vehicle_id);
        }
        
        // Prepare response data
        $response = [
            'space_number' => $space->space_number,
            'type_name' => $space->type_name,
            'status' => $space->status,
            'vehicle' => $vehicle ? [
                'license_plate' => $vehicle->license_plate,
                'type_name' => $vehicle->type_name,
                'owner_name' => $vehicle->owner_name,
                'owner_phone' => $vehicle->owner_phone
            ] : null
        ];
        
        echo json_encode($response);
    }
}
