<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    <p class="text-gray-700 mb-6"><?= $description ?></p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
            <div class="text-blue-800 text-4xl mb-4">
                <i class="fas fa-car"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Parking Management</h3>
            <p class="text-gray-600">Efficient parking space management with real-time monitoring and updates.</p>
        </div>
        
        <div class="bg-green-50 p-6 rounded-lg shadow-sm">
            <div class="text-green-800 text-4xl mb-4">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Reservations</h3>
            <p class="text-gray-600">Reserve your parking spot in advance and ensure availability when you arrive.</p>
        </div>
        
        <div class="bg-purple-50 p-6 rounded-lg shadow-sm">
            <div class="text-purple-800 text-4xl mb-4">
                <i class="fas fa-credit-card"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Automated Billing</h3>
            <p class="text-gray-600">Transparent billing system with accurate time tracking and payment processing.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-blue-800 mb-4">Parking Spaces</h2>
        <p class="text-gray-700 mb-4">Our parking facility offers various types of spaces to accommodate different needs:</p>
        
        <div class="space-y-3">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                <p class="text-gray-700"><span class="font-semibold">Standard Spaces:</span> For regular vehicles</p>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                <p class="text-gray-700"><span class="font-semibold">Handicap Spaces:</span> Accessible parking for those who need it</p>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-purple-500 rounded-full mr-3"></div>
                <p class="text-gray-700"><span class="font-semibold">VIP Spaces:</span> Premium locations with additional benefits</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-blue-800 mb-4">Vehicle Types</h2>
        <p class="text-gray-700 mb-4">We accommodate various vehicle types with specialized parking solutions:</p>
        
        <div class="space-y-3">
            <div class="flex items-center">
                <i class="fas fa-car text-blue-500 mr-3"></i>
                <p class="text-gray-700"><span class="font-semibold">Cars:</span> Standard parking spaces</p>
            </div>
            <div class="flex items-center">
                <i class="fas fa-motorcycle text-green-500 mr-3"></i>
                <p class="text-gray-700"><span class="font-semibold">Motorcycles:</span> Dedicated motorcycle parking</p>
            </div>
            <div class="flex items-center">
                <i class="fas fa-truck text-purple-500 mr-3"></i>
                <p class="text-gray-700"><span class="font-semibold">Trucks & Trailers:</span> Larger spaces for bigger vehicles</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 text-center">
    <?php if(!isLoggedIn()): ?>
        <p class="text-gray-700 mb-4">Create an account to access all features of our parking management system.</p>
        <div class="space-x-4">
            <a href="<?= URL_ROOT ?>/users/register" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">Register Now</a>
            <a href="<?= URL_ROOT ?>/users/login" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">Login</a>
        </div>
    <?php else: ?>
        <p class="text-gray-700 mb-4">Welcome back! Access your dashboard to manage parking.</p>
        <?php if(hasRole(ADMIN_ROLE)): ?>
            <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">Go to Admin Dashboard</a>
        <?php elseif(hasRole(AGENT_ROLE)): ?>
            <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">Go to Agent Dashboard</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
