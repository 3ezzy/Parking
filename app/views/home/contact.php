<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    <p class="text-gray-700 mb-6"><?= $description ?></p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">Contact Information</h2>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="text-blue-600 mr-3">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Address</h3>
                        <p class="text-gray-600">123 Parking Avenue, City Center<br>Postal Code: 12345</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="text-blue-600 mr-3">
                        <i class="fas fa-phone text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Phone</h3>
                        <p class="text-gray-600">+1 (234) 567-8900</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="text-blue-600 mr-3">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Email</h3>
                        <p class="text-gray-600">info@parkingmanagement.com</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="text-blue-600 mr-3">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Operating Hours</h3>
                        <p class="text-gray-600">Monday - Friday: 8:00 AM - 8:00 PM<br>Saturday & Sunday: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h2 class="text-2xl font-semibold text-blue-700 mb-4">Connect With Us</h2>
                <div class="flex space-x-4">
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-blue-400 hover:text-blue-600 text-2xl"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-pink-600 hover:text-pink-800 text-2xl"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-blue-700 hover:text-blue-900 text-2xl"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div>
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">Send Us a Message</h2>
            
            <?php flash('contact_success'); ?>
            
            <form action="<?= URL_ROOT ?>/home/contact" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div>
                    <label for="name" class="block text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $name ?? '' ?>">
                    <?php if (isset($errors['name'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['name'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $email ?? '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="message" class="block text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['message']) ? 'border-red-500' : 'border-gray-300' ?>"><?= $message ?? '' ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['message'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
