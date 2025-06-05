<?php
namespace App\Models;

class Agent extends User
{
    protected $role = 'agent';

    // Example agent-specific method
    public function getAssignedSpaces()
    {
        // Implement logic to get spaces assigned to this agent if needed
        return [];
    }
    
} 