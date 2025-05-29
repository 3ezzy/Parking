<!-- Space Types Section -->
<div id="space-types" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Types</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Manage the types of parking spaces available in your facility. Each space type can have different rates.
                </p>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-700">Current Space Types</h3>
        <button id="add-space-type-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition text-sm">
            <i class="fas fa-plus mr-1"></i> Add New Type
        </button>
    </div>
    
    <?php if (empty($spaceTypes)): ?>
        <div class="bg-gray-100 p-4 rounded-lg text-gray-700">
            No space types found. Click "Add New Type" to create your first space type.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Description</th>
                        <th class="px-4 py-2 border">Display Color</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($spaceTypes as $type): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $type->name ?></td>
                            <td class="px-4 py-2 border"><?= $type->description ?></td>
                            <td class="px-4 py-2 border">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full mr-2" style="background-color: <?= $type->display_color ?>"></div>
                                    <?= $type->display_color ?>
                                </div>
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
                                    <button class="text-blue-600 hover:text-blue-800 edit-space-type-btn" data-id="<?= $type->id ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($type->id > 1): ?>
                                        <button class="text-red-600 hover:text-red-800 ml-2 delete-space-type-btn" data-id="<?= $type->id ?>" data-name="<?= $type->name ?>">
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
    
    <!-- Add/Edit Space Type Modal -->
    <div id="space-type-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 id="space-type-modal-title" class="text-xl font-bold mb-4">Add New Space Type</h2>
            
            <form id="space-type-form" action="<?= URL_ROOT ?>/admin/saveSpaceType" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" id="space-type-id" name="space_type_id" value="">
                
                <div class="space-y-4">
                    <div>
                        <label for="space-type-name" class="block text-gray-700 mb-1 font-medium">Name</label>
                        <input type="text" id="space-type-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                    </div>
                    
                    <div>
                        <label for="space-type-description" class="block text-gray-700 mb-1 font-medium">Description</label>
                        <textarea id="space-type-description" name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"></textarea>
                    </div>
                    
                    <div>
                        <label for="space-type-color" class="block text-gray-700 mb-1 font-medium">Display Color</label>
                        <input type="color" id="space-type-color" name="display_color" class="w-full h-10 px-1 py-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="#3B82F6">
                    </div>
                    
                    <div>
                        <label for="space-type-status" class="block text-gray-700 mb-1 font-medium">Status</label>
                        <select id="space-type-status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" id="space-type-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Space Type Confirmation Modal -->
    <div id="delete-space-type-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
            <p id="delete-space-type-message" class="mb-6"></p>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Warning: Deleting a space type will also delete all associated rates and may affect existing spaces of this type.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="delete-space-type-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                <a id="delete-space-type-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Space Type modal functionality
        const spaceTypeModal = document.getElementById('space-type-modal');
        const spaceTypeModalTitle = document.getElementById('space-type-modal-title');
        const spaceTypeForm = document.getElementById('space-type-form');
        const spaceTypeId = document.getElementById('space-type-id');
        const spaceTypeName = document.getElementById('space-type-name');
        const spaceTypeDescription = document.getElementById('space-type-description');
        const spaceTypeColor = document.getElementById('space-type-color');
        const spaceTypeStatus = document.getElementById('space-type-status');
        const addSpaceTypeBtn = document.getElementById('add-space-type-btn');
        const spaceTypeCancel = document.getElementById('space-type-cancel');
        const editSpaceTypeBtns = document.querySelectorAll('.edit-space-type-btn');
        
        // Add new space type
        if (addSpaceTypeBtn && spaceTypeModal) {
            addSpaceTypeBtn.addEventListener('click', function() {
                spaceTypeModalTitle.textContent = 'Add New Space Type';
                spaceTypeId.value = '';
                spaceTypeForm.reset();
                spaceTypeColor.value = '#3B82F6'; // Default blue
                spaceTypeModal.classList.remove('hidden');
            });
        }
        
        // Cancel button
        if (spaceTypeCancel && spaceTypeModal) {
            spaceTypeCancel.addEventListener('click', function() {
                spaceTypeModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            spaceTypeModal.addEventListener('click', function(e) {
                if (e.target === spaceTypeModal) {
                    spaceTypeModal.classList.add('hidden');
                }
            });
        }
        
        // Edit space type
        editSpaceTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                spaceTypeModalTitle.textContent = 'Edit Space Type';
                spaceTypeId.value = id;
                
                // In a real implementation, you would populate these fields with actual data
                fetch(`<?= URL_ROOT ?>/admin/getSpaceTypeData/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        spaceTypeName.value = data.name;
                        spaceTypeDescription.value = data.description;
                        spaceTypeColor.value = data.display_color;
                        spaceTypeStatus.value = data.status;
                        spaceTypeModal.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching space type data:', error);
                    });
            });
        });
        
        // Delete space type functionality
        const deleteSpaceTypeModal = document.getElementById('delete-space-type-modal');
        const deleteSpaceTypeMessage = document.getElementById('delete-space-type-message');
        const deleteSpaceTypeConfirm = document.getElementById('delete-space-type-confirm');
        const deleteSpaceTypeCancel = document.getElementById('delete-space-type-cancel');
        const deleteSpaceTypeBtns = document.querySelectorAll('.delete-space-type-btn');
        
        // Show delete confirmation
        deleteSpaceTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                deleteSpaceTypeMessage.textContent = `Are you sure you want to delete the space type "${name}"? This action cannot be undone.`;
                deleteSpaceTypeConfirm.href = `<?= URL_ROOT ?>/admin/deleteSpaceType/${id}`;
                deleteSpaceTypeModal.classList.remove('hidden');
            });
        });
        
        // Cancel delete
        if (deleteSpaceTypeCancel && deleteSpaceTypeModal) {
            deleteSpaceTypeCancel.addEventListener('click', function() {
                deleteSpaceTypeModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            deleteSpaceTypeModal.addEventListener('click', function(e) {
                if (e.target === deleteSpaceTypeModal) {
                    deleteSpaceTypeModal.classList.add('hidden');
                }
            });
        }
    });
</script>
