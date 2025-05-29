<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/tickets" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Tickets
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Ticket Information</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-600">Ticket #</h3>
                        <p class="text-lg"><?= $ticket->id ?></p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-600">Status</h3>
                        <p class="text-lg">
                            <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Active</span>
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
                        <h3 class="font-semibold text-gray-600">Current Time</h3>
                        <p class="text-lg"><?= date('M d, Y H:i') ?></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <h3 class="font-semibold text-gray-600">Duration</h3>
                        <p class="text-lg"><?= number_format($ticket->duration_hours, 1) ?> hours</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-600">Amount Due</h3>
                        <p class="text-2xl font-bold text-green-600">$<?= number_format($ticket->calculated_amount, 2) ?></p>
                    </div>
                </div>
                
                <?php if (!empty($ticket->owner_name) || !empty($ticket->owner_phone)): ?>
                    <div class="border-t border-blue-200 pt-4 mt-4">
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
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Process Payment</h2>
                
                <form action="<?= URL_ROOT ?>/admin/closeTicket/<?= $ticket->id ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <p class="font-bold">Please correct the following errors:</p>
                            <ul class="list-disc pl-5">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="amount_paid" class="block text-gray-700 mb-1 font-medium">Amount Paid ($) *</label>
                            <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['amount_paid']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= number_format($ticket->calculated_amount, 2) ?>">
                            <p class="text-gray-500 text-xs mt-1">Enter the amount paid by the customer</p>
                        </div>
                        
                        <div>
                            <label for="payment_method" class="block text-gray-700 mb-1 font-medium">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="mobile_payment">Mobile Payment</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-gray-700 mb-1 font-medium">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"></textarea>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-check-circle mr-2"></i> Complete Payment & Close Ticket
                            </button>
                        </div>
                        
                        <div class="pt-2">
                            <a href="<?= URL_ROOT ?>/admin/ticketDetails/<?= $ticket->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-eye mr-2"></i> View Ticket Details
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
