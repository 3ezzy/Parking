<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/parkingMap" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Parking Map
        </a>
    </div>
    
    <?php flash('space_success'); ?>
    <?php flash('space_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Information</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Space Number</h3>
                            <p class="text-lg"><?= $space->space_number ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Status</h3>
                            <p class="text-lg">
                                <?php if ($space->status === 'available'): ?>
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Available</span>
                                <?php elseif ($space->status === 'occupied'): ?>
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-sm">Occupied</span>
                                <?php elseif ($space->status === 'reserved'): ?>
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">Reserved</span>
                                <?php else: ?>
                                    <span class="bg-gray-500 text-white px-2 py-1 rounded text-sm">Maintenance</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Space Type</h3>
                            <p class="text-lg"><?= $space->type_name ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Floor/Level</h3>
                            <p class="text-lg"><?= $space->floor_level ?? 'Ground' ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Hourly Rate</h3>
                            <p class="text-lg">$<?= number_format($space->hourly_rate, 2) ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Location</h3>
                            <p class="text-lg"><?= $space->location ?? 'Main Area' ?></p>
                        </div>
                    </div>
                    
                    <?php if ($space->status === 'occupied' && isset($activeTicket)): ?>
                        <div class="border-t border-blue-200 pt-4 mt-4">
                            <h3 class="font-semibold text-blue-800 mb-2">Current Occupancy</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-600">License Plate</h4>
                                    <p><?= $activeTicket->license_plate ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-600">Vehicle Type</h4>
                                    <p><?= $activeTicket->vehicle_type ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div>
                                    <h4 class="font-semibold text-gray-600">Entry Time</h4>
                                    <p><?= date('M d, Y H:i', strtotime($activeTicket->entry_time)) ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-600">Duration</h4>
                                    <p><?= number_format($activeTicket->duration_hours, 1) ?> hours</p>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <a href="<?= URL_ROOT ?>/agent/ticketDetails/<?= $activeTicket->id ?>" class="text-blue-600 hover:underline">
                                    <i class="fas fa-ticket-alt mr-1"></i> View Ticket #<?= $activeTicket->id ?>
                                </a>
                            </div>
                        </div>
                    <?php elseif ($space->status === 'reserved' && isset($reservation)): ?>
                        <div class="border-t border-blue-200 pt-4 mt-4">
                            <h3 class="font-semibold text-blue-800 mb-2">Current Reservation</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-600">Reserved For</h4>
                                    <p><?= $reservation->customer_name ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-600">Contact</h4>
                                    <p><?= $reservation->customer_phone ?? 'N/A' ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div>
                                    <h4 class="font-semibold text-gray-600">Start Time</h4>
                                    <p><?= date('M d, Y H:i', strtotime($reservation->start_time)) ?></p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-600">End Time</h4>
                                    <p><?= date('M d, Y H:i', strtotime($reservation->end_time)) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($spaceHistory)): ?>
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm mt-6">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Activity History</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 border">Ticket #</th>
                                    <th class="px-4 py-2 border">License Plate</th>
                                    <th class="px-4 py-2 border">Entry Time</th>
                                    <th class="px-4 py-2 border">Exit Time</th>
                                    <th class="px-4 py-2 border">Duration</th>
                                    <th class="px-4 py-2 border">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($spaceHistory as $history): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border">
                                            <a href="<?= URL_ROOT ?>/agent/ticketDetails/<?= $history->id ?>" class="text-blue-600 hover:underline">
                                                <?= $history->id ?>
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 border"><?= $history->license_plate ?></td>
                                        <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($history->entry_time)) ?></td>
                                        <td class="px-4 py-2 border">
                                            <?= $history->exit_time ? date('M d, Y H:i', strtotime($history->exit_time)) : '-' ?>
                                        </td>
                                        <td class="px-4 py-2 border"><?= number_format($history->duration_hours, 1) ?> hours</td>
                                        <td class="px-4 py-2 border">
                                            <?= $history->amount_paid ? '$' . number_format($history->amount_paid, 2) : '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Space Actions</h2>
                
                <div class="space-y-4">
                    <?php if ($space->status === 'available'): ?>
                        <a href="<?= URL_ROOT ?>/agent/vehicleEntry?space_id=<?= $space->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-car mr-2"></i> Register Vehicle Entry
                        </a>
                        
                        <a href="<?= URL_ROOT ?>/agent/reserveSpace/<?= $space->id ?>" class="block w-full text-center bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Make Reservation
                        </a>
                    <?php elseif ($space->status === 'occupied' && isset($activeTicket)): ?>
                        <a href="<?= URL_ROOT ?>/agent/processExit/<?= $activeTicket->id ?>" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-check-circle mr-2"></i> Process Vehicle Exit
                        </a>
                        
                        <a href="<?= URL_ROOT ?>/agent/ticketDetails/<?= $activeTicket->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-ticket-alt mr-2"></i> View Ticket Details
                        </a>
                    <?php elseif ($space->status === 'reserved' && isset($reservation)): ?>
                        <a href="<?= URL_ROOT ?>/agent/cancelReservation/<?= $reservation->id ?>" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-times-circle mr-2"></i> Cancel Reservation
                        </a>
                        
                        <a href="<?= URL_ROOT ?>/agent/vehicleEntry?space_id=<?= $space->id ?>&reservation_id=<?= $reservation->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-car mr-2"></i> Register Reserved Vehicle
                        </a>
                    <?php else: ?>
                        <a href="<?= URL_ROOT ?>/agent/updateSpaceStatus/<?= $space->id ?>/available" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-check-circle mr-2"></i> Mark as Available
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($space->status !== 'maintenance'): ?>
                        <a href="<?= URL_ROOT ?>/agent/updateSpaceStatus/<?= $space->id ?>/maintenance" class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                            <i class="fas fa-tools mr-2"></i> Mark for Maintenance
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Space Location</h3>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="text-center">
                            <!-- Simple visual representation of the space -->
                            <div class="inline-block w-24 h-12 border-2 
                                <?php if ($space->status === 'available'): ?>
                                    border-green-500 bg-green-100
                                <?php elseif ($space->status === 'occupied'): ?>
                                    border-red-500 bg-red-100
                                <?php elseif ($space->status === 'reserved'): ?>
                                    border-yellow-500 bg-yellow-100
                                <?php else: ?>
                                    border-gray-500 bg-gray-100
                                <?php endif; ?>
                                flex items-center justify-center mb-2">
                                <span class="font-bold"><?= $space->space_number ?></span>
                            </div>
                            <p class="text-sm text-gray-600">
                                <?= $space->location ?? 'Main Area' ?>, Level <?= $space->floor_level ?? 'Ground' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
