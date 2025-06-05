<?php
namespace App\Models;

class Admin extends User
{
    protected $role = 'admin';

    // Example admin-specific method
    public function getAllAgents()
    {
        return $this->findBy('role', 'agent');
    }
    
} 