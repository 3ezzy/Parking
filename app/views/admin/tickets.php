<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('ticket_success'); ?>
    <?php flash('ticket_error'); ?>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-800">Search Tickets</h2>
            <button type="button" class="text-blue-600 hover:text-blue-800" onclick="toggleSearchForm()">
                <i class="fas fa-filter mr-1"></i> Filters
            </button>
        </div>
        
        <form id="search-form" action="<?= URL_ROOT ?>/admin/tickets" method="GET" class="<?= !empty($criteria) ? '' : 'hidden' ?> space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate</label>
                    <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['license_plate'] ?? '' ?>">
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
                    <label for="vehicle_type" class="block text-gray-700 mb-1 font-medium">Vehicle Type</label>
                    <select id="vehicle_type" name="vehicle_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="">All</option>
                        <?php foreach ($vehicleTypes as $type): ?>
                            <option value="<?= $type->id ?>" <?= isset($criteria['vehicle_type']) && $criteria['vehicle_type'] == $type->id ? 'selected' : '' ?>><?= $type->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <a href="<?= URL_ROOT ?>/admin/tickets" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Clear Filters
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold text-blue-800 mb-4">Ticket Results</h2>
        
        <?php if (empty($tickets)): ?>
            <p class="text-gray-700">No tickets found matching your criteria.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">Ticket #</th>
                            <th class="px-4 py-2 border">License Plate</th>
                            <th class="px-4 py-2 border">Vehicle Type</th>
                            <th class="px-4 py-2 border">Space</th>
                            <th class="px-4 py-2 border">Entry Time</th>
                            <th class="px-4 py-2 border">Exit Time</th>
                            <th class="px-4 py-2 border">Duration</th>
                            <th class="px-4 py-2 border">Amount</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?= $ticket->id ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->license_plate ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->vehicle_type ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->space_number ?></td>
                                <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($ticket->entry_time)) ?></td>
                                <td class="px-4 py-2 border">
                                    <?= $ticket->exit_time ? date('M d, Y H:i', strtotime($ticket->exit_time)) : '-' ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if (isset($ticket->duration_hours)): ?>
                                        <?= number_format($ticket->duration_hours, 1) ?> hours
                                    <?php else: ?>
                                        <?php
                                            $entryTime = new DateTime($ticket->entry_time);
                                            $exitTime = $ticket->exit_time ? new DateTime($ticket->exit_time) : new DateTime();
                                            $interval = $entryTime->diff($exitTime);
                                            $hours = $interval->h + ($interval->i / 60);
                                            $days = $interval->d;
                                            $totalHours = $days * 24 + $hours;
                                        ?>
                                        <?= number_format($totalHours, 1) ?> hours
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if ($ticket->status === 'active'): ?>
                                        <span class="text-gray-500">Pending</span>
                                    <?php else: ?>
                                        $<?= number_format($ticket->amount_paid, 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if ($ticket->status === 'active'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Active</span>
                                    <?php elseif ($ticket->status === 'completed'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <a href="<?= URL_ROOT ?>/admin/ticketDetails/<?= $ticket->id ?>" class="text-blue-600 hover:underline block">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <?php if ($ticket->status === 'active'): ?>
                                        <a href="<?= URL_ROOT ?>/admin/closeTicket/<?= $ticket->id ?>" class="text-green-600 hover:underline block mt-1">
                                            <i class="fas fa-check-circle"></i> Close
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-between items-center">
                <div class="text-gray-600 text-sm">
                    Showing <?= count($tickets) ?> results
                </div>
                
                <div>
                    <?php
                        $prevOffset = max(0, $offset - $limit);
                        $nextOffset = $offset + $limit;
                    ?>
                    
                    <div class="flex space-x-2">
                        <?php if ($offset > 0): ?>
                            <a href="<?= URL_ROOT ?>/admin/tickets?<?= http_build_query(array_merge($criteria, ['offset' => $prevOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php if (count($tickets) >= $limit): ?>
                            <a href="<?= URL_ROOT ?>/admin/tickets?<?= http_build_query(array_merge($criteria, ['offset' => $nextOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleSearchForm() {
        const searchForm = document.getElementById('search-form');
        searchForm.classList.toggle('hidden');
    }
</script>

<?php require APP . 'views/includes/footer.php'; ?>
