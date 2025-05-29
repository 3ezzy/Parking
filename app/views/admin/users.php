<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <div class="flex space-x-2">
            <button id="add-user-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Add New User
            </button>
            <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php flash('user_success'); ?>
    <?php flash('user_error'); ?>
    
    <div id="add-user-form" class="bg-gray-50 p-6 rounded-lg shadow-sm mb-6 <?= isset($errors) ? '' : 'hidden' ?>">
        <h2 class="text-xl font-semibold text-blue-800 mb-4">
            <?= isset($user) && isset($user->id) ? 'Edit User' : 'Add New User' ?>
        </h2>
        
        <form action="<?= URL_ROOT ?>/admin/<?= isset($user) && isset($user->id) ? 'editUser/' . $user->id : 'users' ?>" method="POST">
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-gray-700 mb-1 font-medium">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $user->name ?? '' ?>">
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 mb-1 font-medium">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $user->email ?? '' ?>">
                </div>
                
                <div>
                    <label for="role" class="block text-gray-700 mb-1 font-medium">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['role']) ? 'border-red-500' : 'border-gray-300' ?>">
                        <option value="">Select Role</option>
                        <option value="admin" <?= (isset($user->role) && $user->role === 'admin') ? 'selected' : '' ?>>Administrator</option>
                        <option value="agent" <?= (isset($user->role) && $user->role === 'agent') ? 'selected' : '' ?>>Parking Agent</option>
                    </select>
                </div>
                
                <div>
                    <label for="phone" class="block text-gray-700 mb-1 font-medium">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $user->phone ?? '' ?>">
                </div>
                
                <?php if (!isset($user) || !isset($user->id)): ?>
                    <div>
                        <label for="password" class="block text-gray-700 mb-1 font-medium">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?>">
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-gray-700 mb-1 font-medium">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?>">
                    </div>
                <?php else: ?>
                    <div class="md:col-span-2">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Password can only be reset, not viewed. To reset the user's password, use the "Reset Password" button on the user list.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label for="status" class="block text-gray-700 mb-1 font-medium">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="active" <?= (isset($user->status) && $user->status === 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($user->status) && $user->status === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" id="cancel-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <?= isset($user) && isset($user->id) ? 'Update User' : 'Add User' ?>
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-800">User Accounts</h2>
            
            <div class="relative">
                <input type="text" id="search-user" placeholder="Search users..." class="w-64 px-4 py-2 pr-8 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                <div class="absolute right-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
        
        <?php if (empty($users)): ?>
            <p class="text-gray-700">No users found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Email</th>
                            <th class="px-4 py-2 border">Role</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Created</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row hover:bg-gray-50" data-search="<?= strtolower($user->name . ' ' . $user->email . ' ' . $user->role) ?>">
                                <td class="px-4 py-2 border"><?= $user->id ?></td>
                                <td class="px-4 py-2 border"><?= $user->name ?></td>
                                <td class="px-4 py-2 border"><?= $user->email ?></td>
                                <td class="px-4 py-2 border">
                                    <?php if ($user->role === 'admin'): ?>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Administrator</span>
                                    <?php else: ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Parking Agent</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if ($user->status === 'active'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border"><?= date('M d, Y', strtotime($user->created_at)) ?></td>
                                <td class="px-4 py-2 border">
                                    <div class="flex space-x-1">
                                        <a href="<?= URL_ROOT ?>/admin/editUser/<?= $user->id ?>" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <?php if ($_SESSION['user_id'] != $user->id): ?>
                                            <a href="<?= URL_ROOT ?>/admin/resetPassword/<?= $user->id ?>" class="text-yellow-600 hover:text-yellow-800 ml-2" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            
                                            <button 
                                                class="text-red-600 hover:text-red-800 ml-2"
                                                onclick="confirmDelete(<?= $user->id ?>, '<?= $user->name ?>')"
                                                title="Delete User"
                                                <?= $user->id === 1 ? 'disabled' : '' ?>
                                            >
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
            
            <div class="mt-4 flex justify-between items-center">
                <div class="text-gray-600 text-sm">
                    Total: <?= count($users) ?> users
                </div>
                
                <div>
                    <?php
                        $prevOffset = max(0, $offset - $limit);
                        $nextOffset = $offset + $limit;
                    ?>
                    
                    <div class="flex space-x-2">
                        <?php if ($offset > 0): ?>
                            <a href="<?= URL_ROOT ?>/admin/users?offset=<?= $prevOffset ?>&limit=<?= $limit ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php if (count($users) >= $limit): ?>
                            <a href="<?= URL_ROOT ?>/admin/users?offset=<?= $nextOffset ?>&limit=<?= $limit ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete User Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
        <p id="delete-message" class="mb-6"></p>
        
        <div class="flex justify-end space-x-2">
            <button id="delete-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
            <a id="delete-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle add user form
        const addUserBtn = document.getElementById('add-user-btn');
        const addUserForm = document.getElementById('add-user-form');
        const cancelBtn = document.getElementById('cancel-btn');
        
        if (addUserBtn && addUserForm && cancelBtn) {
            addUserBtn.addEventListener('click', function() {
                addUserForm.classList.remove('hidden');
            });
            
            cancelBtn.addEventListener('click', function() {
                addUserForm.classList.add('hidden');
            });
        }
        
        // Search functionality
        const searchInput = document.getElementById('search-user');
        const userRows = document.querySelectorAll('.user-row');
        
        if (searchInput && userRows.length > 0) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();
                
                userRows.forEach(row => {
                    const searchText = row.getAttribute('data-search').toLowerCase();
                    if (searchText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const deleteMessage = document.getElementById('delete-message');
        const deleteConfirm = document.getElementById('delete-confirm');
        const deleteCancel = document.getElementById('delete-cancel');
        
        if (deleteModal && deleteCancel) {
            deleteCancel.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    deleteModal.classList.add('hidden');
                }
            });
        }
    });
    
    function confirmDelete(userId, userName) {
        const deleteModal = document.getElementById('delete-modal');
        const deleteMessage = document.getElementById('delete-message');
        const deleteConfirm = document.getElementById('delete-confirm');
        
        if (deleteModal && deleteMessage && deleteConfirm) {
            deleteMessage.textContent = `Are you sure you want to delete user "${userName}"? This action cannot be undone.`;
            deleteConfirm.href = `<?= URL_ROOT ?>/admin/deleteUser/${userId}`;
            deleteModal.classList.remove('hidden');
        }
    }
</script>

<?php require APP . 'views/includes/footer.php'; ?>
