<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('vehicle_success'); ?>
    <?php flash('vehicle_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-blue-800">Registered Vehicles</h2>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition" onclick="document.getElementById('add-vehicle-form').classList.toggle('hidden')">
                        <i class="fas fa-plus mr-2"></i> Add New Vehicle
                    </button>
                </div>
                
                <div id="add-vehicle-form" class="hidden bg-white p-4 rounded-lg border border-gray-200 mb-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Add New Vehicle</h3>
                    
                    <form action="<?= URL_ROOT ?>/admin/vehicles" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="add_vehicle" value="1">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate *</label>
                                <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['license_plate']) ? 'border-red-500' : 'border-gray-300' ?>" required>
                                <?php if (isset($errors['license_plate'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['license_plate'] ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label for="type_id" class="block text-gray-700 mb-1 font-medium">Vehicle Type *</label>
                                <select id="type_id" name="type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                                    <?php foreach ($vehicleTypes as $type): ?>
                                        <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
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
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="document.getElementById('add-vehicle-form').classList.add('hidden')">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                Add Vehicle
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">License Plate</th>
                                <th class="px-4 py-2 border">Vehicle Type</th>
                                <th class="px-4 py-2 border">Owner Name</th>
                                <th class="px-4 py-2 border">Owner Phone</th>
                                <th class="px-4 py-2 border">Registration Date</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vehicles)): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-2 border text-center text-gray-500">No vehicles found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border"><?= $vehicle->license_plate ?></td>
                                        <td class="px-4 py-2 border"><?= $vehicle->type_name ?></td>
                                        <td class="px-4 py-2 border"><?= $vehicle->owner_name ?: '-' ?></td>
                                        <td class="px-4 py-2 border"><?= $vehicle->owner_phone ?: '-' ?></td>
                                        <td class="px-4 py-2 border"><?= date('M d, Y', strtotime($vehicle->created_at)) ?></td>
                                        <td class="px-4 py-2 border">
                                            <button type="button" class="text-blue-600 hover:text-blue-800 mr-2" onclick="showEditModal(<?= $vehicle->id ?>, '<?= $vehicle->license_plate ?>', <?= $vehicle->type_id ?>, '<?= $vehicle->owner_name ?>', '<?= $vehicle->owner_phone ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="text-red-600 hover:text-red-800" onclick="showDeleteModal(<?= $vehicle->id ?>, '<?= $vehicle->license_plate ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Vehicle Statistics</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-600">Total Vehicles</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-3xl font-bold text-blue-600"><?= count($vehicles) ?></span>
                            <i class="fas fa-car text-2xl text-blue-400"></i>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <h3 class="font-semibold text-gray-600">By Type</h3>
                        <div class="space-y-2 mt-2">
                            <?php
                                $typeCounts = [];
                                
                                foreach ($vehicles as $vehicle) {
                                    if (!isset($typeCounts[$vehicle->type_name])) {
                                        $typeCounts[$vehicle->type_name] = 0;
                                    }
                                    
                                    $typeCounts[$vehicle->type_name]++;
                                }
                                
                                $total = count($vehicles);
                            ?>
                            
                            <?php foreach ($typeCounts as $typeName => $count): ?>
                                <div class="flex justify-between items-center">
                                    <span><?= $typeName ?></span>
                                    <div class="flex items-center">
                                        <span class="font-semibold"><?= $count ?></span>
                                        <span class="text-gray-500 text-sm ml-1">(<?= $total > 0 ? round(($count / $total) * 100) : 0 ?>%)</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg mt-4">
                        <h3 class="font-semibold text-gray-600 mb-2">Vehicle Types Information</h3>
                        
                        <div class="space-y-3">
                            <?php foreach ($vehicleTypes as $type): ?>
                                <div class="border-b border-gray-200 pb-2">
                                    <div class="font-medium"><?= $type->name ?></div>
                                    <div class="text-sm text-gray-600"><?= $type->description ?: 'No description available' ?></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php if ($type->requires_special_space): ?>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Requires special space</span>
                                        <?php else: ?>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Standard space compatible</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div id="edit-vehicle-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-blue-800">Edit Vehicle</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeModal('edit-vehicle-modal')">
                    <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                </button>
            </div>
            
            <form action="<?= URL_ROOT ?>/admin/vehicles" method="POST" class="space-y-4 pt-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="update_vehicle" value="1">
                <input type="hidden" id="edit_vehicle_id" name="vehicle_id" value="">
                
                <div>
                    <label for="edit_license_plate" class="block text-gray-700 mb-1 font-medium">License Plate</label>
                    <input type="text" id="edit_license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" disabled>
                </div>
                
                <div>
                    <label for="edit_type_id" class="block text-gray-700 mb-1 font-medium">Vehicle Type *</label>
                    <select id="edit_type_id" name="type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                        <?php foreach ($vehicleTypes as $type): ?>
                            <option value="<?= $type->id ?>"><?= $type->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="edit_owner_name" class="block text-gray-700 mb-1 font-medium">Owner Name</label>
                    <input type="text" id="edit_owner_name" name="owner_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
                
                <div>
                    <label for="edit_owner_phone" class="block text-gray-700 mb-1 font-medium">Owner Phone</label>
                    <input type="text" id="edit_owner_phone" name="owner_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="closeModal('edit-vehicle-modal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        Update Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Vehicle Modal -->
<div id="delete-vehicle-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-red-800">Delete Vehicle</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeModal('delete-vehicle-modal')">
                    <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                </button>
            </div>
            
            <div class="py-4">
                <p class="text-gray-700">Are you sure you want to delete the vehicle with license plate <span id="delete_license_plate" class="font-semibold"></span>?</p>
                <p class="text-red-600 text-sm mt-2">This action cannot be undone. The vehicle will be permanently removed from the system.</p>
                <p class="text-gray-600 text-sm mt-2">Note: You cannot delete a vehicle that is currently parked or has an active reservation.</p>
            </div>
            
            <form action="<?= URL_ROOT ?>/admin/vehicles" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="delete_vehicle" value="1">
                <input type="hidden" id="delete_vehicle_id" name="vehicle_id" value="">
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="closeModal('delete-vehicle-modal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                        Delete Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to show edit modal
    function showEditModal(id, licensePlate, typeId, ownerName, ownerPhone) {
        document.getElementById('edit_vehicle_id').value = id;
        document.getElementById('edit_license_plate').value = licensePlate;
        document.getElementById('edit_type_id').value = typeId;
        document.getElementById('edit_owner_name').value = ownerName;
        document.getElementById('edit_owner_phone').value = ownerPhone;
        
        document.getElementById('edit-vehicle-modal').classList.remove('hidden');
    }
    
    // Function to show delete modal
    function showDeleteModal(id, licensePlate) {
        document.getElementById('delete_vehicle_id').value = id;
        document.getElementById('delete_license_plate').textContent = licensePlate;
        
        document.getElementById('delete-vehicle-modal').classList.remove('hidden');
    }
    
    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeModal('edit-vehicle-modal');
            closeModal('delete-vehicle-modal');
        }
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
