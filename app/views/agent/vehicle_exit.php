<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <form action="<?= URL_ROOT ?>/agent/vehicleExit" method="POST" class="space-y-4">
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
        
        <div class="max-w-md mx-auto">
            <div class="mb-6">
                <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate *</label>
                <div class="flex">
                    <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-3 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['license_plate']) ? 'border-red-500' : 'border-gray-300' ?>" placeholder="Enter license plate number" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-r-lg transition">
                        <i class="fas fa-search mr-2"></i> Find
                    </button>
                </div>
                <p class="text-gray-500 text-xs mt-1">Enter the vehicle's license plate to process exit</p>
            </div>
        </div>
    </form>
</div>

<div class="bg-blue-50 rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Vehicle Exit Process</h2>
    
    <div class="space-y-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">1</span>
            </div>
            <div>
                <h3 class="font-semibold">Enter License Plate</h3>
                <p class="text-gray-600">Enter the vehicle's license plate number that is exiting the parking facility.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">2</span>
            </div>
            <div>
                <h3 class="font-semibold">Verify Ticket Details</h3>
                <p class="text-gray-600">The system will display the ticket details including entry time and calculated fee.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">3</span>
            </div>
            <div>
                <h3 class="font-semibold">Collect Payment</h3>
                <p class="text-gray-600">Collect payment from the customer based on the calculated parking fee.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-200 text-blue-700 rounded-full p-2 mr-3">
                <span class="font-bold">4</span>
            </div>
            <div>
                <h3 class="font-semibold">Complete Exit Process</h3>
                <p class="text-gray-600">Confirm the payment and complete the exit process. The parking space will be marked as available.</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <span class="font-bold">Note:</span> If a vehicle doesn't have a valid entry ticket, please contact the supervisor.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
