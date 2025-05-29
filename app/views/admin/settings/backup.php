<!-- System Backup Section -->
<div id="system-backup" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Backup & Restore</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Regularly backup your database to prevent data loss. You can schedule automatic backups or create manual backups as needed.
                </p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Manual Backup Section -->
        <div class="bg-white p-5 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Manual Backup</h3>
            
            <p class="text-gray-600 mb-4">Create a backup of your database and download it to your computer.</p>
            
            <form action="<?= URL_ROOT ?>/admin/createBackup" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="include_data" value="1" checked class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Include database data</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="include_structure" value="1" checked class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Include database structure</span>
                        </label>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-download mr-2"></i> Create & Download Backup
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Restore Backup Section -->
        <div class="bg-white p-5 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Restore Backup</h3>
            
            <p class="text-gray-600 mb-4">Restore your database from a previously created backup file.</p>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Warning:</strong> Restoring a backup will overwrite your current database. This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="<?= URL_ROOT ?>/admin/restoreBackup" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="backup_file" class="block text-gray-700 mb-1 font-medium">Backup File</label>
                        <input type="file" id="backup_file" name="backup_file" accept=".sql,.gz" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                        <p class="text-gray-500 text-xs mt-1">Select a .sql or .gz backup file</p>
                    </div>
                    
                    <div>
                        <button type="button" id="restore-btn" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-upload mr-2"></i> Restore Backup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Automated Backup Settings -->
    <div class="mt-6 bg-white p-5 rounded-lg border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Automated Backup Settings</h3>
        
        <form action="<?= URL_ROOT ?>/admin/updateBackupSettings" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_backup_enabled" value="1" <?= isset($backupSettings->auto_backup_enabled) && $backupSettings->auto_backup_enabled ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700">Enable automated backups</span>
                    </label>
                </div>
                
                <div>
                    <label for="backup_frequency" class="block text-gray-700 mb-1 font-medium">Backup Frequency</label>
                    <select id="backup_frequency" name="backup_frequency" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="daily" <?= isset($backupSettings->backup_frequency) && $backupSettings->backup_frequency === 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= isset($backupSettings->backup_frequency) && $backupSettings->backup_frequency === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= isset($backupSettings->backup_frequency) && $backupSettings->backup_frequency === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    </select>
                </div>
                
                <div>
                    <label for="max_backups" class="block text-gray-700 mb-1 font-medium">Maximum Backups to Keep</label>
                    <input type="number" id="max_backups" name="max_backups" min="1" max="50" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $backupSettings->max_backups ?? 5 ?>">
                    <p class="text-gray-500 text-xs mt-1">Older backups will be automatically deleted</p>
                </div>
                
                <div>
                    <label for="backup_time" class="block text-gray-700 mb-1 font-medium">Backup Time</label>
                    <input type="time" id="backup_time" name="backup_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $backupSettings->backup_time ?? '03:00' ?>">
                    <p class="text-gray-500 text-xs mt-1">Time of day to run automated backups (server time)</p>
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i> Save Backup Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Recent Backups -->
    <div class="mt-6 bg-white p-5 rounded-lg border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Backups</h3>
        
        <?php if (empty($recentBackups)): ?>
            <p class="text-gray-700">No backups found. Create your first backup using the options above.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">Filename</th>
                            <th class="px-4 py-2 border">Date Created</th>
                            <th class="px-4 py-2 border">Size</th>
                            <th class="px-4 py-2 border">Type</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBackups as $backup): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?= $backup->filename ?></td>
                                <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($backup->created_at)) ?></td>
                                <td class="px-4 py-2 border"><?= $backup->size_formatted ?></td>
                                <td class="px-4 py-2 border">
                                    <?php if ($backup->type === 'manual'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Manual</span>
                                    <?php else: ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Automated</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <div class="flex space-x-2">
                                        <a href="<?= URL_ROOT ?>/admin/downloadBackup/<?= $backup->id ?>" class="text-blue-600 hover:text-blue-800" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="text-red-600 hover:text-red-800 delete-backup-btn" data-id="<?= $backup->id ?>" data-filename="<?= $backup->filename ?>" title="Delete">
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
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restore-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">Confirm Restore</h2>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Warning:</strong> This action will replace all current data with the data from the backup file. This cannot be undone.
                    </p>
                </div>
            </div>
        </div>
        <p class="mb-6">Are you absolutely sure you want to restore this backup?</p>
        
        <div class="flex justify-end space-x-2">
            <button id="restore-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
            <button id="restore-confirm" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Yes, Restore</button>
        </div>
    </div>
</div>

<!-- Delete Backup Confirmation Modal -->
<div id="delete-backup-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
        <p id="delete-backup-message" class="mb-6"></p>
        
        <div class="flex justify-end space-x-2">
            <button id="delete-backup-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
            <a id="delete-backup-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Restore confirmation modal
        const restoreBtn = document.getElementById('restore-btn');
        const restoreForm = document.querySelector('form[action*="restoreBackup"]');
        const restoreConfirmationModal = document.getElementById('restore-confirmation-modal');
        const restoreCancel = document.getElementById('restore-cancel');
        const restoreConfirm = document.getElementById('restore-confirm');
        
        if (restoreBtn && restoreConfirmationModal) {
            restoreBtn.addEventListener('click', function() {
                const backupFile = document.getElementById('backup_file').value;
                if (backupFile) {
                    restoreConfirmationModal.classList.remove('hidden');
                } else {
                    alert('Please select a backup file to restore.');
                }
            });
        }
        
        if (restoreCancel && restoreConfirmationModal) {
            restoreCancel.addEventListener('click', function() {
                restoreConfirmationModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            restoreConfirmationModal.addEventListener('click', function(e) {
                if (e.target === restoreConfirmationModal) {
                    restoreConfirmationModal.classList.add('hidden');
                }
            });
        }
        
        if (restoreConfirm && restoreForm) {
            restoreConfirm.addEventListener('click', function() {
                restoreForm.submit();
            });
        }
        
        // Delete backup functionality
        const deleteBackupModal = document.getElementById('delete-backup-modal');
        const deleteBackupMessage = document.getElementById('delete-backup-message');
        const deleteBackupConfirm = document.getElementById('delete-backup-confirm');
        const deleteBackupCancel = document.getElementById('delete-backup-cancel');
        const deleteBackupBtns = document.querySelectorAll('.delete-backup-btn');
        
        // Show delete confirmation
        deleteBackupBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const filename = this.getAttribute('data-filename');
                
                deleteBackupMessage.textContent = `Are you sure you want to delete the backup "${filename}"? This action cannot be undone.`;
                deleteBackupConfirm.href = `<?= URL_ROOT ?>/admin/deleteBackup/${id}`;
                deleteBackupModal.classList.remove('hidden');
            });
        });
        
        // Cancel delete
        if (deleteBackupCancel && deleteBackupModal) {
            deleteBackupCancel.addEventListener('click', function() {
                deleteBackupModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            deleteBackupModal.addEventListener('click', function(e) {
                if (e.target === deleteBackupModal) {
                    deleteBackupModal.classList.add('hidden');
                }
            });
        }
    });
</script>
