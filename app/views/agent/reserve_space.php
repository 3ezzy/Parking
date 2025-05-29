<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/viewSpace/<?= $space->id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Space View
        </a>
    </div>
    
    <?php flash('reservation_success'); ?>
    <?php flash('reservation_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Information</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Space Number</h3>
                            <p class="text-lg"><?= $space->space_number ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Status</h3>
                            <p class="text-lg">
                                <?php if ($space->status === 'available'): ?>
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Available</span>
                                <?php else: ?>
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-sm">Not Available</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Space Type</h3>
                            <p class="text-lg"><?= $space->type_name ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Hourly Rate</h3>
                            <p class="text-lg">$<?= number_format($space->hourly_rate, 2) ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Location</h3>
                            <p class="text-lg"><?= $space->location ?? 'Main Area' ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Floor/Level</h3>
                            <p class="text-lg"><?= $space->floor_level ?? 'Ground' ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($existingReservations)): ?>
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm mt-6">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">Existing Reservations</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 border">Reservation #</th>
                                    <th class="px-4 py-2 border">Customer</th>
                                    <th class="px-4 py-2 border">Start Time</th>
                                    <th class="px-4 py-2 border">End Time</th>
                                    <th class="px-4 py-2 border">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($existingReservations as $reservation): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border"><?= $reservation->id ?></td>
                                        <td class="px-4 py-2 border"><?= $reservation->customer_name ?></td>
                                        <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($reservation->start_time)) ?></td>
                                        <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($reservation->end_time)) ?></td>
                                        <td class="px-4 py-2 border">
                                            <?php if ($reservation->status === 'active'): ?>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Active</span>
                                            <?php elseif ($reservation->status === 'completed'): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                            <?php else: ?>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Make Reservation</h2>
                
                <?php if ($space->status !== 'available'): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p>This space is not currently available for reservation.</p>
                    </div>
                <?php else: ?>
                    <form action="<?= URL_ROOT ?>/agent/reserveSpace/<?= $space->id ?>" method="POST">
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
                            <div>
                                <label for="customer_name" class="block text-gray-700 mb-1 font-medium">Customer Name *</label>
                                <input type="text" id="customer_name" name="customer_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['customer_name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['customer_name'] ?? '' ?>" required>
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-gray-700 mb-1 font-medium">Customer Phone</label>
                                <input type="text" id="customer_phone" name="customer_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['customer_phone'] ?? '' ?>">
                            </div>
                            
                            <div>
                                <label for="customer_email" class="block text-gray-700 mb-1 font-medium">Customer Email</label>
                                <input type="email" id="customer_email" name="customer_email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $formData['customer_email'] ?? '' ?>">
                            </div>
                            
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
                            
                            <div>
                                <label for="start_time" class="block text-gray-700 mb-1 font-medium">Start Time *</label>
                                <input type="datetime-local" id="start_time" name="start_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['start_time']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['start_time'] ?? date('Y-m-d\TH:i') ?>" required>
                            </div>
                            
                            <div>
                                <label for="end_time" class="block text-gray-700 mb-1 font-medium">End Time *</label>
                                <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['end_time']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['end_time'] ?? date('Y-m-d\TH:i', strtotime('+2 hours')) ?>" required>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-gray-700 mb-1 font-medium">Notes (Optional)</label>
                                <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"><?= $formData['notes'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                    <i class="fas fa-calendar-check mr-2"></i> Confirm Reservation
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mt-4">
                <h3 class="font-semibold text-blue-800 mb-2">Reservation Policy</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Reservations must be made at least 1 hour in advance</li>
                    <li>• Cancellations must be made 2 hours before start time</li>
                    <li>• Reserved spaces are held for 30 minutes past start time</li>
                    <li>• Payment is collected at the time of parking</li>
                </ul>
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
