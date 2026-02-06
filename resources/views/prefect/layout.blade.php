<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prefect</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/prefect/archive-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/info.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/layouts-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/main-container.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/offenses-sanction.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/settlement-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/table-container.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/closebuttonall.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/pdfandexcel.css') }}">
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo + School name -->
        <div class="sidebar-header">
            <img src="{{ asset('images/logo.png') }}" alt="School Logo" class="sidebar-logo">
            <div class="school-name">
                Tagoloan Senior High School
            </div>
        </div>

        <!-- Role label -->
        <h2>PREFECT</h2>

        <!-- Main navigation -->
        <nav aria-label="Prefect navigation">
            <ul>
                <li class="{{ request()->routeIs('prefect.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('prefect.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard Overview</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('prefect.adviser') ? 'active' : '' }}">
                    <a href="{{ route('prefect.adviser') }}">
                        <i class="fas fa-users"></i>
                        <span>Advisers</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('parent.lists') ? 'active' : '' }}">
                    <a href="{{ route('parent.lists') }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Parents</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('student.management') ? 'active' : '' }}">
                    <a href="{{ route('student.management') }}">
                        <i class="fas fa-user-graduate"></i>
                        <span>Students</span>
                    </a>
                </li>

                {{-- Section label --}}
                <li class="sidebar-section-title">
                    Violations Management
                </li>

                <li class="{{ request()->routeIs('prefect.violation') ? 'active' : '' }}">
                    <a href="{{ route('prefect.violation') }}">
                        <i class="fas fa-book"></i>
                        <span>Violation Record</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('prefect.violationAnecdotal') ? 'active' : '' }}">
                    <a href="{{ route('prefect.violationAnecdotal') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Violation Anecdotal</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('prefect.violationAppointment') ? 'active' : '' }}">
                    <a href="{{ route('prefect.violationAppointment') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Violation Appointment</span>
                    </a>
                </li>

                <li class="sidebar-section-title">
                    Reports
                </li>

                <li class="{{ request()->routeIs('offenses.sanctions') ? 'active' : '' }}">
                    <a href="{{ route('offenses.sanctions') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Offense &amp; Sanctions</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Profile Settings Modal -->
    <div id="profileSettingsModal" class="modal">
        <div class="modal-content profile-modal">
            <div class="modal-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h3>Profile Settings</h3>
                        <p class="header-subtitle">Manage your account information and security</p>
                    </div>
                </div>
                <span class="close">&times;</span>
            </div>

            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="tab-btn active" data-tab="profile-tab">
                        <div class="tab-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="tab-content">
                            <span class="tab-title">Profile</span>
                            <span class="tab-desc">Personal information</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="password-tab">
                        <div class="tab-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="tab-content">
                            <span class="tab-title">Security</span>
                            <span class="tab-desc">Password & verification</span>
                        </div>
                    </button>
                </div>

                <div class="tab-content-container">
                    <!-- Profile Tab -->
                    <div id="profile-tab" class="tab-content active">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="avatar-container">
<div class="avatar-wrapper">
    <img id="profile-image-preview"
         src="{{ !empty(Auth::guard('prefect')->user()->profile_image) ? asset(Auth::guard('prefect')->user()->profile_image) : asset('images/user.jpg') }}"
         alt="Profile Picture" class="profile-avatar">
    <div class="avatar-overlay"
         onclick="document.getElementById('profile-image-input').click()">
        <i class="fas fa-camera"></i>
        <span>Change Photo</span>
    </div>
</div>
                                    <input type="file" id="profile-image-input" accept="image/*"
                                        style="display: none;">
                                </div>
                                <div class="profile-meta">
                                    <h4 id="profile-name">
                                        {{ Auth::guard('prefect')->user()->prefect_fname . ' ' . Auth::guard('prefect')->user()->prefect_lname }}
                                    </h4>
                                    <p class="profile-role">Prefect of Discipline</p>
                                    <div class="status-badge active">
                                        <i class="fas fa-circle"></i>
                                        <span
                                            id="profile-status">{{ Auth::guard('prefect')->user()->status ?? 'Active' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-actions">
                                <button type="button"
                                    onclick="document.getElementById('profile-image-input').click()"
                                    class="btn-action btn-primary">
                                    <i class="fas fa-upload"></i> Upload New Photo
                                </button>
                                <button type="button" onclick="removeProfileImage()"
                                    class="btn-action btn-secondary">
                                    <i class="fas fa-trash-alt"></i> Remove Photo
                                </button>
                            </div>

                            <div id="profile-image-error" class="error-message" style="text-align: center;"></div>

                            <div class="profile-details">
                                <h5 class="section-title">
                                    <i class="fas fa-id-card"></i> Personal Information
                                </h5>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-user-tag"></i>
                                            <span>Full Name</span>
                                        </div>
                                        <div class="info-value" id="profile-name-display">
                                            {{ Auth::guard('prefect')->user()->prefect_fname . ' ' . Auth::guard('prefect')->user()->prefect_lname }}
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-envelope"></i>
                                            <span>Email Address</span>
                                        </div>
                                        <div class="info-value" id="profile-email">
                                            {{ Auth::guard('prefect')->user()->prefect_email }}
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-venus-mars"></i>
                                            <span>Gender</span>
                                        </div>
                                        <div class="info-value" id="profile-gender">
                                            {{ Auth::guard('prefect')->user()->prefect_sex ?? 'Not specified' }}
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-phone"></i>
                                            <span>Contact Number</span>
                                        </div>
                                        <div class="info-value" id="profile-contact">
                                            {{ Auth::guard('prefect')->user()->prefect_contactinfo ?? 'Not provided' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="password-tab" class="tab-content">
                        <div class="security-card">
                            <div class="security-header">
                                <div class="security-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h4>Password Security</h4>
                                    <p class="security-subtitle">Update your password with email verification</p>
                                </div>
                            </div>

                            <form id="changePasswordForm">
                                @csrf

                                <!-- Step 1: Request Verification -->
                                <div id="verification-step-1" class="verification-step">
                                    <div class="step-header">
                                        <div class="step-number">1</div>
                                        <h5>Request Verification Code</h5>
                                    </div>
                                    <div class="step-content">
                                        <p>We'll send a 6-digit verification code to your registered email address for
                                            security verification.</p>
                                        <div class="email-preview">
                                            <i class="fas fa-envelope"></i>
                                            <span
                                                id="verification-email">{{ Auth::guard('prefect')->user()->prefect_email }}</span>
                                        </div>
                                        <button type="button" class="btn-send-code" onclick="sendVerificationCode()"
                                            id="send-code-btn">
                                            <i class="fas fa-paper-plane"></i>
                                            <span>Send Verification Code</span>
                                        </button>
                                        <div class="countdown" id="countdown"></div>
                                    </div>
                                </div>

                                <!-- Step 2: Enter Verification Code -->
                                <div id="verification-step-2" class="verification-step" style="display: none;">
                                    <div class="step-header">
                                        <div class="step-number">2</div>
                                        <h5>Enter Verification Code</h5>
                                    </div>
                                    <div class="step-content">
                                        <p>Enter the 6-digit code sent to your email:</p>
                                        <div class="verification-code-input">
                                            <input type="text" id="verification_code" name="verification_code"
                                                maxlength="6" placeholder="000000" required pattern="[0-9]{6}"
                                                title="Please enter 6-digit code">
                                            <div class="code-hint">6-digit code</div>
                                        </div>
                                        <div class="countdown" id="code-countdown"></div>

                                        <div class="verification-actions">
                                            <button type="button" class="btn-verify-code"
                                                onclick="verifyAndProceed()" id="verify-code-btn"
                                                style="margin-bottom: 10px;">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Verify & Continue</span>
                                            </button>

                                            <button type="button" class="btn-resend-code"
                                                onclick="sendVerificationCode()" id="resend-code-btn" disabled>
                                                <i class="fas fa-redo"></i>
                                                <span>Resend Code (<span id="resend-timer">60</span>s)</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Set New Password -->
                                <div id="verification-step-3" class="verification-step" style="display: none;">
                                    <div class="step-header">
                                        <div class="step-number">3</div>
                                        <h5>Set New Password</h5>
                                    </div>
                                    <div class="step-content">
                                        <div class="password-requirements">
                                            <h6><i class="fas fa-check-circle"></i> Password Requirements:</h6>
                                            <ul>
                                                <li>At least 8 characters long</li>
                                                <li>Contains uppercase & lowercase letters</li>
                                                <li>Includes at least one number</li>
                                                <li>Can include special characters</li>
                                            </ul>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password">
                                                <i class="fas fa-key"></i>
                                                New Password
                                            </label>
                                            <div class="password-input-container">
                                                <input type="password" id="new_password" name="new_password"
                                                    placeholder="Enter new password" required>
                                                <button type="button" class="toggle-password"
                                                    onclick="togglePassword('new_password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="password-strength" id="password-strength">
                                                <div class="strength-bar"></div>
                                                <span class="strength-text">Password strength</span>
                                            </div>
                                            <span class="error-message" id="new_password_error"></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password_confirmation">
                                                <i class="fas fa-key"></i>
                                                Confirm New Password
                                            </label>
                                            <div class="password-input-container">
                                                <input type="password" id="new_password_confirmation"
                                                    name="new_password_confirmation"
                                                    placeholder="Confirm new password" required>
                                                <button type="button" class="toggle-password"
                                                    onclick="togglePassword('new_password_confirmation')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <span class="error-message" id="new_password_confirmation_error"></span>
                                        </div>

                                        <div class="form-actions">
                                            <button type="button" class="btn-cancel" onclick="closeProfileModal()">
                                                <i class="fas fa-times"></i>
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn-submit" id="change-password-btn">
                                                <i class="fas fa-save"></i>
                                                Update Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content1" style="max-width: 400px;">
            <div class="modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Confirm Logout</h3>
                <span class="close" onclick="closeLogoutModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px 0;">
                    <i class="fas fa-question-circle"
                        style="font-size: 48px; color: #e74c3c; margin-bottom: 15px;"></i>
                    <p style="font-size: 16px; margin-bottom: 25px; color: #333;">
                        Are you sure you want to logout?
                    </p>
                </div>
                <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" class="btn-cancel" onclick="closeLogoutModal()"
                        style="padding: 10px 20px;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn-logout" onclick="confirmLogout()" style="padding: 10px 20px;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content area -->
    <main class="main-content" id="mainContent">
        <header class="main-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Student Violation Tracking System</h2>
            </div>
            <div class="header-right">
                <!-- Notification Bell -->
                <div class="modalsbell-container">
                    <div class="modalsbell-bell" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        @if ($referredViolationsCount > 0 || $newDirectViolationsCount > 0)
                            <span
                                class="modalsbell-badge">{{ $referredViolationsCount + $newDirectViolationsCount }}</span>
                        @endif
                    </div>

                    <!-- Notification Dropdown -->
                    <div class="modalsbell-dropdown" id="notificationDropdown">
                        <div class="modalsbell-header">
                            <h4>Violation Notifications</h4>
                            <div class="modalsbell-actions">
                                <button class="modalsbell-action-btn" onclick="showMarkAllReadModal()">
                                    <i class="fas fa-check-double"></i> Mark All Read
                                </button>
                                <button class="modalsbell-action-btn" onclick="showDeleteAllReadModal()">
                                    <i class="fas fa-trash"></i> Delete Read
                                </button>
                            </div>
                        </div>
                        <div class="modalsbell-list">
                            <!-- Referred Violations from Advisers -->
                            @if ($referredViolationsCount > 0)
                                <div class="modalsbell-item unread" data-id="referred-{{ $referredViolationsCount }}"
                                    data-type="referred" data-count="{{ $referredViolationsCount }}">
                                    <div class="modalsbell-icon referred">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div class="modalsbell-content">
                                        <span class="modalsbell-title">Referred Violations</span>
                                        <span class="modalsbell-message">{{ $referredViolationsCount }}
                                            violation(s)
                                            referred from advisers</span>
                                        @if (isset($referredViolations) && count($referredViolations) > 0)
                                            <div class="modalsbell-details">
                                                @foreach ($referredViolations->take(3) as $violation)
                                                    <div>
                                                        <strong>{{ $violation['student_name'] }}</strong>:
                                                        {{ $violation['violation_type'] }}
                                                        <small>(Referred by:
                                                            {{ $violation['adviser_name'] }})</small>
                                                    </div>
                                                @endforeach
                                                @if ($referredViolationsCount > 3)
                                                    <div>... and {{ $referredViolationsCount - 3 }}
                                                        more</div>
                                                @endif
                                            </div>
                                        @endif
                                        <span class="modalsbell-time">Just now</span>
                                    </div>
                                </div>
                            @endif

                            <!-- New Direct Violations -->
                            @if ($newDirectViolationsCount > 0)
                                <div class="modalsbell-item unread" data-id="direct-{{ $newDirectViolationsCount }}"
                                    data-type="direct" data-count="{{ $newDirectViolationsCount }}">
                                    <div class="modalsbell-icon direct">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="modalsbell-content">
                                        <span class="modalsbell-title">New Direct Violations</span>
                                        <span class="modalsbell-message">{{ $newDirectViolationsCount }}
                                            new direct
                                            violation(s)</span>
                                        @if (isset($newDirectViolations) && count($newDirectViolations) > 0)
                                            <div class="modalsbell-details">
                                                @foreach ($newDirectViolations->take(3) as $violation)
                                                    <div>
                                                        <strong>{{ $violation['student_name'] }}</strong>:
                                                        {{ $violation['violation_type'] }}
                                                    </div>
                                                @endforeach
                                                @if ($newDirectViolationsCount > 3)
                                                    <div>... and {{ $newDirectViolationsCount - 3 }}
                                                        more</div>
                                                @endif
                                            </div>
                                        @endif
                                        <span class="modalsbell-time">Today</span>
                                    </div>
                                </div>
                            @endif

                            <!-- No Notifications -->
                            @if ($referredViolationsCount === 0 && $newDirectViolationsCount === 0)
                                <div class="modalsbell-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No new violation notifications</p>
                                </div>
                            @endif
                        </div>
                        <div class="modalsbell-footer">
                            <a href="{{ route('prefect.violation') }}" onclick="closeNotifications()">View
                                All
                                Violations</a>
                        </div>
                    </div>
                </div>

                <div class="user-info" onclick="toggleProfileDropdown()">
                    <img id="header-profile-image"
                        src="{{ Auth::guard('prefect')->user()->profile_image ? asset('storage/' . Auth::guard('prefect')->user()->profile_image) : asset('images/user.jpg') }}"
                        alt="User"
                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db;">
                    <span
                        id="header-user-name">{{ Auth::guard('prefect')->user()->prefect_fname . ' ' . Auth::guard('prefect')->user()->prefect_lname }}</span>
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="#" onclick="openProfileModal()">
                        <i class="fas fa-user-cog"></i> Profile Settings
                    </a>
                    <a href="#" onclick="openProfileModal('password-tab')">
                        <i class="fas fa-lock"></i> Change Password
                    </a>
                    <a href="#" onclick="event.preventDefault(); logout();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    <script>
        // Initialize with prefect data
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial header name from PHP
            const headerUserName = document.getElementById('header-user-name');
            if (headerUserName && headerUserName.textContent === 'Loading...') {
                // Fallback to PHP data
                headerUserName.textContent =
                    '{{ Auth::guard('prefect')->user()->prefect_fname . ' ' . Auth::guard('prefect')->user()->prefect_lname }}';
            }

            // Fetch profile data asynchronously for updates
            fetchProfileData();

            // Initialize new UI components
            initializeProfileUI();
        });

        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        let isSidebarCollapsed = false;

        // Toggle sidebar visibility
        function toggleSidebar() {
            isSidebarCollapsed = !isSidebarCollapsed;

            if (isSidebarCollapsed) {
                sidebar.classList.add('collapsed');
                sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            } else {
                sidebar.classList.remove('collapsed');
                sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }

            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
        }

        // Initialize sidebar state from localStorage
        function initializeSidebarState() {
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState !== null) {
                isSidebarCollapsed = savedState === 'true';

                if (isSidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            }
        }

        // Add event listener to toggle button
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        // Initialize sidebar state
        initializeSidebarState();

        // Profile dropdown functionality
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.getElementById('profileDropdown');
            const userInfo = document.querySelector('.user-info');

            if (profileDropdown && userInfo && !userInfo.contains(event.target) && profileDropdown.classList
                .contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });

        // Modal functionality - NEW IMPROVED VERSION
        function openProfileModal(tab = 'profile-tab') {
            const modal = document.getElementById('profileSettingsModal');
            if (modal) {
                modal.style.display = 'flex';
                // Switch to specified tab
                switchTab(tab);

                // Refresh profile data when opening modal
                fetchProfileData();
            }
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileSettingsModal');
            if (modal) {
                modal.style.display = 'none';
                // Reset password form steps
                resetPasswordForm();
            }
        }

        // Close modal when clicking the X
        const modalCloseBtn = document.querySelector('#profileSettingsModal .close');
        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', closeProfileModal);
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('profileSettingsModal');
            if (event.target === modal) {
                closeProfileModal();
            }
        });

        // NEW Tab switching for improved UI
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content-container .tab-content');
            tabContents.forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Show the selected tab content
            const targetTab = document.getElementById(tabName);
            if (targetTab) {
                targetTab.classList.add('active');
            }

            // Activate the corresponding tab button
            const targetButton = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
            if (targetButton) {
                targetButton.classList.add('active');
            }
        }

        // Initialize new UI components
        function initializeProfileUI() {
            // Tab switching for new UI
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    switchTab(tabId);
                });
            });

            // Password strength indicator
            const newPasswordInput = document.getElementById('new_password');
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', updatePasswordStrength);
            }

            // Profile image upload improvements
            const profileImageInput = document.getElementById('profile-image-input');
            const avatarOverlay = document.querySelector('.avatar-overlay');

            if (avatarOverlay) {
                // Click overlay to trigger file input
                avatarOverlay.addEventListener('click', function() {
                    if (profileImageInput) {
                        profileImageInput.click();
                    }
                });
            }

            if (profileImageInput) {
                profileImageInput.addEventListener('change', handleProfileImageChange);
            }

            // Initialize verification code input
            const verificationCodeInput = document.getElementById('verification_code');
            if (verificationCodeInput) {
                verificationCodeInput.addEventListener('input', function() {
                    // Auto-format as 6-digit code
                    this.value = this.value.replace(/\D/g, '').slice(0, 6);

                    // Auto-advance to next step if code is complete
                    if (this.value.length === 6) {
                        // You can add auto-validation here if needed
                    }
                });
            }

            // Password form submission
            const changePasswordForm = document.getElementById('changePasswordForm');
            if (changePasswordForm) {
                changePasswordForm.addEventListener('submit', handlePasswordChange);
            }
        }

        // Password strength indicator function
        function updatePasswordStrength() {
            const password = this.value;
            const strengthBar = document.querySelector('.strength-bar');
            const strengthText = document.querySelector('.strength-text');

            if (!strengthBar || !strengthText) return;

            let strength = 0;
            let color = '#e74c3c';
            let text = 'Weak';

            // Check criteria
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            // Determine strength level
            if (strength >= 4) {
                color = '#27ae60';
                text = 'Strong';
            } else if (strength >= 2) {
                color = '#f39c12';
                text = 'Medium';
            }

            // Update strength bar
            const strengthBarInner = strengthBar.querySelector('.strength-bar-inner') ||
                document.createElement('div');
            if (!strengthBar.querySelector('.strength-bar-inner')) {
                strengthBarInner.className = 'strength-bar-inner';
                strengthBar.appendChild(strengthBarInner);
            }

            strengthBarInner.style.width = `${strength * 25}%`;
            strengthBarInner.style.background = color;
            strengthBarInner.style.height = '100%';
            strengthBarInner.style.borderRadius = '2px';
            strengthBarInner.style.transition = 'all 0.3s ease';

            // Update text
            strengthText.textContent = `Password strength: ${text}`;
            strengthText.style.color = color;
        }

        // Handle profile image change
        function handleProfileImageChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                showNotification('Please select a valid image file (JPEG, PNG, GIF, WEBP)', 'error');
                return;
            }

            if (file.size > maxSize) {
                showNotification('Image size should be less than 2MB', 'error');
                return;
            }

            // Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-image-preview');
                const headerImg = document.getElementById('header-profile-image');
                if (preview) preview.src = e.target.result;
                if (headerImg) headerImg.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload
            uploadProfileImage(file);
        }

        // Global variables for routes and CSRF token
        const ROUTES = {
            sendVerificationCode: '{{ route('prefect.send-verification-code') }}',
            changePassword: '{{ route('prefect.change-password') }}',
            profileInfo: '{{ route('prefect.profile-info') }}',
            uploadProfileImage: '{{ route('prefect.upload-profile-image') }}',
            removeProfileImage: '{{ route('prefect.remove-profile-image') }}',
            logout: '{{ route('prefect.logout') }}',
            login: '{{ route('login') }}',
            markAllRead: '{{ route('prefect.notifications.mark-all-read') }}',
            deleteRead: '{{ route('prefect.notifications.delete-read') }}'
        };
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Function to fetch and display profile data
        async function fetchProfileData() {
            try {
                const response = await fetch(ROUTES.profileInfo, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch profile data: ${response.status}`);
                }

                const data = await response.json();

                if (data && typeof data === 'object') {
                    // Update all profile fields
                    updateProfileField('profile-name', data.name);
                    updateProfileField('profile-email', data.email);
                    updateProfileField('profile-gender', data.gender);
                    updateProfileField('profile-contact', data.contact);
                    updateProfileField('profile-status', data.status);
                    updateProfileField('header-user-name', data.name);
                    updateProfileField('user-email', data.email);
                    updateProfileField('verification-email', data.email);
                    updateProfileField('profile-name-display', data.name);

                    // Update profile image if exists
                    if (data.profile_image) {
                        updateProfileImage(data.profile_image);
                    }
                }
            } catch (error) {
                console.error('Error fetching profile data:', error);
                // Keep the PHP-loaded data if fetch fails
            }
        }

        // Helper function to update profile fields
        function updateProfileField(elementId, value) {
            const element = document.getElementById(elementId);
            if (element && value) {
                element.textContent = value;
            }
        }

        // CORRECTED: Helper function to update profile image
        function updateProfileImage(imageUrl) {
            const profileImage = document.getElementById('profile-image-preview');
            const headerProfileImage = document.getElementById('header-profile-image');

            if (!imageUrl) return;

            // Use the imageUrl directly as provided by the backend
            if (profileImage) {
                profileImage.src = imageUrl;
                profileImage.onerror = function() {
                    // Only fallback to default if there's an error
                    this.src = '{{ asset('images/user.jpg') }}';
                };
            }

            if (headerProfileImage) {
                headerProfileImage.src = imageUrl;
                headerProfileImage.onerror = function() {
                    // Only fallback to default if there's an error
                    this.src = '{{ asset('images/user.jpg') }}';
                };
            }
        }

        // Function to upload profile image
        async function uploadProfileImage(file) {
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('_token', CSRF_TOKEN);

            console.log('Uploading profile image...', file.name);

            // Show loading state
            const uploadBtn = document.querySelector('.btn-action.btn-primary');
            if (uploadBtn) {
                const originalText = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                uploadBtn.disabled = true;
            }

            try {
                const response = await fetch(ROUTES.uploadProfileImage, {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    console.log('Upload successful, image_url:', data.image_url);

                    // Update both images with the URL from backend
                    updateProfileImage(data.image_url);

                    showNotification('Profile image uploaded successfully', 'success');

                    // Clear any previous errors
                    const errorElement = document.getElementById('profile-image-error');
                    if (errorElement) {
                        errorElement.textContent = '';
                    }
                } else {
                    const errorMsg = data.message || 'Failed to upload image';
                    console.error('Upload failed:', errorMsg);
                    showNotification(errorMsg, 'error');

                    const errorElement = document.getElementById('profile-image-error');
                    if (errorElement) {
                        errorElement.textContent = errorMsg;
                    }
                }
            } catch (error) {
                console.error('Error uploading profile image:', error);
                showNotification('Network error occurred. Please try again.', 'error');

                const errorElement = document.getElementById('profile-image-error');
                if (errorElement) {
                    errorElement.textContent = 'Network error occurred';
                }
            } finally {
                // Restore button state
                if (uploadBtn) {
                    uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload New Photo';
                    uploadBtn.disabled = false;
                }
            }
        }

// UPDATED: Function to remove profile image (Hostinger compatible)
async function removeProfileImage() {
    if (!confirm('Are you sure you want to remove your profile image?')) {
        return;
    }

    // Show loading state
    const removeBtn = document.querySelector('.btn-action.btn-secondary');
    if (removeBtn) {
        const originalText = removeBtn.innerHTML;
        removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
        removeBtn.disabled = true;
    }

    try {
        const response = await fetch(ROUTES.removeProfileImage, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                _token: CSRF_TOKEN
            })
        });

        const data = await response.json();

        if (data.success) {
            // Use the image_url returned from the backend or fallback to default
            const defaultImageUrl = data.image_url || '{{ asset('images/user.jpg') }}';

            // Update both profile images
            const profileImage = document.getElementById('profile-image-preview');
            const headerProfileImage = document.getElementById('header-profile-image');

            if (profileImage) profileImage.src = defaultImageUrl;
            if (headerProfileImage) headerProfileImage.src = defaultImageUrl;

            showNotification('Profile image removed successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to remove image', 'error');
        }
    } catch (error) {
        console.error('Error removing profile image:', error);
        showNotification('Network error occurred', 'error');
    } finally {
        // Restore button state
        if (removeBtn) {
            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Remove Photo';
            removeBtn.disabled = false;
        }
    }
}

        // Password change functionality - IMPROVED
        let verificationTimer = null;
        let resendTimer = null;
        let resendTime = 60;

        async function sendVerificationCode() {
            const sendBtn = document.getElementById('send-code-btn');
            const resendBtn = document.getElementById('resend-code-btn');

            // Disable button and show loading
            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            }

            try {
                const response = await fetch(ROUTES.sendVerificationCode, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification('Verification code sent to your email', 'success');

                    // Show step 2
                    document.getElementById('verification-step-1').style.display = 'none';
                    document.getElementById('verification-step-2').style.display = 'block';

                    // Start countdown for resend
                    startResendCountdown();

                    // Focus on verification code input
                    setTimeout(() => {
                        const verificationInput = document.getElementById('verification_code');
                        if (verificationInput) {
                            verificationInput.focus();
                        }
                    }, 100);
                } else {
                    showNotification(data.message || 'Failed to send verification code', 'error');
                }
            } catch (error) {
                console.error('Error sending verification code:', error);
                showNotification('Network error occurred', 'error');
            } finally {
                // Restore button state
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>Send Verification Code</span>';
                }
            }
        }

        function startResendCountdown() {
            const resendBtn = document.getElementById('resend-code-btn');
            const timerSpan = document.getElementById('resend-timer');

            if (!resendBtn || !timerSpan) return;

            resendBtn.disabled = true;
            resendTime = 60;

            resendTimer = setInterval(() => {
                resendTime--;
                timerSpan.textContent = resendTime;

                if (resendTime <= 0) {
                    clearInterval(resendTimer);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-redo"></i> <span>Resend Code</span>';
                }
            }, 1000);
        }

        // Function to advance to step 3 when verification code is complete
        function setupVerificationCodeAutoAdvance() {
            const verificationCodeInput = document.getElementById('verification_code');
            if (verificationCodeInput) {
                verificationCodeInput.addEventListener('input', function() {
                    // Clean input to only numbers
                    this.value = this.value.replace(/\D/g, '').slice(0, 6);

                    // When 6 digits are entered, validate and advance
                    if (this.value.length === 6) {
                        validateVerificationCode(this.value);
                    }
                });

                // Also allow pressing Enter to proceed
                verificationCodeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && this.value.length === 6) {
                        validateVerificationCode(this.value);
                    }
                });
            }
        }

        async function validateVerificationCode(code) {
            const verifyBtn = document.querySelector('.btn-verify-code');
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            }

            try {
                // Here you should add an API endpoint to validate the code
                // For now, let's proceed to step 3 if code is 6 digits
                if (code.length === 6) {
                    // Show step 3
                    document.getElementById('verification-step-2').style.display = 'none';
                    document.getElementById('verification-step-3').style.display = 'block';

                    // Focus on new password field
                    setTimeout(() => {
                        const newPasswordInput = document.getElementById('new_password');
                        if (newPasswordInput) newPasswordInput.focus();
                    }, 100);
                }
            } catch (error) {
                console.error('Validation error:', error);
            } finally {
                if (verifyBtn) {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = 'Verify Code';
                }
            }
        }

        function verifyAndProceed() {
            const code = document.getElementById('verification_code').value;

            if (!code || code.length !== 6) {
                showNotification('Please enter a valid 6-digit code', 'error');
                return;
            }

            // Directly proceed to step 3
            document.getElementById('verification-step-2').style.display = 'none';
            document.getElementById('verification-step-3').style.display = 'block';

            // Focus on new password field
            setTimeout(() => {
                const newPasswordInput = document.getElementById('new_password');
                if (newPasswordInput) newPasswordInput.focus();
            }, 100);
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;

            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);

            // Update icon
            const icon = field.parentNode.querySelector('.toggle-password i');
            if (icon) {
                icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
            }
        }

        // Handle password change form submission
        async function handlePasswordChange(event) {
            event.preventDefault();

            const changePasswordBtn = document.getElementById('change-password-btn');
            if (!changePasswordBtn) return;

            // Validate passwords
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            const verificationCode = document.getElementById('verification_code').value;

            // Clear previous errors
            clearPasswordErrors();

            // Basic validation
            let isValid = true;

            if (!verificationCode || verificationCode.length !== 6) {
                showFieldError('verification_code', 'Please enter a valid 6-digit verification code');
                isValid = false;
            }

            if (newPassword.length < 8) {
                showFieldError('new_password', 'Password must be at least 8 characters long');
                isValid = false;
            }

            if (newPassword !== confirmPassword) {
                showFieldError('new_password_confirmation', 'Passwords do not match');
                isValid = false;
            }

            if (!isValid) return;

            // Show loading state
            changePasswordBtn.disabled = true;
            changePasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';

            try {
                const response = await fetch(ROUTES.changePassword, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        verification_code: verificationCode,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword,
                        _token: CSRF_TOKEN
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification('Password changed successfully!', 'success');

                    // Close modal after successful password change
                    setTimeout(() => {
                        closeProfileModal();
                        // Reset form
                        resetPasswordForm();
                    }, 1500);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            showFieldError(field, data.errors[field][0]);
                        });
                    } else {
                        showNotification(data.message || 'Failed to change password', 'error');
                    }
                }
            } catch (error) {
                console.error('Error changing password:', error);
                showNotification('Network error occurred', 'error');
            } finally {
                // Restore button state
                changePasswordBtn.disabled = false;
                changePasswordBtn.innerHTML = '<i class="fas fa-save"></i> Update Password';
            }
        }

        function showFieldError(fieldName, message) {
            const errorElement = document.getElementById(`${fieldName}_error`);
            if (errorElement) {
                errorElement.textContent = message;
            }
        }

        function clearPasswordErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(el => {
                el.textContent = '';
            });
        }

        function resetPasswordForm() {
            // Reset all steps
            document.getElementById('verification-step-1').style.display = 'block';
            document.getElementById('verification-step-2').style.display = 'none';
            document.getElementById('verification-step-3').style.display = 'none';

            // Clear inputs
            document.getElementById('verification_code').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';

            // Clear errors
            clearPasswordErrors();

            // Reset timers
            if (resendTimer) {
                clearInterval(resendTimer);
                resendTimer = null;
            }

            // Reset resend button
            const resendBtn = document.getElementById('resend-code-btn');
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> <span>Resend Code</span>';
            }

            // Reset password strength indicator
            const strengthText = document.querySelector('.strength-text');
            if (strengthText) {
                strengthText.textContent = 'Password strength';
                strengthText.style.color = '#7f8c8d';
            }

            const strengthBarInner = document.querySelector('.strength-bar-inner');
            if (strengthBarInner) {
                strengthBarInner.style.width = '0%';
            }
        }

        // ========================= NOTIFICATION FUNCTIONS =========================
        // Notification Functions
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // NEW: Show Mark All Read Confirmation Modal
        function showMarkAllReadModal() {
            const unreadItems = document.querySelectorAll('.modalsbell-item.unread');

            if (unreadItems.length === 0) {
                showNotification('No unread notifications to mark as read.', 'info');
                return;
            }

            // Show confirmation dialog
            if (confirm(`Mark all ${unreadItems.length} notification(s) as read?`)) {
                markAllNotificationsAsRead();
            }
        }

        // NEW: Show Delete All Read Confirmation Modal
        function showDeleteAllReadModal() {
            const readItems = document.querySelectorAll('.modalsbell-item:not(.unread)');

            // Filter out the empty state message
            const actualReadItems = Array.from(readItems).filter(item =>
                !item.querySelector('.modalsbell-empty')
            );

            if (actualReadItems.length === 0) {
                showNotification('No read notifications to delete.', 'info');
                return;
            }

            // Show confirmation dialog
            if (confirm(`Delete all ${actualReadItems.length} read notification(s)? This action cannot be undone.`)) {
                deleteAllReadNotifications();
            }
        }

        // NEW: Mark all notifications as read
        async function markAllNotificationsAsRead() {
            try {
                const response = await fetch(ROUTES.markAllRead, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove unread class from all notifications
                    const unreadItems = document.querySelectorAll('.modalsbell-item.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                    });

                    // Update notification badge
                    updateNotificationBadge(0);

                    showNotification('All notifications marked as read', 'success');

                    // Close dropdown
                    const dropdown = document.getElementById('notificationDropdown');
                    if (dropdown) {
                        dropdown.classList.remove('show');
                    }
                } else {
                    showNotification(data.message || 'Failed to mark notifications as read', 'error');
                }
            } catch (error) {
                console.error('Error marking notifications as read:', error);
                showNotification('An error occurred', 'error');
            }
        }

        // NEW: Delete all read notifications
        async function deleteAllReadNotifications() {
            try {
                const response = await fetch(ROUTES.deleteRead, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove all read notifications from the UI
                    const readItems = document.querySelectorAll('.modalsbell-item:not(.unread)');
                    readItems.forEach(item => {
                        if (!item.querySelector('.modalsbell-empty')) {
                            item.remove();
                        }
                    });

                    // Show empty state if no notifications left
                    const notificationList = document.querySelector('.modalsbell-list');
                    const remainingItems = notificationList.querySelectorAll('.modalsbell-item');

                    if (remainingItems.length === 0 ||
                        (remainingItems.length === 1 && remainingItems[0].querySelector('.modalsbell-empty'))) {
                        // Already has empty state
                    } else {
                        // Check if we need to add empty state
                        const hasEmptyState = notificationList.querySelector('.modalsbell-empty');
                        const hasUnread = notificationList.querySelector('.modalsbell-item.unread');

                        if (!hasEmptyState && !hasUnread) {
                            notificationList.innerHTML = `
                            <div class="modalsbell-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p>No notifications</p>
                            </div>
                        `;
                        }
                    }

                    showNotification('Read notifications deleted successfully', 'success');

                    // Close dropdown
                    const dropdown = document.getElementById('notificationDropdown');
                    if (dropdown) {
                        dropdown.classList.remove('show');
                    }
                } else {
                    showNotification(data.message || 'Failed to delete read notifications', 'error');
                }
            } catch (error) {
                console.error('Error deleting read notifications:', error);
                showNotification('An error occurred', 'error');
            }
        }

        // NEW: Helper function to update notification badge
        function updateNotificationBadge(count) {
            const badge = document.querySelector('.modalsbell-badge');
            const bell = document.querySelector('.modalsbell-bell');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Update title attribute for screen readers
            if (bell) {
                if (count > 0) {
                    bell.setAttribute('title', `${count} unread notifications`);
                } else {
                    bell.setAttribute('title', 'No unread notifications');
                }
            }
        }

        // NEW: Close notifications function (for the "View All Violations" link)
        function closeNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }

        // Logout functionality
        function logout() {
            const logoutModal = document.getElementById('logoutModal');
            if (logoutModal) {
                logoutModal.style.display = 'flex';
            }
        }

        function closeLogoutModal() {
            const logoutModal = document.getElementById('logoutModal');
            if (logoutModal) {
                logoutModal.style.display = 'none';
            }
        }

        function confirmLogout() {
            window.location.href = ROUTES.logout;
        }

        // Close logout modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        });

        // Show notification function - IMPROVED
        function showNotification(message, type) {
            // Remove any existing notifications
            const existingAlerts = document.querySelectorAll('.custom-alert');
            existingAlerts.forEach(alert => alert.remove());

            // Create notification element with new styling
            const notification = document.createElement('div');
            notification.className = `custom-alert alert-${type}`;

            // Set icon based on type
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            if (type === 'warning') icon = 'exclamation-triangle';

            notification.innerHTML = `
            <div class="alert-content">
                <div class="alert-icon">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div class="alert-message">${message}</div>
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            // Add styles if not already added
            if (!document.getElementById('notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                .custom-alert {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    min-width: 300px;
                    max-width: 400px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                    z-index: 9999;
                    overflow: hidden;
                    animation: slideInRight 0.3s ease;
                }
                .alert-content {
                    display: flex;
                    align-items: center;
                    padding: 15px 20px;
                }
                .alert-icon {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 15px;
                    flex-shrink: 0;
                }
                .alert-success .alert-icon {
                    background: #d4edda;
                    color: #155724;
                }
                .alert-error .alert-icon {
                    background: #f8d7da;
                    color: #721c24;
                }
                .alert-info .alert-icon {
                    background: #d1ecf1;
                    color: #0c5460;
                }
                .alert-warning .alert-icon {
                    background: #fff3cd;
                    color: #856404;
                }
                .alert-message {
                    flex: 1;
                    font-size: 14px;
                    line-height: 1.4;
                }
                .alert-close {
                    background: none;
                    border: none;
                    color: #999;
                    cursor: pointer;
                    font-size: 14px;
                    margin-left: 10px;
                    transition: color 0.2s;
                }
                .alert-close:hover {
                    color: #666;
                }
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Add CSS for slide out animation
        const slideOutStyle = document.createElement('style');
        slideOutStyle.textContent = `
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
        document.head.appendChild(slideOutStyle);

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationContainer = document.querySelector('.modalsbell-container');
            const dropdown = document.getElementById('notificationDropdown');

            if (notificationContainer && dropdown && !notificationContainer.contains(event.target) && dropdown
                .classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });

        // Close dropdown with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('notificationDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }

                const profileDropdown = document.getElementById('profileDropdown');
                if (profileDropdown && profileDropdown.classList.contains('show')) {
                    profileDropdown.classList.remove('show');
                }
            }
        });

        // Add CSS for improved profile UI if not already present
        const improvedProfileStyles = document.createElement('style');
        improvedProfileStyles.textContent = `
        .strength-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .strength-bar-inner {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }
        .avatar-overlay {
            cursor: pointer;
        }
        .btn-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-action:disabled:hover {
            transform: none !important;
        }
        .verification-step {
            transition: all 0.3s ease;
        }

        /* Notification read/unread states */
        .modalsbell-item.unread {
            background-color: #f8f9fa;
            border-left: 3px solid #3498db;
        }

        .modalsbell-item:not(.unread) {
            opacity: 0.8;
            background-color: #ffffff;
        }

        .modalsbell-item:not(.unread):hover {
            opacity: 1;
        }
    `;
        document.head.appendChild(improvedProfileStyles);

        // CHANGED: Force HTTPS for images on live server
        function forceHttpsForImages() {
            const images = document.querySelectorAll('img[src^="http://"]');
            images.forEach(img => {
                img.src = img.src.replace('http://', 'https://');
            });
        }

        // Call this on page load for Hostinger
        if (window.location.protocol === 'https:') {
            forceHttpsForImages();
        }
    </script>
</body>

</html>
