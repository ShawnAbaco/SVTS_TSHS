// Main Layout JavaScript
class PrefectLayout {
    constructor() {
        this.init();
    }

    init() {
        this.initializeSidebar();
        this.initializeEventListeners();
        this.fetchProfileInfo();
        this.initializeNotificationHandlers();
    }

    // Sidebar functionality
    initializeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        let isSidebarCollapsed = false;

        // Toggle sidebar visibility
        const toggleSidebar = () => {
            isSidebarCollapsed = !isSidebarCollapsed;

            if (isSidebarCollapsed) {
                sidebar.classList.add('collapsed');
                sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            } else {
                sidebar.classList.remove('collapsed');
                sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }

            localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
        };

        // Initialize sidebar state from localStorage
        const initializeSidebarState = () => {
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState !== null) {
                isSidebarCollapsed = savedState === 'true';

                if (isSidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            }
        };

        // Add event listener to toggle button
        sidebarToggle.addEventListener('click', toggleSidebar);
        initializeSidebarState();

        // Initialize sidebar tooltips
        this.initializeSidebarTooltips();
    }

    initializeSidebarTooltips() {
        const sidebar = document.querySelector('.sidebar');
        const menuItems = document.querySelectorAll('.sidebar li');
        const tooltip = document.createElement('div');
        tooltip.className = 'menu-tooltip';
        document.body.appendChild(tooltip);

        menuItems.forEach(item => {
            const text = item.querySelector('a span')?.innerText || item.querySelector('a').title;
            item.dataset.tooltip = text;

            item.addEventListener('mousemove', (e) => {
                if (sidebar.classList.contains('collapsed')) {
                    tooltip.innerText = item.dataset.tooltip;
                    tooltip.style.left = e.pageX + 15 + 'px';
                    tooltip.style.top = e.pageY + 15 + 'px';
                    tooltip.style.opacity = '1';
                }
            });

            item.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        });
    }

    // Event listeners
    initializeEventListeners() {
        // Profile dropdown
        document.addEventListener('click', (event) => {
            const profileDropdown = document.getElementById('profileDropdown');
            const userInfo = document.querySelector('.user-info');
            
            if (!userInfo.contains(event.target) && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });

        // Modal close events
        this.initializeModalEvents();
        
        // Password form submission
        this.initializePasswordForm();
        
        // Profile image handling
        this.initializeProfileImageHandling();
    }

    initializeModalEvents() {
        // Close profile modal when clicking the X
        document.querySelector('#profileSettingsModal .close').addEventListener('click', () => {
            this.closeProfileModal();
        });

        // Close profile modal when clicking outside
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('profileSettingsModal');
            if (event.target === modal) {
                this.closeProfileModal();
            }
        });

        // Close logout modal when clicking outside
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                this.closeLogoutModal();
            }
        });
    }

    initializePasswordForm() {
        const form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handlePasswordChange(e));
        }
    }

    initializeProfileImageHandling() {
        const profileImageInput = document.getElementById('profile-image-input');
        if (profileImageInput) {
            profileImageInput.addEventListener('change', (e) => this.handleProfileImageUpload(e));
        }
    }

    initializeNotificationHandlers() {
        // Add click event listeners to notification items
        const notificationItems = document.querySelectorAll('.modalsbell-item[data-type]');
        notificationItems.forEach(item => {
            item.addEventListener('click', this.handleNotificationClick);
        });

        // Close notification dropdown when clicking outside
        document.addEventListener('click', (event) => {
            const notificationContainer = document.querySelector('.modalsbell-container');
            const dropdown = document.getElementById('notificationDropdown');

            if (!notificationContainer.contains(event.target) && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });

        // Close dropdown with Escape key
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('notificationDropdown');
                dropdown.classList.remove('show');
            }
        });
    }

    // Profile functionality
    async fetchProfileInfo() {
        try {
            const response = await fetch(ROUTES.profileInfo, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch profile data');
            }

            const data = await response.json();
            this.updateProfileData(data);
            
        } catch (error) {
            console.error('Error fetching profile info:', error);
            this.showNotification('Failed to load profile information', 'error');
        }
    }

    updateProfileData(data) {
        // Update profile modal
        document.getElementById('profile-name').textContent = data.name || 'Not set';
        document.getElementById('profile-email').textContent = data.email || 'Not set';
        document.getElementById('profile-gender').textContent = data.gender || 'Not set';
        document.getElementById('profile-contact').textContent = data.contact_number || 'Not set';
        document.getElementById('profile-status').textContent = data.status || 'Active';
        document.getElementById('user-email').textContent = data.email || '';
        
        // Update header
        document.getElementById('header-user-name').textContent = data.name || 'User';
        
        // Update profile images if available
        if (data.profile_image) {
            document.getElementById('profile-image-preview').src = data.profile_image;
            document.getElementById('header-profile-image').src = data.profile_image;
        }
    }

    // Modal functions
    openProfileModal(tab = 'profile-tab') {
        document.getElementById('profileSettingsModal').style.display = 'flex';
        if (tab) {
            this.openTab(tab);
        }
        // Refresh profile data when opening modal
        this.fetchProfileInfo();
    }

    closeProfileModal() {
        document.getElementById('profileSettingsModal').style.display = 'none';
    }

    // Tab functionality
    openTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(tab => {
            tab.classList.remove('active');
        });

        // Remove active class from all tab buttons
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });

        // Show the selected tab content
        document.getElementById(tabName).classList.add('active');

        // Activate the clicked tab button
        event.currentTarget.classList.add('active');
    }

    // Password functionality
    togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    async sendVerificationCode() {
        try {
            const button = document.getElementById('send-code-btn') || document.getElementById('resend-code-btn');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            const response = await fetch(ROUTES.sendVerificationCode, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Verification code sent to your email', 'success');
                
                // Show verification code input
                document.getElementById('verification-step-1').style.display = 'none';
                document.getElementById('verification-step-2').style.display = 'block';
                
                // Start countdown for resend
                this.startResendCountdown();
            } else {
                throw new Error(data.message || 'Failed to send verification code');
            }
            
        } catch (error) {
            console.error('Error sending verification code:', error);
            this.showNotification(error.message || 'Failed to send verification code', 'error');
            
            const button = document.getElementById('send-code-btn') || document.getElementById('resend-code-btn');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-paper-plane"></i> Send Verification Code';
        }
    }

    startResendCountdown() {
        const resendBtn = document.getElementById('resend-code-btn');
        const timerSpan = document.getElementById('resend-timer');
        let timeLeft = 60;

        const countdown = setInterval(() => {
            timeLeft--;
            timerSpan.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
            }
        }, 1000);
    }

    async handlePasswordChange(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('change-password-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';

        try {
            const formData = new FormData(e.target);
            
            const response = await fetch(ROUTES.changePassword, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Password changed successfully', 'success');
                this.closeProfileModal();
                e.target.reset();
            } else {
                throw new Error(data.message || 'Failed to change password');
            }
            
        } catch (error) {
            console.error('Error changing password:', error);
            this.showNotification(error.message || 'Failed to change password', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-key"></i> Change Password';
        }
    }

    // Profile image handling
    handleProfileImageUpload(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('profile-image-preview').src = e.target.result;
                document.getElementById('header-profile-image').src = e.target.result;
                // You can add AJAX upload here
            };
            reader.readAsDataURL(file);
        }
    }

    removeProfileImage() {
        const defaultImage = "/images/user.jpg";
        document.getElementById('profile-image-preview').src = defaultImage;
        document.getElementById('header-profile-image').src = defaultImage;
        // You can add AJAX call to remove profile image from server
    }

    // Notification functions
    handleNotificationClick(event) {
        const notificationItem = event.currentTarget;
        const notificationType = notificationItem.getAttribute('data-type');
        const notificationId = notificationItem.getAttribute('data-id');
        const notificationCount = parseInt(notificationItem.getAttribute('data-count'));

        // Mark as read
        notificationItem.classList.remove('unread');
        const title = notificationItem.querySelector('.modalsbell-title');
        title.classList.add('read');

        // Remove the notification from the list
        notificationItem.remove();

        // Update the notification count
        this.updateNotificationCount(notificationCount);

        // Navigate to the corresponding module
        if (NOTIFICATION_ROUTES[notificationType]) {
            window.location.href = NOTIFICATION_ROUTES[notificationType];
        }

        // Close the notification dropdown
        document.getElementById('notificationDropdown').classList.remove('show');

        // You can implement AJAX call to mark as read in the database
        this.markNotificationAsRead(notificationId);
    }

    updateNotificationCount(removedCount) {
        const badge = document.querySelector('.modalsbell-badge');
        if (badge) {
            let currentCount = parseInt(badge.textContent);
            currentCount -= removedCount;

            if (currentCount <= 0) {
                badge.style.display = 'none';
                // Check if there are any notifications left
                const notificationList = document.querySelector('.modalsbell-list');
                const remainingItems = notificationList.querySelectorAll('.modalsbell-item');

                if (remainingItems.length === 0) {
                    notificationList.innerHTML = `
                    <div class="modalsbell-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No new notifications</p>
                    </div>
                `;
                }
            } else {
                badge.textContent = currentCount;
            }
        }
    }

    async markNotificationAsRead(notificationId) {
        try {
            const response = await fetch('/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            });

            const data = await response.json();
            if (data.success) {
                console.log('Notification marked as read');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Utility functions
    showNotification(message, type) {
        // Create a temporary notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;

        // Add to the top of the main content
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(notification, mainContent.firstChild);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Logout functionality
    logout() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    confirmLogout() {
        window.location.href = ROUTES.logout;
    }
}

// Global functions that need to be accessible from HTML onclick attributes
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('show');
}

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
}

function openProfileModal(tab = 'profile-tab') {
    if (window.prefectLayout) {
        window.prefectLayout.openProfileModal(tab);
    }
}

function closeProfileModal() {
    if (window.prefectLayout) {
        window.prefectLayout.closeProfileModal();
    }
}

function openTab(tabName) {
    if (window.prefectLayout) {
        window.prefectLayout.openTab(tabName);
    }
}

function togglePassword(inputId) {
    if (window.prefectLayout) {
        window.prefectLayout.togglePassword(inputId);
    }
}

function sendVerificationCode() {
    if (window.prefectLayout) {
        window.prefectLayout.sendVerificationCode();
    }
}

function removeProfileImage() {
    if (window.prefectLayout) {
        window.prefectLayout.removeProfileImage();
    }
}

function logout() {
    if (window.prefectLayout) {
        window.prefectLayout.logout();
    }
}

function closeLogoutModal() {
    if (window.prefectLayout) {
        window.prefectLayout.closeLogoutModal();
    }
}

function confirmLogout() {
    if (window.prefectLayout) {
        window.prefectLayout.confirmLogout();
    }
}

// Notification global functions
function showMarkAllReadModal() {
    document.getElementById('markAllReadModal').style.display = 'block';
}

function closeMarkAllReadModal() {
    document.getElementById('markAllReadModal').style.display = 'none';
}

function showDeleteAllReadModal() {
    document.getElementById('deleteAllReadModal').style.display = 'block';
}

function closeDeleteAllReadModal() {
    document.getElementById('deleteAllReadModal').style.display = 'none';
}

function confirmMarkAllAsRead() {
    if (window.prefectLayout) {
        // Implementation for mark all as read
        const unreadItems = document.querySelectorAll('.modalsbell-item.unread');
        unreadItems.forEach(item => {
            item.classList.remove('unread');
            const title = item.querySelector('.modalsbell-title');
            title.classList.add('read');
        });

        const badge = document.querySelector('.modalsbell-badge');
        if (badge) {
            badge.textContent = '0';
            badge.style.display = 'none';
        }

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            }
        });

        closeMarkAllReadModal();
        window.prefectLayout.showNotification('All notifications marked as read', 'success');
    }
}

function confirmDeleteAllRead() {
    if (window.prefectLayout) {
        // Implementation for delete all read
        const readItems = document.querySelectorAll('.modalsbell-item:not(.unread)');

        if (readItems.length === 0) {
            closeDeleteAllReadModal();
            window.prefectLayout.showNotification('No read notifications to delete', 'info');
            return;
        }

        readItems.forEach(item => {
            item.remove();
        });

        const notificationList = document.querySelector('.modalsbell-list');
        const remainingItems = notificationList.querySelectorAll('.modalsbell-item');

        if (remainingItems.length === 0) {
            notificationList.innerHTML = `
            <div class="modalsbell-empty">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications</p>
            </div>
        `;
        }

        fetch('/notifications/delete-read', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            }
        });

        closeDeleteAllReadModal();
        window.prefectLayout.showNotification('All read notifications deleted', 'success');
    }
}

function closeNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.remove('show');
    window.location.href = '/notifications';
}

// Initialize the layout when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.prefectLayout = new PrefectLayout();
});