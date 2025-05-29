<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition print-ticket-btn">
                <i class="fas fa-print mr-2"></i> Print Ticket
            </button>
            <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition no-print">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>
    
    <div class="ticket bg-white p-6 rounded-lg border border-gray-300 max-w-2xl mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><?= SITE_NAME ?></h2>
            <p class="text-gray-600">Parking Ticket</p>
        </div>
        
        <div class="border-t border-b border-gray-300 py-4 my-4">
            <div class="grid grid-cols-2 gap-4">
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
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <h3 class="font-semibold text-gray-600">License Plate</h3>
                    <p class="text-lg"><?= $ticket->license_plate ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Vehicle Type</h3>
                    <p class="text-lg"><?= $ticket->vehicle_type ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Space Number</h3>
                    <p class="text-lg"><?= $ticket->space_number ?> (<?= $ticket->space_type ?>)</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Hourly Rate</h3>
                    <p class="text-lg">$<?= number_format($ticket->hourly_rate, 2) ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
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
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Duration</h3>
                    <p class="text-lg"><?= number_format($ticket->duration_hours, 1) ?> hours</p>
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
        
        <div class="mt-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold text-gray-600">Processed By</h3>
                    <p><?= $ticket->created_by_name ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-600">Date Issued</h3>
                    <p><?= date('M d, Y', strtotime($ticket->entry_time)) ?></p>
                </div>
            </div>
            
            <?php if (!empty($ticket->owner_name) || !empty($ticket->owner_phone)): ?>
                <div class="mt-4 pt-4 border-t border-gray-300">
                    <h3 class="font-semibold text-gray-600 mb-2">Owner Information</h3>
                    <?php if (!empty($ticket->owner_name)): ?>
                        <p><span class="font-semibold">Name:</span> <?= $ticket->owner_name ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($ticket->owner_phone)): ?>
                        <p><span class="font-semibold">Phone:</span> <?= $ticket->owner_phone ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Thank you for using our parking services!</p>
            <p>Please keep this ticket for your records.</p>
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
            <a href="<?= URL_ROOT ?>/agent/processExit/<?= $ticket->id ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md transition inline-block">
                <i class="fas fa-check-circle mr-2"></i> Process Exit
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
