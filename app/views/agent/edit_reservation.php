<?php require APP . 'views/includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-800"><?= $title ?? 'Edit Reservation' ?></h1>
            <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $reservation_id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Cancel Edit
            </a>
        </div>

        <?php flash('reservation_success'); ?>
        <?php flash('reservation_error'); ?>
        <?php flash('info'); ?>

        <form action="<?= URL_ROOT ?>/agent/editReservation/<?= $reservation_id ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Customer Information -->
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">Customer Information</h2>
                    <div class="mb-4">
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="<?= htmlspecialchars($formData['customer_name'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['customer_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <?php if (!empty($errors['customer_name'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['customer_name'] ?></p><?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                        <input type="email" name="customer_email" id="customer_email" value="<?= htmlspecialchars($formData['customer_email'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['customer_email']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <?php if (!empty($errors['customer_email'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['customer_email'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="<?= htmlspecialchars($formData['customer_phone'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['customer_phone']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <?php if (!empty($errors['customer_phone'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['customer_phone'] ?></p><?php endif; ?>
                    </div>
                </div>

                <!-- Vehicle & Space Information -->
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">Vehicle & Space</h2>
                    <div class="mb-4">
                        <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-1">License Plate</label>
                        <input type="text" name="license_plate" id="license_plate" value="<?= htmlspecialchars($formData['license_plate'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['license_plate']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <?php if (!empty($errors['license_plate'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['license_plate'] ?></p><?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                        <select name="vehicle_type_id" id="vehicle_type_id" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['vehicle_type_id']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Vehicle Type (Optional)</option>
                            <?php if (!empty($vehicleTypes)): ?>
                                <?php foreach ($vehicleTypes as $type): ?>
                                    <option value="<?= $type->id ?>" <?= ($formData['vehicle_type_id'] ?? '') == $type->id ? 'selected' : '' ?>><?= htmlspecialchars($type->name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (!empty($errors['vehicle_type_id'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['vehicle_type_id'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="space_id" class="block text-sm font-medium text-gray-700 mb-1">Parking Space <span class="text-red-500">*</span></label>
                        <select name="space_id" id="space_id" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['space_id']) ? 'border-red-500' : 'border-gray-300' ?> bg-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" disabled>
                            <option value="">Select Parking Space</option>
                            <?php if (!empty($spaces)): ?>
                                <?php foreach ($spaces as $space): ?>
                                    <option value="<?= $space->id ?>" <?= ($formData['space_id'] ?? '') == $space->id ? 'selected' : '' ?>><?= htmlspecialchars($space->space_number . ' (' . $space->type_name . ')') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Space cannot be changed after reservation creation. To change space, cancel and create a new reservation.</p>
                        <?php if (!empty($errors['space_id'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['space_id'] ?></p><?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Reservation Schedule & Notes -->
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm mt-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Reservation Schedule & Notes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_time" id="start_time" value="<?= htmlspecialchars($formData['start_time'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['start_time']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <?php if (!empty($errors['start_time'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['start_time'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_time" id="end_time" value="<?= htmlspecialchars($formData['end_time'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['end_time']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <?php if (!empty($errors['end_time'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['end_time'] ?></p><?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($errors['time_conflict'])): ?><p class="text-red-500 text-xs mb-4"><?= $errors['time_conflict'] ?></p><?php endif; ?>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full px-3 py-2 border <?= !empty($errors['notes']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"><?= htmlspecialchars($formData['notes'] ?? '') ?></textarea>
                    <?php if (!empty($errors['notes'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['notes'] ?></p><?php endif; ?>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?= $errors['general'] ?></span>
                </div>
            <?php endif; ?>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $reservation_id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput && endTimeInput) {
            // Set min attribute for start_time to prevent past dates/times
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            // Only set min if current start time is not in the past (i.e. for new reservations, not for editing past ones)
            // For editing, we allow viewing past times but validation should prevent saving invalid past times.
            // startTimeInput.min = currentDateTime; 

            function updateEndTimeMin() {
                if (startTimeInput.value) {
                    const startTime = new Date(startTimeInput.value);
                    startTime.setHours(startTime.getHours() + 1); // Min duration 1 hour
                    const minEndTimeYear = startTime.getFullYear();
                    const minEndTimeMonth = String(startTime.getMonth() + 1).padStart(2, '0');
                    const minEndTimeDay = String(startTime.getDate()).padStart(2, '0');
                    const minEndTimeHours = String(startTime.getHours()).padStart(2, '0');
                    const minEndTimeMinutes = String(startTime.getMinutes()).padStart(2, '0');
                    endTimeInput.min = `${minEndTimeYear}-${minEndTimeMonth}-${minEndTimeDay}T${minEndTimeHours}:${minEndTimeMinutes}`;

                    const currentEndTime = new Date(endTimeInput.value);
                    if (currentEndTime < startTime) {
                         endTimeInput.value = endTimeInput.min;
                    }
                }
            }

            startTimeInput.addEventListener('change', updateEndTimeMin);
            // Call it once on load to set initial min for end_time based on current start_time
            updateEndTimeMin();
        }
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
