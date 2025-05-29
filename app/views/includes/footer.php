    </main>
    
    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-bold"><?= SITE_NAME ?></h3>
                    <p class="text-sm text-blue-200 mt-1">Efficient parking management solution</p>
                </div>
                
                <div class="flex space-x-4">
                    <a href="<?= URL_ROOT ?>" class="text-blue-200 hover:text-white transition">Home</a>
                    <a href="<?= URL_ROOT ?>/home/about" class="text-blue-200 hover:text-white transition">About</a>
                    <a href="<?= URL_ROOT ?>/home/contact" class="text-blue-200 hover:text-white transition">Contact</a>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm text-blue-200">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                <p class="mt-1">Version <?= APP_VERSION ?></p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= URL_ROOT ?>/assets/js/main.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
