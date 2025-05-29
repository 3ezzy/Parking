<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('settings_success'); ?>
    <?php flash('settings_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-1">
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm sticky top-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-4">Settings Menu</h2>
                
                <nav class="space-y-1">
                    <a href="#general-settings" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-cog w-5 text-center mr-2"></i> General Settings
                    </a>
                    <a href="#rate-settings" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-dollar-sign w-5 text-center mr-2"></i> Parking Rates
                    </a>
                    <a href="#space-types" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-car w-5 text-center mr-2"></i> Space Types
                    </a>
                    <a href="#vehicle-types" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-truck w-5 text-center mr-2"></i> Vehicle Types
                    </a>
                    <a href="#payment-methods" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-credit-card w-5 text-center mr-2"></i> Payment Methods
                    </a>
                    <a href="#system-backup" class="settings-nav-item block px-3 py-2 rounded-lg transition flex items-center hover:bg-blue-100 hover:text-blue-800">
                        <i class="fas fa-database w-5 text-center mr-2"></i> Backup & Restore
                    </a>
                </nav>
            </div>
        </div>
        
        <div class="md:col-span-3">
            <!-- General Settings Section -->
            <div id="general-settings" class="settings-section bg-gray-50 p-6 rounded-lg shadow-sm mb-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">General Settings</h2>
                
                <form action="<?= URL_ROOT ?>/admin/updateSettings" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="settings_type" value="general">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="site_name" class="block text-gray-700 mb-1 font-medium">Site Name</label>
                            <input type="text" id="site_name" name="site_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->site_name ?? SITE_NAME ?>">
                        </div>
                        
                        <div>
                            <label for="company_name" class="block text-gray-700 mb-1 font-medium">Company Name</label>
                            <input type="text" id="company_name" name="company_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->company_name ?? '' ?>">
                        </div>
                        
                        <div>
                            <label for="contact_email" class="block text-gray-700 mb-1 font-medium">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->contact_email ?? '' ?>">
                        </div>
                        
                        <div>
                            <label for="contact_phone" class="block text-gray-700 mb-1 font-medium">Contact Phone</label>
                            <input type="text" id="contact_phone" name="contact_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->contact_phone ?? '' ?>">
                        </div>
                        
                        <div>
                            <label for="business_hours" class="block text-gray-700 mb-1 font-medium">Business Hours</label>
                            <input type="text" id="business_hours" name="business_hours" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->business_hours ?? '24/7' ?>">
                            <p class="text-gray-500 text-xs mt-1">Example: Monday-Friday: 8AM-8PM, Weekends: 10AM-6PM</p>
                        </div>
                        
                        <div>
                            <label for="currency_symbol" class="block text-gray-700 mb-1 font-medium">Currency Symbol</label>
                            <input type="text" id="currency_symbol" name="currency_symbol" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->currency_symbol ?? '$' ?>">
                        </div>
                        
                        <div>
                            <label for="lost_ticket_fee" class="block text-gray-700 mb-1 font-medium">Lost Ticket Fee</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                    <?= $settings->currency_symbol ?? '$' ?>
                                </span>
                                <input type="number" id="lost_ticket_fee" name="lost_ticket_fee" step="0.01" min="0" class="flex-1 px-4 py-2 border rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $settings->lost_ticket_fee ?? '20.00' ?>">
                            </div>
                        </div>
                        
                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i> Save General Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Include other settings sections using PHP includes to keep the file size manageable -->
            <?php require APP . 'views/admin/settings/rates.php'; ?>
            <?php require APP . 'views/admin/settings/space_types.php'; ?>
            <?php require APP . 'views/admin/settings/vehicle_types.php'; ?>
            <?php require APP . 'views/admin/settings/payment_methods.php'; ?>
            <?php require APP . 'views/admin/settings/backup.php'; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight active section in navigation
        const navItems = document.querySelectorAll('.settings-nav-item');
        const sections = document.querySelectorAll('.settings-section');
        
        function setActiveSection() {
            let scrollPosition = window.scrollY + 100;
            
            sections.forEach(section => {
                if (scrollPosition >= section.offsetTop) {
                    const id = section.getAttribute('id');
                    
                    navItems.forEach(item => {
                        item.classList.remove('bg-blue-100', 'text-blue-800', 'font-semibold');
                        
                        if (item.getAttribute('href') === '#' + id) {
                            item.classList.add('bg-blue-100', 'text-blue-800', 'font-semibold');
                        }
                    });
                }
            });
        }
        
        // Smooth scroll to sections when clicking nav items
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                window.scrollTo({
                    top: targetSection.offsetTop - 20,
                    behavior: 'smooth'
                });
            });
        });
        
        window.addEventListener('scroll', setActiveSection);
        setActiveSection();
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
