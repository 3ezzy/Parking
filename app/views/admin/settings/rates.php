<!-- Parking Rates Section -->
<div id="rate-settings" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Parking Rates</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Set hourly rates for different types of parking spaces and vehicles. These rates will be used to calculate parking fees.
                </p>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-700">Current Rates</h3>
        <button id="add-rate-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition text-sm">
            <i class="fas fa-plus mr-1"></i> Add New Rate
        </button>
    </div>
    
    <?php if (empty($rates)): ?>
        <div class="bg-gray-100 p-4 rounded-lg text-gray-700">
            No parking rates found. Click "Add New Rate" to create your first rate.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Space Type</th>
                        <th class="px-4 py-2 border">Vehicle Type</th>
                        <th class="px-4 py-2 border">Hourly Rate</th>
                        <th class="px-4 py-2 border">Day Rate (Optional)</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rates as $rate): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $rate->space_type_name ?></td>
                            <td class="px-4 py-2 border"><?= $rate->vehicle_type_name ?></td>
                            <td class="px-4 py-2 border"><?= $settings->currency_symbol ?? '$' ?><?= number_format($rate->hourly_rate, 2) ?></td>
                            <td class="px-4 py-2 border">
                                <?= $rate->day_rate ? ($settings->currency_symbol ?? '$') . number_format($rate->day_rate, 2) : 'N/A' ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <div class="flex space-x-1">
                                    <button class="text-blue-600 hover:text-blue-800 edit-rate-btn" data-id="<?= $rate->id ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 ml-2 delete-rate-btn" data-id="<?= $rate->id ?>" data-space="<?= $rate->space_type_name ?>" data-vehicle="<?= $rate->vehicle_type_name ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <!-- Add/Edit Rate Modal -->
    <div id="rate-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 id="rate-modal-title" class="text-xl font-bold mb-4">Add New Rate</h2>
            
            <form id="rate-form" action="<?= URL_ROOT ?>/admin/saveRate" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" id="rate-id" name="rate_id" value="">
                
                <div class="space-y-4">
                    <div>
                        <label for="space-type-id" class="block text-gray-700 mb-1 font-medium">Space Type</label>
                        <select id="space-type-id" name="space_type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                            <option value="">Select Space Type</option>
                            <?php foreach ($spaceTypes as $type): ?>
                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vehicle-type-id" class="block text-gray-700 mb-1 font-medium">Vehicle Type</label>
                        <select id="vehicle-type-id" name="vehicle_type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                            <option value="">Select Vehicle Type</option>
                            <?php foreach ($vehicleTypes as $type): ?>
                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="hourly-rate" class="block text-gray-700 mb-1 font-medium">Hourly Rate</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                <?= $settings->currency_symbol ?? '$' ?>
                            </span>
                            <input type="number" id="hourly-rate" name="hourly_rate" step="0.01" min="0" class="flex-1 px-4 py-2 border rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="day-rate" class="block text-gray-700 mb-1 font-medium">Day Rate (Optional)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                <?= $settings->currency_symbol ?? '$' ?>
                            </span>
                            <input type="number" id="day-rate" name="day_rate" step="0.01" min="0" class="flex-1 px-4 py-2 border rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        </div>
                        <p class="text-gray-500 text-xs mt-1">Maximum charge for a 24-hour period. Leave empty to use hourly rate only.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" id="rate-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Rate Confirmation Modal -->
    <div id="delete-rate-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
            <p id="delete-rate-message" class="mb-6"></p>
            
            <div class="flex justify-end space-x-2">
                <button id="delete-rate-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                <a id="delete-rate-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Rate modal functionality
        const rateModal = document.getElementById('rate-modal');
        const rateModalTitle = document.getElementById('rate-modal-title');
        const rateForm = document.getElementById('rate-form');
        const rateId = document.getElementById('rate-id');
        const spaceTypeId = document.getElementById('space-type-id');
        const vehicleTypeId = document.getElementById('vehicle-type-id');
        const hourlyRate = document.getElementById('hourly-rate');
        const dayRate = document.getElementById('day-rate');
        const addRateBtn = document.getElementById('add-rate-btn');
        const rateCancel = document.getElementById('rate-cancel');
        const editRateBtns = document.querySelectorAll('.edit-rate-btn');
        
        // Add new rate
        if (addRateBtn && rateModal) {
            addRateBtn.addEventListener('click', function() {
                rateModalTitle.textContent = 'Add New Rate';
                rateId.value = '';
                rateForm.reset();
                rateModal.classList.remove('hidden');
            });
        }
        
        // Cancel button
        if (rateCancel && rateModal) {
            rateCancel.addEventListener('click', function() {
                rateModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            rateModal.addEventListener('click', function(e) {
                if (e.target === rateModal) {
                    rateModal.classList.add('hidden');
                }
            });
        }
        
        // Edit rate (Note: In a real implementation, you would fetch the rate data via AJAX)
        editRateBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                rateModalTitle.textContent = 'Edit Rate';
                rateId.value = id;
                
                // In a real implementation, you would populate these fields with actual data
                // For demonstration purposes, we're using placeholder logic
                fetch(`<?= URL_ROOT ?>/admin/getRateData/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        spaceTypeId.value = data.space_type_id;
                        vehicleTypeId.value = data.vehicle_type_id;
                        hourlyRate.value = data.hourly_rate;
                        dayRate.value = data.day_rate || '';
                        rateModal.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching rate data:', error);
                    });
            });
        });
        
        // Delete rate functionality
        const deleteRateModal = document.getElementById('delete-rate-modal');
        const deleteRateMessage = document.getElementById('delete-rate-message');
        const deleteRateConfirm = document.getElementById('delete-rate-confirm');
        const deleteRateCancel = document.getElementById('delete-rate-cancel');
        const deleteRateBtns = document.querySelectorAll('.delete-rate-btn');
        
        // Show delete confirmation
        deleteRateBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const space = this.getAttribute('data-space');
                const vehicle = this.getAttribute('data-vehicle');
                
                deleteRateMessage.textContent = `Are you sure you want to delete the rate for ${space} - ${vehicle}? This action cannot be undone.`;
                deleteRateConfirm.href = `<?= URL_ROOT ?>/admin/deleteRate/${id}`;
                deleteRateModal.classList.remove('hidden');
            });
        });
        
        // Cancel delete
        if (deleteRateCancel && deleteRateModal) {
            deleteRateCancel.addEventListener('click', function() {
                deleteRateModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            deleteRateModal.addEventListener('click', function(e) {
                if (e.target === deleteRateModal) {
                    deleteRateModal.classList.add('hidden');
                }
            });
        }
    });
</script>
