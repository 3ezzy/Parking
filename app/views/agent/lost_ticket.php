<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('ticket_success'); ?>
    <?php flash('ticket_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-yellow-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-yellow-800 mb-4">Lost Ticket Processing</h2>
                
                <div class="bg-white border border-yellow-200 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-yellow-500">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-yellow-800">Lost Ticket Policy</h3>
                            <p class="text-gray-700">When a customer has lost their parking ticket, follow these steps:</p>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-gray-600">
                                <li>Ask for vehicle information (license plate, make, model)</li>
                                <li>Verify vehicle presence in the parking facility</li>
                                <li>Collect lost ticket fee: $<?= number_format($lostTicketFee, 2) ?></li>
                                <li>Issue a replacement ticket for exit processing</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <form action="<?= URL_ROOT ?>/agent/lostTicket" method="POST">
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
                                <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate *</label>
                                <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['license_plate']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['license_plate'] ?? '' ?>" required>
                            </div>
                            
                            <div>
                                <label for="vehicle_type_id" class="block text-gray-700 mb-1 font-medium">Vehicle Type *</label>
                                <select id="vehicle_type_id" name="vehicle_type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['vehicle_type_id']) ? 'border-red-500' : 'border-gray-300' ?>" required>
                                    <option value="">Select Vehicle Type</option>
                                    <?php foreach ($vehicleTypes as $type): ?>
                                        <option value="<?= $type->id ?>" <?= (isset($formData['vehicle_type_id']) && $formData['vehicle_type_id'] == $type->id) ? 'selected' : '' ?>><?= $type->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="vehicle_make" class="block text-gray-700 mb-1 font-medium">Vehicle Make</label>
                                <input type="text" id="vehicle_make" name="vehicle_make" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['vehicle_make'] ?? '' ?>">
                                <p class="text-gray-500 text-xs mt-1">e.g. Toyota, Honda, Ford</p>
                            </div>
                            
                            <div>
                                <label for="vehicle_model" class="block text-gray-700 mb-1 font-medium">Vehicle Model</label>
                                <input type="text" id="vehicle_model" name="vehicle_model" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['vehicle_model'] ?? '' ?>">
                                <p class="text-gray-500 text-xs mt-1">e.g. Camry, Civic, F-150</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="vehicle_color" class="block text-gray-700 mb-1 font-medium">Vehicle Color</label>
                                <input type="text" id="vehicle_color" name="vehicle_color" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['vehicle_color'] ?? '' ?>">
                            </div>
                            
                            <div>
                                <label for="parking_area" class="block text-gray-700 mb-1 font-medium">Approximate Parking Area</label>
                                <select id="parking_area" name="parking_area" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                    <option value="">Select Area (if known)</option>
                                    <option value="section_a" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'section_a') ? 'selected' : '' ?>>Section A</option>
                                    <option value="section_b" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'section_b') ? 'selected' : '' ?>>Section B</option>
                                    <option value="section_c" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'section_c') ? 'selected' : '' ?>>Section C</option>
                                    <option value="floor_1" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'floor_1') ? 'selected' : '' ?>>Floor 1</option>
                                    <option value="floor_2" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'floor_2') ? 'selected' : '' ?>>Floor 2</option>
                                    <option value="unknown" <?= (isset($formData['parking_area']) && $formData['parking_area'] == 'unknown') ? 'selected' : '' ?>>Unknown</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="entry_time_estimate" class="block text-gray-700 mb-1 font-medium">Estimated Entry Time</label>
                            <input type="datetime-local" id="entry_time_estimate" name="entry_time_estimate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['entry_time_estimate'] ?? date('Y-m-d\TH:i') ?>">
                            <p class="text-gray-500 text-xs mt-1">Approximate time the vehicle entered the facility</p>
                        </div>
                        
                        <div class="border-t border-yellow-200 pt-4 mt-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">Owner Information (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="owner_name" class="block text-gray-700 mb-1 font-medium">Owner Name</label>
                                    <input type="text" id="owner_name" name="owner_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['owner_name'] ?? '' ?>">
                                </div>
                                
                                <div>
                                    <label for="owner_phone" class="block text-gray-700 mb-1 font-medium">Owner Phone</label>
                                    <input type="text" id="owner_phone" name="owner_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['owner_phone'] ?? '' ?>">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label for="notes" class="block text-gray-700 mb-1 font-medium">Additional Notes</label>
                                <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"><?= $formData['notes'] ?? '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-ticket-alt mr-2"></i> Process Lost Ticket
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Lost Ticket Fee</h2>
                
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Base Fee:</span>
                        <span class="text-xl font-bold text-yellow-600">$<?= number_format($lostTicketFee, 2) ?></span>
                    </div>
                    <p class="text-gray-500 text-sm mt-2">This fee covers the administrative cost of processing a lost ticket.</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Additional Information</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <span>If the vehicle is found in the system, the standard parking fee will apply in addition to the lost ticket fee.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <span>A search will be conducted to locate the vehicle in our database.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <span>For security reasons, owner ID verification may be required.</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mt-6 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Search for Vehicle</h3>
                    <p class="text-gray-600 mb-3">If customer knows their license plate, you can search for an existing ticket:</p>
                    <a href="<?= URL_ROOT ?>/agent/vehicleExit" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                        <i class="fas fa-search mr-2"></i> Go to Vehicle Search
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
