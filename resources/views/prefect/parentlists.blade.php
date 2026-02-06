@extends('prefect.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Parent Management</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by parent name or ID..." id="searchInput" class="search-input">
                <a href="{{ route('create.parent') }}" class="btn-primary" id="createBtn">
                    <i class="fas fa-plus"></i> Add Parent
                </a>
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

        <!-- Parent Table -->
        <div class="table-container">
            <table class="table" id="parentTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Relationship</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($parents as $parent)
                        <tr data-parent-id="{{ $parent->parent_id }}" data-fname="{{ $parent->parent_fname }}"
                            data-lname="{{ $parent->parent_lname }}" data-sex="{{ $parent->parent_sex }}"
                            data-birthdate="{{ $parent->parent_birthdate }}" data-email="{{ $parent->parent_email }}"
                            data-contact="{{ $parent->parent_contactinfo }}"
                            data-relationship="{{ $parent->parent_relationship }}" data-status="{{ $parent->status }}"
                            data-students="{{ $parent->students->toJson() }}">
                            <td>{{ $parent->parent_id }}</td>
                            <td>{{ $parent->parent_fname }}</td>
                            <td>{{ $parent->parent_lname }}</td>
                            <td>{{ $parent->parent_relationship ?? 'N/A' }}</td>
                            <td>
                                <!-- View Button -->
                                <button class="btn-view" data-parent-id="{{ $parent->parent_id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <!-- Edit Button -->
                                <button class="btn-edit" data-parent-id="{{ $parent->parent_id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="no-data">⚠️ No parents found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
            <div class="pagination-links">
                @if ($parents->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($parents->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $parents->previousPageUrl() }}" rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($parents->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $parents->nextPageUrl() }}" rel="next">Next ›</a>
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
                Showing {{ $parents->firstItem() ?? 0 }} to {{ $parents->lastItem() ?? 0 }} of {{ $parents->total() }}
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
                        Edit Parent Information
                    </h2>
                    <button class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="edit-modal-body">
                    <form id="editParentForm">
                        <input type="hidden" id="edit_parent_id" name="parent_id">

                        <div class="edit-form-grid">
                            <!-- First Name -->
                            <div class="edit-form-group">
                                <label for="edit_fname">
                                    <i class="fas fa-user"></i>
                                    First Name
                                </label>
                                <input type="text" id="edit_fname" name="parent_fname" placeholder="Enter first name"
                                    required>
                                <div class="form-hint">Enter the parent's first name</div>
                            </div>

                            <!-- Last Name -->
                            <div class="edit-form-group">
                                <label for="edit_lname">
                                    <i class="fas fa-user"></i>
                                    Last Name
                                </label>
                                <input type="text" id="edit_lname" name="parent_lname" placeholder="Enter last name"
                                    required>
                                <div class="form-hint">Enter the parent's last name</div>
                            </div>

                            <!-- Sex -->
                            <div class="edit-form-group">
                                <label for="edit_sex">
                                    <i class="fas fa-venus-mars"></i>
                                    Sex
                                </label>
                                <select id="edit_sex" name="parent_sex" required>
                                    <option value="">Select sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="form-hint">Select the parent's sex</div>
                            </div>

                            <!-- Relationship -->
                            <div class="edit-form-group">
                                <label for="edit_relationship">
                                    <i class="fas fa-heart"></i>
                                    Relationship
                                </label>
                                <select id="edit_relationship" name="parent_relationship" required>
                                    <option value="">Select relationship</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Guardian">Guardian</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="form-hint">Select the relationship to student</div>
                            </div>

                            <!-- Birthdate -->
                            <div class="edit-form-group">
                                <label for="edit_birthdate">
                                    <i class="fas fa-birthday-cake"></i>
                                    Birthdate
                                </label>
                                <input type="date" id="edit_birthdate" name="parent_birthdate" required>
                                <div class="form-hint">Select the parent's birthdate</div>
                            </div>

                            <!-- Email -->
                            <div class="edit-form-group">
                                <label for="edit_email" class="optional">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                </label>
                                <input type="email" id="edit_email" name="parent_email"
                                    placeholder="parent@email.com">
                                <div class="form-hint">Enter a valid email address (optional)</div>
                            </div>

                            <!-- Contact Information -->
                            <div class="edit-form-group">
                                <label for="edit_contact" class="optional">
                                    <i class="fas fa-phone"></i>
                                    Contact Number
                                </label>
                                <input type="tel" id="edit_contact" name="parent_contactinfo"
                                    placeholder="+63 XXX XXX XXXX" required>
                                <div class="form-hint">Enter contact number</div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="edit-form-group" style="grid-column: span 2;">
                            <label for="edit_status">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </label>
                            <select id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="form-hint">Set parent account status</div>
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

        <!-- 👤 Parent Info Modal - Updated Design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Parent Information</h3>
                            <p class="modal-subtitle" id="info_fullname"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeModalBtn">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- ID & Sex Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-id-badge"></i> Parent ID
                            </label>
                            <span class="info-value" id="info_parent_id"></span>
                        </div>
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-venus-mars"></i> Sex
                            </label>
                            <span class="info-value" id="info_sex"></span>
                        </div>
                    </div>

                    <!-- Birthdate & Relationship Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-birthday-cake"></i> Birthdate
                            </label>
                            <span class="info-value" id="info_birthdate"></span>
                        </div>
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-heart"></i> Relationship
                            </label>
                            <span class="info-value" id="info_relationship"></span>
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

                    <!-- Associated Students -->
                    <div class="students-section">
                        <h4 class="section-title">
                            <i class="fas fa-graduation-cap"></i> Associated Students
                        </h4>
                        <div class="students-container" id="studentsContainer">
                            <!-- Student data will be populated here via JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <!-- Modal Footer - REMOVE the close button -->
                <div class="modal-footer">
                    <!-- REMOVED: Close button -->
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
            const editForm = document.getElementById('editParentForm');
            const saveEditBtn = document.getElementById('saveEditBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const closeEditBtn = document.querySelector('#editModal .close-btn');

            // Edit button event listeners
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const row = this.closest('tr');
                    const parentId = row.getAttribute('data-parent-id');
                    const fname = row.getAttribute('data-fname');
                    const lname = row.getAttribute('data-lname');
                    const sex = row.getAttribute('data-sex');
                    const birthdate = row.getAttribute('data-birthdate');
                    const email = row.getAttribute('data-email');
                    const contact = row.getAttribute('data-contact');
                    const relationship = row.getAttribute('data-relationship');

                    // Fill edit form with data
                    document.getElementById('edit_parent_id').value = parentId;
                    document.getElementById('edit_fname').value = fname || '';
                    document.getElementById('edit_lname').value = lname || '';

                    // Set select values properly
                    const sexSelect = document.getElementById('edit_sex');
                    if (sex && sex.trim() !== '') {
                        sexSelect.value = sex;
                    } else {
                        sexSelect.value = '';
                    }

                    const relationshipSelect = document.getElementById('edit_relationship');
                    if (relationship && relationship.trim() !== '') {
                        relationshipSelect.value = relationship;
                    } else {
                        relationshipSelect.value = '';
                    }

                    // Format birthdate for input[type="date"]
                    const birthdateInput = document.getElementById('edit_birthdate');
                    if (birthdate && birthdate !== 'null' && birthdate.trim() !== '') {
                        try {
                            const formattedDate = new Date(birthdate).toISOString().split('T')[0];
                            birthdateInput.value = formattedDate;
                        } catch (error) {
                            console.error('Error formatting date:', error);
                            birthdateInput.value = '';
                        }
                    } else {
                        birthdateInput.value = '';
                    }

                    document.getElementById('edit_email').value = email || '';
                    document.getElementById('edit_contact').value = contact || '';

                    // Set status - check if parent has status attribute, otherwise default to active
                    const status = row.getAttribute('data-status') || 'active';
                    document.getElementById('edit_status').value = status;

                    // Show edit modal
                    editModal.style.display = 'flex';
                });
            });

            // Save edit button
            saveEditBtn.addEventListener('click', function() {
                saveParentChanges();
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

            // Function to save parent changes
            function saveParentChanges() {
                const formData = new FormData(editForm);
                const parentId = formData.get('parent_id');

                // Validate required fields
                const requiredFields = ['parent_fname', 'parent_lname', 'parent_sex', 'parent_relationship',
                    'parent_birthdate', 'parent_contactinfo'
                ];
                let isValid = true;

                requiredFields.forEach(field => {
                    const value = formData.get(field);
                    if (!value || value.trim() === '') {
                        isValid = false;
                        const input = document.getElementById(`edit_${field.replace('parent_', '')}`);
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
                notifications.showNotification('Updating parent information...', 'info');

                // Send AJAX request - FIXED: Use parentId variable, not id
                fetch('{{ route('parents.update') }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errData => {
                                throw new Error(errData.message || 'Update failed');
                            });
                        }
                        return response.json();
                    })
                    .then(responseData => {
                        // Hide edit modal
                        editModal.style.display = 'none';
                        editForm.reset();

                        // Update the table row with new data
                        updateTableRow(parentId, data);

                        // Show success notification
                        notifications.showNotification(responseData.message || 'Parent updated successfully!',
                            'success');

                        // Optionally reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error updating parent:', error);
                        notifications.showNotification(error.message ||
                            'Failed to update parent. Please try again.', 'error');
                    });
            }

            // Function to update table row with new data
            function updateTableRow(parentId, data) {
                const row = document.querySelector(`tr[data-parent-id="${parentId}"]`);
                if (row) {
                    // Update row attributes
                    row.setAttribute('data-fname', data.parent_fname);
                    row.setAttribute('data-lname', data.parent_lname);
                    row.setAttribute('data-sex', data.parent_sex);
                    row.setAttribute('data-birthdate', data.parent_birthdate);
                    row.setAttribute('data-email', data.parent_email);
                    row.setAttribute('data-contact', data.parent_contactinfo);
                    row.setAttribute('data-relationship', data.parent_relationship);

                    // Update table cells
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 5) {
                        // ID cell (unchanged)
                        // cells[0] - ID

                        // First Name cell
                        cells[1].textContent = data.parent_fname;

                        // Last Name cell
                        cells[2].textContent = data.parent_lname;

                        // Relationship cell
                        cells[3].textContent = data.parent_relationship;
                    }
                }
            }
        });

        // ==========================
        // Export All Parents (PDF & Excel)
        // ==========================

        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            exportAllParents('pdf');
        });

        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            exportAllParents('excel');
        });

        function exportAllParents(format) {
            // Get all parent rows from the current page
            const parentRows = document.querySelectorAll('#tableBody tr');

            if (parentRows.length === 0) {
                notifications.showNotification('No parents found to export.', 'warning');
                return;
            }

            // Collect all parent data
            let parentData = [];
            parentRows.forEach(row => {
                if (row.classList.contains('no-data')) return;

                parentData.push({
                    id: row.getAttribute('data-parent-id'),
                    fname: row.getAttribute('data-fname'),
                    lname: row.getAttribute('data-lname'),
                    sex: row.getAttribute('data-sex'),
                    birthdate: row.getAttribute('data-birthdate'),
                    email: row.getAttribute('data-email'),
                    contact: row.getAttribute('data-contact'),
                    relationship: row.getAttribute('data-relationship')
                });
            });

            if (parentData.length === 0) {
                notifications.showNotification('No parents found to export.', 'warning');
                return;
            }

            if (format === 'pdf') {
                exportToPDF(parentData, true);
            } else if (format === 'excel') {
                exportToExcel(parentData, true);
            }
        }

        function exportToPDF(parentData, isSelectAll = false) {
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

            const reportTitle = isSelectAll ? 'Complete Parent Registry' : 'Selected Parent Registry';
            const rowCount = parentData.length;

            // Create simple table HTML without emojis
            let tableHTML = `
                <table style="width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; margin-top: 15px;">
                    <thead>
                        <tr>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Parent ID</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">First Name</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Last Name</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Sex</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Birthdate</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Relationship</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Contact Number</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Add each parent's data to the table
            parentData.forEach((parent, index) => {
                // Format birthdate
                let formattedBirthdate = 'N/A';
                if (parent.birthdate && parent.birthdate !== 'null') {
                    formattedBirthdate = new Date(parent.birthdate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }

                // Format contact
                let formattedContact = parent.contact || 'N/A';

                // Format email
                let formattedEmail = parent.email || 'N/A';

                // Alternate row colors
                const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

                tableHTML += `
                    <tr style="background-color: ${rowColor};">
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${parent.id}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${parent.fname || 'N/A'}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${parent.lname || 'N/A'}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${parent.sex || 'N/A'}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${formattedBirthdate}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${parent.relationship || 'N/A'}</td>
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
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Parent Management System</h2>
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
                                    Total Records: <strong style="color: #000000;">${rowCount} Parent(s)</strong>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Document ID</div>
                                <div style="font-size: 14px; font-weight: 600; color: #000000;">PAR-${Date.now().toString().slice(-6)}</div>
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
                                This document contains sensitive parent information. Unauthorized distribution is prohibited.
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
                        pdf.text('Tagoloan Senior High School - Parent Management System',
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

        function exportToExcel(parentData, isSelectAll = false) {
            // Create worksheet data
            const worksheetData = [
                // Headers
                ['Parent ID', 'First Name', 'Last Name', 'Sex', 'Birthdate', 'Relationship', 'Email Address',
                    'Contact Number'
                ],
                // Data rows
                ...parentData.map(parent => {
                    // Format birthdate
                    let formattedBirthdate = 'N/A';
                    if (parent.birthdate && parent.birthdate !== 'null') {
                        formattedBirthdate = new Date(parent.birthdate).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    }

                    return [
                        parent.id,
                        parent.fname || 'N/A',
                        parent.lname || 'N/A',
                        parent.sex || 'N/A',
                        formattedBirthdate,
                        parent.relationship || 'N/A',
                        parent.email || 'N/A',
                        parent.contact || 'N/A'
                    ]
                })
            ];

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(worksheetData);

            // Set column widths
            const colWidths = [{
                    wch: 12
                }, // Parent ID
                {
                    wch: 15
                }, // First Name
                {
                    wch: 15
                }, // Last Name
                {
                    wch: 10
                }, // Sex
                {
                    wch: 15
                }, // Birthdate
                {
                    wch: 15
                }, // Relationship
                {
                    wch: 25
                }, // Email Address
                {
                    wch: 18
                } // Contact Number
            ];
            ws['!cols'] = colWidths;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Parents');

            // Generate Excel file
            const fileName = `Parents_Export_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

            notifications.showNotification('Excel file exported successfully', 'success');
        }

        // 🔍 Search Functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Modal initialization
        document.addEventListener('DOMContentLoaded', function() {
            const infoModal = document.getElementById('infoModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const closeInfoBtn = document.getElementById('closeInfoModalBtn');
            const printInfoBtn = document.getElementById('printInfoBtn');

            // Initialize modal functionality
            initializeModal();

            function initializeModal() {
                // Close modal buttons
                closeModalBtn.addEventListener('click', () => {
                    infoModal.style.display = 'none';
                });



                // Export functionality for individual parent
                printInfoBtn.addEventListener('click', function() {
                    exportParentInfo();
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
                        const parentId = row.getAttribute('data-parent-id');
                        const fname = row.getAttribute('data-fname');
                        const lname = row.getAttribute('data-lname');
                        const sex = row.getAttribute('data-sex');
                        const birthdate = row.getAttribute('data-birthdate');
                        const email = row.getAttribute('data-email');
                        const contact = row.getAttribute('data-contact');
                        const relationship = row.getAttribute('data-relationship');
                        const studentsData = row.getAttribute('data-students');

                        // Format birthdate
                        let formattedBirthdate = 'N/A';
                        if (birthdate && birthdate !== 'null') {
                            formattedBirthdate = new Date(birthdate).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }

                        // Fill info modal
                        const fullName = `${fname} ${lname}`;
                        document.getElementById('info_fullname').textContent = fullName;
                        document.getElementById('info_parent_id').textContent = parentId;
                        document.getElementById('info_sex').textContent = sex || 'N/A';
                        document.getElementById('info_birthdate').textContent = formattedBirthdate;
                        document.getElementById('info_relationship').textContent = relationship ||
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

                        // Populate student information
                        populateStudentData(studentsData);

                        // Show modal
                        infoModal.style.display = 'flex';
                    });
                });
            }

            // Export function with simple table layout
            function exportParentInfo() {
                // Get all the data from the modal
                const parentName = document.getElementById('info_fullname').textContent;
                const parentId = document.getElementById('info_parent_id').textContent;
                const parentSex = document.getElementById('info_sex').textContent;
                const parentBirthdate = document.getElementById('info_birthdate').textContent;
                const parentRelationship = document.getElementById('info_relationship').textContent;
                const parentEmail = document.getElementById('info_email').textContent;
                const parentContact = document.getElementById('info_contact').textContent;

                // Get student data
                const studentsContainer = document.getElementById('studentsContainer');
                let studentsHTML = '';

                if (studentsContainer.querySelector('.no-students')) {
                    studentsHTML = `
                        <div style="margin-top: 20px;">
                            <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Associated Students</h4>
                            <p style="text-align: center; color: #666666; font-style: italic; padding: 20px; font-size: 11px; background: #f7fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                                No students associated with this parent.
                            </p>
                        </div>
                    `;
                } else {
                    const studentItems = studentsContainer.querySelectorAll('.student-item');

                    studentsHTML = `
                        <div style="margin-top: 20px;">
                            <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Associated Students</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; margin-top: 10px;">
                                <thead>
                                    <tr>
                                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; width: 30%;">Student Name</th>
                                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; width: 15%;">Grade</th>
                                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; width: 20%;">Section</th>
                                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; width: 35%;">Adviser</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    studentItems.forEach((item, index) => {
                        const name = item.querySelector('.student-name').textContent;
                        const grade = item.querySelector('.student-grade')?.textContent || 'N/A';
                        const section = item.querySelector('.student-section')?.textContent || 'N/A';
                        const adviser = item.querySelector('.student-adviser')?.textContent || 'N/A';

                        const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

                        studentsHTML += `
                            <tr style="background-color: ${rowColor};">
                                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${name}</td>
                                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${grade}</td>
                                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${section}</td>
                                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${adviser}</td>
                            </tr>
                        `;
                    });

                    studentsHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }

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

                // Create simple parent information table
                const parentInfoTable = `
                    <div style="margin: 0 25px 25px 25px;">
                        <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Parent Information</h4>
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
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentName}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Parent ID</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentId}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sex</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentSex}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Birthdate</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentBirthdate}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Relationship</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentRelationship}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Email Address</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentEmail.includes('Not provided') ? 'N/A' : parentEmail}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Contact Number</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentContact.includes('Not provided') ? 'N/A' : parentContact}</td>
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
                                <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Parent Management System</h2>
                                <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Individual Parent Profile</p>
                            </div>
                            <div style="text-align: right;">
                                <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                            </div>
                        </div>

                        <!-- Report Summary -->
                        <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">Parent Profile</h3>
                                    <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                        Generated: ${currentDate} at ${currentTime}
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 12px; color: #000000;">Document ID</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #000000;">PAR-PROFILE-${Date.now().toString().slice(-6)}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Parent Information Table -->
                        ${parentInfoTable}

                        <!-- Associated Students Section -->
                        ${studentsHTML}

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
                                    This document contains sensitive parent information. Unauthorized distribution is prohibited.
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Load html2pdf library if not already loaded
                if (typeof html2pdf === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = () => generatePDF(element, parentName);
                    document.head.appendChild(script);
                } else {
                    generatePDF(element, parentName);
                }

                function generatePDF(element, name) {
                    // PDF options for new tab preview
                    const options = {
                        margin: [10, 15, 25, 15],
                        filename: `Parent_Profile_${name.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
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
                            pdf.text('Tagoloan Senior High School - Parent Management System',
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

            // Function to populate student data in compact format
            function populateStudentData(studentsData) {
                const studentsContainer = document.getElementById('studentsContainer');

                try {
                    const students = JSON.parse(studentsData);

                    if (!students || students.length === 0) {
                        studentsContainer.innerHTML = `
                            <div class="no-students">
                                <i class="fas fa-user-graduate"></i>
                                <span class="no-students-text">No students associated with this parent</span>
                            </div>
                        `;
                        return;
                    }

                    let studentsHTML = '';
                    students.forEach(student => {
                        // Get grade, section, and adviser from adviser relationship
                        const grade = student.adviser?.adviser_gradelevel || 'N/A';
                        const section = student.adviser?.adviser_section || 'N/A';
                        const adviser = student.adviser ?
                            `${student.adviser.adviser_fname} ${student.adviser.adviser_lname}` : 'N/A';

                        studentsHTML += `
                            <div class="student-item">
                                <div class="student-avatar">
                                    ${student.student_fname.charAt(0)}${student.student_lname.charAt(0)}
                                </div>
                                <div class="student-info">
                                    <div class="student-name">${student.student_fname} ${student.student_lname}</div>
                                    <div class="student-details">
                                        <span class="student-detail">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span class="student-grade">${grade}</span>
                                        </span>
                                        <span class="student-detail">
                                            <i class="fas fa-users"></i>
                                            <span class="student-section">${section}</span>
                                        </span>
                                        <span class="student-detail">
                                            <i class="fas fa-user-tie"></i>
                                            <span class="student-adviser">${adviser}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    studentsContainer.innerHTML = studentsHTML;

                } catch (error) {
                    console.error('Error parsing student data:', error);
                    studentsContainer.innerHTML = `
                        <div class="no-students">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="no-students-text">Error loading student information</span>
                        </div>
                    `;
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

        /* ==================== */
        /* Updated Modal Styles */
        /* ==================== */

        .compact-modal {
            max-width: 500px !important;
            width: 90% !important;
            margin: auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
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
            flex-shrink: 0;
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
            overflow-y: auto;
            flex-grow: 1;
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

        /* Students Section */
        .students-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .students-container {
            max-height: 200px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .no-students {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            color: #94a3b8;
            text-align: center;
        }

        .no-students i {
            font-size: 32px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .no-students-text {
            font-size: 14px;
            font-style: italic;
        }

        .student-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        .student-item:hover {
            background: #ffffff;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .student-info {
            flex: 1;
            min-width: 0;
        }

        .student-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .student-details {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .student-detail {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
        }

        .student-detail i {
            font-size: 11px;
            color: #94a3b8;
        }

        /* Modal Footer - Centered single button */
        .modal-footer {
            display: flex;
            justify-content: center;
            /* Changed from space-between to center */
            gap: 10px;
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .btn-secondary {
            padding: 10px 20px;
            background: #64748b;
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

        .btn-secondary:hover {
            background: #475569;
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

        /* Scrollbar Styling */
        .modal-body::-webkit-scrollbar,
        .students-container::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track,
        .students-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb,
        .students-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover,
        .students-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .compact-modal {
                width: 95% !important;
                max-height: 90vh;
            }

            .info-row {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-secondary,
            .modal-export {
                width: 100%;
                justify-content: center;
            }

            .student-details {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
@endsection
