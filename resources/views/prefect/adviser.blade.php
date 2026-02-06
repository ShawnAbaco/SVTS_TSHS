@extends('prefect.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Adviser Management</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by adviser name or ID..." id="searchInput" class="search-input">
                <a href="{{ route('create.adviser') }}" class="btn-primary" id="createBtn">
                    <i class="fas fa-plus"></i> Add Adviser
                </a>
            </div>
        </div>

        <!-- Grade Filter Section -->
        <div class="grade-filter-section"
            style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-weight: 600; color: #374151; font-size: 14px;">
                    <i class="fas fa-filter"></i> Filter by Grade:
                </div>
                <div class="grade-buttons" style="display: flex; gap: 10px;">
                    <button class="grade-btn active" data-grade="all"
                        style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        All Grades
                    </button>
                    <button class="grade-btn" data-grade="11"
                        style="padding: 8px 16px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        Grade 11
                    </button>
                    <button class="grade-btn" data-grade="12"
                        style="padding: 8px 16px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        Grade 12
                    </button>
                </div>
                <div style="margin-left: auto; font-size: 13px; color: #6b7280;">
                    <span id="filterCount">{{ $advisers->count() }}</span> advisers shown
                </div>
            </div>
        </div>

        <!-- Export Buttons Container -->
        <div class="export-buttons-container" style="display: flex; justify-content: flex-end; margin: 20px 0; gap: 10px;">
            <button class="btn-export" id="exportPdfBtn">
                📄 Export PDF
            </button>
            <button class="btn-export excel" id="exportExcelBtn">
                📊 Export Excel
            </button>
        </div>

        <!-- Adviser Table -->
        <div class="table-container">
            <table class="table" id="adviserTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Section</th>
                        <th>Grade Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($advisers as $adviser)
                        <tr data-adviser-id="{{ $adviser->adviser_id }}" data-fname="{{ $adviser->adviser_fname }}"
                            data-lname="{{ $adviser->adviser_lname }}" data-section="{{ $adviser->adviser_section }}"
                            data-gradelevel="{{ $adviser->adviser_gradelevel }}" data-email="{{ $adviser->adviser_email }}"
                            data-contact="{{ $adviser->adviser_contactinfo }}">
                            <td>{{ $adviser->adviser_id }} </td>
                            <td>{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}</td>
                            <td>{{ $adviser->adviser_section }}</td>
                            <td>{{ $adviser->adviser_gradelevel }}</td>
                            <!-- In your table actions column, update this part: -->
                            <td>
                                <!-- View Button -->
                                <button class="btn-view" data-adviser-id="{{ $adviser->adviser_id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <!-- Edit Button -->
                                <button class="btn-edit" data-adviser-id="{{ $adviser->adviser_id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="no-data">⚠️ No advisers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
            <div class="pagination-links">
                @if ($advisers->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($advisers->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $advisers->previousPageUrl() }}" rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($advisers->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $advisers->nextPageUrl() }}" rel="next">Next
                                        ›</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">Next ›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
            <div class="pagination-info">
                Showing {{ $advisers->firstItem() ?? 0 }} to {{ $advisers->lastItem() ?? 0 }} of {{ $advisers->total() }}
                entries
            </div>
        </div>

        <!-- ===== EDIT MODAL ===== -->
        <div class="modal" id="editModal" style="display: none;">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="edit-modal-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h2>
                        <i class="fas fa-edit"></i>
                        Edit Adviser Information
                    </h2>
                    <button class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="edit-modal-body">
                    <form id="editAdviserForm">
                        <input type="hidden" id="edit_adviser_id" name="adviser_id">

                        <div class="edit-form-grid">
                            <!-- First Name -->
                            <div class="edit-form-group">
                                <label for="edit_fname">
                                    <i class="fas fa-user"></i>
                                    First Name
                                </label>
                                <input type="text" id="edit_fname" name="adviser_fname" placeholder="Enter first name"
                                    required>
                                <div class="form-hint">Enter the adviser's first name</div>
                            </div>

                            <!-- Last Name -->
                            <div class="edit-form-group">
                                <label for="edit_lname">
                                    <i class="fas fa-user"></i>
                                    Last Name
                                </label>
                                <input type="text" id="edit_lname" name="adviser_lname" placeholder="Enter last name"
                                    required>
                                <div class="form-hint">Enter the adviser's last name</div>
                            </div>

                            <!-- Section -->
                            <div class="edit-form-group">
                                <label for="edit_section">
                                    <i class="fas fa-users"></i>
                                    Section
                                </label>
                                <input type="text" id="edit_section" name="adviser_section"
                                    placeholder="e.g., Section A" required>
                                <div class="form-hint">Enter the section assigned to this adviser</div>
                            </div>

                            <!-- Grade Level -->
                            <div class="edit-form-group">
                                <label for="edit_gradelevel">
                                    <i class="fas fa-graduation-cap"></i>
                                    Grade Level
                                </label>
                                <select id="edit_gradelevel" name="adviser_gradelevel" required>
                                    <option value="">Select grade level</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <div class="form-hint">Select the grade level for this adviser</div>
                            </div>

                            <!-- Email -->
                            <div class="edit-form-group">
                                <label for="edit_email" class="optional">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                </label>
                                <input type="email" id="edit_email" name="adviser_email"
                                    placeholder="adviser@email.com">
                                <div class="form-hint">Enter a valid email address (optional)</div>
                            </div>

                            <!-- Contact Information -->
                            <div class="edit-form-group">
                                <label for="edit_contact" class="optional">
                                    <i class="fas fa-phone"></i>
                                    Contact Number
                                </label>
                                <input type="tel" id="edit_contact" name="adviser_contactinfo"
                                    placeholder="+63 XXX XXX XXXX">
                                <div class="form-hint">Enter contact number (optional)</div>
                            </div>
                        </div>

                        <div class="required-fields-note">
                            Indicates required fields
                        </div>
                    </form>
                </div>

                <!-- Modal Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- 👤 Adviser Info Modal - Updated Design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Adviser Information</h3>
                            <p class="modal-subtitle" id="info_fullname"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeModalBtn">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- ID & Section Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-id-badge"></i> Adviser ID
                            </label>
                            <span class="info-value" id="info_adviser_id"></span>
                        </div>
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-users"></i> Section
                            </label>
                            <span class="info-value" id="info_section"></span>
                        </div>
                    </div>

                    <!-- Grade Level & Relationship Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-graduation-cap"></i> Grade Level
                            </label>
                            <span class="info-value" id="info_gradelevel"></span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-section">
                        <h4 class="section-title">
                            <i class="fas fa-address-book"></i> Contact Information
                        </h4>

                        <!-- Email -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <div class="contact-label">Email</div>
                                <a href="#" class="contact-value" id="info_email"></a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <div class="contact-label">Contact Number</div>
                                <a href="#" class="contact-value" id="info_contact"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button class="btn-export modal-export" id="printInfoBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div class="notification-modal" id="notificationModal">
            <div class="notification-content" id="notificationContent">
                <div class="notification-icon" id="notificationIcon"></div>
                <div class="notification-message" id="notificationMessage"></div>
                <div class="notification-actions" id="notificationActions">
                    <!-- OK button removed for success messages -->
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="notification-modal" id="confirmationModal">
            <div class="notification-content">
                <div class="notification-icon">⚠️</div>
                <div class="notification-message" id="confirmationMessage"></div>
                <div class="notification-actions">
                    <button class="btn-confirm" id="confirmAction">Confirm</button>
                    <button class="btn-cancel" id="cancelAction">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // ==========================
        // Notification Management
        // ==========================

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
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Confirmation modal
                this.confirmAction.addEventListener('click', () => {
                    if (this.confirmCallback) {
                        this.confirmCallback();
                    }
                    this.hideConfirmation();
                });

                this.cancelAction.addEventListener('click', () => {
                    if (this.cancelCallback) {
                        this.cancelCallback();
                    }
                    this.hideConfirmation();
                });

                // Close modals when clicking outside
                this.notificationModal.addEventListener('click', (e) => {
                    if (e.target === this.notificationModal) {
                        this.hideNotification();
                    }
                });

                this.confirmationModal.addEventListener('click', (e) => {
                    if (e.target === this.confirmationModal) {
                        this.hideConfirmation();
                    }
                });
            }

            showNotification(message, type = 'info') {
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
                this.notificationModal.style.display = 'none';
                if (this.autoCloseTimeout) {
                    clearTimeout(this.autoCloseTimeout);
                    this.autoCloseTimeout = null;
                }
            }

            showConfirmation(message, confirmCallback, cancelCallback = null) {
                this.confirmationMessage.textContent = message;
                this.confirmCallback = confirmCallback;
                this.cancelCallback = cancelCallback;
                this.confirmationModal.style.display = 'flex';
            }

            hideConfirmation() {
                this.confirmationModal.style.display = 'none';
                this.confirmCallback = null;
                this.cancelCallback = null;
            }
        }

        // Initialize notification manager
        const notifications = new NotificationManager();

        // ==========================
        // Edit Modal Functionality
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editAdviserForm');
            const saveEditBtn = document.getElementById('saveEditBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const closeEditBtn = document.querySelector('#editModal .close-btn');

            // Edit button event listeners
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const row = this.closest('tr');
                    const adviserId = row.getAttribute('data-adviser-id');
                    const fname = row.getAttribute('data-fname');
                    const lname = row.getAttribute('data-lname');
                    const section = row.getAttribute('data-section');
                    const gradelevel = row.getAttribute('data-gradelevel');
                    const email = row.getAttribute('data-email');
                    const contact = row.getAttribute('data-contact');

                    // Fill edit form with data
                    document.getElementById('edit_adviser_id').value = adviserId;
                    document.getElementById('edit_fname').value = fname || '';
                    document.getElementById('edit_lname').value = lname || '';
                    document.getElementById('edit_section').value = section || '';
                    document.getElementById('edit_gradelevel').value = gradelevel || '';
                    document.getElementById('edit_email').value = email || '';
                    document.getElementById('edit_contact').value = contact || '';

                    // Show edit modal
                    editModal.style.display = 'flex';
                });
            });

            // Save edit button
            saveEditBtn.addEventListener('click', function() {
                saveAdviserChanges();
            });

            // Cancel edit button
            cancelEditBtn.addEventListener('click', function() {
                editModal.style.display = 'none';
                editForm.reset();
            });

            // Close button in edit modal header
            closeEditBtn.addEventListener('click', function() {
                editModal.style.display = 'none';
                editForm.reset();
            });

            // Close modal when clicking outside
            editModal.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    editModal.style.display = 'none';
                    editForm.reset();
                }
            });

            // Function to save adviser changes
            function saveAdviserChanges() {
                const formData = new FormData(editForm);
                const adviserId = formData.get('adviser_id');

                // Validate required fields
                const requiredFields = ['adviser_fname', 'adviser_lname', 'adviser_section', 'adviser_gradelevel'];
                let isValid = true;

                requiredFields.forEach(field => {
                    const value = formData.get(field);
                    if (!value || value.trim() === '') {
                        isValid = false;
                        const input = document.getElementById(`edit_${field.replace('adviser_', '')}`);
                        if (input) {
                            input.style.borderColor = '#ef4444';
                            setTimeout(() => {
                                input.style.borderColor = '';
                            }, 2000);
                        }
                    }
                });

                if (!isValid) {
                    notifications.showNotification('Please fill in all required fields', 'warning');
                    return;
                }

                // Convert FormData to JSON
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Show loading notification
                notifications.showNotification('Updating adviser information...', 'info');

                // Send AJAX request
                fetch('{{ route('advisers.update') }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json().then(responseData => {
                        if (!response.ok) {
                            throw new Error(responseData.message || 'Update failed');
                        }
                        return responseData;
                    }))
                    .then(responseData => {
                        // Hide edit modal
                        editModal.style.display = 'none';
                        editForm.reset();

                        // Update the table row with new data
                        updateTableRow(adviserId, data);

                        // Show success notification
                        notifications.showNotification(responseData.message || 'Adviser updated successfully!',
                            'success');

                        // Optionally reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error updating adviser:', error);
                        notifications.showNotification(error.message ||
                            'Failed to update adviser. Please try again.', 'error');
                    });
            }

            // Function to update table row with new data
            function updateTableRow(adviserId, data) {
                const row = document.querySelector(`tr[data-adviser-id="${adviserId}"]`);
                if (row) {
                    // Update row attributes
                    row.setAttribute('data-fname', data.adviser_fname);
                    row.setAttribute('data-lname', data.adviser_lname);
                    row.setAttribute('data-section', data.adviser_section);
                    row.setAttribute('data-gradelevel', data.adviser_gradelevel);
                    row.setAttribute('data-email', data.adviser_email);
                    row.setAttribute('data-contact', data.adviser_contactinfo);

                    // Update table cells
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        // ID cell (unchanged)
                        // cells[0] - ID

                        // Full Name cell
                        cells[1].textContent = `${data.adviser_fname} ${data.adviser_lname}`;

                        // Section cell
                        cells[2].textContent = data.adviser_section;

                        // Grade Level cell
                        cells[3].textContent = data.adviser_gradelevel;
                    }
                }
            }
        });

        // ==========================
        // Grade Level Filter - Button Style
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const gradeButtons = document.querySelectorAll('.grade-btn');
            const filterCount = document.getElementById('filterCount');

            // Initialize active state
            gradeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    gradeButtons.forEach(btn => {
                        btn.classList.remove('active');
                        // Reset styles
                        if (btn.getAttribute('data-grade') === 'all') {
                            btn.style.background = '#e0f2fe';
                            btn.style.color = '#0369a1';
                            btn.style.border = '1px solid #bae6fd';
                        } else if (btn.getAttribute('data-grade') === '11') {
                            btn.style.background = '#e0f2fe';
                            btn.style.color = '#0369a1';
                            btn.style.border = '1px solid #bae6fd';
                        } else if (btn.getAttribute('data-grade') === '12') {
                            btn.style.background = '#fef3c7';
                            btn.style.color = '#92400e';
                            btn.style.border = '1px solid #fde68a';
                        }
                    });

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Set active styles
                    this.style.background = '#3b82f6';
                    this.style.color = 'white';
                    this.style.border = 'none';

                    // Apply filter
                    const selectedGrade = this.getAttribute('data-grade');
                    filterTableByGrade(selectedGrade);
                });
            });

            function filterTableByGrade(selectedGrade) {
                const rows = document.querySelectorAll('#tableBody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.classList.contains('no-data')) return;

                    const gradeLevel = row.getAttribute('data-gradelevel') || '';
                    const gradeNumber = gradeLevel.toString().replace('Grade ', '');

                    if (selectedGrade === 'all') {
                        row.style.display = '';
                        visibleCount++;
                    } else if (gradeNumber === selectedGrade) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                filterCount.textContent = visibleCount;

                // Show no results message if needed
                if (visibleCount === 0) {
                    const noDataRow = document.querySelector('#tableBody .no-data');
                    if (!noDataRow) {
                        const tbody = document.getElementById('tableBody');
                        const newRow = document.createElement('tr');
                        newRow.innerHTML =
                            '<td colspan="6" class="no-data">⚠️ No advisers found for this grade level</td>';
                        tbody.appendChild(newRow);
                    }
                } else {
                    const noDataRow = document.querySelector('#tableBody .no-data');
                    if (noDataRow) {
                        noDataRow.remove();
                    }
                }
            }

            // Combined search and filter function
            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const selectedGrade = document.querySelector('.grade-btn.active').getAttribute('data-grade');
                const rows = document.querySelectorAll('#tableBody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.classList.contains('no-data')) return;

                    const text = row.innerText.toLowerCase();
                    const gradeLevel = row.getAttribute('data-gradelevel') || '';
                    const gradeNumber = gradeLevel.toString().replace('Grade ', '');

                    let shouldShow = true;

                    // Apply grade filter
                    if (selectedGrade !== 'all' && gradeNumber !== selectedGrade) {
                        shouldShow = false;
                    }

                    // Apply search filter
                    if (shouldShow && searchTerm && !text.includes(searchTerm)) {
                        shouldShow = false;
                    }

                    if (shouldShow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                filterCount.textContent = visibleCount;

                // Show no results message if needed
                if (visibleCount === 0) {
                    const noDataRow = document.querySelector('#tableBody .no-data');
                    if (!noDataRow) {
                        const tbody = document.getElementById('tableBody');
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = '<td colspan="6" class="no-data">⚠️ No advisers found</td>';
                        tbody.appendChild(newRow);
                    }
                } else {
                    const noDataRow = document.querySelector('#tableBody .no-data');
                    if (noDataRow) {
                        noDataRow.remove();
                    }
                }
            }

            // Add event listener for search input
            document.getElementById('searchInput').addEventListener('input', filterTable);
        });

        // ==========================
        // Export All Advisers (PDF & Excel)
        // ==========================

        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            exportAllAdvisers('pdf');
        });

        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            exportAllAdvisers('excel');
        });

        function exportAllAdvisers(format) {
            const selectedGrade = document.querySelector('.grade-btn.active').getAttribute('data-grade');
            const rows = document.querySelectorAll('#tableBody tr');

            let adviserData = [];
            rows.forEach(row => {
                if (row.classList.contains('no-data') || row.style.display === 'none') return;

                const gradeLevel = row.getAttribute('data-gradelevel') || '';
                const gradeNumber = gradeLevel.toString().replace('Grade ', '');

                if (selectedGrade !== 'all' && gradeNumber !== selectedGrade) {
                    return;
                }

                adviserData.push({
                    id: row.getAttribute('data-adviser-id'),
                    fname: row.getAttribute('data-fname'),
                    lname: row.getAttribute('data-lname'),
                    section: row.getAttribute('data-section'),
                    gradelevel: row.getAttribute('data-gradelevel'),
                    email: row.getAttribute('data-email'),
                    contact: row.getAttribute('data-contact')
                });
            });

            if (adviserData.length === 0) {
                notifications.showNotification('No advisers found to export.', 'warning');
                return;
            }

            // Determine report title based on filter
            let reportTitle = 'Complete Adviser Registry';
            if (selectedGrade === '11') {
                reportTitle = 'Grade 11 Advisers';
            } else if (selectedGrade === '12') {
                reportTitle = 'Grade 12 Advisers';
            }

            if (format === 'pdf') {
                exportToPDF(adviserData, true, reportTitle);
            } else if (format === 'excel') {
                exportToExcel(adviserData, true, reportTitle);
            }
        }

        function exportToPDF(adviserData, isSelectAll = false, reportTitle = 'Complete Adviser Registry') {
            // Get current date and time
            const currentDate = new Date().toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const currentTime = new Date().toLocaleTimeString('en-PH', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const rowCount = adviserData.length;

            // Create simple table HTML without emojis
            let tableHTML = `
            <table style="width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; margin-top: 15px;">
                <thead>
                    <tr>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Adviser ID</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">First Name</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Last Name</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Section</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Grade Level</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Contact Number</th>
                    </tr>
                </thead>
                <tbody>
        `;

            // Add each adviser's data to the table
            adviserData.forEach((adviser, index) => {
                // Format email
                let formattedEmail = adviser.email || 'N/A';

                // Format contact
                let formattedContact = adviser.contact || 'N/A';

                // Alternate row colors
                const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

                tableHTML += `
                <tr style="background-color: ${rowColor};">
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser.id}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser.fname || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser.lname || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser.section || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser.gradelevel || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${formattedEmail}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${formattedContact}</td>
                </tr>
            `;
            });

            tableHTML += `
                </tbody>
            </table>
        `;

            // Create a temporary element for PDF generation
            const element = document.createElement('div');
            element.innerHTML = `
            <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                <!-- Professional Header with Logo on Right -->
                <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                    <div style="flex: 1;">
                        <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                        <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Adviser Management System</h2>
                        <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Official Registry Document</p>
                    </div>
                    <div style="text-align: right;">
                        <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                    </div>
                </div>

                <!-- Report Summary -->
                <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${reportTitle}</h3>
                            <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                Total Records: <strong style="color: #000000;">${rowCount} Adviser(s)</strong>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 12px; color: #000000;">Document ID</div>
                            <div style="font-size: 14px; font-weight: 600; color: #000000;">ADV-${Date.now().toString().slice(-6)}</div>
                        </div>
                    </div>
                </div>

                <!-- Simple Table Container -->
                <div style="overflow: hidden; margin: 0 25px;">
                    ${tableHTML}
                </div>

                <!-- Footer Section -->
                <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div style="text-align: left;">
                            <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                            <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                            </div>
                            <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                            <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                Prefect of Discipline
                            </div>
                        </div>

                        <!-- Date moved to bottom RIGHT -->
                        <div style="text-align: right;">
                            <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Generated On:</div>
                            <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                ${currentDate} at ${currentTime}
                            </div>
                        </div>
                    </div>

                    <!-- Confidential Notice -->
                    <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                        <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                            CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                        </div>
                        <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
                            This document contains sensitive adviser information. Unauthorized distribution is prohibited.
                        </div>
                    </div>
                </div>
            </div>
        `;

            // Load html2pdf library if not already loaded
            if (typeof html2pdf === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                script.onload = () => generatePDF(element, reportTitle);
                document.head.appendChild(script);
            } else {
                generatePDF(element, reportTitle);
            }

            function generatePDF(element, title) {
                // PDF options for new tab preview
                const options = {
                    margin: [10, 15, 25, 15],
                    filename: `${title.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        scrollY: -window.scrollY
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'landscape',
                        compress: true,
                        hotfixes: ["px_scaling"]
                    },
                    pagebreak: {
                        mode: ['avoid-all', 'css', 'legacy'],
                        before: '.page-break-before',
                        after: '.page-break-after',
                        avoid: ['tr', 'td', 'th']
                    }
                };

                notifications.showNotification('Opening PDF preview...', 'info');

                // Generate PDF and open in new tab
                html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();

                    // Add footer to each page
                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);

                        // Add footer with system name on left and page number on right
                        pdf.setFontSize(8);
                        pdf.setTextColor(100, 100, 100);

                        // System name on left footer
                        pdf.text('Tagoloan Senior High School - Adviser Management System',
                            pdf.internal.pageSize.getWidth() / 2 - 65,
                            pdf.internal.pageSize.getHeight() - 8);

                        // Page number on right footer
                        pdf.text(`Page ${i} of ${totalPages}`,
                            pdf.internal.pageSize.getWidth() - 25,
                            pdf.internal.pageSize.getHeight() - 8);
                    }

                    // Open PDF in new tab
                    const pdfBlob = pdf.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl, '_blank');

                    notifications.showNotification('PDF exported successfully', 'success');
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    notifications.showNotification('PDF generation failed. Please try again.', 'error');
                });
            }
        }

        function exportToExcel(adviserData, isSelectAll = false, reportTitle = 'Complete Adviser Registry') {
            // Create worksheet data
            const worksheetData = [
                // Headers
                ['Adviser ID', 'First Name', 'Last Name', 'Section', 'Grade Level', 'Email Address', 'Contact Number'],
                // Data rows
                ...adviserData.map(adviser => [
                    adviser.id,
                    adviser.fname || 'N/A',
                    adviser.lname || 'N/A',
                    adviser.section || 'N/A',
                    adviser.gradelevel || 'N/A',
                    adviser.email || 'N/A',
                    adviser.contact || 'N/A'
                ])
            ];

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(worksheetData);

            // Set column widths
            const colWidths = [{
                    wch: 12
                }, // Adviser ID
                {
                    wch: 15
                }, // First Name
                {
                    wch: 15
                }, // Last Name
                {
                    wch: 20
                }, // Section
                {
                    wch: 12
                }, // Grade Level
                {
                    wch: 25
                }, // Email Address
                {
                    wch: 18
                } // Contact Number
            ];
            ws['!cols'] = colWidths;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Advisers');

            // Generate Excel file with appropriate name
            const fileName = `${reportTitle.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

            notifications.showNotification('Excel file exported successfully', 'success');
        }

        // Modal initialization
        document.addEventListener('DOMContentLoaded', function() {
            const infoModal = document.getElementById('infoModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const printInfoBtn = document.getElementById('printInfoBtn');

            // Initialize modal functionality
            initializeModal();

            function initializeModal() {
                // Close modal button (X in header)
                closeModalBtn.addEventListener('click', () => {
                    infoModal.style.display = 'none';
                });

                // Print functionality for individual adviser
                printInfoBtn.addEventListener('click', function() {
                    exportAdviserInfo();
                });

                // Close modal when clicking outside
                infoModal.addEventListener('click', function(event) {
                    if (event.target === infoModal) {
                        infoModal.style.display = 'none';
                    }
                });

                // View button functionality
                document.querySelectorAll('.btn-view').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event bubbling

                        // Get the parent row
                        const row = this.closest('tr');

                        // Get data from the row
                        const adviserId = row.getAttribute('data-adviser-id');
                        const fname = row.getAttribute('data-fname');
                        const lname = row.getAttribute('data-lname');
                        const section = row.getAttribute('data-section');
                        const gradelevel = row.getAttribute('data-gradelevel');
                        const email = row.getAttribute('data-email');
                        const contact = row.getAttribute('data-contact');

                        // Fill info modal
                        const fullName = `${fname} ${lname}`;
                        document.getElementById('info_fullname').textContent = fullName;
                        document.getElementById('info_adviser_id').textContent = adviserId;
                        document.getElementById('info_section').textContent = section || 'N/A';
                        document.getElementById('info_gradelevel').textContent = gradelevel ||
                            'N/A';

                        // Email with clickable link
                        const emailLink = document.getElementById('info_email');
                        if (email && email.trim() !== '' && email !== 'null') {
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
                        if (contact && contact.trim() !== '' && contact !== 'null') {
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
            }

            // Print function for individual adviser
            function exportAdviserInfo() {
                // Get all the data from the modal
                const adviserName = document.getElementById('info_fullname').textContent;
                const adviserId = document.getElementById('info_adviser_id').textContent;
                const adviserSection = document.getElementById('info_section').textContent;
                const adviserGradelevel = document.getElementById('info_gradelevel').textContent;
                const adviserEmail = document.getElementById('info_email').textContent;
                const adviserContact = document.getElementById('info_contact').textContent;

                // Get current date and time
                const currentDate = new Date().toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const currentTime = new Date().toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Create simple adviser information table
                const adviserInfoTable = `
                <div style="margin: 0 25px 25px 25px;">
                    <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Adviser Information</h4>
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; margin-top: 10px; border: 1px solid #e2e8f0;">
                        <thead>
                            <tr>
                                <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Field</th>
                                <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background-color: #ffffff;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000; width: 30%;">Full Name</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserName}</td>
                            </tr>
                            <tr style="background-color: #f7fafc;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Adviser ID</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserId}</td>
                            </tr>
                            <tr style="background-color: #ffffff;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Section</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserSection}</td>
                            </tr>
                            <tr style="background-color: #f7fafc;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Grade Level</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserGradelevel}</td>
                            </tr>
                            <tr style="background-color: #ffffff;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Email Address</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserEmail.includes('Not provided') ? 'N/A' : adviserEmail}</td>
                            </tr>
                            <tr style="background-color: #f7fafc;">
                                <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Contact Number</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${adviserContact.includes('Not provided') ? 'N/A' : adviserContact}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;

                // Create a temporary element for PDF generation
                const element = document.createElement('div');
                element.innerHTML = `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                    <!-- Professional Header with Logo on Right -->
                    <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Adviser Management System</h2>
                            <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Individual Adviser Profile</p>
                        </div>
                        <div style="text-align: right;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Report Summary -->
                    <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">Adviser Profile</h3>
                                <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                    Generated: ${currentDate} at ${currentTime}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Document ID</div>
                                <div style="font-size: 14px; font-weight: 600; color: #000000;">ADV-PROFILE-${Date.now().toString().slice(-6)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Simple Adviser Information Table -->
                    ${adviserInfoTable}

                    <!-- Footer Section -->
                    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="text-align: left;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Prefect of Discipline
                                </div>
                            </div>

                            <!-- Date moved to bottom RIGHT -->
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Verified By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    School Principal
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Tagoloan Senior High School
                                </div>
                            </div>
                        </div>

                        <!-- Confidential Notice -->
                        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                            <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                                CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                            </div>
                            <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
                                This document contains sensitive adviser information. Unauthorized distribution is prohibited.
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Load html2pdf library if not already loaded
                if (typeof html2pdf === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = () => generatePDF(element, adviserName);
                    document.head.appendChild(script);
                } else {
                    generatePDF(element, adviserName);
                }

                function generatePDF(element, name) {
                    // PDF options for new tab preview
                    const options = {
                        margin: [10, 15, 25, 15],
                        filename: `Adviser_Profile_${name.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            logging: false,
                            scrollY: -window.scrollY
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait',
                            compress: true,
                            hotfixes: ["px_scaling"]
                        },
                        pagebreak: {
                            mode: ['avoid-all', 'css', 'legacy'],
                            before: '.page-break-before',
                            after: '.page-break-after',
                            avoid: ['tr', 'td', 'th']
                        }
                    };

                    notifications.showNotification('Opening PDF preview...', 'info');

                    // Generate PDF and open in new tab
                    html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                        const totalPages = pdf.internal.getNumberOfPages();

                        // Add footer to each page
                        for (let i = 1; i <= totalPages; i++) {
                            pdf.setPage(i);

                            // Add footer with system name on left and page number on right
                            pdf.setFontSize(8);
                            pdf.setTextColor(100, 100, 100);

                            // System name on left footer
                            pdf.text('Tagoloan Senior High School - Adviser Management System',
                                pdf.internal.pageSize.getWidth() / 2 - 65,
                                pdf.internal.pageSize.getHeight() - 8);

                            // Page number on right footer
                            pdf.text(`Page ${i} of ${totalPages}`,
                                pdf.internal.pageSize.getWidth() - 25,
                                pdf.internal.pageSize.getHeight() - 8);
                        }

                        // Open PDF in new tab
                        const pdfBlob = pdf.output('blob');
                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        window.open(pdfUrl, '_blank');

                        notifications.showNotification('PDF exported successfully', 'success');
                    }).catch(error => {
                        console.error('PDF generation error:', error);
                        notifications.showNotification('PDF generation failed. Please try again.', 'error');
                    });
                }
            }
        });
    </script>
    <style>
        /* View Button Styles */
        .btn-view {
            padding: 6px 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-view:active {
            transform: translateY(0);
        }

        .btn-view i {
            font-size: 12px;
        }

        /* Table adjustments for new column */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }

        .table tbody tr:hover {
            background: #f1f5f9;
        }

        .table tbody tr td:last-child {
            text-align: center;
        }

        /* Grade Filter Section */
        .grade-filter-section {
            transition: all 0.3s ease;
        }

        .grade-btn {
            transition: all 0.3s ease;
        }

        .grade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* ==================== */
        /* Updated Modal Styles */
        /* ==================== */

        .compact-modal {
            max-width: 450px !important;
            width: 90% !important;
            margin: auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Modal Header */
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-subtitle {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }

        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .close-modal:hover {
            opacity: 1;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
            background: #ffffff;
            max-height: 60vh;
            overflow-y: auto;
        }

        .info-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-group {
            flex: 1;
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-value {
            display: block;
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        /* Contact Section */
        .contact-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #475569;
            margin: 0 0 15px 0;
            font-weight: 600;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background 0.2s;
        }

        .contact-item:hover {
            background: #f1f5f9;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
            background: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .contact-details {
            flex: 1;
        }

        .contact-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .contact-value {
            display: block;
            font-size: 13px;
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
            margin-top: 2px;
            word-break: break-all;
        }

        .contact-value:hover {
            text-decoration: underline;
        }

        .contact-value.disabled {
            color: #94a3b8;
            cursor: default;
            text-decoration: none;
        }

        /* Modal Footer - UPDATED: Center single button */
        .modal-footer {
            display: flex;
            justify-content: center;
            /* Changed from space-between */
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .modal-export {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .modal-export:hover {
            background: #2563eb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .grade-filter-section>div {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .grade-buttons {
                width: 100%;
            }

            .grade-btn {
                flex: 1;
                text-align: center;
            }

            #filterCount {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 480px) {
            .compact-modal {
                width: 95% !important;
            }

            .info-row {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-export {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection
