<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('space_success'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-blue-800">Parking Spaces</h2>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition" onclick="document.getElementById('add-space-form').classList.toggle('hidden')">
                        <i class="fas fa-plus mr-2"></i> Add New Space
                    </button>
                </div>
                
                <div id="add-space-form" class="hidden bg-white p-4 rounded-lg border border-gray-200 mb-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Add New Parking Space</h3>
                    
                    <form action="<?= URL_ROOT ?>/admin/spaces" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="add_space" value="1">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="space_number" class="block text-gray-700 mb-1 font-medium">Space Number *</label>
                                <input type="text" id="space_number" name="space_number" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['space_number']) ? 'border-red-500' : 'border-gray-300' ?>" required>
                                <?php if (isset($errors['space_number'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['space_number'] ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label for="type_id" class="block text-gray-700 mb-1 font-medium">Space Type *</label>
                                <select id="type_id" name="type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                                    <?php foreach ($spaceTypes as $type): ?>
                                        <option value="<?= $type->id ?>"><?= $type->name ?> ($<?= number_format($type->hourly_rate, 2) ?>/hr)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="floor" class="block text-gray-700 mb-1 font-medium">Floor</label>
                                <input type="number" id="floor" name="floor" min="1" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="1">
                            </div>
                            
                            <div>
                                <label for="zone" class="block text-gray-700 mb-1 font-medium">Zone</label>
                                <input type="text" id="zone" name="zone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="document.getElementById('add-space-form').classList.add('hidden')">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                Add Space
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Space #</th>
                                <th class="px-4 py-2 border">Type</th>
                                <th class="px-4 py-2 border">Rate</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Floor</th>
                                <th class="px-4 py-2 border">Zone</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($spaces)): ?>
                                <tr>
                                    <td colspan="7" class="px-4 py-2 border text-center text-gray-500">No parking spaces found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($spaces as $space): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border"><?= $space->space_number ?></td>
                                        <td class="px-4 py-2 border"><?= $space->type_name ?></td>
                                        <td class="px-4 py-2 border">$<?= number_format($space->hourly_rate, 2) ?></td>
                                        <td class="px-4 py-2 border">
                                            <?php if ($space->status === 'available'): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Available</span>
                                            <?php elseif ($space->status === 'occupied'): ?>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Occupied</span>
                                            <?php elseif ($space->status === 'reserved'): ?>
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Reserved</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Maintenance</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 border"><?= $space->floor ?></td>
                                        <td class="px-4 py-2 border"><?= $space->zone ?: '-' ?></td>
                                        <td class="px-4 py-2 border">
                                            <button type="button" class="text-blue-600 hover:text-blue-800 mr-2" onclick="showEditModal(<?= $space->id ?>, '<?= $space->space_number ?>', <?= $space->type_id ?>, <?= $space->floor ?>, '<?= $space->zone ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="text-yellow-600 hover:text-yellow-800" onclick="showStatusModal(<?= $space->id ?>, '<?= $space->status ?>')">
                                                <i class="fas fa-sync-alt"></i>
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
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Statistics</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-600">Total Spaces</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-3xl font-bold text-blue-600"><?= count($spaces) ?></span>
                            <i class="fas fa-parking text-2xl text-blue-400"></i>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <h3 class="font-semibold text-gray-600">By Status</h3>
                        <div class="space-y-2 mt-2">
                            <?php
                                $available = 0;
                                $occupied = 0;
                                $reserved = 0;
                                $maintenance = 0;
                                
                                foreach ($spaces as $space) {
                                    switch ($space->status) {
                                        case 'available':
                                            $available++;
                                            break;
                                        case 'occupied':
                                            $occupied++;
                                            break;
                                        case 'reserved':
                                            $reserved++;
                                            break;
                                        case 'maintenance':
                                            $maintenance++;
                                            break;
                                    }
                                }
                                
                                $total = count($spaces);
                            ?>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-green-600">Available</span>
                                <div class="flex items-center">
                                    <span class="font-semibold"><?= $available ?></span>
                                    <span class="text-gray-500 text-sm ml-1">(<?= $total > 0 ? round(($available / $total) * 100) : 0 ?>%)</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-red-600">Occupied</span>
                                <div class="flex items-center">
                                    <span class="font-semibold"><?= $occupied ?></span>
                                    <span class="text-gray-500 text-sm ml-1">(<?= $total > 0 ? round(($occupied / $total) * 100) : 0 ?>%)</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-600">Reserved</span>
                                <div class="flex items-center">
                                    <span class="font-semibold"><?= $reserved ?></span>
                                    <span class="text-gray-500 text-sm ml-1">(<?= $total > 0 ? round(($reserved / $total) * 100) : 0 ?>%)</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Maintenance</span>
                                <div class="flex items-center">
                                    <span class="font-semibold"><?= $maintenance ?></span>
                                    <span class="text-gray-500 text-sm ml-1">(<?= $total > 0 ? round(($maintenance / $total) * 100) : 0 ?>%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <h3 class="font-semibold text-gray-600">By Type</h3>
                        <div class="space-y-2 mt-2">
                            <?php
                                $typeCounts = [];
                                
                                foreach ($spaces as $space) {
                                    if (!isset($typeCounts[$space->type_name])) {
                                        $typeCounts[$space->type_name] = 0;
                                    }
                                    
                                    $typeCounts[$space->type_name]++;
                                }
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Space Modal -->
<div id="edit-space-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-blue-800">Edit Parking Space</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeModal('edit-space-modal')">
                    <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                </button>
            </div>
            
            <form action="<?= URL_ROOT ?>/admin/spaces" method="POST" class="space-y-4 pt-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="update_space" value="1">
                <input type="hidden" id="edit_space_id" name="space_id" value="">
                
                <div>
                    <label for="edit_space_number" class="block text-gray-700 mb-1 font-medium">Space Number</label>
                    <input type="text" id="edit_space_number" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" disabled>
                </div>
                
                <div>
                    <label for="edit_type_id" class="block text-gray-700 mb-1 font-medium">Space Type *</label>
                    <select id="edit_type_id" name="type_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                        <?php foreach ($spaceTypes as $type): ?>
                            <option value="<?= $type->id ?>"><?= $type->name ?> ($<?= number_format($type->hourly_rate, 2) ?>/hr)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="edit_floor" class="block text-gray-700 mb-1 font-medium">Floor</label>
                    <input type="number" id="edit_floor" name="floor" min="1" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="1">
                </div>
                
                <div>
                    <label for="edit_zone" class="block text-gray-700 mb-1 font-medium">Zone</label>
                    <input type="text" id="edit_zone" name="zone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="closeModal('edit-space-modal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        Update Space
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div id="status-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-blue-800">Update Space Status</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeModal('status-modal')">
                    <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                </button>
            </div>
            
            <form action="<?= URL_ROOT ?>/admin/spaces" method="POST" class="space-y-4 pt-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" id="status_space_id" name="space_id" value="">
                
                <div>
                    <label for="status" class="block text-gray-700 mb-1 font-medium">Space Status *</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                        <option value="available">Available</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    <p class="text-red-500 text-sm mt-1">Note: You can only change to 'Available' or 'Maintenance'. Occupied and Reserved statuses are managed by the system.</p>
                </div>
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition" onclick="closeModal('status-modal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to show edit modal
    function showEditModal(id, spaceNumber, typeId, floor, zone) {
        document.getElementById('edit_space_id').value = id;
        document.getElementById('edit_space_number').value = spaceNumber;
        document.getElementById('edit_type_id').value = typeId;
        document.getElementById('edit_floor').value = floor;
        document.getElementById('edit_zone').value = zone;
        
        document.getElementById('edit-space-modal').classList.remove('hidden');
    }
    
    // Function to show status modal
    function showStatusModal(id, status) {
        document.getElementById('status_space_id').value = id;
        document.getElementById('status').value = status;
        
        document.getElementById('status-modal').classList.remove('hidden');
    }
    
    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeModal('edit-space-modal');
            closeModal('status-modal');
        }
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
