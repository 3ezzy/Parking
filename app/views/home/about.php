<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    <p class="text-gray-700 mb-6"><?= $description ?></p>
    
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-blue-700 mb-3">Our Mission</h2>
            <p class="text-gray-700">Our mission is to provide an efficient, secure, and user-friendly parking management solution that simplifies the parking experience for both administrators and users. We strive to optimize parking space utilization while ensuring fair pricing and excellent service.</p>
        </div>
        
        <div>
            <h2 class="text-2xl font-semibold text-blue-700 mb-3">Our System</h2>
            <p class="text-gray-700 mb-4">The Parking Management System offers a comprehensive solution for managing all aspects of parking operations:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Real-time Monitoring</h3>
                    <p class="text-gray-600">Our system provides real-time updates on parking space availability, allowing administrators to monitor the parking facility effectively.</p>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Automated Ticketing</h3>
                    <p class="text-gray-600">The system automatically generates tickets for vehicles entering the parking facility, recording entry time for accurate billing.</p>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Reservation System</h3>
                    <p class="text-gray-600">Users can reserve parking spaces in advance, ensuring availability when they arrive and optimizing space utilization.</p>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Transparent Billing</h3>
                    <p class="text-gray-600">Our billing system calculates fees based on actual parking duration, ensuring fair and transparent pricing for all users.</p>
                </div>
            </div>
        </div>
        
        <div>
            <h2 class="text-2xl font-semibold text-blue-700 mb-3">Our Team</h2>
            <p class="text-gray-700 mb-4">Our team consists of experienced professionals dedicated to providing the best parking management solution:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg">John Doe</h3>
                    <p class="text-blue-600">CEO & Founder</p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg">Jane Smith</h3>
                    <p class="text-blue-600">Operations Manager</p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg">Mike Johnson</h3>
                    <p class="text-blue-600">Technical Lead</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
