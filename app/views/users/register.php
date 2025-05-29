<?php require APP . 'views/includes/header.php'; ?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h1 class="text-2xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    
    <form action="<?= URL_ROOT ?>/users/register" method="POST" class="space-y-4" data-validate="true">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <div>
            <label for="name" class="block text-gray-700 mb-1">Name</label>
            <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $name ?>">
            <?php if(isset($errors['name'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['name'] ?></p>
            <?php endif; ?>
        </div>
        
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
            <p class="text-gray-500 text-xs mt-1">Password must be at least 6 characters</p>
        </div>
        
        <div>
            <label for="confirm_password" class="block text-gray-700 mb-1">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?>">
            <?php if(isset($errors['confirm_password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['confirm_password'] ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">Register</button>
        </div>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600">Already have an account? <a href="<?= URL_ROOT ?>/users/login" class="text-blue-600 hover:underline">Login here</a></p>
    </div>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
