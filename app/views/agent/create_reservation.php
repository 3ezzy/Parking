<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/reservations" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reservations
        </a>
    </div>
    
    <?php flash('reservation_success'); ?>
    <?php flash('reservation_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Reservation Details</h2>
                
                <form action="<?= URL_ROOT ?>/agent/createReservation" method="POST">
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
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-gray-700 mb-1 font-medium">Customer Name *</label>
                                <input type="text" id="customer_name" name="customer_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['customer_name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['customer_name'] ?? '' ?>" required>
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-gray-700 mb-1 font-medium">Customer Phone</label>
                                <input type="text" id="customer_phone" name="customer_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['customer_phone'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-gray-700 mb-1 font-medium">Customer Email</label>
                            <input type="email" id="customer_email" name="customer_email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['customer_email'] ?? '' ?>">
                        </div>
                        
                        <div>
                            <label for="space_id" class="block text-gray-700 mb-1 font-medium">Parking Space *</label>
                            <select id="space_id" name="space_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['space_id']) ? 'border-red-500' : 'border-gray-300' ?>" required>
                                <option value="">Select Parking Space</option>
                                <?php foreach ($spaces as $space): ?>
                                    <option value="<?= $space->id ?>" <?= (isset($formData['space_id']) && $formData['space_id'] == $space->id) ? 'selected' : '' ?>>
                                        Space #<?= $space->space_number ?> (<?= $space->type_name ?>) - $<?= number_format($space->hourly_rate, 2) ?>/hr
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-gray-500 text-xs mt-1">Only available spaces are shown</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate (Optional)</label>
                                <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['license_plate'] ?? '' ?>">
                                <p class="text-gray-500 text-xs mt-1">If known in advance</p>
                            </div>
                            
                            <div>
                                <label for="vehicle_type_id" class="block text-gray-700 mb-1 font-medium">Vehicle Type</label>
                                <select id="vehicle_type_id" name="vehicle_type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                    <option value="">Select Vehicle Type</option>
                                    <?php foreach ($vehicleTypes as $type): ?>
                                        <option value="<?= $type->id ?>" <?= (isset($formData['vehicle_type_id']) && $formData['vehicle_type_id'] == $type->id) ? 'selected' : '' ?>><?= $type->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-gray-700 mb-1 font-medium">Start Time *</label>
                                <input type="datetime-local" id="start_time" name="start_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['start_time']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['start_time'] ?? date('Y-m-d\TH:i', strtotime('+1 hour')) ?>" required>
                            </div>
                            
                            <div>
                                <label for="end_time" class="block text-gray-700 mb-1 font-medium">End Time *</label>
                                <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['end_time']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['end_time'] ?? date('Y-m-d\TH:i', strtotime('+3 hours')) ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-gray-700 mb-1 font-medium">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"><?= $formData['notes'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-calendar-plus mr-2"></i> Create Reservation
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Reservation Information</h2>
                
                <div class="space-y-3">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="font-semibold text-yellow-800 mb-2">Guidelines</h3>
                        <ul class="text-sm text-yellow-700 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2"></i>
                                <span>Reservations must be made at least 1 hour in advance.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2"></i>
                                <span>Customers should arrive within 30 minutes of their reservation start time or risk losing the space.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2"></i>
                                <span>A no-show fee of $<?= RESERVATION_PENALTY ?> may apply if the customer doesn't arrive.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <h3 class="font-semibold text-gray-700 mb-2">Today's Reservation Count</h3>
                        <div class="text-3xl font-bold text-blue-600">
                            <?= count($spaces) ?>
                        </div>
                        <p class="text-gray-500 text-sm">available spaces</p>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-2">Pricing</h3>
                        <ul class="text-sm space-y-1">
                            <li class="flex justify-between">
                                <span>Standard Space:</span>
                                <span class="font-medium">$<?= STANDARD_HOURLY_RATE ?>/hour</span>
                            </li>
                            <li class="flex justify-between">
                                <span>VIP Space:</span>
                                <span class="font-medium">$<?= VIP_HOURLY_RATE ?>/hour</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Handicap Space:</span>
                                <span class="font-medium">$<?= HANDICAP_HOURLY_RATE ?>/hour</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                // Ensure end time is at least 1 hour after start time
                const startTime = new Date(this.value);
                const endTime = new Date(endTimeInput.value);
                
                if (endTime <= startTime) {
                    // Set end time to 2 hours after start time
                    startTime.setHours(startTime.getHours() + 2);
                    const year = startTime.getFullYear();
                    const month = String(startTime.getMonth() + 1).padStart(2, '0');
                    const day = String(startTime.getDate()).padStart(2, '0');
                    const hours = String(startTime.getHours()).padStart(2, '0');
                    const minutes = String(startTime.getMinutes()).padStart(2, '0');
                    
                    endTimeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                }
            });
        }
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
