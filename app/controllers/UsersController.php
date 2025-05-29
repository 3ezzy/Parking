<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Users Controller
 * Handles user authentication and management
 */
class UsersController extends Controller
{
    private $userModel;
    
    /**
     * Constructor - initialize models
     */
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Register a new user
     *
     * @return void
     */
    public function register()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Register',
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Form data
            $data['name'] = trim($_POST['name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);
            
            // Validate name
            if (empty($data['name'])) {
                $data['errors']['name'] = 'Please enter your name';
            }
            
            // Validate email
            if (empty($data['email'])) {
                $data['errors']['email'] = 'Please enter your email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Please enter a valid email';
            } elseif ($this->userModel->findByEmail($data['email'])) {
                $data['errors']['email'] = 'Email is already taken';
            }
            
            // Validate password
            if (empty($data['password'])) {
                $data['errors']['password'] = 'Please enter a password';
            } elseif (strlen($data['password']) < 6) {
                $data['errors']['password'] = 'Password must be at least 6 characters';
            }
            
            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['errors']['confirm_password'] = 'Please confirm your password';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }
            
            // If no errors, register user
            if (empty($data['errors'])) {
                // Register user
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => 'agent'  // Default role
                ];
                
                $userId = $this->userModel->register($userData);
                
                if ($userId) {
                    // Log activity
                    $this->userModel->logActivity($userId, 'User registered', $_SERVER['REMOTE_ADDR']);
                    
                    // Set flash message and redirect
                    flash('register_success', 'You are registered and can now log in');
                    $this->redirect('users/login');
                } else {
                    // Registration failed
                    die('Something went wrong');
                }
            }
        }
        
        // Load view
        $this->view('users/register', $data);
    }
    
    /**
     * Login a user
     *
     * @return void
     */
    public function login()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Login',
            'email' => '',
            'password' => '',
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Form data
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            
            // Validate email
            if (empty($data['email'])) {
                $data['errors']['email'] = 'Please enter your email';
            }
            
            // Validate password
            if (empty($data['password'])) {
                $data['errors']['password'] = 'Please enter your password';
            }
            
            // If no errors, login user
            if (empty($data['errors'])) {
                // Log in user
                $user = $this->userModel->login($data['email'], $data['password']);
                
                if ($user) {
                    // Create session
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_role'] = $user->role;
                    
                    // Log activity
                    $this->userModel->logActivity($user->id, 'User logged in', $_SERVER['REMOTE_ADDR']);
                    
                    // Redirect based on role
                    if ($user->role === 'admin') {
                        $this->redirect('admin/dashboard');
                    } else {
                        $this->redirect('agent/dashboard');
                    }
                } else {
                    $data['errors']['login'] = 'Invalid email or password';
                }
            }
        }
        
        // Load view
        $this->view('users/login', $data);
    }
    
    /**
     * Logout a user
     *
     * @return void
     */
    public function logout()
    {
        // Simply destroy the session without logging activity
        // This avoids potential foreign key constraint issues
        
        // Unset session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        
        // Destroy session
        session_destroy();
        
        // Redirect to login
        $this->redirect('users/login');
    }
    
    /**
     * Show user profile
     *
     * @return void
     */
    public function profile()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('users/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        $data = [
            'title' => 'Profile',
            'user' => $this->userModel->findById($userId),
            'activities' => $this->userModel->getActivities($userId, 10),
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Check which form was submitted
            if (isset($_POST['update_profile'])) {
                // Update profile
                $name = trim($_POST['name']);
                
                // Validate name
                if (empty($name)) {
                    $data['errors']['name'] = 'Please enter your name';
                }
                
                // If no errors, update profile
                if (empty($data['errors'])) {
                    $updateData = [
                        'name' => $name
                    ];
                    
                    if ($this->userModel->update($userId, $updateData)) {
                        // Update session
                        $_SESSION['user_name'] = $name;
                        
                        // Log activity
                        $this->userModel->logActivity($userId, 'Profile updated', $_SERVER['REMOTE_ADDR']);
                        
                        // Set flash message
                        flash('profile_success', 'Profile updated successfully');
                        $this->redirect('users/profile');
                    } else {
                        die('Something went wrong');
                    }
                }
            } elseif (isset($_POST['change_password'])) {
                // Change password
                $currentPassword = trim($_POST['current_password']);
                $newPassword = trim($_POST['new_password']);
                $confirmPassword = trim($_POST['confirm_password']);
                
                // Validate current password
                if (empty($currentPassword)) {
                    $data['errors']['current_password'] = 'Please enter your current password';
                } elseif (!password_verify($currentPassword, $data['user']->password)) {
                    $data['errors']['current_password'] = 'Current password is incorrect';
                }
                
                // Validate new password
                if (empty($newPassword)) {
                    $data['errors']['new_password'] = 'Please enter a new password';
                } elseif (strlen($newPassword) < 6) {
                    $data['errors']['new_password'] = 'Password must be at least 6 characters';
                }
                
                // Validate confirm password
                if (empty($confirmPassword)) {
                    $data['errors']['confirm_password'] = 'Please confirm your new password';
                } elseif ($newPassword !== $confirmPassword) {
                    $data['errors']['confirm_password'] = 'Passwords do not match';
                }
                
                // If no errors, change password
                if (empty($data['errors'])) {
                    if ($this->userModel->changePassword($userId, $newPassword)) {
                        // Log activity
                        $this->userModel->logActivity($userId, 'Password changed', $_SERVER['REMOTE_ADDR']);
                        
                        // Set flash message
                        flash('profile_success', 'Password changed successfully');
                        $this->redirect('users/profile');
                    } else {
                        die('Something went wrong');
                    }
                }
            }
        }
        
        // Load view
        $this->view('users/profile', $data);
    }
}
