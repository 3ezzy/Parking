<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Parking Space Statistics -->
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Parking Spaces</h3>
            <div class="flex justify-between items-center">
                <span class="text-3xl font-bold text-blue-600"><?= $spaceStats->total_spaces ?></span>
                <i class="fas fa-parking text-2xl text-blue-400"></i>
            </div>
            <div class="mt-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Available:</span>
                    <span class="font-semibold text-green-600"><?= $spaceStats->available_spaces ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Occupied:</span>
                    <span class="font-semibold text-red-600"><?= $spaceStats->occupied_spaces ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Reserved:</span>
                    <span class="font-semibold text-yellow-600"><?= $spaceStats->reserved_spaces ?></span>
                </div>
            </div>
        </div>
        
        <!-- Ticket Statistics -->
        <div class="bg-green-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Today's Tickets</h3>
            <div class="flex justify-between items-center">
                <span class="text-3xl font-bold text-green-600"><?= $ticketStats->total_tickets ?></span>
                <i class="fas fa-ticket-alt text-2xl text-green-400"></i>
            </div>
            <div class="mt-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Active:</span>
                    <span class="font-semibold text-blue-600"><?= $ticketStats->active_tickets ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Completed:</span>
                    <span class="font-semibold text-green-600"><?= $ticketStats->completed_tickets ?></span>
                </div>
            </div>
        </div>
        
        <!-- Occupancy Rate -->
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Occupancy Rate</h3>
            <div class="flex justify-between items-center">
                <span class="text-3xl font-bold text-yellow-600">
                    <?= $spaceStats->total_spaces > 0 ? round(($spaceStats->occupied_spaces / $spaceStats->total_spaces) * 100) : 0 ?>%
                </span>
                <i class="fas fa-chart-pie text-2xl text-yellow-400"></i>
            </div>
            <div class="mt-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Available Rate:</span>
                    <span class="font-semibold text-green-600">
                        <?= $spaceStats->total_spaces > 0 ? round(($spaceStats->available_spaces / $spaceStats->total_spaces) * 100) : 0 ?>%
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-blue-800">Quick Actions</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <a href="<?= URL_ROOT ?>/agent/vehicleEntry" class="flex items-center p-4 bg-blue-100 rounded-lg shadow-sm hover:bg-blue-200 transition">
            <div class="rounded-full bg-blue-500 text-white p-3 mr-4">
                <i class="fas fa-car"></i>
            </div>
            <div>
                <h3 class="font-semibold">Vehicle Entry</h3>
                <p class="text-sm text-gray-600">Register a new parking entry</p>
            </div>
        </a>
        
        <a href="<?= URL_ROOT ?>/agent/vehicleExit" class="flex items-center p-4 bg-green-100 rounded-lg shadow-sm hover:bg-green-200 transition">
            <div class="rounded-full bg-green-500 text-white p-3 mr-4">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div>
                <h3 class="font-semibold">Vehicle Exit</h3>
                <p class="text-sm text-gray-600">Process a vehicle leaving</p>
            </div>
        </a>
        
        <a href="<?= URL_ROOT ?>/agent/parkingMap" class="flex items-center p-4 bg-purple-100 rounded-lg shadow-sm hover:bg-purple-200 transition">
            <div class="rounded-full bg-purple-500 text-white p-3 mr-4">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div>
                <h3 class="font-semibold">Parking Map</h3>
                <p class="text-sm text-gray-600">View parking space status</p>
            </div>
        </a>
        
        <a href="<?= URL_ROOT ?>/agent/reservations" class="flex items-center p-4 bg-indigo-100 rounded-lg shadow-sm hover:bg-indigo-200 transition">
            <div class="rounded-full bg-indigo-500 text-white p-3 mr-4">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h3 class="font-semibold">Reservations</h3>
                <p class="text-sm text-gray-600">Manage parking reservations</p>
            </div>
        </a>
        
        <a href="<?= URL_ROOT ?>/agent/searchTickets" class="flex items-center p-4 bg-yellow-100 rounded-lg shadow-sm hover:bg-yellow-200 transition">
            <div class="rounded-full bg-yellow-500 text-white p-3 mr-4">
                <i class="fas fa-search"></i>
            </div>
            <div>
                <h3 class="font-semibold">Search Tickets</h3>
                <p class="text-sm text-gray-600">Find and view ticket details</p>
            </div>
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-blue-800">Active Parking Tickets</h2>
        <a href="<?= URL_ROOT ?>/agent/searchTickets" class="text-blue-600 hover:underline text-sm">View All</a>
    </div>
    
    <?php if (empty($activeTickets)): ?>
        <p class="text-gray-700">No active parking tickets found.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Ticket #</th>
                        <th class="px-4 py-2 border">License Plate</th>
                        <th class="px-4 py-2 border">Space</th>
                        <th class="px-4 py-2 border">Entry Time</th>
                        <th class="px-4 py-2 border">Duration</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeTickets as $ticket): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $ticket->id ?></td>
                            <td class="px-4 py-2 border"><?= $ticket->license_plate ?></td>
                            <td class="px-4 py-2 border"><?= $ticket->space_number ?> (<?= $ticket->space_type ?>)</td>
                            <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($ticket->entry_time)) ?></td>
                            <td class="px-4 py-2 border"><?= number_format($ticket->duration_hours, 1) ?> hours</td>
                            <td class="px-4 py-2 border">
                                <a href="<?= URL_ROOT ?>/agent/ticketDetails/<?= $ticket->id ?>" class="text-blue-600 hover:underline mr-2">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?= URL_ROOT ?>/agent/processExit/<?= $ticket->id ?>" class="text-green-600 hover:underline">
                                    <i class="fas fa-check-circle"></i> Process Exit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
