<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="flex items-center">
            <div class="w-5 h-5 bg-green-500 rounded-full mr-2"></div>
            <span>Available</span>
        </div>
        <div class="flex items-center">
            <div class="w-5 h-5 bg-red-500 rounded-full mr-2"></div>
            <span>Occupied</span>
        </div>
        <div class="flex items-center">
            <div class="w-5 h-5 bg-yellow-500 rounded-full mr-2"></div>
            <span>Reserved</span>
        </div>
        <div class="flex items-center">
            <div class="w-5 h-5 bg-gray-500 rounded-full mr-2"></div>
            <span>Maintenance</span>
        </div>
        <div class="flex items-center">
            <div class="w-5 h-5 border-l-4 border-blue-500 bg-white mr-2"></div>
            <span>Handicap</span>
        </div>
        <div class="flex items-center">
            <div class="w-5 h-5 border-l-4 border-purple-500 bg-white mr-2"></div>
            <span>VIP</span>
        </div>
    </div>
    
    <div id="parking-map" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach ($spaces as $space): ?>
            <?php 
                $statusClass = '';
                $statusText = '';
                
                switch ($space->status) {
                    case 'available':
                        $statusClass = 'available';
                        $statusText = 'Available';
                        break;
                    case 'occupied':
                        $statusClass = 'occupied';
                        $statusText = 'Occupied';
                        break;
                    case 'reserved':
                        $statusClass = 'reserved';
                        $statusText = 'Reserved';
                        break;
                    case 'maintenance':
                        $statusClass = 'bg-gray-300 text-gray-700 border-2 border-gray-400';
                        $statusText = 'Maintenance';
                        break;
                }
                
                $typeClass = '';
                
                if ($space->type_name === 'Handicap') {
                    $typeClass = 'handicap';
                } elseif ($space->type_name === 'VIP') {
                    $typeClass = 'vip';
                }
            ?>
            
            <div class="parking-space <?= $statusClass ?> <?= $typeClass ?>" data-id="<?= $space->id ?>" data-status="<?= $space->status ?>">
                <div class="space-number"><?= $space->space_number ?></div>
                <div class="space-type"><?= $space->type_name ?></div>
                <?php if ($space->status === 'occupied' || $space->status === 'reserved'): ?>
                    <div class="vehicle-info">
                        <?= $statusText ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Space Action Modal -->
<div id="space-action-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50" data-modal="space-action-modal"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content p-6">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-blue-800 modal-title">Space Details</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeModal('space-action-modal')">
                    <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                </button>
            </div>
            
            <div class="modal-body py-4">
                <!-- Modal content will be dynamically inserted here -->
            </div>
        </div>
    </div>
</div>

<script>
    // Add specific code for the parking map page
    document.addEventListener('DOMContentLoaded', function() {
        const parkingSpaces = document.querySelectorAll('.parking-space');
        const modal = document.getElementById('space-action-modal');
        const modalBody = modal.querySelector('.modal-body');
        
        parkingSpaces.forEach(space => {
            space.addEventListener('click', async function() {
                const spaceId = this.dataset.id;
                const spaceStatus = this.dataset.status;
                
                try {
                    // Fetch space details
                    const response = await fetch(`<?= URL_ROOT ?>/agent/getSpaceDetails/${spaceId}`);
                    const data = await response.json();
                    
                    // Update modal content
                    modalBody.innerHTML = `
                        <div class="space-details">
                            <p><strong>Space Number:</strong> ${data.space_number}</p>
                            <p><strong>Type:</strong> ${data.type_name}</p>
                            <p><strong>Status:</strong> <span class="status-${data.status}">${data.status}</span></p>
                            ${data.vehicle ? `
                                <div class="mt-4">
                                    <h4 class="font-semibold mb-2">Vehicle Details</h4>
                                    <p><strong>License Plate:</strong> ${data.vehicle.license_plate}</p>
                                    <p><strong>Type:</strong> ${data.vehicle.type_name}</p>
                                    ${data.vehicle.owner_name ? `<p><strong>Owner:</strong> ${data.vehicle.owner_name}</p>` : ''}
                                    ${data.vehicle.owner_phone ? `<p><strong>Phone:</strong> ${data.vehicle.owner_phone}</p>` : ''}
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    // Show modal
                    modal.classList.remove('hidden');
                } catch (error) {
                    console.error('Error fetching space details:', error);
                    modalBody.innerHTML = '<p class="text-red-500">Error loading space details</p>';
                }
            });
        });

        // Close modal when clicking outside or on close button
        modal.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                closeModal('space-action-modal');
            }
        });

        // Close modal when pressing escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('space-action-modal');
            }
        });
    });

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
    }
</script>

<?php require APP . 'views/includes/footer.php'; ?>
