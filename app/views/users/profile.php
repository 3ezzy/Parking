<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold text-blue-800 mb-4"><?= $title ?></h1>
    
    <?php flash('profile_success'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-gray-100 p-4 rounded-lg text-center">
                <div class="w-24 h-24 bg-blue-800 rounded-full mx-auto mb-3 flex items-center justify-center">
                    <span class="text-white text-2xl font-bold"><?= substr($user->name, 0, 1) ?></span>
                </div>
                <h2 class="text-xl font-semibold"><?= $user->name ?></h2>
                <p class="text-gray-600"><?= $user->email ?></p>
                <p class="text-sm bg-blue-100 text-blue-800 rounded px-2 py-1 mt-2 inline-block capitalize"><?= $user->role ?></p>
                <p class="text-gray-500 text-sm mt-2">Joined: <?= date('M d, Y', strtotime($user->created_at)) ?></p>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-gray-100 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold mb-3">Update Profile</h3>
                
                <form action="<?= URL_ROOT ?>/users/profile" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div>
                        <label for="name" class="block text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?>" value="<?= $user->name ?>">
                        <?php if(isset($errors['name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['name'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg bg-gray-100 border-gray-300" value="<?= $user->email ?>" disabled>
                        <p class="text-gray-500 text-xs mt-1">Email cannot be changed</p>
                    </div>
                    
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">Update Profile</button>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-3">Change Password</h3>
                
                <form action="<?= URL_ROOT ?>/users/profile" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div>
                        <label for="current_password" class="block text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['current_password']) ? 'border-red-500' : 'border-gray-300' ?>">
                        <?php if(isset($errors['current_password'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['current_password'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="new_password" class="block text-gray-700 mb-1">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['new_password']) ? 'border-red-500' : 'border-gray-300' ?>">
                        <?php if(isset($errors['new_password'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['new_password'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?= isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?>">
                        <?php if(isset($errors['confirm_password'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold text-blue-800 mb-4">Recent Activity</h2>
    
    <?php if(empty($activities)): ?>
        <p class="text-gray-700">No recent activity found.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Activity</th>
                        <th class="px-4 py-2 border">IP Address</th>
                        <th class="px-4 py-2 border">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($activities as $activity): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $activity->activity ?></td>
                            <td class="px-4 py-2 border"><?= $activity->ip_address ?></td>
                            <td class="px-4 py-2 border"><?= date('M d, Y H:i', strtotime($activity->timestamp)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP . 'views/includes/footer.php'; ?>
