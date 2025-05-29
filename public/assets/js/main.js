/**
 * Main JavaScript file for Parking Management System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initFlashMessages();
    initParkingMap();
    initFormValidation();
    
    // Add event listeners for dynamic content
    setupEventListeners();
});

/**
 * Initialize flash messages (auto hide after delay)
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert');
    
    flashMessages.forEach(message => {
        // Auto hide flash messages after 5 seconds
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
}

/**
 * Initialize the parking map visualization
 */
function initParkingMap() {
    const parkingMap = document.getElementById('parking-map');
    
    if (!parkingMap) return;
    
    // If the map is interactive, add click handlers
    const parkingSpaces = parkingMap.querySelectorAll('.parking-space');
    
    parkingSpaces.forEach(space => {
        space.addEventListener('click', function() {
            const spaceId = this.dataset.id;
            const spaceStatus = this.dataset.status;
            
            // If this is an admin or agent view, show action modal
            if (document.body.classList.contains('admin-view') || document.body.classList.contains('agent-view')) {
                showSpaceActionModal(spaceId, spaceStatus);
            } 
            // If this is a user view and the space is available, show reservation modal
            else if (spaceStatus === 'available') {
                showReservationModal(spaceId);
            }
        });
    });
}

/**
 * Show the space action modal for admin/agent
 */
function showSpaceActionModal(spaceId, spaceStatus) {
    const modal = document.getElementById('space-action-modal');
    
    if (!modal) return;
    
    // Update modal content based on space status
    const modalTitle = modal.querySelector('.modal-title');
    const modalContent = modal.querySelector('.modal-content');
    const spaceIdInput = modal.querySelector('input[name="space_id"]');
    
    modalTitle.textContent = `Space #${spaceId}`;
    spaceIdInput.value = spaceId;
    
    // Show different options based on current status
    if (spaceStatus === 'available') {
        modalContent.innerHTML = `
            <div class="space-y-4">
                <p class="text-green-600">This space is currently available.</p>
                <div class="flex space-x-2">
                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" onclick="assignVehicle(${spaceId})">Assign Vehicle</button>
                    <button type="button" class="px-4 py-2 bg-yellow-600 text-white rounded" onclick="blockSpace(${spaceId})">Block Space</button>
                </div>
            </div>
        `;
    } else if (spaceStatus === 'occupied') {
        modalContent.innerHTML = `
            <div class="space-y-4">
                <p class="text-red-600">This space is currently occupied.</p>
                <div class="flex space-x-2">
                    <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" onclick="releaseVehicle(${spaceId})">Release Vehicle</button>
                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" onclick="viewDetails(${spaceId})">View Details</button>
                </div>
            </div>
        `;
    } else if (spaceStatus === 'reserved') {
        modalContent.innerHTML = `
            <div class="space-y-4">
                <p class="text-yellow-600">This space is currently reserved.</p>
                <div class="flex space-x-2">
                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" onclick="checkInReservation(${spaceId})">Check In</button>
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded" onclick="cancelReservation(${spaceId})">Cancel Reservation</button>
                </div>
            </div>
        `;
    }
    
    // Show the modal
    modal.classList.remove('hidden');
}

/**
 * Show reservation modal for users
 */
function showReservationModal(spaceId) {
    const modal = document.getElementById('reservation-modal');
    
    if (!modal) return;
    
    // Update modal content
    const spaceIdInput = modal.querySelector('input[name="space_id"]');
    spaceIdInput.value = spaceId;
    
    // Show the modal
    modal.classList.remove('hidden');
}

/**
 * Close any modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Add error message if it doesn't exist
                    let errorMessage = field.nextElementSibling;
                    
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('p');
                        errorMessage.classList.add('text-red-500', 'text-sm', 'mt-1', 'error-message');
                        errorMessage.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMessage, field.nextSibling);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    
                    // Remove error message if it exists
                    const errorMessage = field.nextElementSibling;
                    
                    if (errorMessage && errorMessage.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Set up event listeners for dynamic content
 */
function setupEventListeners() {
    // Delegate event handlers for dynamically loaded content
    document.addEventListener('click', function(event) {
        // Close modal when clicking outside
        if (event.target.classList.contains('modal-overlay')) {
            const modalId = event.target.dataset.modal;
            closeModal(modalId);
        }
        
        // Print ticket button
        if (event.target.classList.contains('print-ticket-btn')) {
            printTicket();
        }
    });
}

/**
 * Print ticket
 */
function printTicket() {
    window.print();
}

/**
 * Dynamic functions that will be implemented with AJAX calls
 * These are placeholders that will be completed in later development
 */
function assignVehicle(spaceId) {
    // Will be implemented with AJAX
    console.log('Assign vehicle to space', spaceId);
}

function blockSpace(spaceId) {
    // Will be implemented with AJAX
    console.log('Block space', spaceId);
}

function releaseVehicle(spaceId) {
    // Will be implemented with AJAX
    console.log('Release vehicle from space', spaceId);
}

function viewDetails(spaceId) {
    // Will be implemented with AJAX
    console.log('View details for space', spaceId);
}

function checkInReservation(spaceId) {
    // Will be implemented with AJAX
    console.log('Check in reservation for space', spaceId);
}

function cancelReservation(spaceId) {
    // Will be implemented with AJAX
    console.log('Cancel reservation for space', spaceId);
}
