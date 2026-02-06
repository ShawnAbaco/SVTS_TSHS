@extends('adviser.NewAdviser.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Student Management</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput" class="search-input">
                <a href="{{ route('adviser.create.student') }}" class="btn-primary" id="createBtn">
                    <i class="fas fa-plus"></i> Add Student
                </a>
            </div>
        </div>



        <!-- Export Buttons Container -->
        <div class="export-buttons-container" style="display: flex; justify-content: flex-end; margin: 20px 0; gap: 10px;">
            <button class="btn-export" id="exportPdfBtn">
                Export PDF
            </button>
            <button class="btn-export excel" id="exportExcelBtn">
                Export Excel
            </button>
        </div>

        <!-- Student Table -->
        <div class="table-container">
            <table class="table" id="studentTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Sex</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Adviser</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($students as $student)
                        @php
                            // Get parent data if it exists
                            $parentData = $student->parent
                                ? [
                                    'parent_fname' => $student->parent->parent_fname,
                                    'parent_lname' => $student->parent->parent_lname,
                                    'parent_sex' => $student->parent->parent_sex,
                                    'parent_email' => $student->parent->parent_email,
                                    'parent_contactinfo' => $student->parent->parent_contactinfo,
                                    'parent_relationship' => $student->parent->parent_relationship,
                                    'parent_birthdate' => $student->parent->parent_birthdate,
                                ]
                                : null;
                        @endphp
                        <tr data-student-id="{{ $student->student_id }}" data-fname="{{ $student->student_fname }}"
                            data-lname="{{ $student->student_lname }}" data-sex="{{ $student->student_sex }}"
                            data-birthdate="{{ $student->student_birthdate }}"
                            data-address="{{ $student->student_address }}"
                            data-contact="{{ $student->student_contactinfo }}"
                            data-parent="{{ $parentData ? json_encode($parentData) : 'null' }}"
                            data-grade="{{ $student->adviser->adviser_gradelevel ?? 'N/A' }}"
                            data-section="{{ $student->adviser->adviser_section ?? 'N/A' }}"
                            data-adviser="{{ $student->adviser ? $student->adviser->adviser_fname . ' ' . $student->adviser->adviser_lname : 'N/A' }}">
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->student_fname }}</td>
                            <td>{{ $student->student_lname }}</td>
                            <td>{{ ucfirst($student->student_sex) }}</td>
                            <td>{{ $student->adviser->adviser_gradelevel ?? 'N/A' }}</td>
                            <td>{{ $student->adviser->adviser_section ?? 'N/A' }}</td>
                            <td>
                                @if ($student->adviser)
                                    {{ $student->adviser->adviser_fname }} {{ $student->adviser->adviser_lname }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <!-- View Button -->
                                <button class="btn-view" data-student-id="{{ $student->student_id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <!-- Edit Button -->
                                <button class="btn-edit" data-student-id="{{ $student->student_id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="no-data">⚠️ No students found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
            <div class="pagination-links">
                @if ($students->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($students->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($students->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next">Next ›</a>
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
                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of
                {{ $students->total() }} entries
            </div>
        </div>

        <!-- ===== EDIT MODAL ===== -->
        <div class="modal" id="editModal" style="display: none;">
            <div class="modal-content" style="max-width: 700px;">
                <!-- Modal Header -->
                <div class="edit-modal-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h2>
                        <i class="fas fa-edit"></i>
                        Edit Student Information
                    </h2>
                    <button class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="edit-modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="edit_student_id" name="student_id">

                        <div class="edit-form-grid" style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <!-- First Name -->
                            <div class="edit-form-group">
                                <label for="edit_fname">
                                    <i class="fas fa-user"></i>
                                    First Name
                                </label>
                                <input type="text" id="edit_fname" name="student_fname" placeholder="Enter first name"
                                    required>
                                <div class="form-hint">Enter the student's first name</div>
                            </div>

                            <!-- Last Name -->
                            <div class="edit-form-group">
                                <label for="edit_lname">
                                    <i class="fas fa-user"></i>
                                    Last Name
                                </label>
                                <input type="text" id="edit_lname" name="student_lname" placeholder="Enter last name"
                                    required>
                                <div class="form-hint">Enter the student's last name</div>
                            </div>

                            <!-- Sex -->
                            <div class="edit-form-group">
                                <label for="edit_sex">
                                    <i class="fas fa-venus-mars"></i>
                                    Sex
                                </label>
                                <select id="edit_sex" name="student_sex" required>
                                    <option value="">Select sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div class="form-hint">Select the student's sex</div>
                            </div>

                            <!-- Birthdate -->
                            <div class="edit-form-group">
                                <label for="edit_birthdate">
                                    <i class="fas fa-birthday-cake"></i>
                                    Birthdate
                                </label>
                                <input type="date" id="edit_birthdate" name="student_birthdate" required>
                                <div class="form-hint">Select the student's birthdate</div>
                            </div>

                            <!-- Address -->
                            <div class="edit-form-group" style="grid-column: span 2;">
                                <label for="edit_address">
                                    <i class="fas fa-home"></i>
                                    Address
                                </label>
                                <input type="text" id="edit_address" name="student_address"
                                    placeholder="Enter complete address" required>
                                <div class="form-hint">Enter the student's complete address</div>
                            </div>

                            <!-- Contact Information -->
                            <div class="edit-form-group" style="grid-column: span 2;">
                                <label for="edit_contact">
                                    <i class="fas fa-phone"></i>
                                    Contact Number
                                </label>
                                <input type="tel" id="edit_contact" name="student_contactinfo"
                                    placeholder="+63 XXX XXX XXXX" required>
                                <div class="form-hint">Enter contact number</div>
                            </div>

                            <!-- Status -->
                            <div class="edit-form-group">
                                <label for="edit_status">
                                    <i class="fas fa-toggle-on"></i>
                                    Status
                                </label>
                                <select id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="form-hint">Set student account status</div>
                            </div>

                            <!-- Adviser Selection -->
                            <div class="edit-form-group">
                                <label for="edit_adviser_id">
                                    <i class="fas fa-user-tie"></i>
                                    Adviser
                                </label>
                                <select id="edit_adviser_id" name="adviser_id" required>
                                    <option value="">Select Adviser</option>
                                    @foreach ($advisers as $adviser)
                                        <option value="{{ $adviser->adviser_id }}">
                                            {{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }} - Grade
                                            {{ $adviser->adviser_gradelevel }} - {{ $adviser->adviser_section }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-hint">Select the student's adviser</div>
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

        <!-- 👤 Student Info Modal - Updated Compact Design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Student Information</h3>
                            <p class="modal-subtitle" id="info_fullname"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeModalBtn">&times;</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="modal-tabs">
                    <button class="tab-btn active" data-tab="student-info">
                        <i class="fas fa-user"></i> Student Info
                    </button>
                    <button class="tab-btn" data-tab="violations">
                        <i class="fas fa-exclamation-triangle"></i> Violations
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Student Information Tab -->
                    <div class="tab-pane active" id="student-info-tab">
                        <!-- Student Details -->
                        <div class="modal-body">
                            <!-- Basic Information -->
                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-id-badge"></i> Student ID
                                    </label>
                                    <span class="info-value" id="info_student_id"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-venus-mars"></i> Sex
                                    </label>
                                    <span class="info-value" id="info_sex"></span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-birthday-cake"></i> Birthdate
                                    </label>
                                    <span class="info-value" id="info_birthdate"></span>
                                </div>
                            </div>

                            <!-- School Information -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-school"></i> School Information
                                </h4>
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-graduation-cap"></i> Grade
                                        </label>
                                        <span class="info-value" id="info_grade"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-users"></i> Section
                                        </label>
                                        <span class="info-value" id="info_section"></span>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-user-tie"></i> Adviser
                                        </label>
                                        <span class="info-value" id="info_adviser"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="contact-section">
                                <h4 class="section-title">
                                    <i class="fas fa-address-book"></i> Contact Information
                                </h4>

                                <!-- Address -->
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="contact-details">
                                        <div class="contact-label">Address</div>
                                        <span class="contact-value" id="info_address"></span>
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

                            <!-- Parent/Guardian Information -->
                            <div class="parents-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user-friends"></i> Parent/Guardian
                                </h4>
                                <div class="parents-container" id="parentsContainer">
                                    <!-- Parent data will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Violations Tab -->
                    <div class="tab-pane" id="violations-tab">
                        <div class="modal-body">
                            <div class="violations-header">
                                <h4 id="violation_student_name"></h4>
                                <p id="violation_student_id"></p>
                            </div>

                            <div class="violations-container" id="violationsContainer">
                                <!-- Violations will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer - UPDATED -->
                <div class="modal-footer">
                    <button class="btn-export modal-export" id="printInfoBtn" style="flex: 1; justify-content: center;">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn-export modal-export" id="printViolationsBtn"
                        style="display: none; flex: 1; justify-content: center;">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>s

        <!-- Notification Modal -->
        <div class="notification-modal" id="notificationModal">
            <div class="notification-content" id="notificationContent">
                <div class="notification-icon" id="notificationIcon"></div>
                <div class="notification-message" id="notificationMessage"></div>
                <div class="notification-actions" id="notificationActions"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // ==========================
        // Helper Functions
        // ==========================
        function formatDateTime(dateTimeString) {
            if (!dateTimeString || dateTimeString === 'N/A' || dateTimeString === 'null') return 'Not Set';

            try {
                const date = new Date(dateTimeString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateTimeString;
            }
        }

        async function loadAdvisers() {
            try {
                const response = await fetch('/adviser/advisers/all', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const advisers = await response.json();
                const select = document.getElementById('edit_adviser_id');
                select.innerHTML = '<option value="">Select Adviser</option>';

                advisers.forEach(adviser => {
                    const option = document.createElement('option');
                    option.value = adviser.adviser_id;
                    option.textContent =
                        `${adviser.adviser_fname} ${adviser.adviser_lname} - Grade ${adviser.adviser_gradelevel} - ${adviser.adviser_section}`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading advisers:', error);
                document.getElementById('edit_adviser_id').innerHTML =
                    '<option value="">Error loading advisers</option>';
            }
        }

        // ==========================
        // Notification Manager
        // ==========================
        class NotificationManager {
            constructor() {
                this.notificationModal = document.getElementById('notificationModal');
                this.notificationMessage = document.getElementById('notificationMessage');
                this.notificationIcon = document.getElementById('notificationIcon');
                this.notificationActions = document.getElementById('notificationActions');
                this.autoCloseTimeout = null;
                this.setupEventListeners();
            }

            setupEventListeners() {
                this.notificationModal.addEventListener('click', (e) => {
                    if (e.target === this.notificationModal) {
                        this.hideNotification();
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

                if (this.autoCloseTimeout) {
                    clearTimeout(this.autoCloseTimeout);
                }

                if (type === 'success') {
                    this.notificationActions.innerHTML = '';
                    this.notificationModal.style.display = 'flex';

                    this.autoCloseTimeout = setTimeout(() => {
                        this.hideNotification();
                    }, 1000);
                } else {
                    this.notificationActions.innerHTML =
                        '<button class="btn-confirm" id="notificationConfirm">OK</button>';

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
        }

        const notifications = new NotificationManager();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // ==========================
        // Edit Modal Functionality for Students
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editStudentForm');
            const saveEditBtn = document.getElementById('saveEditBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const closeEditBtn = document.querySelector('#editModal .close-btn');

            // Edit button event listeners
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const row = this.closest('tr');
                    const studentId = row.getAttribute('data-student-id');
                    const fname = row.getAttribute('data-fname');
                    const lname = row.getAttribute('data-lname');
                    const sex = row.getAttribute('data-sex');
                    const birthdate = row.getAttribute('data-birthdate');
                    const address = row.getAttribute('data-address');
                    const contact = row.getAttribute('data-contact');
                    const grade = row.getAttribute('data-grade');
                    const section = row.getAttribute('data-section');
                    const adviserName = row.getAttribute('data-adviser');

                    // Fill edit form with data
                    document.getElementById('edit_student_id').value = studentId;
                    document.getElementById('edit_fname').value = fname || '';
                    document.getElementById('edit_lname').value = lname || '';

                    // Set sex select value
                    if (sex && sex.trim() !== '') {
                        document.getElementById('edit_sex').value = sex;
                    } else {
                        document.getElementById('edit_sex').value = '';
                    }

                    // Format birthdate for input[type="date"]
                    if (birthdate && birthdate !== 'null' && birthdate.trim() !== '') {
                        try {
                            const formattedDate = new Date(birthdate).toISOString().split('T')[0];
                            document.getElementById('edit_birthdate').value = formattedDate;
                        } catch (error) {
                            console.error('Error formatting date:', error);
                            document.getElementById('edit_birthdate').value = '';
                        }
                    } else {
                        document.getElementById('edit_birthdate').value = '';
                    }

                    document.getElementById('edit_address').value = address || '';
                    document.getElementById('edit_contact').value = contact || '';

                    // Get adviser ID from the row if available, or try to match from adviser name
                    let adviserId = '';
                    const adviserSelect = document.getElementById('edit_adviser_id');

                    // Try to find matching adviser in dropdown
                    for (let i = 0; i < adviserSelect.options.length; i++) {
                        const option = adviserSelect.options[i];
                        if (option.text.includes(adviserName) && adviserName !== 'N/A') {
                            adviserId = option.value;
                            break;
                        }
                    }

                    if (adviserId) {
                        adviserSelect.value = adviserId;
                    } else {
                        adviserSelect.value = '';
                    }

                    // Set status - default to active
                    document.getElementById('edit_status').value = 'active';

                    // Show edit modal
                    editModal.style.display = 'flex';
                });
            });

            // Save edit button
            saveEditBtn.addEventListener('click', function() {
                saveStudentChanges();
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

            function saveStudentChanges() {
                const formData = new FormData(editForm);
                const studentId = formData.get('student_id');

                // Validate required fields
                const requiredFields = ['student_fname', 'student_lname', 'student_sex', 'student_birthdate',
                    'student_address', 'student_contactinfo', 'adviser_id'
                ];
                let isValid = true;

                requiredFields.forEach(field => {
                    const value = formData.get(field);
                    if (!value || value.trim() === '') {
                        isValid = false;
                        const input = document.getElementById(`edit_${field.replace('student_', '')}`);
                        if (!input) {
                            // Try without student_ prefix for adviser_id
                            const input2 = document.getElementById(`edit_${field}`);
                            if (input2) {
                                input2.style.borderColor = '#ef4444';
                                setTimeout(() => {
                                    input2.style.borderColor = '';
                                }, 2000);
                            }
                        } else {
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
                notifications.showNotification('Updating student information...', 'info');

                // Send AJAX request - FIXED: Use correct route name and fix typo
                fetch('{{ route('adviser.students.update') }}', {
                        method: 'PUT', // Fixed typo from "mmethod" to "method"
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ...data,
                            student_id: studentId // Make sure student_id is included
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errData => {
                                throw new Error(errData.message || 'Update failed with status: ' +
                                    response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(responseData => {
                        // Hide edit modal
                        editModal.style.display = 'none';
                        editForm.reset();

                        // Update the table row with new data
                        updateStudentTableRow(studentId, data);

                        // Show success notification
                        notifications.showNotification(responseData.message || 'Student updated successfully!',
                            'success');

                        // Optionally reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error updating student:', error);
                        notifications.showNotification(error.message ||
                            'Failed to update student. Please try again.', 'error');
                    });
            }

            // Function to update table row with new data
            function updateStudentTableRow(studentId, data) {
                const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
                if (row) {
                    // Update row attributes
                    row.setAttribute('data-fname', data.student_fname);
                    row.setAttribute('data-lname', data.student_lname);
                    row.setAttribute('data-sex', data.student_sex);
                    row.setAttribute('data-birthdate', data.student_birthdate);
                    row.setAttribute('data-address', data.student_address);
                    row.setAttribute('data-contact', data.student_contactinfo);

                    // Update table cells
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 8) {
                        // ID cell (unchanged)
                        // cells[0] - ID

                        // First Name cell
                        cells[1].textContent = data.student_fname;

                        // Last Name cell
                        cells[2].textContent = data.student_lname;

                        // Sex cell
                        cells[3].textContent = data.student_sex || 'N/A';
                    }
                }
            }
        });

        // ==========================
        // Export All Students (PDF & Excel)
        // ==========================
        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            exportAllStudents('pdf');
        });

        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            exportAllStudents('excel');
        });

        function exportAllStudents(format) {
            const rows = document.querySelectorAll('#tableBody tr');

            let studentData = [];
            rows.forEach(row => {
                if (row.classList.contains('no-data')) return;

                studentData.push({
                    id: row.getAttribute('data-student-id'),
                    fname: row.getAttribute('data-fname'),
                    lname: row.getAttribute('data-lname'),
                    sex: row.getAttribute('data-sex'),
                    birthdate: row.getAttribute('data-birthdate'),
                    address: row.getAttribute('data-address'),
                    contact: row.getAttribute('data-contact'),
                    grade: row.getAttribute('data-grade'),
                    section: row.getAttribute('data-section'),
                    adviser: row.getAttribute('data-adviser')
                });
            });

            if (studentData.length === 0) {
                notifications.showNotification('No students found to export.', 'warning');
                return;
            }

            // Determine report title based on filter
            let reportTitle = 'Complete Student Registry';

            if (format === 'pdf') {
                exportToPDF(studentData, reportTitle);
            } else if (format === 'excel') {
                exportToExcel(studentData, reportTitle);
            }
        }

        function exportToPDF(studentData, reportTitle = 'Complete Student Registry') {
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

            const rowCount = studentData.length;

            // Create table HTML
            let tableHTML = `
            <table style="width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; margin-top: 15px;">
                <thead>
                    <tr>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Student ID</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">First Name</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Last Name</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Sex</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Grade</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Section</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Adviser</th>
                        <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px;">Contact</th>
                    </tr>
                </thead>
                <tbody>
        `;

            // Add each student's data to the table
            studentData.forEach((student, index) => {
                // Format birthdate
                let formattedBirthdate = 'N/A';
                if (student.birthdate && student.birthdate !== 'null') {
                    formattedBirthdate = new Date(student.birthdate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }

                // Format contact
                let formattedContact = student.contact || 'N/A';

                // Alternate row colors
                const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

                tableHTML += `
                <tr style="background-color: ${rowColor};">
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.id}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.fname || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.lname || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.sex || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.grade || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.section || 'N/A'}</td>
                    <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 9px; color: #000000; word-wrap: break-word;">${student.adviser || 'N/A'}</td>
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
                        <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Student Management System</h2>
                        <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">${reportTitle} Document</p>
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
                                Total Records: <strong style="color: #000000;">${rowCount} Student(s)</strong>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 12px; color: #000000;">Document ID</div>
                            <div style="font-size: 14px; font-weight: 600; color: #000000;">STUD-${Date.now().toString().slice(-6)}</div>
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
                                {{ Auth::user()->adviser_fname }} {{ Auth::user()->adviser_lname }}
                            </div>
                            <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                            <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                Adviser
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
                            This document contains sensitive student information. Unauthorized distribution is prohibited.
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
                        pdf.text('Tagoloan Senior High School - Student Management System',
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

        function exportToExcel(studentData, reportTitle = 'Complete Student Registry') {
            // Create worksheet data
            const worksheetData = [
                // Headers
                ['Student ID', 'First Name', 'Last Name', 'Sex', 'Grade', 'Section', 'Adviser', 'Contact'],
                // Data rows
                ...studentData.map(student => {
                    // Format contact
                    let formattedContact = student.contact || 'N/A';

                    return [
                        student.id,
                        student.fname || 'N/A',
                        student.lname || 'N/A',
                        student.sex || 'N/A',
                        student.grade || 'N/A',
                        student.section || 'N/A',
                        student.adviser || 'N/A',
                        formattedContact
                    ]
                })
            ];

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(worksheetData);

            // Set column widths
            const colWidths = [{
                    wch: 12
                }, // Student ID
                {
                    wch: 15
                }, // First Name
                {
                    wch: 15
                }, // Last Name
                {
                    wch: 8
                }, // Sex
                {
                    wch: 8
                }, // Grade
                {
                    wch: 12
                }, // Section
                {
                    wch: 20
                }, // Adviser
                {
                    wch: 15
                } // Contact
            ];
            ws['!cols'] = colWidths;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Students');

            // Generate Excel file
            const fileName = `${reportTitle.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

            notifications.showNotification('Excel file exported successfully', 'success');
        }

        // ==========================
        // Combined Student Info Modal with Tabs
        // ==========================
        document.addEventListener('DOMContentLoaded', function() {
            const infoModal = document.getElementById('infoModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const printInfoBtn = document.getElementById('printInfoBtn');
            const printViolationsBtn = document.getElementById('printViolationsBtn');
            const violationsContainer = document.getElementById('violationsContainer');

            // Tab elements
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            let currentStudentData = null;

            // Initialize modal functionality
            initializeModal();

            function initializeModal() {
                // Close modal buttons
                closeModalBtn.addEventListener('click', () => {
                    infoModal.style.display = 'none';
                });

                // Export functionality
                printInfoBtn.addEventListener('click', function() {
                    exportStudentProfilePDF();
                });

                printViolationsBtn.addEventListener('click', function() {
                    exportViolationsPDF();
                });

                // Close modal when clicking outside
                infoModal.addEventListener('click', function(event) {
                    if (event.target === infoModal) {
                        infoModal.style.display = 'none';
                    }
                });

                // Tab switching functionality
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');

                        // Update active tab button
                        tabBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        // Show corresponding tab pane
                        tabPanes.forEach(pane => pane.classList.remove('active'));
                        document.getElementById(`${tabId}-tab`).classList.add('active');

                        // Show/hide export buttons based on active tab
                        if (tabId === 'student-info') {
                            printInfoBtn.style.display = 'flex';
                            printViolationsBtn.style.display = 'none';
                        } else if (tabId === 'violations') {
                            printInfoBtn.style.display = 'none';
                            printViolationsBtn.style.display = 'flex';

                            // Load violations if not already loaded
                            if (currentStudentData && (!violationsContainer.querySelector(
                                        '.violation-item') ||
                                    violationsContainer.querySelector('.loading'))) {
                                loadViolations(currentStudentData.id);
                            }
                        }
                    });
                });

                // View button functionality
                document.querySelectorAll('.btn-view').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event bubbling

                        // Get the parent row
                        const row = this.closest('tr');

                        // Get data from the row
                        const studentId = row.getAttribute('data-student-id');
                        const fname = row.getAttribute('data-fname');
                        const lname = row.getAttribute('data-lname');
                        const sex = row.getAttribute('data-sex');
                        const birthdate = row.getAttribute('data-birthdate');
                        const address = row.getAttribute('data-address');
                        const contact = row.getAttribute('data-contact');
                        const parentDataStr = row.getAttribute('data-parent');
                        const grade = row.getAttribute('data-grade');
                        const section = row.getAttribute('data-section');
                        const adviser = row.getAttribute('data-adviser');

                        currentStudentData = {
                            id: studentId,
                            name: `${fname} ${lname}`,
                            fname: fname,
                            lname: lname,
                            sex: sex,
                            birthdate: birthdate,
                            address: address,
                            contact: contact,
                            grade: grade,
                            section: section,
                            adviser: adviser,
                            parentData: parentDataStr
                        };

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
                        document.getElementById('info_fullname').textContent = currentStudentData
                            .name;
                        document.getElementById('info_student_id').textContent = studentId;
                        document.getElementById('info_sex').textContent = sex || 'N/A';
                        document.getElementById('info_birthdate').textContent = formattedBirthdate;
                        document.getElementById('info_address').textContent = address || 'N/A';
                        document.getElementById('info_grade').textContent = grade || 'N/A';
                        document.getElementById('info_section').textContent = section || 'N/A';
                        document.getElementById('info_adviser').textContent = adviser || 'N/A';

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

                        // Fill violations tab header
                        document.getElementById('violation_student_name').textContent =
                            currentStudentData.name;
                        document.getElementById('violation_student_id').textContent =
                            `Student ID: ${studentId}`;

                        // Populate parent information
                        populateParentData(parentDataStr);

                        // Clear violations container
                        violationsContainer.innerHTML = '';

                        // Show modal and set to first tab (Student Information)
                        tabBtns.forEach(b => b.classList.remove('active'));
                        tabPanes.forEach(pane => pane.classList.remove('active'));

                        document.querySelector('.tab-btn[data-tab="student-info"]').classList.add(
                            'active');
                        document.getElementById('student-info-tab').classList.add('active');

                        printInfoBtn.style.display = 'flex';
                        printViolationsBtn.style.display = 'none';

                        infoModal.style.display = 'flex';
                    });
                });
            }

            // Function to populate parent data
            function populateParentData(parentDataStr) {
                const parentsContainer = document.getElementById('parentsContainer');

                try {
                    if (parentDataStr && parentDataStr !== 'null') {
                        const parentData = JSON.parse(parentDataStr);

                        if (parentData && parentData.parent_fname) {
                            let parentBirthdate = 'N/A';
                            if (parentData.parent_birthdate && parentData.parent_birthdate !== 'null') {
                                parentBirthdate = new Date(parentData.parent_birthdate).toLocaleDateString(
                                    'en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    });
                            }

                            let parentHTML = `
                            <div class="parent-card">
                                <div class="parent-header">
                                    <h5>${parentData.parent_fname} ${parentData.parent_lname}</h5>
                                </div>
                                <div class="parent-details">
                                    <div class="parent-detail">
                                        <span class="detail-label">
                                            <i class="fas fa-heart"></i> Relationship:
                                        </span>
                                        <span class="detail-value">${parentData.parent_relationship || 'N/A'}</span>
                                    </div>
                                    <div class="parent-detail">
                                        <span class="detail-label">
                                            <i class="fas fa-venus-mars"></i> Sex:
                                        </span>
                                        <span class="detail-value">${parentData.parent_sex || 'N/A'}</span>
                                    </div>
                                    <div class="parent-detail">
                                        <span class="detail-label">
                                            <i class="fas fa-birthday-cake"></i> Birthdate:
                                        </span>
                                        <span class="detail-value">${parentBirthdate}</span>
                                    </div>
                        `;

                            if (parentData.parent_contactinfo || parentData.parent_email) {
                                if (parentData.parent_contactinfo) {
                                    parentHTML += `
                                    <div class="parent-detail">
                                        <span class="detail-label">
                                            <i class="fas fa-phone"></i> Contact:
                                        </span>
                                        <span class="detail-value">
                                            <a href="tel:${parentData.parent_contactinfo}">${parentData.parent_contactinfo}</a>
                                        </span>
                                    </div>
                                `;
                                }

                                if (parentData.parent_email) {
                                    parentHTML += `
                                    <div class="parent-detail">
                                        <span class="detail-label">
                                            <i class="fas fa-envelope"></i> Email:
                                        </span>
                                        <span class="detail-value">
                                            <a href="mailto:${parentData.parent_email}">${parentData.parent_email}</a>
                                        </span>
                                    </div>
                                `;
                                }
                            }

                            parentHTML += `</div></div>`;
                            parentsContainer.innerHTML = parentHTML;
                        } else {
                            parentsContainer.innerHTML = `
                            <div class="no-parents">
                                <i class="fas fa-user-slash"></i>
                                <span class="no-parents-text">No parent/guardian information available</span>
                            </div>
                        `;
                        }
                    } else {
                        parentsContainer.innerHTML = `
                        <div class="no-parents">
                            <i class="fas fa-user-slash"></i>
                            <span class="no-parents-text">No parent/guardian information available</span>
                        </div>
                    `;
                    }
                } catch (error) {
                    console.error('Error parsing parent data:', error);
                    parentsContainer.innerHTML = `
                    <div class="no-parents">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="no-parents-text">Error loading parent information</span>
                    </div>
                `;
                }
            }

            // Load violations function
            async function loadViolations(studentId) {
                try {
                    violationsContainer.innerHTML = `
                    <div class="loading-violations">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading violations...</span>
                    </div>
                `;

                    const response = await fetch(`/adviser/students/${studentId}/violationsforstudent`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load violations');
                    }

                    const result = await response.json();

                    // Handle both response structures
                    let violations = [];
                    if (result.success === false) {
                        throw new Error(result.message || 'Failed to load violations');
                    } else if (Array.isArray(result)) {
                        violations = result;
                    } else if (result.violations) {
                        violations = result.violations;
                    } else {
                        violations = result.data || [];
                    }

                    if (violations.length === 0) {
                        violationsContainer.innerHTML = `
                        <div class="no-violations">
                            <i class="fas fa-check-circle"></i>
                            <span>No violations found for this student.</span>
                        </div>
                    `;
                    } else {
                        let violationsHTML = '';
                        violations.forEach(violation => {
                            // Format date committed
                            let commitDateTime = 'N/A';
                            if (violation.date_committed || violation.violation_date) {
                                const dateStr = violation.date_committed || violation.violation_date;
                                const commitDate = new Date(dateStr);
                                commitDateTime = commitDate.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }

                            // Format status update date/time - MOVED TO HEADER
                            let statusUpdateDateTime = 'N/A';
                            if (violation.updated_at) {
                                const statusUpdateDate = new Date(violation.updated_at);
                                statusUpdateDateTime = statusUpdateDate.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }

                            // Get sanction details
                            const sanctionConsequences = violation.sanction_consequences || violation
                                .sanction || 'No sanction assigned';
                            const sanctionDetails = violation.sanctions || violation.description ||
                                'No sanction details';

                            // Format offense type badge
                            let offenseBadge = '';
                            const offenseTypeLower = violation.offense_category ? violation
                                .offense_category.toLowerCase() : '';
                            switch (offenseTypeLower) {
                                case 'minor':
                                    offenseBadge =
                                        '<span class="violation-badge minor">Minor Offense</span>';
                                    break;
                                case 'major':
                                    offenseBadge =
                                        '<span class="violation-badge major">Major Offense</span>';
                                    break;
                                case 'serious':
                                    offenseBadge =
                                        '<span class="violation-badge serious">Serious Offense</span>';
                                    break;
                                default:
                                    offenseBadge =
                                        `<span class="violation-badge">${violation.offense_type || 'N/A'}</span>`;
                            }

                            // Format violation status badge
                            let statusBadge = '';
                            switch (violation.status) {
                                case 'pending':
                                    statusBadge = '<span class="status-badge pending">Pending</span>';
                                    break;
                                case 'in_progress':
                                    statusBadge =
                                        '<span class="status-badge in-progress">In Progress</span>';
                                    break;
                                case 'resolved':
                                    statusBadge = '<span class="status-badge resolved">Resolved</span>';
                                    break;
                                case 'dismissed':
                                    statusBadge =
                                        '<span class="status-badge dismissed">Dismissed</span>';
                                    break;
                                case 'closed':
                                    statusBadge = '<span class="status-badge closed">Closed</span>';
                                    break;
                                case 'settled':
                                    statusBadge = '<span class="status-badge settled">Settled</span>';
                                    break;
                                default:
                                    statusBadge =
                                        `<span class="status-badge">${formatStatus(violation.status)}</span>`;
                            }

                            // Format sanction status badge - MOVED TO DATES SECTION
                            let sanctionStatusText = '';
                            let sanctionStatusClass = '';
                            switch (violation.sanction_status) {
                                case 'pending':
                                    sanctionStatusText = 'Pending';
                                    sanctionStatusClass = 'sanction-pending';
                                    break;
                                case 'ongoing':
                                    sanctionStatusText = 'Ongoing';
                                    sanctionStatusClass = 'sanction-ongoing';
                                    break;
                                case 'completed':
                                    sanctionStatusText = 'Completed';
                                    sanctionStatusClass = 'sanction-completed';
                                    break;
                                case 'missed':
                                    sanctionStatusText = 'Missed';
                                    sanctionStatusClass = 'sanction-missed';
                                    break;
                                case 'cancelled':
                                    sanctionStatusText = 'Cancelled';
                                    sanctionStatusClass = 'sanction-cancelled';
                                    break;
                                default:
                                    sanctionStatusText = formatSanctionStatus(violation
                                        .sanction_status);
                                    sanctionStatusClass = 'sanction-default';
                            }

                            // Format Sanction Time Start and Sanction Time End
                            let sanctionTimeStartFormatted = violation.sanction_start_at ?
                                formatDateTime(violation.sanction_start_at) : 'Not Set';
                            let sanctionTimeEndFormatted = violation.sanction_end_at ? formatDateTime(
                                violation.sanction_end_at) : 'Not Set';

                            // Use correct incident description field
                            const incidentDesc = violation.incident_description || violation
                                .description || violation.violation_incident || 'N/A';

                            violationsHTML += `
                            <div class="violation-item">
                                <div class="violation-header">
                                    ${offenseBadge}
                                    ${statusBadge}
                                    <span class="status-update-date">
                                        <i class="fas fa-clock"></i>
                                        <span>Updated: ${statusUpdateDateTime}</span>
                                    </span>
                                </div>
                                <div class="violation-content">
                                    <div class="violation-field">
                                        <label><i class="fas fa-exclamation-circle"></i> Incident:</label>
                                        <p>${incidentDesc}</p>
                                    </div>
                                    <div class="violation-field">
                                        <label><i class="fas fa-gavel"></i> Sanction:</label>
                                        <p><strong>${sanctionConsequences}</strong></p>
                                    </div>
                                    <div class="violation-dates">
                                        <div class="date-item">
                                            <i class="fas fa-calendar-plus"></i>
                                            <span>Committed: ${commitDateTime}</span>
                                        </div>
                                        <div class="date-item sanction-time-start">
                                            <i class="fas fa-hourglass-start"></i>
                                            <span>Sanction Start: ${sanctionTimeStartFormatted}</span>
                                        </div>
                                        <div class="date-item sanction-time-end">
                                            <i class="fas fa-hourglass-end"></i>
                                            <span>Sanction End: ${sanctionTimeEndFormatted}</span>
                                        </div>
                                        <div class="date-item sanction-status ${sanctionStatusClass}">
                                            <i class="fas fa-tasks"></i>
                                            <span>Sanction Status: ${sanctionStatusText}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        violationsContainer.innerHTML = violationsHTML;
                    }
                } catch (error) {
                    console.error('Error loading violations:', error);
                    violationsContainer.innerHTML = `
                    <div class="error-violations">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Error loading violations: ${error.message}</span>
                    </div>
                `;
                }
            }

            // Helper function to format sanction status
            function formatSanctionStatus(status) {
                if (!status) return 'Not Set';

                const statusMap = {
                    'pending': 'Pending',
                    'ongoing': 'Ongoing',
                    'completed': 'Completed',
                    'missed': 'Missed',
                    'cancelled': 'Cancelled'
                };

                return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
            }

            // Format status text
            function formatStatus(status) {
                const statusMap = {
                    'pending': 'Pending',
                    'in_progress': 'In Progress',
                    'resolved': 'Resolved',
                    'dismissed': 'Dismissed',
                    'closed': 'Closed',
                    'settled': 'Settled'
                };
                return statusMap[status] || status;
            }

            // Export student profile PDF
            function exportStudentProfilePDF() {
                if (!currentStudentData) return;

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

                // Format birthdate
                let formattedBirthdate = 'N/A';
                if (currentStudentData.birthdate && currentStudentData.birthdate !== 'null') {
                    formattedBirthdate = new Date(currentStudentData.birthdate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                // Parse parent data for PDF
                let parentInfoHTML = '';
                try {
                    if (currentStudentData.parentData && currentStudentData.parentData !== 'null') {
                        const parentData = JSON.parse(currentStudentData.parentData);

                        if (parentData && parentData.parent_fname) {
                            let parentBirthdate = 'N/A';
                            if (parentData.parent_birthdate && parentData.parent_birthdate !== 'null') {
                                parentBirthdate = new Date(parentData.parent_birthdate).toLocaleDateString(
                                    'en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    });
                            }

                            parentInfoHTML = `
                            <div style="margin-top: 20px;">
                                <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Parent/Guardian Information</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; border: 1px solid #e2e8f0;">
                                    <tbody>
                                        <tr style="background-color: #ffffff;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000; width: 30%;">Name</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentData.parent_fname} ${parentData.parent_lname}</td>
                                        </tr>
                                        <tr style="background-color: #f7fafc;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Relationship</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentData.parent_relationship || 'N/A'}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sex</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentData.parent_sex || 'N/A'}</td>
                                        </tr>
                                        <tr style="background-color: #f7fafc;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Birthdate</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentBirthdate}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Contact Number</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentData.parent_contactinfo || 'N/A'}</td>
                                        </tr>
                                        <tr style="background-color: #f7fafc;">
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Email Address</td>
                                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${parentData.parent_email || 'N/A'}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                        }
                    }
                } catch (error) {
                    console.error('Error parsing parent data for PDF:', error);
                }

                // Create a temporary element for PDF generation
                const element = document.createElement('div');
                element.innerHTML = `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Student Management System</h2>
                            <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Student Profile</p>
                        </div>
                        <div style="text-align: right;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div style="margin: 0 25px 25px 25px;">
                        <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Student Information</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; border: 1px solid #e2e8f0;">
                            <tbody>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000; width: 30%;">Full Name</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.name}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Student ID</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.id}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sex</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.sex}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Birthdate</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${formattedBirthdate}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Grade Level</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.grade}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Section</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.section}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Adviser</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.adviser}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Address</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.address || 'N/A'}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Contact Number</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${currentStudentData.contact || 'N/A'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Parent Information -->
                    ${parentInfoHTML}

                    <!-- Footer -->
                    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="text-align: left;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    {{ Auth::user()->adviser_fname }} {{ Auth::user()->adviser_lname }}
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Adviser
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Generated On:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600;">
                                    ${currentDate} at ${currentTime}
                                </div>
                            </div>
                        </div>

                        <!-- Confidential Notice -->
                        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                            <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                                CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Generate PDF
                generatePDF(element,
                    `Student_Profile_${currentStudentData.name.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`
                );
            }

            // Export violations PDF function - UPDATED WITH PROPER RESPONSE HANDLING
            async function exportViolationsPDF() {
                if (!currentStudentData) return;

                try {
                    const response = await fetch(
                        `/adviser/students/${currentStudentData.id}/violationsforstudent`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                    if (!response.ok) {
                        throw new Error(`Failed to load violations: ${response.status}`);
                    }

                    const result = await response.json();

                    // DEBUG: Log the response structure
                    console.log('Violations API Response:', result);

                    // Handle different response structures
                    let violations = [];
                    if (result.success === false) {
                        throw new Error(result.message || 'Failed to load violations');
                    } else if (Array.isArray(result)) {
                        // If response is directly an array
                        violations = result;
                    } else if (result.violations && Array.isArray(result.violations)) {
                        // If response has a violations property
                        violations = result.violations;
                    } else if (result.data && Array.isArray(result.data)) {
                        // If response has a data property
                        violations = result.data;
                    } else {
                        // If no array found, throw error
                        throw new Error('Invalid response format from server');
                    }

                    // If still not an array, set to empty array
                    if (!Array.isArray(violations)) {
                        console.warn('Violations data is not an array:', violations);
                        violations = [];
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

                    let violationsHTML = '';
                    if (violations.length === 0) {
                        violationsHTML = `
            <tr>
                <td colspan="9" style="text-align: center; padding: 30px; color: #666;">
                    No violations found for this student.
                </td>
            </tr>
        `;
                    } else {
                        violations.forEach((violation, index) => {
                            // Format date committed
                            let dateTimeCommitted = 'N/A';
                            if (violation.date_committed || violation.violation_date) {
                                const dateStr = violation.date_committed || violation.violation_date;
                                try {
                                    const commitDate = new Date(dateStr);
                                    dateTimeCommitted = commitDate.toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                } catch (e) {
                                    console.warn('Error formatting date:', e);
                                    dateTimeCommitted = dateStr;
                                }
                            }

                            // Format status update date/time
                            let recordUpdatedDateTime = 'N/A';
                            if (violation.updated_at) {
                                try {
                                    const statusUpdateDate = new Date(violation.updated_at);
                                    recordUpdatedDateTime = statusUpdateDate.toLocaleDateString(
                                    'en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                } catch (e) {
                                    console.warn('Error formatting updated_at:', e);
                                }
                            }

                            // Format Sanction Time Start
                            let sanctionTimeStartFormatted = 'Not Set';
                            if (violation.sanction_start_at && violation.sanction_start_at !== 'null') {
                                try {
                                    sanctionTimeStartFormatted = new Date(violation.sanction_start_at)
                                        .toLocaleString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                } catch (e) {
                                    console.warn('Error formatting sanction_start_at:', e);
                                }
                            }

                            // Format Sanction Time End
                            let sanctionTimeEndFormatted = 'Not Set';
                            if (violation.sanction_end_at && violation.sanction_end_at !== 'null') {
                                try {
                                    sanctionTimeEndFormatted = new Date(violation.sanction_end_at)
                                        .toLocaleString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                } catch (e) {
                                    console.warn('Error formatting sanction_end_at:', e);
                                }
                            }

                            // Get sanction consequences
                            const sanctionConsequences = violation.sanction_consequences || violation
                                .sanction ||
                                violation.consequences || 'No sanction assigned';

                            // Format offense type
                            let offenseType = violation.offense_type || violation.offense_category ||
                                'N/A';
                            const offenseTypeLower = offenseType.toLowerCase();
                            switch (offenseTypeLower) {
                                case 'minor':
                                    offenseType = 'Minor Offense';
                                    break;
                                case 'major':
                                    offenseType = 'Major Offense';
                                    break;
                                case 'serious':
                                    offenseType = 'Serious Offense';
                                    break;
                                default:
                                    // Capitalize first letter
                                    offenseType = offenseType.charAt(0).toUpperCase() + offenseType
                                        .slice(1);
                            }

                            // Format record status (violation status)
                            let statusText = formatStatus(violation.status);

                            // Format sanction status
                            let sanctionStatusText = '';
                            if (violation.sanction_status) {
                                const statusLower = violation.sanction_status.toLowerCase();
                                switch (statusLower) {
                                    case 'pending':
                                        sanctionStatusText = 'Pending';
                                        break;
                                    case 'ongoing':
                                    case 'in_progress':
                                        sanctionStatusText = 'Ongoing';
                                        break;
                                    case 'completed':
                                        sanctionStatusText = 'Completed';
                                        break;
                                    case 'missed':
                                        sanctionStatusText = 'Missed';
                                        break;
                                    case 'cancelled':
                                        sanctionStatusText = 'Cancelled';
                                        break;
                                    default:
                                        sanctionStatusText = violation.sanction_status.charAt(0)
                                            .toUpperCase() +
                                            violation.sanction_status.slice(1);
                                }
                            } else {
                                sanctionStatusText = 'Not Set';
                            }

                            // Get incident description
                            const incidentDesc = violation.incident_description || violation
                                .description ||
                                violation.violation_incident || violation.incident || 'N/A';

                            const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

                            violationsHTML += `
                <tr style="background-color: ${rowColor};">
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${offenseType}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${incidentDesc}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${sanctionConsequences}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${dateTimeCommitted}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${sanctionTimeStartFormatted}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${sanctionTimeEndFormatted}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${sanctionStatusText}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${statusText}
                    </td>
                    <td style="padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; vertical-align: top;">
                        ${recordUpdatedDateTime}
                    </td>
                </tr>
            `;
                        });
                    }

                    // Create a temporary element for PDF generation
                    const element = document.createElement('div');
                    element.innerHTML = `
        <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
            <!-- Header -->
            <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                <div style="flex: 1;">
                    <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                    <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Student Management System</h2>
                    <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Student Violations Record</p>
                </div>
                <div style="text-align: right;">
                    <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </div>
            </div>

            <!-- Student Information -->
            <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin: 0 25px 25px 25px;">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${currentStudentData.name}</h3>
                        <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                            Student ID: ${currentStudentData.id} | Grade: ${currentStudentData.grade} | Section: ${currentStudentData.section}
                        </p>
                        <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                            Adviser: ${currentStudentData.adviser}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #000000;">Generated On:</div>
                        <div style="font-size: 14px; color: #000000; font-weight: 600;">
                            ${currentDate} at ${currentTime}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Violations Table -->
            <div style="margin: 0 25px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-top: 10px; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Offense Type</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 18%;">Incident Description</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 14%;">Sanction</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Date & Time</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Sanction<br>Started</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Sanction<br>Ended</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Sanction<br>Status</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 10%;">Record<br>Status</th>
                            <th style="background: #1e3a8a; color: white; padding: 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 10px; width: 8%;">Record<br>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${violationsHTML}
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="text-align: left;">
                        <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                        <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                            {{ Auth::user()->adviser_fname }} {{ Auth::user()->adviser_lname }}
                        </div>
                        <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                        <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                            Adviser
                        </div>
                    </div>
                </div>

                <!-- Confidential Notice -->
                <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                    <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                        CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                    </div>
                </div>
            </div>
        </div>
    `;

                    // Generate PDF in LANDSCAPE mode
                    if (typeof html2pdf === 'undefined') {
                        const script = document.createElement('script');
                        script.src =
                            'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                        script.onload = () => createViolationsPDF(element,
                            `Violations_${currentStudentData.name.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`
                        );
                        document.head.appendChild(script);
                    } else {
                        createViolationsPDF(element,
                            `Violations_${currentStudentData.name.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`
                        );
                    }

                } catch (error) {
                    console.error('Error exporting violations PDF:', error);
                    notifications.showNotification(`Failed to export violations: ${error.message}`, 'error');
                }
            }
            // Updated createPDF function for violations (LANDSCAPE)
            function createViolationsPDF(element, filename) {
                const options = {
                    margin: [10, 15, 25, 15],
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'landscape', // CHANGED TO LANDSCAPE
                        compress: true,
                        hotfixes: ["px_scaling"]
                    }
                };

                notifications.showNotification('Generating PDF...', 'info');

                html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                    // Add page numbers in landscape
                    const totalPages = pdf.internal.getNumberOfPages();

                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);

                        // Add page numbers for landscape
                        pdf.setFontSize(8);
                        pdf.setTextColor(100, 100, 100);

                        // System name on left
                        pdf.text('Tagoloan SHS - Student Management System',
                            pdf.internal.pageSize.getWidth() / 2 - 60,
                            pdf.internal.pageSize.getHeight() - 10);

                        // Page number on right
                        pdf.text(`Page ${i} of ${totalPages}`,
                            pdf.internal.pageSize.getWidth() - 25,
                            pdf.internal.pageSize.getHeight() - 10);
                    }

                    const pdfBlob = pdf.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl, '_blank');

                    notifications.showNotification('PDF exported successfully', 'success');
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    notifications.showNotification('PDF generation failed. Please try again.', 'error');
                });
            }

            // Common PDF generation function
            function generatePDF(element, filename) {
                if (typeof html2pdf === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = () => createPDF(element, filename);
                    document.head.appendChild(script);
                } else {
                    createPDF(element, filename);
                }

                function createPDF(element, filename) {
                    const options = {
                        margin: [10, 15, 25, 15],
                        filename: filename,
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            logging: false
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    };

                    notifications.showNotification('Generating PDF...', 'info');

                    html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
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
        /* View Button Style */
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
            max-width: 550px !important;
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

        /* Modal Tabs */
        .modal-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .tab-btn {
            padding: 12px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #1e293b;
            background: #f1f5f9;
        }

        .tab-btn.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            background: #ffffff;
        }

        /* Tab Content */
        .tab-content {
            flex-grow: 1;
            overflow-y: auto;
        }

        .tab-pane {
            display: none;
            height: 100%;
        }

        .tab-pane.active {
            display: block;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
            background: #ffffff;
            height: 100%;
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

        /* Info Section */
        .info-section {
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

        /* Contact Section */
        .contact-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
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

        /* Parents Section - UPDATED */
        .parents-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .no-parents {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            color: #94a3b8;
            text-align: center;
        }

        .no-parents i {
            font-size: 32px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .no-parents-text {
            font-size: 14px;
            font-style: italic;
        }

        .parent-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: 1px solid #2d4fc1;
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }

        .parent-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            flex-direction: column;
        }

        .parent-header i {
            color: white;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .parent-header h5 {
            margin: 0;
            font-size: 18px;
            color: white;
            font-weight: 600;
            text-align: center;
            width: 100%;
        }

        .parent-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .parent-detail {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            min-width: 130px;
            flex-shrink: 0;
        }

        .detail-label i {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            width: 20px;
            text-align: center;
        }

        .detail-value {
            color: white;
            flex: 1;
            font-weight: 500;
        }

        .detail-value a {
            color: white;
            text-decoration: none;
            transition: color 0.2s;
        }

        .detail-value a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Violations Tab Styles */
        .violations-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .violations-header h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #1e293b;
            font-weight: 600;
        }

        .violations-header p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }

        .violations-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .loading-violations,
        .no-violations,
        .error-violations {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }

        .loading-violations i,
        .no-violations i,
        .error-violations i {
            font-size: 32px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .loading-violations span,
        .no-violations span,
        .error-violations span {
            font-size: 14px;
            color: #64748b;
        }

        .loading-violations i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-violations i {
            color: #10b981;
        }

        .error-violations i {
            color: #ef4444;
        }

        .violation-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .violation-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-update-date {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            margin-left: auto;
        }

        .status-update-date i {
            font-size: 11px;
            color: #64748b;
        }

        /* Violation badges */
        .violation-badge,
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .violation-badge.minor {
            background: #fff3cd;
            color: #856404;
        }

        .violation-badge.major {
            background: #f8d7da;
            color: #721c24;
        }

        .violation-badge.serious {
            background: #dc3545;
            color: white;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.in-progress {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.resolved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.dismissed {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-badge.closed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.settled {
            background: #d4edda;
            color: #155724;
        }

        .violation-content {
            font-size: 13px;
        }

        .violation-field {
            margin-bottom: 10px;
        }

        .violation-field label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            color: #475569;
            margin-bottom: 4px;
        }

        .violation-field label i {
            color: #94a3b8;
            font-size: 12px;
        }

        .violation-field p {
            margin: 0;
            color: #1e293b;
            line-height: 1.5;
        }

        .violation-dates {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .date-item i {
            font-size: 11px;
            color: #64748b;
        }

        /* Status date styling */
        .date-item.status-date {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .date-item.status-date i {
            color: #0ea5e9;
        }

        /* Sanction Time Start styling */
        .date-item.sanction-time-start {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .date-item.sanction-time-start i {
            color: #0ea5e9;
        }

        /* Sanction Time End styling */
        .date-item.sanction-time-end {
            background: #e0f2fe;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .date-item.sanction-time-end i {
            color: #0ea5e9;
        }

        /* Sanction Status in Dates Section */
        .date-item.sanction-status {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Sanction status color classes */
        .date-item.sanction-pending {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .date-item.sanction-ongoing {
            background: #cce5ff;
            border: 1px solid #b8daff;
            color: #004085;
        }

        .date-item.sanction-completed {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .date-item.sanction-missed {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .date-item.sanction-cancelled {
            background: #e2e3e5;
            border: 1px solid #d6d8db;
            color: #383d41;
        }

        .date-item.sanction-default {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #64748b;
        }

        /* Modal Footer - UPDATED */
        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .btn-secondary {
            display: none;
            /* Hide the close button */
        }

        .modal-export {
            padding: 12px 24px;
            background: #3b82f6;

            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 200px;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }

        .modal-export:hover {
            background: #3b82f6;

            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
        }

        .modal-export:active {
            transform: translateY(0);
        }

        /* Scrollbar Styling */
        .tab-content::-webkit-scrollbar,
        .violations-container::-webkit-scrollbar {
            width: 6px;
        }

        .tab-content::-webkit-scrollbar-track,
        .violations-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb,
        .violations-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb:hover,
        .violations-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }


        .violation-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .violation-badge,
        .status-badge {
            width: 100%;
            text-align: center;
        }

        .status-update-date {
            width: 100%;
            justify-content: center;
            margin-left: 0;
        }

        .violation-dates {
            grid-template-columns: 1fr;
            gap: 8px;
        }
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
                align-items: center;
            }

            .modal-export {
                width: 100%;
                max-width: 100%;
                justify-content: center;
            }

            .violation-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .violation-dates {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .parent-header {
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .parent-header i {
                margin-bottom: 5px;
            }

            .parent-detail {
                flex-direction: column;
                gap: 5px;
            }

            .detail-label {
                min-width: auto;
                justify-content: center;
            }
        }
    </style>
@endsection
