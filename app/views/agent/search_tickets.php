<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('ticket_success'); ?>
    <?php flash('ticket_error'); ?>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-800">Search Tickets</h2>
            <button type="button" class="text-blue-600 hover:text-blue-800" onclick="toggleSearchForm()">
                <i class="fas fa-filter mr-1"></i> Toggle Filters
            </button>
        </div>
        
        <form id="search-form" action="<?= URL_ROOT ?>/agent/searchTickets" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="license_plate" class="block text-gray-700 mb-1 font-medium">License Plate</label>
                    <input type="text" id="license_plate" name="license_plate" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['license_plate'] ?? '' ?>" placeholder="Enter license plate">
                </div>
                
                <div>
                    <label for="status" class="block text-gray-700 mb-1 font-medium">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="">All Statuses</option>
                        <option value="active" <?= isset($criteria['status']) && $criteria['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="pending" <?= isset($criteria['status']) && $criteria['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= isset($criteria['status']) && $criteria['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="checked_in" <?= isset($criteria['status']) && $criteria['status'] === 'checked_in' ? 'selected' : '' ?>>Checked In</option>
                        <option value="completed" <?= isset($criteria['status']) && $criteria['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= isset($criteria['status']) && $criteria['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <option value="no_show" <?= isset($criteria['status']) && $criteria['status'] === 'no_show' ? 'selected' : '' ?>>No Show</option>
                    </select>
                </div>
                
                <div>
                    <label for="customer_name" class="block text-gray-700 mb-1 font-medium">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $criteria['customer_name'] ?? '' ?>" placeholder="Enter customer name">
                </div>
                
                <div>
                    <label for="date_range" class="block text-gray-700 mb-1 font-medium">Date Range</label>
                    <select id="date_range" name="date_range" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="today" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="yesterday" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                        <option value="this_week" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'this_week' ? 'selected' : '' ?>>This Week</option>
                        <option value="last_week" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'last_week' ? 'selected' : '' ?>>Last Week</option>
                        <option value="this_month" <?= isset($criteria['date_range']) && $criteria['date_range'] === 'this_month' ? 'selected' : '' ?>>This Month</option>
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
                <a href="<?= URL_ROOT ?>/agent/searchTickets" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Clear Filters
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-800">Tickets & Reservations Results</h2>
            <div class="flex space-x-4 text-sm">
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-2">
                        <i class="fas fa-ticket-alt mr-1"></i>Ticket
                    </span>
                    <span class="text-gray-600">Walk-in parking</span>
                </div>
                <div class="flex items-center">
                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs mr-2">
                        <i class="fas fa-calendar-alt mr-1"></i>Reservation
                    </span>
                    <span class="text-gray-600">Pre-booked parking</span>
                </div>
            </div>
        </div>
        
        <?php if (empty($tickets)): ?>
            <p class="text-gray-700">No tickets or reservations found matching your criteria.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">Type</th>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Customer</th>
                            <th class="px-4 py-2 border">License Plate</th>
                            <th class="px-4 py-2 border">Space</th>
                            <th class="px-4 py-2 border">Start Time</th>
                            <th class="px-4 py-2 border">End Time</th>
                            <th class="px-4 py-2 border">Duration</th>
                            <th class="px-4 py-2 border">Amount</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">
                                    <?php if ($ticket->type === 'ticket'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                            <i class="fas fa-ticket-alt mr-1"></i>Ticket
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                            <i class="fas fa-calendar-alt mr-1"></i>Reservation
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border"><?= $ticket->id ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->customer_name ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->license_plate ?></td>
                                <td class="px-4 py-2 border"><?= $ticket->space_number ?> (<?= $ticket->space_type ?>)</td>
                                <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($ticket->start_time)) ?></td>
                                <td class="px-4 py-2 border">
                                    <?= $ticket->end_time ? date('M d, Y H:i', strtotime($ticket->end_time)) : '-' ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php
                                        $startTime = new DateTime($ticket->start_time);
                                        $endTime = $ticket->end_time ? new DateTime($ticket->end_time) : new DateTime();
                                        $interval = $startTime->diff($endTime);
                                        $hours = $interval->h + ($interval->i / 60);
                                        $days = $interval->d;
                                        $totalHours = $days * 24 + $hours;
                                    ?>
                                    <?= number_format($totalHours, 1) ?> hours
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if ($ticket->status === 'active' || $ticket->status === 'pending'): ?>
                                        <span class="text-gray-500">Pending</span>
                                    <?php elseif ($ticket->amount_paid): ?>
                                        $<?= number_format($ticket->amount_paid, 2) ?>
                                        <?php if ($ticket->payment_method): ?>
                                            <br><span class="text-xs text-gray-500"><?= ucfirst(str_replace('_', ' ', $ticket->payment_method)) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php if ($ticket->status === 'active'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Active</span>
                                    <?php elseif ($ticket->status === 'pending'): ?>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Pending</span>
                                    <?php elseif ($ticket->status === 'confirmed'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>
                                    <?php elseif ($ticket->status === 'checked_in'): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Checked In</span>
                                    <?php elseif ($ticket->status === 'completed'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                    <?php elseif ($ticket->status === 'cancelled'): ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                    <?php elseif ($ticket->status === 'no_show'): ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">No Show</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs"><?= ucfirst($ticket->status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <div class="flex flex-col space-y-1">
                                        <?php if ($ticket->type === 'ticket'): ?>
                                            <a href="<?= URL_ROOT ?>/agent/ticketDetails/<?= $ticket->id ?>" class="text-blue-600 hover:underline text-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            <?php if ($ticket->status === 'active'): ?>
                                                <a href="<?= URL_ROOT ?>/agent/processExit/<?= $ticket->id ?>" class="text-green-600 hover:underline text-sm">
                                                    <i class="fas fa-check-circle"></i> Process Exit
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $ticket->id ?>" class="text-blue-600 hover:underline text-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            <?php if (in_array($ticket->status, ['pending', 'confirmed', 'active'])): ?>
                                                <a href="<?= URL_ROOT ?>/agent/editReservation/<?= $ticket->id ?>" class="text-yellow-600 hover:underline text-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array($ticket->status, ['active', 'checked_in', 'completed']) && !$ticket->amount_paid): ?>
                                                <a href="<?= URL_ROOT ?>/agent/processReservationPayment/<?= $ticket->id ?>" class="text-green-600 hover:underline text-sm">
                                                    <i class="fas fa-dollar-sign"></i> Process Payment
                                                </a>
                                            <?php endif; ?>
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
                    Showing <?= count($tickets) ?> results
                </div>
                
                <div>
                    <?php
                        $prevOffset = max(0, $offset - $limit);
                        $nextOffset = $offset + $limit;
                    ?>
                    
                    <div class="flex space-x-2">
                        <?php if ($offset > 0): ?>
                            <a href="<?= URL_ROOT ?>/agent/searchTickets?<?= http_build_query(array_merge($criteria, ['offset' => $prevOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php if (count($tickets) >= $limit): ?>
                            <a href="<?= URL_ROOT ?>/agent/searchTickets?<?= http_build_query(array_merge($criteria, ['offset' => $nextOffset, 'limit' => $limit])) ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
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
    
    document.addEventListener('DOMContentLoaded', function() {
        const dateRange = document.getElementById('date_range');
        const customDateRange = document.getElementById('custom-date-range');
        
        dateRange.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
