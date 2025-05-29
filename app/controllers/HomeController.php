<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * HomeController class
 * Handles the homepage and public information
 */
class HomeController extends Controller
{
    /**
     * Display the homepage
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'title' => SITE_NAME,
            'description' => 'Welcome to the Parking Management System'
        ];
        
        $this->view('home/index', $data);
    }
    
    /**
     * Display the about page
     *
     * @return void
     */
    public function about()
    {
        $data = [
            'title' => 'About Us',
            'description' => 'Learn more about our parking system and services'
        ];
        
        $this->view('home/about', $data);
    }
    
    /**
     * Display the contact page
     *
     * @return void
     */
    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'description' => 'Get in touch with our team'
        ];
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form data
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $message = sanitize($_POST['message'] ?? '');
            
            // Basic validation
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Name is required';
            }
            
            if (empty($email)) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            }
            
            if (empty($message)) {
                $errors['message'] = 'Message is required';
            }
            
            if (empty($errors)) {
                // In a real application, you would send the email or save to database
                // For now, just set a success message
                flash('contact_success', 'Your message has been sent successfully!');
                $this->redirect('home/contact');
            } else {
                $data['errors'] = $errors;
                $data['name'] = $name;
                $data['email'] = $email;
                $data['message'] = $message;
            }
        }
        
        $this->view('home/contact', $data);
    }
}
