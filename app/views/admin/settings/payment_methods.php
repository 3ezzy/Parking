<!-- Payment Methods Section -->
<div id="payment-methods" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">Payment Methods</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Configure the payment methods that are accepted for parking tickets. Inactive payment methods will not be available during checkout.
                </p>
            </div>
        </div>
    </div>
    
    <form action="<?= URL_ROOT ?>/admin/updatePaymentMethods" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <div class="space-y-4">
            <?php foreach ($paymentMethods as $method): ?>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 text-center mr-3 text-xl">
                                <i class="<?= $method->icon ?>"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold"><?= $method->name ?></h3>
                                <p class="text-sm text-gray-500"><?= $method->description ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="switch relative inline-block w-12 h-6 mr-2">
                                <input type="checkbox" name="payment_method_status[<?= $method->id ?>]" value="active" <?= $method->status === 'active' ? 'checked' : '' ?> class="opacity-0 w-0 h-0">
                                <span class="slider absolute cursor-pointer top-0 left-0 right-0 bottom-0 bg-gray-300 rounded-full transition-all before:absolute before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all checked:bg-blue-500 checked:before:transform checked:before:translate-x-6"></span>
                            </label>
                            <span class="text-sm font-medium <?= $method->status === 'active' ? 'text-blue-600' : 'text-gray-500' ?>">
                                <?= $method->status === 'active' ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($method->has_config): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="font-medium text-gray-700 mb-2">Configuration</h4>
                            
                            <?php if ($method->key === 'credit_card'): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="cc_processor" class="block text-gray-700 mb-1 text-sm">Payment Processor</label>
                                        <select id="cc_processor" name="payment_config[<?= $method->id ?>][processor]" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                            <option value="stripe" <?= isset($method->config->processor) && $method->config->processor === 'stripe' ? 'selected' : '' ?>>Stripe</option>
                                            <option value="paypal" <?= isset($method->config->processor) && $method->config->processor === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                                            <option value="manual" <?= isset($method->config->processor) && $method->config->processor === 'manual' ? 'selected' : '' ?>>Manual Processing</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="cc_types" class="block text-gray-700 mb-1 text-sm">Accepted Card Types</label>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="visa" name="payment_config[<?= $method->id ?>][card_types][]" value="visa" <?= isset($method->config->card_types) && in_array('visa', $method->config->card_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="visa" class="ml-2 text-sm text-gray-700">Visa</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="mastercard" name="payment_config[<?= $method->id ?>][card_types][]" value="mastercard" <?= isset($method->config->card_types) && in_array('mastercard', $method->config->card_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="mastercard" class="ml-2 text-sm text-gray-700">Mastercard</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="amex" name="payment_config[<?= $method->id ?>][card_types][]" value="amex" <?= isset($method->config->card_types) && in_array('amex', $method->config->card_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="amex" class="ml-2 text-sm text-gray-700">American Express</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="discover" name="payment_config[<?= $method->id ?>][card_types][]" value="discover" <?= isset($method->config->card_types) && in_array('discover', $method->config->card_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="discover" class="ml-2 text-sm text-gray-700">Discover</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($method->key === 'mobile_payment'): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 mb-1 text-sm">Accepted Mobile Payments</label>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="apple_pay" name="payment_config[<?= $method->id ?>][mobile_types][]" value="apple_pay" <?= isset($method->config->mobile_types) && in_array('apple_pay', $method->config->mobile_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="apple_pay" class="ml-2 text-sm text-gray-700">Apple Pay</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="google_pay" name="payment_config[<?= $method->id ?>][mobile_types][]" value="google_pay" <?= isset($method->config->mobile_types) && in_array('google_pay', $method->config->mobile_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="google_pay" class="ml-2 text-sm text-gray-700">Google Pay</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="samsung_pay" name="payment_config[<?= $method->id ?>][mobile_types][]" value="samsung_pay" <?= isset($method->config->mobile_types) && in_array('samsung_pay', $method->config->mobile_types) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <label for="samsung_pay" class="ml-2 text-sm text-gray-700">Samsung Pay</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <div id="add-payment-method" class="bg-white p-4 rounded-lg border border-dashed border-gray-300 text-center cursor-pointer hover:bg-gray-50 transition">
                <div class="py-4">
                    <i class="fas fa-plus-circle text-blue-500 text-3xl mb-2"></i>
                    <p class="text-gray-700">Add Custom Payment Method</p>
                </div>
            </div>
            
            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Payment Methods
                </button>
            </div>
        </div>
    </form>
    
    <!-- Add Payment Method Modal -->
    <div id="payment-method-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Add Custom Payment Method</h2>
            
            <form id="payment-method-form" action="<?= URL_ROOT ?>/admin/addPaymentMethod" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="payment-method-name" class="block text-gray-700 mb-1 font-medium">Name</label>
                        <input type="text" id="payment-method-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" required>
                    </div>
                    
                    <div>
                        <label for="payment-method-description" class="block text-gray-700 mb-1 font-medium">Description</label>
                        <textarea id="payment-method-description" name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"></textarea>
                    </div>
                    
                    <div>
                        <label for="payment-method-icon" class="block text-gray-700 mb-1 font-medium">Icon</label>
                        <select id="payment-method-icon" name="icon" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="fas fa-credit-card">Credit Card</option>
                            <option value="fas fa-money-bill-wave">Cash</option>
                            <option value="fas fa-mobile-alt">Mobile Payment</option>
                            <option value="fas fa-university">Bank Transfer</option>
                            <option value="fab fa-paypal">PayPal</option>
                            <option value="fab fa-cc-visa">Visa</option>
                            <option value="fab fa-cc-mastercard">Mastercard</option>
                            <option value="fab fa-cc-amex">American Express</option>
                            <option value="fab fa-bitcoin">Bitcoin</option>
                            <option value="fas fa-qrcode">QR Code</option>
                        </select>
                        <div class="mt-2 text-center">
                            <span id="selected-payment-icon-preview" class="text-3xl">
                                <i class="fas fa-credit-card"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="payment-method-status" class="block text-gray-700 mb-1 font-medium">Status</label>
                        <select id="payment-method-status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" id="payment-method-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle switch functionality
        const switches = document.querySelectorAll('.switch input[type="checkbox"]');
        switches.forEach(switchElem => {
            switchElem.addEventListener('change', function() {
                const statusLabel = this.parentElement.nextElementSibling;
                if (this.checked) {
                    statusLabel.textContent = 'Active';
                    statusLabel.classList.remove('text-gray-500');
                    statusLabel.classList.add('text-blue-600');
                } else {
                    statusLabel.textContent = 'Inactive';
                    statusLabel.classList.remove('text-blue-600');
                    statusLabel.classList.add('text-gray-500');
                }
            });
        });
        
        // Add Payment Method modal functionality
        const addPaymentMethodBtn = document.getElementById('add-payment-method');
        const paymentMethodModal = document.getElementById('payment-method-modal');
        const paymentMethodCancel = document.getElementById('payment-method-cancel');
        const paymentMethodIcon = document.getElementById('payment-method-icon');
        const selectedPaymentIconPreview = document.getElementById('selected-payment-icon-preview');
        
        // Update icon preview when selection changes
        if (paymentMethodIcon && selectedPaymentIconPreview) {
            paymentMethodIcon.addEventListener('change', function() {
                selectedPaymentIconPreview.innerHTML = `<i class="${this.value}"></i>`;
            });
        }
        
        if (addPaymentMethodBtn && paymentMethodModal) {
            addPaymentMethodBtn.addEventListener('click', function() {
                paymentMethodModal.classList.remove('hidden');
            });
        }
        
        if (paymentMethodCancel && paymentMethodModal) {
            paymentMethodCancel.addEventListener('click', function() {
                paymentMethodModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            paymentMethodModal.addEventListener('click', function(e) {
                if (e.target === paymentMethodModal) {
                    paymentMethodModal.classList.add('hidden');
                }
            });
        }
    });
</script>
