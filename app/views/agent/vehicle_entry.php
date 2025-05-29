<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <form action="<?= URL_ROOT ?>/agent/vehicleEntry" method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc pl-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate *</label>
                <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['license_plate']) ? 'border-red-500' : 'border-gray-300' ?>" required>
                <p class="text-gray-500 text-xs mt-1">Enter the vehicle's license plate number</p>
            </div>
            
            <div>
                <label for="type_id" class="block text-gray-700 mb-1 font-medium">Vehicle Type *</label>
                <select id="vehicle_type_id" name="vehicle_type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                    <?php foreach ($vehicleTypes as $type): ?>
                        <option value="<?= $type->id ?>"><?= $type->name ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-gray-500 text-xs mt-1">Select the type of vehicle</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4 mt-4">
            <h3 class="text-lg font-semibold mb-2">Owner Information (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="owner_name" class="block text-gray-700 mb-1 font-medium">Owner Name</label>
                    <input type="text" id="owner_name" name="owner_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
                
                <div>
                    <label for="owner_phone" class="block text-gray-700 mb-1 font-medium">Owner Phone</label>
                    <input type="text" id="owner_phone" name="owner_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">
                <i class="fas fa-car mr-2"></i> Register Vehicle Entry
            </button>
        </div>
    </form>
</div>

<div class="bg-blue-50 rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Vehicle Entry Instructions</h2>
    
    <div class="space-y-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">1</span>
            </div>
            <div>
                <h3 class="font-semibold">Enter License Plate</h3>
                <p class="text-gray-600">Enter the vehicle's license plate number. If the vehicle has been here before, the system will recognize it.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">2</span>
            </div>
            <div>
                <h3 class="font-semibold">Select Vehicle Type</h3>
                <p class="text-gray-600">Choose the appropriate vehicle type. This helps the system assign a compatible parking space.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">3</span>
            </div>
            <div>
                <h3 class="font-semibold">Add Owner Information (Optional)</h3>
                <p class="text-gray-600">Enter owner details if available. This is useful for contacting the owner if needed.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">4</span>
            </div>
            <div>
                <h3 class="font-semibold">Complete Registration</h3>
                <p class="text-gray-600">Click the "Register Vehicle Entry" button to complete the process. The system will automatically assign an available space and generate a ticket.</p>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
