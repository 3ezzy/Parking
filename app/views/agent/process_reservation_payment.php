<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $reservation->id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reservation
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Reservation Information</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Reservation #</h3>
                            <p class="text-lg"><?= $reservation->id ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Status</h3>
                            <p class="text-lg">
                                <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm"><?= ucfirst($reservation->status) ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Customer Name</h3>
                            <p class="text-lg"><?= $reservation->customer_name ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Contact</h3>
                            <p class="text-lg">
                                <?php if (!empty($reservation->customer_phone)): ?>
                                    <span class="block"><?= $reservation->customer_phone ?></span>
                                <?php endif; ?>
                                <?php if (!empty($reservation->customer_email)): ?>
                                    <span class="block text-sm text-gray-500"><?= $reservation->customer_email ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Space Number</h3>
                            <p class="text-lg"><?= $reservation->space_number ?> (<?= $reservation->space_type ?>)</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Hourly Rate</h3>
                            <p class="text-lg">$<?= number_format($reservation->hourly_rate, 2) ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Start Time</h3>
                            <p class="text-lg"><?= date('M d, Y H:i', strtotime($reservation->start_time)) ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">End Time</h3>
                            <p class="text-lg"><?= date('M d, Y H:i', strtotime($reservation->end_time)) ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-600">Duration</h3>
                            <p class="text-lg"><?= number_format($duration_hours, 1) ?> hours</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-600">Amount Due</h3>
                            <p class="text-lg font-bold text-green-600">$<?= number_format($calculated_amount, 2) ?></p>
                        </div>
                    </div>

                    <?php if (!empty($reservation->vehicle_type_name) || !empty($reservation->license_plate)): ?>
                        <div class="pt-4 border-t border-gray-300">
                            <h3 class="font-semibold text-gray-600 mb-2">Vehicle Information</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <?php if (!empty($reservation->license_plate)): ?>
                                    <div>
                                        <h4 class="text-gray-600">License Plate</h4>
                                        <p class="font-medium"><?= $reservation->license_plate ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($reservation->vehicle_type_name)): ?>
                                    <div>
                                        <h4 class="text-gray-600">Vehicle Type</h4>
                                        <p><?= $reservation->vehicle_type_name ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Payment Processing</h2>
                
                <form action="<?= URL_ROOT ?>/agent/processReservationPayment/<?= $reservation->id ?>" method="POST">
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
                            <label for="amount_paid" class="block text-gray-700 mb-1 font-medium">Amount Paid ($)</label>
                            <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['amount_paid']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $formData['amount_paid'] ?? number_format($calculated_amount, 2) ?>">
                            <p class="text-gray-500 text-xs mt-1">Minimum amount: $<?= number_format($calculated_amount, 2) ?></p>
                        </div>
                        
                        <div>
                            <label for="payment_method" class="block text-gray-700 mb-1 font-medium">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['payment_method']) ? 'border-red-500' : 'border-gray-300' ?>">
                                <option value="">Select Payment Method</option>
                                <option value="cash" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="credit_card" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                                <option value="debit_card" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'debit_card' ? 'selected' : '' ?>>Debit Card</option>
                                <option value="mobile_payment" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'mobile_payment' ? 'selected' : '' ?>>Mobile Payment</option>
                                <option value="bank_transfer" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-gray-700 mb-1 font-medium">Payment Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" placeholder="Any additional notes about the payment..."><?= $formData['notes'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-check-circle mr-2"></i> Process Payment & Complete Reservation
                            </button>
                        </div>
                        
                        <div class="pt-2">
                            <a href="<?= URL_ROOT ?>/agent/viewReservation/<?= $reservation->id ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-eye mr-2"></i> View Reservation Details
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-calculate change if amount paid is more than due
    document.getElementById('amount_paid').addEventListener('input', function() {
        const amountPaid = parseFloat(this.value) || 0;
        const amountDue = <?= $calculated_amount ?>;
        
        if (amountPaid > amountDue) {
            const change = amountPaid - amountDue;
            if (!document.getElementById('change-info')) {
                const changeDiv = document.createElement('div');
                changeDiv.id = 'change-info';
                changeDiv.className = 'mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm';
                changeDiv.innerHTML = `<strong>Change to give:</strong> $${change.toFixed(2)}`;
                this.parentNode.appendChild(changeDiv);
            } else {
                document.getElementById('change-info').innerHTML = `<strong>Change to give:</strong> $${change.toFixed(2)}`;
            }
        } else {
            const changeInfo = document.getElementById('change-info');
            if (changeInfo) {
                changeInfo.remove();
            }
        }
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?> 