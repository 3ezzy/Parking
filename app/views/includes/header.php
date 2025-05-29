<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? SITE_NAME ?></title>
    <meta name="description" content="<?= $description ?? 'Parking Management System' ?>">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="<?= URL_ROOT ?>" class="text-xl font-bold"><?= SITE_NAME ?></a>
                
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
                
                <div class="hidden md:flex space-x-4">
                    <a href="<?= URL_ROOT ?>" class="hover:text-blue-200 transition">Home</a>
                    <a href="<?= URL_ROOT ?>/home/about" class="hover:text-blue-200 transition">About</a>
                    <a href="<?= URL_ROOT ?>/home/contact" class="hover:text-blue-200 transition">Contact</a>
                    
                    <?php if(isLoggedIn()): ?>
                        <?php if(hasRole(ADMIN_ROLE)): ?>
                            <a href="<?= URL_ROOT ?>/admin/dashboard" class="hover:text-blue-200 transition">Admin Dashboard</a>
                        <?php elseif(hasRole(AGENT_ROLE)): ?>
                            <a href="<?= URL_ROOT ?>/agent/dashboard" class="hover:text-blue-200 transition">Agent Dashboard</a>
                        <?php endif; ?>
                        <a href="<?= URL_ROOT ?>/users/logout" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition">Logout</a>
                    <?php else: ?>
                        <a href="<?= URL_ROOT ?>/users/login" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded transition">Login</a>
                        <a href="<?= URL_ROOT ?>/users/register" class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded transition">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden py-2 mt-2">
                <a href="<?= URL_ROOT ?>" class="block py-2 hover:text-blue-200 transition">Home</a>
                <a href="<?= URL_ROOT ?>/home/about" class="block py-2 hover:text-blue-200 transition">About</a>
                <a href="<?= URL_ROOT ?>/home/contact" class="block py-2 hover:text-blue-200 transition">Contact</a>
                
                <?php if(isLoggedIn()): ?>
                    <?php if(hasRole(ADMIN_ROLE)): ?>
                        <a href="<?= URL_ROOT ?>/admin/dashboard" class="block py-2 hover:text-blue-200 transition">Admin Dashboard</a>
                    <?php elseif(hasRole(AGENT_ROLE)): ?>
                        <a href="<?= URL_ROOT ?>/agent/dashboard" class="block py-2 hover:text-blue-200 transition">Agent Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= URL_ROOT ?>/users/logout" class="block py-2 text-red-400 hover:text-red-300 transition">Logout</a>
                <?php else: ?>
                    <a href="<?= URL_ROOT ?>/users/login" class="block py-2 text-blue-400 hover:text-blue-300 transition">Login</a>
                    <a href="<?= URL_ROOT ?>/users/register" class="block py-2 text-green-400 hover:text-green-300 transition">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Flash messages -->
    <div class="container mx-auto px-4 mt-4">
        <?php flash('success'); ?>
        <?php flash('error', '', 'alert alert-danger'); ?>
    </div>
    
    <!-- Main content -->
    <main class="container mx-auto px-4 py-6 flex-grow">
