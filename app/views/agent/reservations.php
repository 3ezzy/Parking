<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <div class="flex space-x-2">
            <a href="<?= URL_ROOT ?>/agent/createReservation" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus-circle mr-2"></i> New Reservation
            </a>
            <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php flash('reservation_success'); ?>
    <?php flash('reservation_error'); ?>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-800">Search Reservations</h2>
            <button type="button" class="text-blue-600 hover:text-blue-800" onclick="toggleSearchForm()">
                <i class="fas fa-filter mr-1"></i> Toggle Filters
            </button>
        </div>
        
        <form id="search-form" action="<?= URL_ROOT ?>/agent/reservations" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="customer_name" class="block text-gray-700 mb-1 font-medium">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['customer_name'] ?? '' ?>">
                </div>
                
                <div>
                    <label for="status" class="block text-gray-700 mb-1 font-medium">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="">All</option>
                        <option value="active" <?= isset($criteria['status']) && $criteria['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="completed" <?= isset($criteria['status']) && $criteria['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= isset($criteria['status']) && $criteria['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="date_range" class="block text-gray-700 mb-1 font-medium">Date Range</label>
                    <select id="date_range" name="date_range" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="today" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="tomorrow" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'tomorrow' ? 'selected' : '' ?>>Tomorrow</option>
                        <option value="this_week" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'this_week' ? 'selected' : '' ?>>This Week</option>
                        <option value="next_week" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'next_week' ? 'selected' : '' ?>>Next Week</option>
                        <option value="custom" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'custom' ? 'selected' : '' ?>>Custom Range</option>
                    </select>
                </div>
            </div>
            
            <div id="custom-date-range" class="grid grid-cols-1 md:grid-cols-2 gap-4 <?= isset($criteria['date_range']) && $criteria['date_range'] === 'custom' ? '' : 'hidden' ?>">
                <div>
                    <label for="date_from" class="block text-gray-700 mb-1 font-medium">Date From</label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['date_from'] ?? '' ?>">
                </div>
                
                <div>
                    <label for="date_to" class="block text-gray-700 mb-1 font-medium">Date To</label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['date_to'] ?? '' ?>">
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <a href="<?= URL_ROOT ?>/agent/reservations" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Clear Filters
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-blue-800">Reservations</h2>
        <a href="<?= URL_ROOT ?>/agent/parkingMap" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus-circle mr-2"></i> New Reservation
        </a>
    </div>
    
    <?php if (empty($reservations)): ?>
        <div class="bg-gray-100 p-6 rounded-lg text-center">
            <p class="text-gray-700">No reservations found matching your criteria.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Customer</th>
                        <th class="px-4 py-2 border">Contact</th>
                        <th class="px-4 py-2 border">Space</th>
                        <th class="px-4 py-2 border">Start Time</th>
                        <th class="px-4 py-2 border">End Time</th>
                        <th class="px-4 py-2 border">Vehicle</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr class="hover:bg-gray-50 <?= $reservation->status === 'active' && strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time() ? 'bg-yellow-50' : '' ?>">
                            <td class="px-4 py-2 border"><?= $reservation->id ?></td>
                            <td class="px-4 py-2 border"><?= $reservation->customer_name ?></td>
                            <td class="px-4 py-2 border">
                                <?php if (!empty($reservation->customer_phone)): ?>
                                    <span class="block"><?= $reservation->customer_phone ?></span>
                                <?php endif; ?>
                                <?php if (!empty($reservation->customer_email)): ?>
                                    <span class="block text-xs text-gray-500"><?= $reservation->customer_email ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border"><?= $reservation->space_number ?> (<?= $reservation->space_type ?>)</td>
                            <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($reservation->start_time)) ?></td>
                            <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($reservation->end_time)) ?></td>
                            <td class="px-4 py-2 border"><?= !empty($reservation->license_plate) ? $reservation->license_plate : 'Not specified' ?></td>
                            <td class="px-4 py-2 border">
                                <?php if ($reservation->status === 'active'): ?>
                                    <?php if (strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Current</span>
                                    <?php elseif (strtotime($reservation->start_time) > time()): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Upcoming</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Expired</span>
                                    <?php endif; ?>
                                <?php elseif ($reservation->status === 'completed'): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <div class="flex flex-col space-y-1">
                                    <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $reservation->id ?>" class="text-blue-600 hover:underline text-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <?php if ($reservation->status === 'active'): ?>
                                        <?php if (strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()): ?>
                                            <a href="<?= URL_ROOT ?>/agent/vehicleEntry?reservation_id=<?= $reservation->id ?>" class="text-green-600 hover:underline text-sm">
                                                <i class="fas fa-car"></i> Register Entry
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?= URL_ROOT ?>/agent/editReservation/<?= $reservation->id ?>" class="text-yellow-600 hover:underline text-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <button onclick="confirmCancel(<?= $reservation->id ?>, '<?= $reservation->customer_name ?>')" class="text-red-600 hover:underline text-sm text-left">
                                            <i class="fas fa-times"></i> Cancel
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
                Showing <?= count($reservations) ?> results
            </div>
            
            <div>
                <?php
                    $prevOffset = max(0, $offset - $limit);
                    $nextOffset = $offset + $limit;
                ?>
                
                <div class="flex space-x-2">
                    <?php if ($offset > 0): ?>
                        <a href="<?= URL_ROOT ?>/agent/reservations?<?= http_build_query(array_merge($criteria, ['offset' => $prevOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                            Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php if (count($reservations) >= $limit): ?>
                        <a href="<?= URL_ROOT ?>/agent/reservations?<?= http_build_query(array_merge($criteria, ['offset' => $nextOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancel-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h2 class="text-xl font-bold mb-4">Confirm Cancellation</h2>
        <p id="cancel-message" class="mb-6"></p>
        
        <div class="flex justify-end space-x-2">
            <button id="cancel-modal-close" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">No, Keep It</button>
            <a id="cancel-confirm" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Yes, Cancel</a>
        </div>
    </div>
</div>

<script>
    function toggleSearchForm() {
        const searchForm = document.getElementById('search-form');
        searchForm.classList.toggle('hidden');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const dateRange = document.getElementById('date_range');
        const customDateRange = document.getElementById('custom-date-range');
        
        if (dateRange && customDateRange) {
            dateRange.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.classList.remove('hidden');
                } else {
                    customDateRange.classList.add('hidden');
                }
            });
        }
        
        // Cancel modal functionality
        const cancelModal = document.getElementById('cancel-modal');
        const cancelMessage = document.getElementById('cancel-message');
        const cancelConfirm = document.getElementById('cancel-confirm');
        const cancelModalClose = document.getElementById('cancel-modal-close');
        
        if (cancelModalClose && cancelModal) {
            cancelModalClose.addEventListener('click', function() {
                cancelModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            cancelModal.addEventListener('click', function(e) {
                if (e.target === cancelModal) {
                    cancelModal.classList.add('hidden');
                }
            });
        }
    });
    
    function confirmCancel(reservationId, customerName) {
        const cancelModal = document.getElementById('cancel-modal');
        const cancelMessage = document.getElementById('cancel-message');
        const cancelConfirm = document.getElementById('cancel-confirm');
        
        if (cancelModal && cancelMessage && cancelConfirm) {
            cancelMessage.textContent = `Are you sure you want to cancel the reservation for ${customerName}? This action cannot be undone.`;
            cancelConfirm.href = `<?= URL_ROOT ?>/agent/cancelReservation/${reservationId}`;
            cancelModal.classList.remove('hidden');
        }
    }
</script>

<?php require APP . 'views/includes/footer.php'; ?>
