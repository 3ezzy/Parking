<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition print-ticket-btn">
                <i class="fas fa-print mr-2"></i> Print Ticket
            </button>
            <a href="<?= URL_ROOT ?>/admin/tickets" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition no-print">
                <i class="fas fa-arrow-left mr-2"></i> Back to Tickets
            </a>
        </div>
    </div>
    
    <div class="ticket bg-white p-6 rounded-lg border border-gray-300 max-w-4xl mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><?= SITE_NAME ?></h2>
            <p class="text-gray-600">Parking Ticket</p>
        </div>
        
        <div class="border-t border-b border-gray-300 py-4 my-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Ticket #</h3>
                    <p class="text-lg"><?= $ticket->id ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Status</h3>
                    <p class="text-lg">
                        <?php if ($ticket->status === 'active'): ?>
                            <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Active</span>
                        <?php elseif ($ticket->status === 'completed'): ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Completed</span>
                        <?php else: ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-sm">Cancelled</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">License Plate</h3>
                    <p class="text-lg"><?= $ticket->license_plate ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Vehicle Type</h3>
                    <p class="text-lg"><?= $ticket->vehicle_type ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Space Number</h3>
                    <p class="text-lg"><?= $ticket->space_number ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Space Type</h3>
                    <p class="text-lg"><?= $ticket->space_type ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Hourly Rate</h3>
                    <p class="text-lg">$<?= number_format($ticket->hourly_rate, 2) ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Duration</h3>
                    <p class="text-lg"><?= number_format($ticket->duration_hours, 1) ?> hours</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Entry Time</h3>
                    <p class="text-lg"><?= date('M d, Y H:i', strtotime($ticket->entry_time)) ?></p>
                </div>
                <div>
                    <?php if ($ticket->status === 'active'): ?>
                        <h3 class="font-semibold text-gray-600">Current Time</h3>
                        <p class="text-lg"><?= date('M d, Y H:i') ?></p>
                    <?php else: ?>
                        <h3 class="font-semibold text-gray-600">Exit Time</h3>
                        <p class="text-lg"><?= date('M d, Y H:i', strtotime($ticket->exit_time)) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Created By</h3>
                    <p class="text-lg"><?= $ticket->created_by_name ?></p>
                </div>
                <div>
                    <?php if ($ticket->status === 'active'): ?>
                        <h3 class="font-semibold text-gray-600">Current Amount</h3>
                        <p class="text-lg font-bold text-green-600">$<?= number_format($ticket->calculated_amount, 2) ?></p>
                    <?php else: ?>
                        <h3 class="font-semibold text-gray-600">Amount Paid</h3>
                        <p class="text-lg font-bold text-green-600">$<?= number_format($ticket->amount_paid, 2) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($ticket->owner_name) || !empty($ticket->owner_phone)): ?>
            <div class="mt-4 pt-4 border-t border-gray-300">
                <h3 class="font-semibold text-gray-600 mb-2">Owner Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (!empty($ticket->owner_name)): ?>
                        <div>
                            <h4 class="text-gray-600">Name</h4>
                            <p><?= $ticket->owner_name ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ticket->owner_phone)): ?>
                        <div>
                            <h4 class="text-gray-600">Phone</h4>
                            <p><?= $ticket->owner_phone ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Thank you for using our parking services!</p>
            <p>This ticket serves as an official receipt.</p>
            
            <?php if ($ticket->status === 'active'): ?>
                <div class="mt-4 text-red-600 font-semibold">
                    <p>This ticket must be presented upon exit.</p>
                    <p>Lost ticket fee: $20.00</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($ticket->status === 'active'): ?>
        <div class="mt-6 text-center no-print">
            <a href="<?= URL_ROOT ?>/admin/closeTicket/<?= $ticket->id ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">
                <i class="fas fa-check-circle mr-2"></i> Close Ticket
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
