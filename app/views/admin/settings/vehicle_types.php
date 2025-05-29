<!-- Vehicle Types Section -->
<div id="vehicle-types" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Vehicle Types</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Manage the types of vehicles allowed in your parking facility. Each vehicle type can have different rates for different space types.
                </p>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-700">Current Vehicle Types</h3>
        <button id="add-vehicle-type-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition text-sm">
            <i class="fas fa-plus mr-1"></i> Add New Type
        </button>
    </div>
    
    <?php if (empty($vehicleTypes)): ?>
        <div class="bg-gray-100 p-4 rounded-lg text-gray-700">
            No vehicle types found. Click "Add New Type" to create your first vehicle type.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Description</th>
                        <th class="px-4 py-2 border">Icon</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicleTypes as $type): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $type->name ?></td>
                            <td class="px-4 py-2 border"><?= $type->description ?></td>
                            <td class="px-4 py-2 border">
                                <i class="<?= $type->icon ?? 'fas fa-car' ?> text-xl"></i>
                            </td>
                            <td class="px-4 py-2 border">
                                <?php if ($type->status === 'active'): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <div class="flex space-x-1">
                                    <button class="text-blue-600 hover:text-blue-800 edit-vehicle-type-btn" data-id="<?= $type->id ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($type->id > 1): ?>
                                        <button class="text-red-600 hover:text-red-800 ml-2 delete-vehicle-type-btn" data-id="<?= $type->id ?>" data-name="<?= $type->name ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <!-- Add/Edit Vehicle Type Modal -->
    <div id="vehicle-type-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 id="vehicle-type-modal-title" class="text-xl font-bold mb-4">Add New Vehicle Type</h2>
            
            <form id="vehicle-type-form" action="<?= URL_ROOT ?>/admin/saveVehicleType" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" id="vehicle-type-id" name="vehicle_type_id" value="">
                
                <div class="space-y-4">
                    <div>
                        <label for="vehicle-type-name" class="block text-gray-700 mb-1 font-medium">Name</label>
                        <input type="text" id="vehicle-type-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                    </div>
                    
                    <div>
                        <label for="vehicle-type-description" class="block text-gray-700 mb-1 font-medium">Description</label>
                        <textarea id="vehicle-type-description" name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"></textarea>
                    </div>
                    
                    <div>
                        <label for="vehicle-type-icon" class="block text-gray-700 mb-1 font-medium">Icon</label>
                        <select id="vehicle-type-icon" name="icon" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="fas fa-car">Car</option>
                            <option value="fas fa-truck">Truck</option>
                            <option value="fas fa-motorcycle">Motorcycle</option>
                            <option value="fas fa-bicycle">Bicycle</option>
                            <option value="fas fa-bus">Bus</option>
                            <option value="fas fa-shuttle-van">Van</option>
                            <option value="fas fa-truck-pickup">Pickup Truck</option>
                            <option value="fas fa-ambulance">Ambulance</option>
                            <option value="fas fa-taxi">Taxi</option>
                        </select>
                        <div class="mt-2 text-center">
                            <span id="selected-icon-preview" class="text-3xl">
                                <i class="fas fa-car"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="vehicle-type-status" class="block text-gray-700 mb-1 font-medium">Status</label>
                        <select id="vehicle-type-status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" id="vehicle-type-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Vehicle Type Confirmation Modal -->
    <div id="delete-vehicle-type-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
            <p id="delete-vehicle-type-message" class="mb-6"></p>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Warning: Deleting a vehicle type will also delete all associated rates and may affect existing vehicles of this type.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="delete-vehicle-type-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                <a id="delete-vehicle-type-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vehicle Type modal functionality
        const vehicleTypeModal = document.getElementById('vehicle-type-modal');
        const vehicleTypeModalTitle = document.getElementById('vehicle-type-modal-title');
        const vehicleTypeForm = document.getElementById('vehicle-type-form');
        const vehicleTypeId = document.getElementById('vehicle-type-id');
        const vehicleTypeName = document.getElementById('vehicle-type-name');
        const vehicleTypeDescription = document.getElementById('vehicle-type-description');
        const vehicleTypeIcon = document.getElementById('vehicle-type-icon');
        const vehicleTypeStatus = document.getElementById('vehicle-type-status');
        const addVehicleTypeBtn = document.getElementById('add-vehicle-type-btn');
        const vehicleTypeCancel = document.getElementById('vehicle-type-cancel');
        const editVehicleTypeBtns = document.querySelectorAll('.edit-vehicle-type-btn');
        const selectedIconPreview = document.getElementById('selected-icon-preview');
        
        // Update icon preview when selection changes
        if (vehicleTypeIcon && selectedIconPreview) {
            vehicleTypeIcon.addEventListener('change', function() {
                selectedIconPreview.innerHTML = `<i class="${this.value}"></i>`;
            });
        }
        
        // Add new vehicle type
        if (addVehicleTypeBtn && vehicleTypeModal) {
            addVehicleTypeBtn.addEventListener('click', function() {
                vehicleTypeModalTitle.textContent = 'Add New Vehicle Type';
                vehicleTypeId.value = '';
                vehicleTypeForm.reset();
                selectedIconPreview.innerHTML = '<i class="fas fa-car"></i>';
                vehicleTypeModal.classList.remove('hidden');
            });
        }
        
        // Cancel button
        if (vehicleTypeCancel && vehicleTypeModal) {
            vehicleTypeCancel.addEventListener('click', function() {
                vehicleTypeModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            vehicleTypeModal.addEventListener('click', function(e) {
                if (e.target === vehicleTypeModal) {
                    vehicleTypeModal.classList.add('hidden');
                }
            });
        }
        
        // Edit vehicle type
        editVehicleTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                vehicleTypeModalTitle.textContent = 'Edit Vehicle Type';
                vehicleTypeId.value = id;
                
                // In a real implementation, you would populate these fields with actual data
                fetch(`<?= URL_ROOT ?>/admin/getVehicleTypeData/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        vehicleTypeName.value = data.name;
                        vehicleTypeDescription.value = data.description;
                        vehicleTypeIcon.value = data.icon;
                        selectedIconPreview.innerHTML = `<i class="${data.icon}"></i>`;
                        vehicleTypeStatus.value = data.status;
                        vehicleTypeModal.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching vehicle type data:', error);
                    });
            });
        });
        
        // Delete vehicle type functionality
        const deleteVehicleTypeModal = document.getElementById('delete-vehicle-type-modal');
        const deleteVehicleTypeMessage = document.getElementById('delete-vehicle-type-message');
        const deleteVehicleTypeConfirm = document.getElementById('delete-vehicle-type-confirm');
        const deleteVehicleTypeCancel = document.getElementById('delete-vehicle-type-cancel');
        const deleteVehicleTypeBtns = document.querySelectorAll('.delete-vehicle-type-btn');
        
        // Show delete confirmation
        deleteVehicleTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                deleteVehicleTypeMessage.textContent = `Are you sure you want to delete the vehicle type "${name}"? This action cannot be undone.`;
                deleteVehicleTypeConfirm.href = `<?= URL_ROOT ?>/admin/deleteVehicleType/${id}`;
                deleteVehicleTypeModal.classList.remove('hidden');
            });
        });
        
        // Cancel delete
        if (deleteVehicleTypeCancel && deleteVehicleTypeModal) {
            deleteVehicleTypeCancel.addEventListener('click', function() {
                deleteVehicleTypeModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            deleteVehicleTypeModal.addEventListener('click', function(e) {
                if (e.target === deleteVehicleTypeModal) {
                    deleteVehicleTypeModal.classList.add('hidden');
                }
            });
        }
    });
</script>
