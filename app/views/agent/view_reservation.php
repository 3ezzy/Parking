<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/reservations" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reservations
        </a>
    </div>
    
    <?php flash('reservation_success'); ?>
    <?php flash('reservation_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-blue-800">Reservation Details</h2>
                    <span class="text-sm">
                        <?php if (
                            $reservation->status === 'active') : ?>
                            <?php if (strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()) : ?>
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded">Current</span>
                            <?php elseif (strtotime($reservation->start_time) > time()) : ?>
                                <span class="bg-blue-500 text-white px-3 py-1 rounded">Upcoming</span>
                            <?php else : ?>
                                <span class="bg-red-500 text-white px-3 py-1 rounded">Expired</span>
                            <?php endif; ?>
                        <?php elseif ($reservation->status === 'completed') : ?>
                            <span class="bg-green-500 text-white px-3 py-1 rounded">Completed</span>
                        <?php elseif ($reservation->status === 'pending') : ?>
                            <span class="bg-purple-500 text-white px-3 py-1 rounded">Pending</span>
                        <?php elseif ($reservation->status === 'confirmed') : ?>
                            <span class="bg-blue-500 text-white px-3 py-1 rounded">Confirmed</span>
                        <?php elseif ($reservation->status === 'cancelled') : ?>
                            <span class="bg-red-500 text-white px-3 py-1 rounded">Cancelled</span>
                        <?php elseif ($reservation->status === 'no_show') : ?>
                            <span class="bg-gray-500 text-white px-3 py-1 rounded">No Show</span>
                        <?php else : ?>
                            <span class="bg-gray-500 text-white px-3 py-1 rounded"><?= ucfirst($reservation->status) ?></span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-600">Reservation ID</h3>
                        <p class="text-lg"><?= $reservation->id ?></p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-600">Created On</h3>
                        <p class="text-lg"><?= date('M d, Y H:i', strtotime($reservation->created_at)) ?></p>
                    </div>
                </div>
                
                <div class="border-t border-blue-200 pt-4 mb-4">
                    <h3 class="font-semibold text-blue-800 mb-2">Customer Information</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-600">Name</h4>
                            <p class="text-lg"><?= $reservation->customer_name ?></p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Phone</h4>
                            <p class="text-lg"><?= $reservation->customer_phone ?? 'Not provided' ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <h4 class="font-semibold text-gray-600">Email</h4>
                        <p><?= $reservation->customer_email ?? 'Not provided' ?></p>
                    </div>
                </div>
                
                <div class="border-t border-blue-200 pt-4 mb-4">
                    <h3 class="font-semibold text-blue-800 mb-2">Reservation Schedule</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-600">Start Time</h4>
                            <p class="text-lg"><?= date('M d, Y H:i', strtotime($reservation->start_time)) ?></p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">End Time</h4>
                            <p class="text-lg"><?= date('M d, Y H:i', strtotime($reservation->end_time)) ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <h4 class="font-semibold text-gray-600">Duration</h4>
                            <?php
                                $startTime = new DateTime($reservation->start_time);
                                $endTime = new DateTime($reservation->end_time);
                                $interval = $startTime->diff($endTime);
                                $hours = $interval->h + ($interval->days * 24);
                                $minutes = $interval->i;
                            ?>
                            <p><?= $hours ?> hours<?= $minutes > 0 ? ", {$minutes} minutes" : "" ?></p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Status</h4>
                            <p>
                                <?php if ($reservation->status === 'active'): ?>
                                    <?php if (strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()): ?>
                                        <span class="text-yellow-600">Currently Active</span>
                                    <?php elseif (strtotime($reservation->start_time) > time()): ?>
                                        <span class="text-blue-600">Upcoming (Starts in 
                                            <?php
                                                $now = new DateTime();
                                                $start = new DateTime($reservation->start_time);
                                                $interval = $now->diff($start);
                                                if ($interval->days > 0) {
                                                    echo $interval->days . ' days';
                                                } elseif ($interval->h > 0) {
                                                    echo $interval->h . ' hours';
                                                } else {
                                                    echo $interval->i . ' minutes';
                                                }
                                            ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-red-600">Expired (Ended 
                                            <?php
                                                $now = new DateTime();
                                                $end = new DateTime($reservation->end_time);
                                                $interval = $end->diff($now);
                                                if ($interval->days > 0) {
                                                    echo $interval->days . ' days ago';
                                                } elseif ($interval->h > 0) {
                                                    echo $interval->h . ' hours ago';
                                                } else {
                                                    echo $interval->i . ' minutes ago';
                                                }
                                            ?>)
                                        </span>
                                    <?php endif; ?>
                                <?php elseif ($reservation->status === 'completed'): ?>
                                    <span class="text-green-600">Completed</span>
                                <?php elseif ($reservation->status === 'pending'): ?>
                                    <span class="text-purple-600">Pending</span>
                                <?php elseif ($reservation->status === 'confirmed'): ?>
                                    <span class="text-blue-600">Confirmed</span>
                                <?php elseif ($reservation->status === 'cancelled'): ?>
                                    <span class="text-red-600">Cancelled</span>
                                <?php elseif ($reservation->status === 'no_show'): ?>
                                    <span class="text-gray-600">No Show</span>
                                <?php else: ?>
                                    <span class="text-gray-600"><?= ucfirst($reservation->status) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-blue-200 pt-4">
                    <h3 class="font-semibold text-blue-800 mb-2">Space Information</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-600">Space Number</h4>
                            <p class="text-lg"><?= $reservation->space_number ?></p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Space Type</h4>
                            <p class="text-lg"><?= $reservation->space_type ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <h4 class="font-semibold text-gray-600">Location</h4>
                            <p><?= $reservation->location ?? 'Main Area' ?></p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Floor/Level</h4>
                            <p><?= $reservation->floor_level ?? 'Ground' ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <a href="<?= URL_ROOT ?>/agent/viewSpace/<?= $reservation->space_id ?>" class="text-blue-600 hover:underline">
                            <i class="fas fa-map-marker-alt mr-1"></i> View Space Details
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($reservation->license_plate) || !empty($reservation->vehicle_type)): ?>
                    <div class="border-t border-blue-200 pt-4 mt-4">
                        <h3 class="font-semibold text-blue-800 mb-2">Vehicle Information</h3>
                        
                        <?php if (!empty($reservation->license_plate)): ?>
                            <div>
                                <h4 class="font-semibold text-gray-600">License Plate</h4>
                                <p><?= $reservation->license_plate ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($reservation->vehicle_type)): ?>
                            <div class="mt-2">
                                <h4 class="font-semibold text-gray-600">Vehicle Type</h4>
                                <p><?= $reservation->vehicle_type ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($reservation->notes)): ?>
                    <div class="border-t border-blue-200 pt-4 mt-4">
                        <h3 class="font-semibold text-blue-800 mb-2">Notes</h3>
                        <p class="text-gray-700"><?= nl2br($reservation->notes) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($reservation->created_by_name)): ?>
                    <div class="border-t border-blue-200 pt-4 mt-4">
                        <h3 class="font-semibold text-blue-800 mb-2">Additional Information</h3>
                        <p>Created by: <?= $reservation->created_by_name ?></p>
                        
                        <?php if (!empty($reservation->updated_at) && $reservation->updated_at != $reservation->created_at): ?>
                            <p class="mt-1">Last updated: <?= date('M d, Y H:i', strtotime($reservation->updated_at)) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Actions</h2>
                
                <div class="space-y-3">
                    <?php if ($reservation->status === 'active'): ?>
                        <?php if (strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()): ?>
                            <a href="<?= URL_ROOT ?>/agent/vehicleEntry?reservation_id=<?= $reservation->id ?>" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-car mr-2"></i> Register Vehicle Entry
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= URL_ROOT ?>/agent/editReservation/<?= $reservation->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-edit mr-2"></i> Edit Reservation
                        </a>
                        
                        <form id="cancelReservationForm_<?= $reservation->id ?>" action="<?= URL_ROOT ?>/agent/cancelReservationPost/<?= $reservation->id ?>" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                            <button type="button" onclick="confirmAndSubmitCancel(<?= $reservation->id ?>, '<?= htmlspecialchars(addslashes($reservation->customer_name)) ?>')" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-times mr-2"></i> Cancel Reservation
                            </button>
                        </form>
                    <?php elseif ($reservation->status === 'completed'): ?>
                        <p class="text-center text-gray-700">This reservation has been completed and cannot be modified.</p>
                    <?php else: ?>
                        <p class="text-center text-gray-700">This reservation has been cancelled and cannot be modified.</p>
                    <?php endif; ?>
                    
                    <a href="<?= URL_ROOT ?>/agent/printReservation/<?= $reservation->id ?>" class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                        <i class="fas fa-print mr-2"></i> Print Reservation
                    </a>
                </div>
                
                <?php if ($reservation->status === 'active' && strtotime($reservation->start_time) <= time() && strtotime($reservation->end_time) >= time()): ?>
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="font-semibold text-yellow-800 mb-2">Current Reservation</h3>
                        <p class="text-sm text-yellow-700 mb-2">This reservation is currently active. The customer should be given this space.</p>
                        <p class="text-sm text-yellow-700">The reservation will expire in:
                            <?php
                                $now = new DateTime();
                                $end = new DateTime($reservation->end_time);
                                $interval = $now->diff($end);
                                if ($interval->days > 0) {
                                    echo $interval->days . ' days, ' . $interval->h . ' hours';
                                } elseif ($interval->h > 0) {
                                    echo $interval->h . ' hours, ' . $interval->i . ' minutes';
                                } else {
                                    echo $interval->i . ' minutes';
                                }
                            ?>
                        </p>
                    </div>
                <?php elseif ($reservation->status === 'active' && strtotime($reservation->start_time) > time()): ?>
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-blue-800 mb-2">Upcoming Reservation</h3>
                        <p class="text-sm text-blue-700 mb-2">This reservation has not started yet.</p>
                        <p class="text-sm text-blue-700">The reservation will start in:
                            <?php
                                $now = new DateTime();
                                $start = new DateTime($reservation->start_time);
                                $interval = $now->diff($start);
                                if ($interval->days > 0) {
                                    echo $interval->days . ' days, ' . $interval->h . ' hours';
                                } elseif ($interval->h > 0) {
                                    echo $interval->h . ' hours, ' . $interval->i . ' minutes';
                                } else {
                                    echo $interval->i . ' minutes';
                                }
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
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

<script>
function confirmAndSubmitCancel(reservationId, customerName) {
    if (confirm(`Are you sure you want to cancel the reservation for ${customerName} (ID: ${reservationId})? This action cannot be undone.`)) {
        document.getElementById('cancelReservationForm_' + reservationId).submit();
    }
}
</script>

<?php require APP . 'views/includes/footer.php'; ?>
