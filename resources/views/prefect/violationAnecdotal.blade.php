@extends('prefect.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- ✅ Toolbar -->
        <div class="toolbar">
            <h2>Violation Anecdotals Management</h2>
            <div class="actions" style="display: flex; align-items: center; gap: 10px;">
                <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput" class="search-input">
                <button class="export-btn" id="exportExcelBtn" title="Export to Excel">
                    📊 Export Excel
                </button>
                <button class="export-btn" id="exportAllPdfBtn" title="Export All to PDF">
                    📄 Export Pdf
                </button>
            </div>
        </div>

        <!-- Anecdotals Table -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Solution</th>
                        <th>Recommendation</th>

                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($vanecdotals as $anec)
                        @if ($anec->status === 'completed')
                            <tr data-anec-id="{{ $anec->violation_anec_id }}"
                                data-solution="{{ $anec->violation_anec_solution }}"
                                data-recommendation="{{ $anec->violation_anec_recommendation }}"
                                data-date="{{ $anec->violation_anec_date }}"
                                data-time="{{ \Carbon\Carbon::parse($anec->violation_anec_time)->format('h:i A') }}"
                                data-status="{{ $anec->status }}"
                                data-student-name="{{ $anec->violation->student->student_fname ?? 'N/A' }} {{ $anec->violation->student->student_lname ?? '' }}"
                                data-incident="{{ $anec->violation->violation_incident ?? 'N/A' }}"
                                data-offense="{{ $anec->violation->offense->offense_type ?? 'N/A' }}"
                                data-student-id="{{ $anec->violation->student->student_id ?? 'N/A' }}"
                                data-grade-section="{{ $anec->violation->student->grade_level ?? 'N/A' }} - {{ $anec->violation->student->section ?? 'N/A' }}"
                                data-adviser="{{ $anec->violation->student->adviser->adviser_fname ?? 'N/A' }} {{ $anec->violation->student->adviser->adviser_lname ?? '' }}"
                                data-parent-name="{{ $anec->violation->student->parent->parent_fname ?? 'N/A' }} {{ $anec->violation->student->parent->parent_lname ?? 'N/A' }}"
                                data-violation-date="{{ $anec->violation->violation_date ?? 'N/A' }}">
                                <td>{{ $anec->violation_anec_id }}</td>
                                <td>
                                    {{ $anec->violation->student->student_fname ?? 'N/A' }}
                                    {{ $anec->violation->student->student_lname ?? '' }}
                                </td>
                                <td>{{ Str::limit($anec->violation_anec_solution, 50) }}</td>
                                <td>{{ Str::limit($anec->violation_anec_recommendation, 50) }}</td>

                                <td>
                                    <button class="view-btn" data-anec-id="{{ $anec->violation_anec_id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="no-data">⚠️ No completed anecdotal records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
            <div class="pagination-links">
                @if ($vanecdotals instanceof \Illuminate\Pagination\LengthAwarePaginator && $vanecdotals->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($vanecdotals->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vanecdotals->previousPageUrl() }}" rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($vanecdotals->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vanecdotals->nextPageUrl() }}" rel="next">Next ›</a>
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
                @if ($vanecdotals instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Showing {{ $vanecdotals->firstItem() }} to {{ $vanecdotals->lastItem() }} of
                    {{ $vanecdotals->total() }} entries
                @else
                    Showing {{ $vanecdotals->count() }} record(s)
                @endif
            </div>
        </div>

        <!-- 👤 Anecdotal Info Modal - Updated with Compact Design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-clipboard"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Anecdotal Information</h3>
                            <p class="modal-subtitle" id="info_anecdotal_id_display"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeInfoModalBtn">&times;</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="modal-tabs">
                    <button class="tab-btn active" data-tab="anecdotal-info">
                        <i class="fas fa-info-circle"></i> Anecdotal Info
                    </button>
                    <button class="tab-btn" data-tab="student-details">
                        <i class="fas fa-user-graduate"></i> Student Details
                    </button>
                    <button class="tab-btn" data-tab="violation-info">
                        <i class="fas fa-exclamation-triangle"></i> Violation Info
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Anecdotal Information Tab -->
                    <div class="tab-pane active" id="anecdotal-info-tab">
                        <div class="modal-body">
                            <!-- Basic Information -->
                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-id-badge"></i> Anecdotal ID
                                    </label>
                                    <span class="info-value" id="info_anecdotal_id"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-calendar-day"></i> Date
                                    </label>
                                    <span class="info-value" id="info_date"></span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-clock"></i> Time
                                    </label>
                                    <span class="info-value" id="info_time"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-check-circle"></i> Status
                                    </label>
                                    <span class="info-value" id="info_status"></span>
                                </div>
                            </div>

                            <!-- Solution Details -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-lightbulb"></i> Solution Implemented
                                </h4>
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-clipboard-check"></i> Solution
                                        </label>
                                        <span class="info-value" id="info_solution"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recommendation Details -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-star"></i> Recommendations
                                </h4>
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-bullseye"></i> Recommendation
                                        </label>
                                        <span class="info-value" id="info_recommendation"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Details Tab -->
                    <div class="tab-pane" id="student-details-tab">
                        <div class="modal-body">
                            <!-- Student Information -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user-graduate"></i> Student Information
                                </h4>
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-id-card"></i> Student ID
                                        </label>
                                        <span class="info-value" id="info_student_id"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-user"></i> Student Name
                                        </label>
                                        <span class="info-value" id="info_student_name"></span>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-graduation-cap"></i> Grade & Section
                                        </label>
                                        <span class="info-value" id="info_grade_section"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-user-tie"></i> Adviser
                                        </label>
                                        <span class="info-value" id="info_adviser"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Parent/Guardian Information -->
                            <div class="contact-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user-friends"></i> Parent/Guardian
                                </h4>

                                <!-- Parent Name -->
                                <div class="contact-item">
                                    <div class="contact-icon" style="background: #10b981;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="contact-details">
                                        <div class="contact-label">Parent/Guardian Name</div>
                                        <span class="contact-value" id="info_parent"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Violation Information Tab -->
                    <div class="tab-pane" id="violation-info-tab">
                        <div class="modal-body">
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-exclamation-triangle"></i> Violation Information
                                </h4>

                                <!-- Incident Information -->
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-clipboard"></i> Incident
                                        </label>
                                        <span class="info-value" id="info_incident"></span>
                                    </div>
                                </div>

                                <!-- Offense Type -->
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-gavel"></i> Offense Type
                                        </label>
                                        <span class="info-value" id="info_offense"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-calendar-alt"></i> Violation Date
                                        </label>
                                        <span class="info-value" id="info_violation_date"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
               <!-- Modal Footer - UPDATED: Only Export PDF button, centered -->
<div class="modal-footer">
    <button class="btn-export modal-export" id="exportSinglePdfBtn">
        <i class="fas fa-file-pdf"></i> Export PDF
    </button>
</div>
            </div>
        </div>

        <!-- 🗃️ VIOLATION ARCHIVE MODAL -->
        <div class="modal" id="violationAnecdotalsArchiveModal">
            <div class="modal-content">
                <div class="modal-header">🗃️ Archived Violation Anecdotals</div>
                <div class="modal-body">
                    <div class="modal-actions">
                        <label class="select-all-label">
                            <input type="checkbox" id="selectAllViolationAnecdotalsArchived">
                            <span>Select All</span>
                        </label>
                        <div class="filter-container">
                            <select id="violationAnecdotalsStatusFilter" class="filter-select">
                                <option value="all">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="search-container">
                            <input type="search" id="violationAnecdotalsArchiveSearch"
                                placeholder="🔍 Search archived anecdotals..." class="search-input">
                        </div>
                    </div>

                    <div class="archive-table-container">
                        <div id="archiveViolationAnecdotalsTable" class="archive-table-wrapper">
                            <table class="archive-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <label class="select-label">
                                                <input type="checkbox" id="selectAllViolationAnecdotalsArchived">
                                                <span>Select All</span>
                                            </label>
                                        </th>
                                        <th>ID</th>
                                        <th>Student Name</th>
                                        <th>Solution</th>
                                        <th>Recommendation</th>
                                        <th>Status</th>
                                        <th>Date Archived</th>
                                    </tr>
                                </thead>
                                <tbody id="archiveViolationAnecdotalsBody">
                                    <!-- Archived violation anecdotals will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-secondary" id="restoreViolationAnecdotalsBtn">🔄 Restore</button>
                        <button class="btn-danger" id="deleteViolationAnecdotalsBtn">🗑️ Delete</button>
                        <button class="btn-close" id="closeViolationAnecdotalsArchive">❌ Close</button>
                    </div>
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

    <!-- Include html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Include SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // ==========================
        // Notification System
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

        // Get CSRF Token
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        const csrfToken = getCsrfToken();

        // ==================== MODAL FUNCTIONALITY ====================
        document.addEventListener('DOMContentLoaded', function() {
            const infoModal = document.getElementById('infoModal');
            const closeModalBtn = document.getElementById('closeInfoModalBtn');
            const exportSinglePdfBtn = document.getElementById('exportSinglePdfBtn');

            // Tab elements
            const tabBtns = document.querySelectorAll('#infoModal .tab-btn');
            const tabPanes = document.querySelectorAll('#infoModal .tab-pane');

            // Initialize modal functionality
            if (infoModal) {
                // Close modal buttons
                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', () => {
                        infoModal.style.display = 'none';
                    });
                }

              

                // Export functionality
                if (exportSinglePdfBtn) {
                    exportSinglePdfBtn.addEventListener('click', function() {
                        const anecdotalId = this.dataset.anecdotalId;
                        const row = document.querySelector(`tr[data-anec-id="${anecdotalId}"]`);
                        if (row) {
                            generateAndShowPDF(row);
                        }
                    });
                }

                // Close modal when clicking outside
                infoModal.addEventListener('click', function(event) {
                    if (event.target === infoModal) {
                        infoModal.style.display = 'none';
                    }
                });

                // Tab switching functionality
                if (tabBtns.length > 0) {
                    tabBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tabId = this.getAttribute('data-tab');

                            // Update active tab button
                            tabBtns.forEach(b => b.classList.remove('active'));
                            this.classList.add('active');

                            // Show corresponding tab pane
                            tabPanes.forEach(pane => pane.classList.remove('active'));
                            document.getElementById(`${tabId}-tab`).classList.add('active');
                        });
                    });
                }

                // View button functionality
                document.querySelectorAll('.view-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event bubbling

                        const row = this.closest('tr');
                        if (!row) return;

                        // Get data from the row
                        const anecdotalId = row.getAttribute('data-anec-id');
                        const studentName = row.getAttribute('data-student-name');
                        const studentId = row.getAttribute('data-student-id');
                        const gradeSection = row.getAttribute('data-grade-section');
                        const adviser = row.getAttribute('data-adviser');
                        const parentName = row.getAttribute('data-parent-name');
                        const incident = row.getAttribute('data-incident');
                        const offense = row.getAttribute('data-offense');
                        const solution = row.getAttribute('data-solution');
                        const recommendation = row.getAttribute('data-recommendation');
                        const date = row.getAttribute('data-date');
                        const time = row.getAttribute('data-time');
                        const violationDate = row.getAttribute('data-violation-date');
                        const status = row.getAttribute('data-status');

                        // Fill info modal
                        document.getElementById('info_anecdotal_id').textContent = anecdotalId ||
                            'N/A';
                        document.getElementById('info_anecdotal_id_display').textContent =
                            `Anecdotal ID: ${anecdotalId || 'N/A'}`;
                        document.getElementById('info_student_name').textContent = studentName ||
                            'N/A';
                        document.getElementById('info_student_id').textContent = studentId || 'N/A';
                        document.getElementById('info_grade_section').textContent = gradeSection ||
                            'N/A';
                        document.getElementById('info_adviser').textContent = adviser || 'N/A';
                        document.getElementById('info_parent').textContent = parentName || 'N/A';
                        document.getElementById('info_incident').textContent = incident || 'N/A';
                        document.getElementById('info_offense').textContent = offense || 'N/A';
                        document.getElementById('info_violation_date').textContent =
                            violationDate || 'N/A';
                        document.getElementById('info_solution').textContent = solution ||
                            'No solution available';
                        document.getElementById('info_recommendation').textContent =
                            recommendation || 'No recommendation available';
                        document.getElementById('info_date').textContent = date || 'N/A';
                        document.getElementById('info_time').textContent = time || 'N/A';

                        // Status with badge styling
                        const statusElement = document.getElementById('info_status');
                        const statusText = status ? status.charAt(0).toUpperCase() + status.slice(
                            1) : 'N/A';
                        statusElement.textContent = statusText;

                        // Apply status color
                        const statusColorMap = {
                            'completed': '#10b981',
                            'pending': '#f59e0b',
                            'cancelled': '#ef4444'
                        };
                        const statusColor = statusColorMap[status] || '#6b7280';
                        statusElement.style.color = 'white';
                        statusElement.style.backgroundColor = statusColor;
                        statusElement.style.padding = '4px 8px';
                        statusElement.style.borderRadius = '4px';
                        statusElement.style.fontSize = '12px';
                        statusElement.style.fontWeight = '600';
                        statusElement.style.display = 'inline-block';

                        // Store anecdotal ID for export button
                        document.getElementById('exportSinglePdfBtn').dataset.anecdotalId =
                            anecdotalId;

                        // Set first tab as active
                        if (tabBtns.length > 0) {
                            tabBtns.forEach(b => b.classList.remove('active'));
                            tabPanes.forEach(pane => pane.classList.remove('active'));
                            document.querySelector('.tab-btn[data-tab="anecdotal-info"]').classList
                                .add('active');
                            document.getElementById('anecdotal-info-tab').classList.add('active');
                        }

                        // Show modal
                        infoModal.style.display = 'flex';
                    });
                });
            }
        });

        // ==================== EXPORT FUNCTIONALITY ====================

        // Export All to PDF
        document.getElementById('exportAllPdfBtn').addEventListener('click', function() {
            notifications.showNotification('Preparing PDF export for all records...', 'info');

            // Get all visible rows
            const rows = document.querySelectorAll('#tableBody tr[data-anec-id]');
            if (rows.length === 0) {
                notifications.showNotification('No records to export', 'warning');
                return;
            }

            // Create a consolidated PDF content
            const consolidatedContent = generateConsolidatedPDFContent(rows);

            // Generate PDF
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = consolidatedContent;
            document.body.appendChild(tempDiv);

            const options = {
                margin: [10, 15, 10, 15],
                filename: `Violation_Anecdotals_All_Records_${new Date().toISOString().split('T')[0]}.pdf`,
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
                    orientation: 'portrait',
                    compress: true
                }
            };

            html2pdf().set(options).from(tempDiv).toPdf().get('pdf').then(function(pdf) {
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);
                window.open(pdfUrl, '_blank');
                notifications.showNotification('All records PDF exported successfully!', 'success');
            }).catch(error => {
                console.error('PDF generation error:', error);
                notifications.showNotification('PDF generation failed. Please try again.', 'error');
            }).finally(() => {
                document.body.removeChild(tempDiv);
            });
        });

        // Export to Excel (all records)
        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            notifications.showNotification('Preparing Excel export...', 'info');

            // Get all visible rows data
            const rows = document.querySelectorAll('#tableBody tr[data-anec-id]');
            if (rows.length === 0) {
                notifications.showNotification('No records to export', 'warning');
                return;
            }

            // Prepare data for Excel
            const data = [];

            // Add headers
            data.push([
                'ID',
                'Student Name',
                'Student ID',
                'Grade & Section',
                'Adviser',
                'Parent/Guardian',
                'Incident',
                'Offense Type',
                'Solution',
                'Recommendation',
                'Anecdotal Date',
                'Violation Date',
                'Time'
            ]);

            // Add rows
            rows.forEach(row => {
                data.push([
                    row.dataset.anecId,
                    row.dataset.studentName,
                    row.dataset.studentId,
                    row.dataset.gradeSection,
                    row.dataset.adviser,
                    row.dataset.parentName,
                    row.dataset.incident,
                    row.dataset.offense,
                    row.dataset.solution,
                    row.dataset.recommendation,
                    row.dataset.date,
                    row.dataset.violationDate,
                    row.dataset.time
                ]);
            });

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Violation Anecdotals");

            // Generate Excel file
            const fileName = `Violation_Anecdotals_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);

            notifications.showNotification('Excel file exported successfully!', 'success');
        });

        function formatReadableDate(dateString) {
            if (!dateString || dateString === 'N/A') return "____________";

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return "____________";

            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        }

        function generateAndShowPDF(row) {
            console.log('Generating PDF for row:', row);

            // Show notification immediately when export starts
            notifications.showNotification('Opening PDF preview...', 'info');

            // Get all data from the row
            const data = {
                anecdotalId: row.dataset.anecId,
                studentName: row.dataset.studentName,
                studentId: row.dataset.studentId,
                gradeSection: row.dataset.gradeSection,
                adviser: row.dataset.adviser,
                parentName: row.dataset.parentName,
                incident: row.dataset.incident,
                offense: row.dataset.offense,
                solution: row.dataset.solution,
                recommendation: row.dataset.recommendation,
                anecdotalDate: formatReadableDate(row.dataset.date),
                violationDate: formatReadableDate(row.dataset.violationDate)
            };

            console.log('PDF data:', data);

            // Generate PDF content
            const pdfContent = generatePDFContent(data);

            // Generate and open PDF in new tab
            generatePDFPreview(pdfContent, data);
        }

        // Generate consolidated PDF content for all records
        function generateConsolidatedPDFContent(rows) {
            let content = `
                <div style="font-family: 'Times New Roman', serif; color: #000; padding: 55px;">
                    <!-- DEPED HEADER WITH LOGO -->
                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <div style="flex: 0 0 auto; margin-right: 20px;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 80px; height: auto;">
                        </div>
                        
                        <div style="flex: 1; text-align: center; line-height: 1.4;">
                            <div style="margin-bottom: 2px;">Republic of Philippines</div>
                            <div style="margin-bottom: 2px;">Department of Education</div>
                            <div style="margin-bottom: 2px;"><strong>REGION X - NORTHERN MINDANAO</strong></div>
                            <div style="margin-bottom: 2px;"><strong>SCHOOLS DIVISION OF MISAMIS ORIENTAL</strong></div>
                            <div><strong>TAGOLOAN SENIOR HIGH SCHOOL</strong></div>
                        </div>
                        
                        <div style="flex: 0 0 80px; margin-left: 20px;"></div>
                    </div>
                                        
                    <!-- HEADER LINE -->
                    <div style="margin: 5px 0 20px 0; border-top: 1px solid #000;"></div>

                    <!-- TITLE -->
                    <div style="text-align: center; margin-bottom: 30px; font-size: 18px; font-weight: 700;">
                        VIOLATION ANECDOTAL RECORDS - ALL COMPLETED
                    </div>

                    <div style="text-align: center; margin-bottom: 20px; font-size: 14px;">
                        Generated on: ${new Date().toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </div>
            `;

            // Add each record
            rows.forEach((row, index) => {
                const data = {
                    anecdotalId: row.dataset.anecId,
                    studentName: row.dataset.studentName,
                    studentId: row.dataset.studentId,
                    gradeSection: row.dataset.gradeSection,
                    adviser: row.dataset.adviser,
                    parentName: row.dataset.parentName,
                    incident: row.dataset.incident,
                    offense: row.dataset.offense,
                    solution: row.dataset.solution,
                    recommendation: row.dataset.recommendation,
                    anecdotalDate: formatReadableDate(row.dataset.date),
                    violationDate: formatReadableDate(row.dataset.violationDate),
                    time: row.dataset.time
                };

                content += `
                    <div style="page-break-before: ${index > 0 ? 'always' : 'auto'}; margin-top: ${index > 0 ? '40px' : '0'};">
                        <div style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc;">
                            <strong>Record ${index + 1}: ${data.studentName} (ID: ${data.anecdotalId})</strong>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Student Information:</strong><br>
                            Student ID: ${data.studentId}<br>
                            Name: ${data.studentName}<br>
                            Grade & Section: ${data.gradeSection}<br>
                            Adviser: ${data.adviser}<br>
                            Parent/Guardian: ${data.parentName}
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Violation Information:</strong><br>
                            Incident: ${data.incident}<br>
                            Offense Type: ${data.offense}<br>
                            Violation Date: ${data.violationDate}
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Anecdotal Information:</strong><br>
                            Anecdotal ID: ${data.anecdotalId}<br>
                            Date: ${data.anecdotalDate}<br>
                            Time: ${data.time}<br>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Solution:</strong><br>
                            ${data.solution}
                        </div>

                        <div style="margin-bottom: 30px;">
                            <strong>Recommendation:</strong><br>
                            ${data.recommendation}
                        </div>
                    </div>
                `;
            });

            content += `
                </div>
            `;

            return content;
        }

        function generatePDFContent(data) {
            return `
            <div style="font-family: 'Times New Roman', serif; color: #000; padding: 55px;">

               <!-- DEPED HEADER WITH LOGO -->
                <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <!-- Logo -->
                    <div style="flex: 0 0 auto; margin-right: 20px;">
                        <img src="/images/Logo.png" alt="School Logo" style="width: 80px; height: auto;">
                    </div>
                    
                    <!-- School Information -->
                    <div style="flex: 1; text-align: center; line-height: 1.4;">
                        <div style="margin-bottom: 2px;">Republic of Philippines</div>
                        <div style="margin-bottom: 2px;">Department of Education</div>
                        <div style="margin-bottom: 2px;"><strong>REGION X - NORTHERN MINDANAO</strong></div>
                        <div style="margin-bottom: 2px;"><strong>SCHOOLS DIVISION OF MISAMIS ORIENTAL</strong></div>
                        <div><strong>TAGOLOAN SENIOR HIGH SCHOOL</strong></div>
                    </div>
                    
                    <!-- Optional: Add space for balance if needed -->
                    <div style="flex: 0 0 80px; margin-left: 20px;"></div>
                </div>
                                    
                <!-- HEADER LINE -->
                <div style="margin: 5px 0 20px 0; border-top: 1px solid #000;"></div>

                <!-- TITLE -->
                <div style="text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: 700;">
                    VIOLATION ANECDOTAL RECORD
                </div>

                <div style="text-align: center; font-size: 14px;">
                    Prefect Of Discipline
                </div>

                <div style="text-align: center; margin-bottom: 30px; font-size: 14px;">
                    Date: ${new Date().toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}
                </div>

                <!-- INCIDENT -->
                <div style="margin-bottom: 30px; font-size: 14px; text-align: justify;">
                    <strong>INCIDENT:</strong><br>
                    <div style="margin-left: 20px;">
                        ${data.incident}
                    </div>
                </div>

                <!-- RECOMMENDATION -->
                <div style="margin-bottom: 30px; font-size: 14px; text-align: justify;">
                    <strong>RECOMMENDATION:</strong><br>
                    <div style="margin-left: 20px;">
                        ${data.recommendation}
                    </div>
                </div>

                <!-- SOLUTION -->
                <div style="margin-bottom: 30px; font-size: 14px; text-align: justify;">
                    <strong>SOLUTION:</strong><br>
                    <div style="margin-left: 20px;">
                        ${data.solution}
                    </div>
                </div>

                <!-- SIGNATURES -->
                <div style="margin-top: 40px;">
                    <!-- First Row: Student & Parent -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
                        <!-- Student Signature -->
                        <div style="width: 45%;">
                            <div style="text-align: center;">
                                <div style="margin-bottom: 2px;">
                                    <strong>${data.studentName || ''}</strong>
                                </div>
                                <div style="border-top: 1px solid #000; width: 100%;"></div>
                                <div style="font-size: 10px; margin-top: 3px;">
                                    Student's Name and Signature
                                </div>
                            </div>
                        </div>
                        
                        <!-- Parent Signature -->
                        <div style="width: 45%;">
                            <div style="text-align: center;">
                                <div style="margin-bottom: 2px;">
                                    <strong>${data.parentName || ''}</strong>
                                </div>
                                <div style="border-top: 1px solid #000; width: 100%;"></div>
                                <div style="font-size: 10px; margin-top: 3px;">
                                    Parent/Guardian's Name and Signature
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row: Adviser & Prefect -->
                    <div style="display: flex; justify-content: space-between;">
                        <!-- Adviser Signature -->
                        <div style="width: 45%;">
                            <div style="text-align: center;">
                                <div style="margin-bottom: 2px;">
                                    <strong>${data.adviser || ''}</strong>
                                </div>
                                <div style="border-top: 1px solid #000; width: 100%;"></div>
                                <div style="font-size: 10px; margin-top: 3px;">
                                    Adviser's Name and Signature
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prefect Signature -->
                        <div style="width: 45%;">
                            <div style="text-align: center;">
                                <div style="margin-bottom: 2px;">
                                    <strong>{{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}</strong>
                                </div>
                                <div style="border-top: 1px solid #000; width: 100%;"></div>
                                <div style="font-size: 10px; margin-top: 3px;">
                                    Prefect of Discipline In-Charge
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }

        function generatePDFPreview(content, data) {
            // Create a temporary element for PDF generation
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            document.body.appendChild(tempDiv);

            // Generate file name
            const fileName =
                `Violation_Anecdotal_Record_${data.studentName.replace(/[^a-zA-Z0-9]/g, '_')}_${data.anecdotalId}.pdf`;

            // PDF options
            const options = {
                margin: [10, 15, 10, 15],
                filename: fileName,
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
                    compress: true
                }
            };

            // Generate PDF
            html2pdf().set(options).from(tempDiv).toPdf().get('pdf').then(function(pdf) {
                // Open PDF in new tab
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);
                window.open(pdfUrl, '_blank');

                notifications.showNotification('PDF opened in new tab', 'success');

                // Close the info modal
                document.getElementById('infoModal').style.display = 'none';
            }).catch(error => {
                console.error('PDF generation error:', error);
                notifications.showNotification('PDF generation failed. Please try again.', 'error');
            }).finally(() => {
                // Clean up
                document.body.removeChild(tempDiv);
            });
        }

        // ==================== EXISTING FUNCTIONALITY ====================

        // 🔍 Search Functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr[data-anec-id]');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = [
                'violationAnecdotalsArchiveModal', 'infoModal', 'notificationModal', 'confirmationModal'
            ];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    if (modalId === 'notificationModal') {
                        notifications.hideNotification();
                    } else if (modalId === 'confirmationModal') {
                        notifications.hideConfirmation();
                    } else {
                        modal.style.display = 'none';
                    }
                }
            });
        });
    </script>

    <style>
        /* ==================== */
        /* Compact Modal Styles for Anecdotals */
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
            flex: 1;
            justify-content: center;
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

        .info-group.full-width {
            flex: 100%;
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
            min-height: 36px;
            word-break: break-word;
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

        /* Modal Footer */
       .modal-footer {
    display: flex;
    justify-content: center; /* Changed from space-between */
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
    background: #3b82f6; /* Keep same color on hover */
}

        /* Scrollbar Styling */
        .tab-content::-webkit-scrollbar {
            width: 6px;
        }

        .tab-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Export Excel = Green */
        .info-modal-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
            background-color: #f9f9f9;
        }

        #exportExcelBtn {
            background-color: #28a745 !important;
            color: white !important;
            border: none !important;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Export PDF = Blue */
        #exportAllPdfBtn {
            background-color: #007bff !important;
            color: white !important;
            border: none !important;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        /* View Button Styles */
        .view-btn {
            background: #3b82f6;
            color: white !important;
            border: none !important;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn:hover {
            background: #3b82f6;
        }

        /* Hover Effect */
        #exportExcelBtn:hover {
            background-color: #218838 !important;
        }

        #exportAllPdfBtn:hover {
            background-color: #0069d9 !important;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
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

            .modal-tabs {
                flex-wrap: wrap;
            }

            .tab-btn {
                flex: 1 0 auto;
                min-width: 33.33%;
                font-size: 12px;
                padding: 10px 5px;
            }
        }
    </style>
@endsection
