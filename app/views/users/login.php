<?php require APP . 'views/includes/header.php'; ?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h1 class="text-2xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    
    <?php flash('register_success'); ?>
    
    <?php if(isset($errors['login'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $errors['login'] ?>
        </div>
    <?php endif; ?>
    
    <form action="<?= URL_ROOT ?>/users/login" method="POST" class="space-y-4" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <div>
            <label for="email" class="block text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $email ?>">
            <?php if(isset($errors['email'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <label for="password" class="block text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?>">
            <?php if(isset($errors['password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">Login</button>
        </div>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600">Don't have an account? <a href="<?= URL_ROOT ?>/users/register" class="text-blue-600 hover:underline">Register here</a></p>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
