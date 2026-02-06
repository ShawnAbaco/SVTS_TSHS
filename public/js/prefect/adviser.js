// ==========================
// Adviser Management System
// ==========================

console.log("Adviser JS loaded successfully!");

// Notification Manager Class
class NotificationManager {
    constructor() {
        this.notificationModal = document.getElementById('notificationModal');
        this.confirmationModal = document.getElementById('confirmationModal');
        this.notificationMessage = document.getElementById('notificationMessage');
        this.confirmationMessage = document.getElementById('confirmationMessage');
        this.notificationIcon = document.getElementById('notificationIcon');
        this.notificationActions = document.getElementById('notificationActions');
        this.confirmAction = document.getElementById('confirmAction');
        this.cancelAction = document.getElementById('cancelAction');

        this.autoCloseTimeout = null;
        this.confirmCallback = null;
        this.cancelCallback = null;
        
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Confirmation modal
        if (this.confirmAction) {
            this.confirmAction.addEventListener('click', () => {
                if (this.confirmCallback) {
                    this.confirmCallback();
                }
                this.hideConfirmation();
            });
        }

        if (this.cancelAction) {
            this.cancelAction.addEventListener('click', () => {
                if (this.cancelCallback) {
                    this.cancelCallback();
                }
                this.hideConfirmation();
            });
        }

        // Close modals when clicking outside
        if (this.notificationModal) {
            this.notificationModal.addEventListener('click', (e) => {
                if (e.target === this.notificationModal) {
                    this.hideNotification();
                }
            });
        }

        if (this.confirmationModal) {
            this.confirmationModal.addEventListener('click', (e) => {
                if (e.target === this.confirmationModal) {
                    this.hideConfirmation();
                }
            });
        }
    }

    showNotification(message, type = 'info') {
        if (!this.notificationModal) {
            console.error('Notification modal not found');
            return;
        }

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        this.notificationIcon.textContent = icons[type] || icons.info;
        this.notificationMessage.textContent = message;
        this.notificationModal.className = `notification-modal notification-${type}`;

        // Clear any existing timeout
        if (this.autoCloseTimeout) {
            clearTimeout(this.autoCloseTimeout);
        }

        // For success messages, hide OK button and auto-close after 1 second
        if (type === 'success') {
            this.notificationActions.innerHTML = ''; // Remove OK button
            this.notificationModal.style.display = 'flex';

            // Auto-close after 1 second
            this.autoCloseTimeout = setTimeout(() => {
                this.hideNotification();
            }, 1000);
        } else {
            // For other message types, show OK button
            this.notificationActions.innerHTML =
                '<button class="btn-confirm" id="notificationConfirm">OK</button>';

            // Add event listener for the newly created button
            const okButton = document.getElementById('notificationConfirm');
            if (okButton) {
                okButton.addEventListener('click', () => {
                    this.hideNotification();
                });
            }

            this.notificationModal.style.display = 'flex';
        }
    }

    hideNotification() {
        if (this.notificationModal) {
            this.notificationModal.style.display = 'none';
        }
        if (this.autoCloseTimeout) {
            clearTimeout(this.autoCloseTimeout);
            this.autoCloseTimeout = null;
        }
    }

    showConfirmation(message, confirmCallback, cancelCallback = null) {
        if (!this.confirmationModal) {
            console.error('Confirmation modal not found');
            return;
        }

        this.confirmationMessage.textContent = message;
        this.confirmCallback = confirmCallback;
        this.cancelCallback = cancelCallback;
        this.confirmationModal.style.display = 'flex';
    }

    hideConfirmation() {
        if (this.confirmationModal) {
            this.confirmationModal.style.display = 'none';
        }
        this.confirmCallback = null;
        this.cancelCallback = null;
    }
}

// Main Adviser Management Class
class AdviserManager {
    constructor() {
        console.log('Available routes:', window.laravelRoutes);
        
        if (!window.laravelRoutes) {
            console.error('Routes not found! Make sure window.laravelRoutes is defined before loading this script.');
        }
        
        this.notifications = new NotificationManager();
        this.csrfToken = window.csrfToken || this.getCsrfToken();
        this.init();
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    init() {
        console.log("Adviser Manager initialized");
        this.setupEventListeners();
        this.setupModalHandlers();
    }

    // Safe event listener assignment
    safeAddEventListener(elementId, event, callback) {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener(event, callback);
            console.log(`✅ Added ${event} listener to ${elementId}`);
        } else {
            console.warn(`⚠️ Element ${elementId} not found`);
        }
    }

    setupEventListeners() {
        // Search functionality
        this.safeAddEventListener('searchInput', 'input', (e) => this.handleSearch(e));
        
        // Select All checkboxes
        this.safeAddEventListener('selectAll', 'change', (e) => this.handleSelectAll(e));
        this.safeAddEventListener('selectAllArchived', 'change', (e) => this.handleSelectAllArchived(e));
        
        // Action buttons
        this.safeAddEventListener('moveToTrashBtn', 'click', () => this.handleMoveToTrash());
        this.safeAddEventListener('archiveBtn', 'click', () => this.handleOpenArchive());
        this.safeAddEventListener('restoreArchiveBtn', 'click', () => this.handleRestoreArchive());
        this.safeAddEventListener('deleteArchiveBtn', 'click', () => this.handleDeleteArchive());
        this.safeAddEventListener('closeArchive', 'click', () => this.handleCloseArchive());
        
        // Archive search
        this.safeAddEventListener('archiveSearch', 'input', (e) => this.handleArchiveSearch(e));
    }

    setupModalHandlers() {
        // Info Modal
        this.setupInfoModal();
        
        // Edit Modal
        this.setupEditModal();
        
        // Global modal close handlers
        this.setupGlobalModalHandlers();
    }

    // 🔍 Search Functionality
    handleSearch(event) {
        const filter = event.target.value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    // ✅ Select All - Main Table
    handleSelectAll(event) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = event.target.checked;
        });
    }

    // ✅ Select All - Archive Table
    handleSelectAllArchived(event) {
        const archiveCheckboxes = document.querySelectorAll('.archiveCheckbox');
        archiveCheckboxes.forEach(cb => {
            cb.checked = event.target.checked;
        });
    }

    // 🗑️ Move to Trash (Archive)
    async handleMoveToTrash() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

        if (!selectedCheckboxes.length) {
            this.notifications.showNotification('Please select at least one adviser.', 'warning');
            return;
        }

        const adviserIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        this.notifications.showConfirmation(
            `Are you sure you want to archive ${adviserIds.length} adviser(s)?`,
            async () => {
                try {
                    console.log('Moving to trash:', adviserIds);
                    console.log('Route:', window.laravelRoutes.moveToTrash);
                    
                    const response = await fetch(window.laravelRoutes.moveToTrash, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            adviser_ids: adviserIds
                        })
                    });

                    console.log('Response status:', response.status);
                    
                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        this.notifications.showNotification(
                            `${adviserIds.length} adviser(s) moved to archive.`, 'success');
                        
                        // Remove the archived rows from the main table
                        adviserIds.forEach(id => {
                            const row = document.querySelector(`tr[data-adviser-id="${id}"]`);
                            if (row) row.remove();
                        });

                        // Update UI
                        document.getElementById('selectAll').checked = false;

                        // Reload to update counts
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        this.notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.notifications.showNotification('Error moving advisers to archive: ' + error.message, 'error');
                }
            }
        );
    }

    // 🗃️ Archive Modal - Load archived advisers
    async handleOpenArchive() {
        try {
            console.log('Fetching archived advisers from:', window.laravelRoutes.getArchived);
            
            const response = await fetch(window.laravelRoutes.getArchived, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            console.log('Response status:', response.status);
            
            if (!response.ok) {
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorData.error || errorMessage;
                } catch (e) {
                    const errorText = await response.text();
                    errorMessage = errorText || errorMessage;
                }
                throw new Error(errorMessage);
            }

            const archivedAdvisers = await response.json();
            console.log('Archived advisers loaded:', archivedAdvisers);
            
            const archiveTableBody = document.getElementById('archiveTableBody');
            
            if (!archiveTableBody) {
                console.error('Archive table body not found');
                return;
            }
            
            archiveTableBody.innerHTML = '';

            if (!archivedAdvisers || archivedAdvisers.length === 0) {
                archiveTableBody.innerHTML =
                    '<tr><td colspan="8" class="no-data">⚠️ No archived advisers found</td></tr>';
            } else {
                archivedAdvisers.forEach(adviser => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-adviser-id', adviser.adviser_id);
                    row.innerHTML = `
                        <td><input type="checkbox" class="archiveCheckbox" value="${adviser.adviser_id}"></td>
                        <td>${adviser.adviser_id}</td>
                        <td>${adviser.adviser_fname}</td>
                        <td>${adviser.adviser_lname}</td>
                        <td>${adviser.adviser_section}</td>
                        <td>${adviser.adviser_gradelevel}</td>
                        <td><span class="status-badge status-inactive">${adviser.status}</span></td>
                    `;
                    archiveTableBody.appendChild(row);
                });
            }

            const archiveModal = document.getElementById('archiveModal');
            if (archiveModal) {
                archiveModal.style.display = 'flex';
            }
        } catch (error) {
            console.error('Error loading archived advisers:', error);
            this.notifications.showNotification('Error loading archived advisers: ' + error.message, 'error');
        }
    }

    // 🔄 Restore Archived Advisers
    async handleRestoreArchive() {
        const selectedCheckboxes = document.querySelectorAll('.archiveCheckbox:checked');

        if (!selectedCheckboxes.length) {
            this.notifications.showNotification('Please select at least one adviser to restore.', 'warning');
            return;
        }

        const adviserIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        this.notifications.showConfirmation(
            `Are you sure you want to restore ${adviserIds.length} adviser(s)?`,
            async () => {
                try {
                    console.log('Restoring:', adviserIds);
                    console.log('Route:', window.laravelRoutes.restore);
                    
                    const response = await fetch(window.laravelRoutes.restore, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            adviser_ids: adviserIds
                        })
                    });

                    console.log('Response status:', response.status);
                    
                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        this.notifications.showNotification(
                            `${adviserIds.length} adviser(s) restored successfully.`, 'success');
                        
                        // Remove the restored rows from archive table
                        adviserIds.forEach(id => {
                            const row = document.querySelector(`#archiveTableBody tr[data-adviser-id="${id}"]`);
                            if (row) row.remove();
                        });

                        // Reload the page to show restored advisers in main table
                        location.reload();
                    } else {
                        this.notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.notifications.showNotification('Error restoring advisers: ' + error.message, 'error');
                }
            }
        );
    }

    // 🗑️ Delete Archived Advisers Permanently
    async handleDeleteArchive() {
        const selectedCheckboxes = document.querySelectorAll('.archiveCheckbox:checked');

        if (!selectedCheckboxes.length) {
            this.notifications.showNotification(
                'Please select at least one adviser to delete permanently.',
                'warning');
            return;
        }

        const adviserIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        this.notifications.showConfirmation(
            'WARNING: This will permanently delete these advisers. This action cannot be undone!',
            async () => {
                try {
                    console.log('Deleting permanently:', adviserIds);
                    console.log('Route:', window.laravelRoutes.destroyMultiple);
                    
                    const response = await fetch(window.laravelRoutes.destroyMultiple, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            adviser_ids: adviserIds
                        })
                    });

                    console.log('Response status:', response.status);
                    
                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        this.notifications.showNotification(
                            `${adviserIds.length} adviser(s) deleted permanently.`, 'success');
                        
                        // Remove the deleted rows from archive table
                        adviserIds.forEach(id => {
                            const row = document.querySelector(`#archiveTableBody tr[data-adviser-id="${id}"]`);
                            if (row) row.remove();
                        });

                        // If no more archived advisers, show message
                        const remainingRows = document.querySelectorAll('#archiveTableBody tr');
                        if (remainingRows.length === 0) {
                            document.getElementById('archiveTableBody').innerHTML =
                                '<tr><td colspan="8" class="no-data">⚠️ No archived advisers found</td></tr>';
                        }
                    } else {
                        this.notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.notifications.showNotification('Error deleting advisers: ' + error.message, 'error');
                }
            }
        );
    }

    // Close Archive Modal
    handleCloseArchive() {
        const archiveModal = document.getElementById('archiveModal');
        if (archiveModal) {
            archiveModal.style.display = 'none';
        }
    }

    // Archive Search
    handleArchiveSearch(event) {
        const filter = event.target.value.toLowerCase();
        const rows = document.querySelectorAll('#archiveTableBody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    // 👤 Adviser Info Modal Functionality
    setupInfoModal() {
        const infoModal = document.getElementById('infoModal');
        const closeInfoBtn = document.getElementById('closeInfoModalBtn');
        const closeInfoHeaderBtn = document.getElementById('closeInfoBtn');

        if (!infoModal) {
            console.warn('Info modal not found');
            return;
        }

        // Clickable rows to show info modal
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', (e) => {
                // Don't trigger if clicking on checkbox or edit button
                if (e.target.type === 'checkbox' || e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                    return;
                }

                // Get data from the row
                const email = row.getAttribute('data-email');
                const contact = row.getAttribute('data-contact');
                const status = row.getAttribute('data-status');

                // Get other data from table cells
                const cells = row.cells;
                const adviserId = cells[1].textContent;
                const fname = cells[2].textContent;
                const lname = cells[3].textContent;
                const section = cells[4].textContent;
                const gradeLevel = cells[5].textContent;

                // Fill info modal
                const fullName = `${fname} ${lname}`;
                document.getElementById('info_fullname').textContent = fullName;
                document.getElementById('info_section').textContent = section;
                document.getElementById('info_gradelevel').textContent = gradeLevel;

                // Set status badge
                const statusBadge = document.getElementById('info_status');
                statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                statusBadge.className = `profile-status ${status === 'active' ? 'active' : 'inactive'}`;

                // Email with clickable link
                const emailLink = document.getElementById('info_email');
                if (email && email.trim() !== '') {
                    emailLink.textContent = email;
                    emailLink.href = `mailto:${email}`;
                    emailLink.classList.remove('disabled');
                } else {
                    emailLink.textContent = 'Not provided';
                    emailLink.href = '#';
                    emailLink.classList.add('disabled');
                }

                // Contact with clickable link
                const contactLink = document.getElementById('info_contact');
                if (contact && contact.trim() !== '') {
                    contactLink.textContent = contact;
                    contactLink.href = `tel:${contact}`;
                    contactLink.classList.remove('disabled');
                } else {
                    contactLink.textContent = 'Not provided';
                    contactLink.href = '#';
                    contactLink.classList.add('disabled');
                }

                // Show modal
                infoModal.style.display = 'flex';
            });
        });

        // Close info modal
        if (closeInfoBtn) {
            closeInfoBtn.addEventListener('click', () => {
                infoModal.style.display = 'none';
            });
        }

        if (closeInfoHeaderBtn) {
            closeInfoHeaderBtn.addEventListener('click', () => {
                infoModal.style.display = 'none';
            });
        }
    }

    // ✏️ Edit Modal Functionality
    setupEditModal() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = document.getElementById('editModal');
        const closeEditModal = document.getElementById('closeEditModal');
        const cancelEditBtn = document.getElementById('cancelEditBtn');

        if (!editModal) {
            console.warn('Edit modal not found');
            return;
        }

        // 🎯 When "Edit" Button Clicked
        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent row click event
                const row = e.target.closest('tr');
                const adviserId = row.cells[1].textContent;
                const fname = row.cells[2].textContent;
                const lname = row.cells[3].textContent;
                const section = row.cells[4].textContent;
                const gradeLevel = row.cells[5].textContent;
                const email = row.getAttribute('data-email');
                const contact = row.getAttribute('data-contact');

                // 📝 Fill Form
                document.getElementById('edit_adviser_id').value = adviserId;
                document.getElementById('edit_adviser_fname').value = fname;
                document.getElementById('edit_adviser_lname').value = lname;
                document.getElementById('edit_adviser_section').value = section;
                document.getElementById('edit_adviser_gradelevel').value = gradeLevel;
                document.getElementById('edit_adviser_email').value = email || '';
                document.getElementById('edit_adviser_contactinfo').value = contact || '';

                // Set form action dynamically
                const editForm = document.getElementById('editAdviserForm');
                if (editForm) {
                    editForm.action = window.laravelRoutes.update;
                }

                // Show modal
                editModal.style.display = 'flex';
            });
        });

        // ❌ Close / Cancel Modal
        if (closeEditModal) {
            closeEditModal.addEventListener('click', () => {
                editModal.style.display = 'none';
            });
        }

        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', () => {
                editModal.style.display = 'none';
            });
        }
    }

    // Global Modal Close Handlers
    setupGlobalModalHandlers() {
        document.addEventListener('click', (event) => {
            const archiveModal = document.getElementById('archiveModal');
            if (event.target === archiveModal) {
                archiveModal.style.display = 'none';
            }

            const notificationModal = document.getElementById('notificationModal');
            if (event.target === notificationModal) {
                this.notifications.hideNotification();
            }

            const infoModal = document.getElementById('infoModal');
            if (event.target === infoModal) {
                infoModal.style.display = 'none';
            }

            const editModal = document.getElementById('editModal');
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");
    window.adviserManager = new AdviserManager();
});