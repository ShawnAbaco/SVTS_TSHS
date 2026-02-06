@extends('adviser.NewAdviser.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar (Updated to match parent page) -->
        <div class="toolbar">
            <h2>Violation Appointments</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput" class="search-input">
                <a href="#" class="btn-primary" id="addAppointmentBtn">
                    <i class="fas fa-plus"></i> Add Appointment
                </a>
            </div>
        </div>

        <!-- Export Buttons Container (Matching parent page style) -->
        <div class="export-buttons-container" style="display: flex; justify-content: flex-end; margin: 20px 0; gap: 10px;">
            <button class="btn-export" id="exportPdfBtn">
                📄 Export PDF
            </button>
            <button class="btn-export excel" id="exportExcelBtn">
                📊 Export Excel
            </button>
        </div>

        <!-- Status Filter & Bulk Actions -->
        <div class="select-options" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
            <!-- Left Side: Status Filter -->
            <div class="left-controls" style="display: flex; align-items: center; gap: 20px;">
                <!-- Status Filter -->
                <div class="status-filter" style="display: flex; align-items: center; gap: 10px;">
                    <label for="statusFilter" style="font-weight: 600; color: #495057; font-size: 14px;">Filter by Status:</label>
                    <select id="statusFilter" class="status-dropdown" style="padding: 8px 12px; border: 2px solid #4a6baf; border-radius: 6px; background: white; color: #333; font-weight: 500; cursor: pointer; min-width: 180px;">
                        <option value="all">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Rescheduled">Rescheduled</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            <!-- Right Side: Bulk Actions -->
            <div class="right-controls" style="display: flex; align-items: center; gap: 10px;">
                <button class="btn-info" id="bulkUpdateStatusBtn">⏱️ Update Appointment</button>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="table-container">
            <table class="table" id="appointmentTable">
                <thead>
                    <tr>
                        <th style="width: 30px;">
                            <label class="select-label">
                                <input type="checkbox" id="selectAll">
                            </label>
                        </th>
                        <th>Student Name</th>
                        <th>Offense</th>
                        <th>Incident</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($vappointments as $appt)
                        <tr data-app-id="{{ $appt->violation_app_id }}"
                            data-status="{{ $appt->violation_app_status }}"
                            data-date="{{ $appt->violation_app_date }}"
                            data-time="{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}"
                            data-notes="{{ $appt->violation_app_notes }}"
                            data-student-name="{{ $appt->violation->student->student_fname ?? 'N/A' }} {{ $appt->violation->student->student_lname ?? '' }}"
                            data-offense="{{ $appt->violation->offense->offense_type ?? 'N/A' }}"
                            data-incident="{{ $appt->violation->violation_incident ?? 'N/A' }}"
                            data-student-id="{{ $appt->violation->student->student_id ?? 'N/A' }}"
                            data-grade-section="{{ $appt->violation->student->grade_level ?? 'N/A' }} - {{ $appt->violation->student->section ?? 'N/A' }}"
                            data-adviser="{{ $appt->violation->student->adviser->adviser_fname ?? 'N/A' }} {{ $appt->violation->student->adviser->adviser_lname ?? '' }}"
                            data-parent-name="{{ $appt->violation->student->parent->parent_fname ?? 'N/A' }} {{ $appt->violation->student->parent->parent_lname ?? 'N/A' }}"
                            data-violation-date="{{ $appt->violation->violation_date ?? 'N/A' }}">

                            <!-- Checkbox column -->
                            <td style="text-align: center;">
                                <input type="checkbox" class="row-checkbox appointment-checkbox"
                                       value="{{ $appt->violation_app_id }}"
                                       data-app-id="{{ $appt->violation_app_id }}"
                                       data-status="{{ $appt->violation_app_status }}"
                                       data-student-name="{{ $appt->violation->student->student_fname ?? 'N/A' }} {{ $appt->violation->student->student_lname ?? '' }}">
                            </td>

                            <td>
                                {{ $appt->violation->student->student_fname ?? 'N/A' }}
                                {{ $appt->violation->student->student_lname ?? '' }}
                            </td>
                            <td>{{ $appt->violation->offense->offense_type ?? 'N/A' }}</td>
                            <td>
                                <div class="incident-preview">
                                    {{ Str::limit($appt->violation->violation_incident ?? 'No incident details', 50) }}
                                    @if ($appt->violation->violation_incident && strlen($appt->violation->violation_incident) > 50)
                                        <span class="view-full-incident"
                                            data-incident="{{ $appt->violation->violation_incident }}">View full</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($appt->violation_app_date)->format('F j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}</td>
                            <td>
                                <div class="notes-preview">
                                    {{ $appt->violation_app_notes ? Str::limit($appt->violation_app_notes, 30) : 'No notes' }}
                                    @if ($appt->violation_app_notes && strlen($appt->violation_app_notes) > 30)
                                        <span class="view-full-notes" data-notes="{{ $appt->violation_app_notes }}">View
                                            full</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-badge
                                    @if($appt->violation_app_status === 'Scheduled') status-scheduled
                                    @elseif($appt->violation_app_status === 'Completed') status-completed
                                    @elseif($appt->violation_app_status === 'Pending') status-pending
                                    @elseif($appt->violation_app_status === 'Rescheduled') status-rescheduled
                                    @elseif($appt->violation_app_status === 'Cancelled') status-cancelled @endif">
                                    {{ $appt->violation_app_status }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- View Button - Matching parent page style -->
                                    <button class="btn-view" data-app-id="{{ $appt->violation_app_id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="no-data">⚠️ No appointment records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section (Matching parent page) -->
        <div class="pagination-container">
            <div class="pagination-links">
                @if ($vappointments->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($vappointments->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vappointments->previousPageUrl() }}" rel="prev">‹ Previous</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($vappointments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-page-link" href="{{ $vappointments->nextPageUrl() }}" rel="next">Next ›</a>
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
                Showing {{ $vappointments->firstItem() ?? 0 }} to {{ $vappointments->lastItem() ?? 0 }} of {{ $vappointments->total() }} entries
            </div>
        </div>

<!-- ✏️ Bulk Update Status Modal - MAXIMIZED VERSION -->
<div class="modal" id="bulkUpdateStatusModal">
    <div class="modal-content large-modal">
        <!-- Header -->
        <div class="edit-modal-header">
            <h2>Update Selected Appointments</h2>
            <button type="button" class="close-btn" id="closeBulkStatusModal">✖</button>
        </div>

        <!-- Body -->
        <div class="edit-modal-body">
            <form id="bulkUpdateStatusForm" method="POST" action="{{ route('adviser.appointments.bulkUpdateStatus') }}">
                @csrf

                <div class="selected-violations">
                    <h5 class="section-title">Selected Appointments (<span id="selectedCount">0</span>)</h5>
                    <div id="selectedAppointmentsList" class="selected-list"></div>
                </div>

                <div class="edit-form-grid-maximized">
                    <!-- Date -->
                    <div class="edit-form-group">
                        <label for="bulk_appointment_date">Date <span style="color: #e74c3c;">*</span></label>
                        <input type="date" id="bulk_appointment_date" name="appointment_date"
                               min="{{ date('Y-m-d') }}" required>
                        <span class="error-message" id="bulk_appointment_date_error"></span>
                    </div>

                    <!-- Time -->
                    <div class="edit-form-group">
                        <label for="bulk_appointment_time">Time <span style="color: #e74c3c;">*</span></label>
                        <input type="time" id="bulk_appointment_time" name="appointment_time" required>
                        <span class="error-message" id="bulk_appointment_time_error"></span>
                    </div>

                    <!-- Status -->
                    <div class="edit-form-group">
                        <label for="bulk_new_status">Status <span style="color: #e74c3c;">*</span></label>
                        <select id="bulk_new_status" name="new_status" required>
                            <!-- Options will be dynamically populated -->
                        </select>
                        <span class="error-message" id="bulk_new_status_error"></span>
                    </div>

                    <!-- Notes - Full Width -->
                    <div class="edit-form-group full-width-maximized">
                        <label for="bulk_appointment_notes">Notes</label>
                        <textarea id="bulk_appointment_notes" name="appointment_notes"
                                  rows="4" placeholder="Add notes for the appointments..."></textarea>
                        <span class="error-message" id="bulk_appointment_notes_error"></span>
                        <div class="form-hint" id="bulkFormHint">Note: All selected appointments will be updated with these values</div>
                    </div>
                </div>

                <div class="required-fields-note">* Indicates required fields</div>

                <!-- Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelBulkStatusBtn">
                        <span>❌ Cancel</span>
                    </button>
                    <button type="submit" class="btn-primary" id="bulkUpdateSubmitBtn">
                        <span>💾 Update Selected</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- ✏️ Individual Update Status Modal -->
        <div class="modal" id="updateStatusModal">
            <div class="modal-content">
                <!-- Header -->
                <div class="edit-modal-header">
                    <h2>Update Appointment Status</h2>
                    <button class="close-btn" id="closeStatusModal">✖</button>
                </div>

                <!-- Body -->
                <div class="edit-modal-body">
                    <form id="updateStatusForm" method="POST" action="{{ route('adviser.appointments.updateStatus') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="appointment_id" id="update_appointment_id">
                        <input type="hidden" name="current_status" id="update_current_status">

                        <!-- Appointment Details -->
                        <div class="selected-violations">
                            <h5 class="section-title">Appointment Details</h5>
                            <div class="appointment-details">
                                <div class="detail-item">
                                    <span class="detail-label">Student:</span>
                                    <span class="detail-value" id="update_student_name"></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Current Status:</span>
                                    <span class="detail-value" id="update_current_status_display"></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Date & Time:</span>
                                    <span class="detail-value" id="update_date_time"></span>
                                </div>
                            </div>
                        </div>

                        <div class="edit-form-grid">
                            <!-- New Status -->
                            <div class="edit-form-group full-width">
                                <label for="new_status">New Status <span style="color: #e74c3c;">*</span></label>
                                <select id="new_status" name="new_status" required>
                                    <!-- Options will be dynamically populated -->
                                </select>
                                <span class="error-message" id="new_status_error"></span>
                                <div class="form-hint" id="individualFormHint">Once marked as "Completed" or "Cancelled", status cannot be changed again</div>
                            </div>
                        </div>

                        <div class="required-fields-note">* Indicates required fields</div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelStatusBtn">
                        <span>❌ Cancel</span>
                    </button>
                    <button type="submit" class="btn-primary" form="updateStatusForm" id="individualUpdateSubmitBtn">
                        <span>💾 Update Status</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 👤 Appointment Info Modal - Matching parent page design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Appointment Information</h3>
                            <p class="modal-subtitle" id="info_student_name"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeModalBtn">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Appointment ID & Status Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-id-badge"></i> Appointment ID
                            </label>
                            <span class="info-value" id="info_appointment_id"></span>
                        </div>
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-info-circle"></i> Status
                            </label>
                            <span class="info-value" id="info_status"></span>
                        </div>
                    </div>

                    <!-- Date & Time Row -->
                    <div class="info-row">
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-calendar-day"></i> Appointment Date
                            </label>
                            <span class="info-value" id="info_date"></span>
                        </div>
                        <div class="info-group">
                            <label class="info-label">
                                <i class="fas fa-clock"></i> Appointment Time
                            </label>
                            <span class="info-value" id="info_time"></span>
                        </div>
                    </div>

                    <!-- Student Information Section -->
                    <div class="contact-section">
                        <h4 class="section-title">
                            <i class="fas fa-user-graduate"></i> Student Information
                        </h4>

                        <!-- Student ID -->
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #4CAF50;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="contact-details">
                                <div class="contact-label">Student ID</div>
                                <span class="contact-value" id="info_student_id"></span>
                            </div>
                        </div>

                        <!-- Grade & Section -->
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #2196F3;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="contact-details">
                                <div class="contact-label">Grade & Section</div>
                                <span class="contact-value" id="info_grade_section"></span>
                            </div>
                        </div>

                        <!-- Adviser -->
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #9C27B0;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="contact-details">
                                <div class="contact-label">Adviser</div>
                                <span class="contact-value" id="info_adviser"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Violation Information -->
                    <div class="students-section">
                        <h4 class="section-title">
                            <i class="fas fa-exclamation-triangle"></i> Violation Information
                        </h4>
                        <div class="students-container">
                            <!-- Offense -->
                            <div class="contact-item">
                                <div class="contact-icon" style="background: #FF9800;">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Offense Type</div>
                                    <span class="contact-value" id="info_offense"></span>
                                </div>
                            </div>

                            <!-- Incident -->
                            <div class="contact-item">
                                <div class="contact-icon" style="background: #F44336;">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Incident</div>
                                    <span class="contact-value" id="info_incident"></span>
                                </div>
                            </div>

                            <!-- Violation Date -->
                            <div class="contact-item">
                                <div class="contact-icon" style="background: #795548;">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Violation Date</div>
                                    <span class="contact-value" id="info_violation_date"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent/Guardian Information -->
                    <div class="students-section">
                        <h4 class="section-title">
                            <i class="fas fa-user-friends"></i> Parent/Guardian Information
                        </h4>
                        <div class="students-container">
                            <div class="contact-item">
                                <div class="contact-icon" style="background: #3F51B5;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Parent/Guardian</div>
                                    <span class="contact-value" id="info_parent"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="students-section">
                        <h4 class="section-title">
                            <i class="fas fa-sticky-note"></i> Appointment Notes
                        </h4>
                        <div class="notes-container" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 10px;">
                            <p id="info_notes" style="margin: 0; color: #4a5568; font-size: 14px; line-height: 1.5;"></p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer (Matching parent page) -->
               <!-- Modal Footer (Matching parent page) - UPDATED: Only Export PDF button, centered -->
<div class="modal-footer">
    <button class="btn-export modal-export" id="exportSinglePdfBtn">
        <i class="fas fa-file-pdf"></i> Export PDF
    </button>
</div>
            </div>
        </div>

        <!-- Notification Modal (Matching parent page) -->
        <div class="notification-modal" id="notificationModal">
            <div class="notification-content" id="notificationContent">
                <div class="notification-icon" id="notificationIcon"></div>
                <div class="notification-message" id="notificationMessage"></div>
                <div class="notification-actions" id="notificationActions">
                    <!-- OK button removed for success messages -->
                </div>
            </div>
        </div>

        <!-- Confirmation Modal (Matching parent page) -->
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

<style>
    /* Update Options Styles */
.update-options {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.option-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.option-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.option-checkbox:hover {
    background-color: #e9ecef;
}

.option-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.option-checkbox span {
    font-size: 14px;
    color: #495057;
    font-weight: 500;
}

/* Selected list improvements */
.selected-violation-item {
    padding: 12px;
    margin-bottom: 10px;
    background: white;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s;
}

.selected-violation-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
}

.current-status {
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

/* ==================== */
/* MAXIMIZED BULK UPDATE MODAL STYLES */
/* ==================== */

.large-modal {
    max-width: 800px !important;
    width: 90% !important;
    max-height: 90vh !important;
    margin: auto;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

.edit-form-grid-maximized {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.edit-form-group.full-width-maximized {
    grid-column: 1 / -1;
}

/* Form improvements for large modal */
.edit-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.edit-form-group input[type="date"],
.edit-form-group input[type="time"],
.edit-form-group select,
.edit-form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.edit-form-group input[type="date"]:focus,
.edit-form-group input[type="time"]:focus,
.edit-form-group select:focus,
.edit-form-group textarea:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.edit-form-group textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
    width: 100%;
}

/* Disabled form styles */
.edit-form-group input:disabled,
.edit-form-group select:disabled,
.edit-form-group textarea:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
    border-color: #dee2e6;
}

.edit-form-group input:disabled::placeholder,
.edit-form-group textarea:disabled::placeholder {
    color: #adb5bd;
}

/* Status option disabled styles */
select option:disabled {
    color: #adb5bd;
    background-color: #f8f9fa;
    font-style: italic;
}

/* Completed status warning */
.completed-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
}

.completed-warning i {
    margin-right: 8px;
}

/* Selected list styling for bulk modal - Maximized */
.selected-list {
    max-height: 150px;
    overflow-y: auto;
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.selected-list::-webkit-scrollbar {
    width: 8px;
}

.selected-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.selected-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.selected-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Modal Body adjustments */
.edit-modal-body {
    padding: 25px;
    overflow-y: auto;
    flex: 1;
}

.edit-modal-body::-webkit-scrollbar {
    width: 8px;
}

.edit-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.edit-modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.edit-modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* ==================== */
/* Existing Styles (Keep these) */
/* ==================== */

        /* Bulk Update Status Button */
        .btn-info {
            padding: 8px 16px;
            background: #ffc107;
            color: #212529;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-info:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }

        .selected-violation-item {
            padding: 8px 12px;
            margin-bottom: 8px;
            background: white;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .selected-violation-item:last-child {
            margin-bottom: 0;
        }

        /* Action buttons container */
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: nowrap;
        }

        /* Update Status button */
        .update-status-btn {
            padding: 6px 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .update-status-btn:hover:not(:disabled) {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .update-status-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Appointment details in modal */
        .appointment-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 8px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            width: 120px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #212529;
            flex: 1;
        }

        /* Modal form styles */
        .edit-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
            flex-shrink: 0;
        }

        .edit-modal-header h2 {
            margin: 0;
            font-size: 1.5em;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            padding: 5px;
        }

        .close-btn:hover {
            opacity = 1;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .edit-form-group.full-width {
            grid-column: 1 / -1;
        }

        .edit-form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .edit-form-group select,
        .edit-form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .form-hint {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
            font-style: italic;
        }

        .required-fields-note {
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
            font-style: italic;
        }

        .edit-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 0 0 8px 8px;
            flex-shrink: 0;
        }

        /* View Button Styles (Matching parent page) */
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

        /* Table adjustments */
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

        /* Status Badge Styles */
      /* Update Status Badge Styles */
.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Updated to blue */
.status-scheduled {
    background-color: #d1e7fd; /* Lighter blue */
    color: #004085; /* Darker blue text */
    border: 1px solid #b8daff;
}

/* Updated to red */
.status-rescheduled {
    background-color: #f8d7da; /* Lighter red */
    color: #721c24; /* Darker red text */
    border: 1px solid #f5c6cb;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

        /* Incident and Notes preview */
        .incident-preview,
        .notes-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .view-full-incident,
        .view-full-notes {
            color: #007bff;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .view-full-incident:hover,
        .view-full-notes:hover {
            text-decoration: underline;
        }

        /* Export buttons */
        .export-buttons-container {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
            gap: 10px;
        }

        .btn-export {
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap = 8px;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background: #2563eb;
        }

        .btn-export.excel {
            background: #10b981;
        }

        .btn-export.excel:hover {
            background: #059669;
        }

        /* ==================== */
        /* Updated Modal Styles (Matching parent page) */
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
            opacity = 1;
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
            border-radius = 8px;
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
            color: #1e293b;
            font-weight: 500;
            margin-top: 2px;
            word-break: break-all;
        }

        /* Students Section */
        .students-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .students-container {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        /* Notes container */
        .notes-container {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 10px;
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
    background: #2563eb;
}

        /* Scrollbar Styling */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .large-modal {
                max-width: 95% !important;
                width: 95% !important;
                max-height: 95vh !important;
            }

            .edit-form-grid-maximized {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .edit-form-group.full-width-maximized {
                grid-column: 1;
            }

            .selected-list {
                max-height: 120px;
            }

            .select-options {
                flex-wrap: wrap;
                gap: 15px;
            }

            .left-controls, .right-controls {
                width: 100%;
            }

            .right-controls {
                justify-content: flex-start;
                margin-top: 10px;
            }

            .btn-info {
                width: 100%;
                justify-content: center;
            }
        }

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

            .btn-secondary, .modal-export {
                width: 100%;
                justify-content: center;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-view, .update-status-btn {
                width: 100%;
                justify-content: center;
            }

            .edit-modal-actions {
                flex-direction: column;
            }

            .edit-modal-actions .btn-secondary,
            .edit-modal-actions .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }

        /* Pagination Styles (Matching parent page) */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-nav {
            display: flex;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
        }

        .page-item {
            margin: 0;
        }

        .page-link {
            display: block;
            padding: 8px 12px;
            color: #3b82f6;
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e0;
        }

        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            cursor: not-allowed;
        }

        .pagination-info {
            font-size: 14px;
            color: #64748b;
        }

        /* No data message */
        .no-data {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-style: italic;
        }

        /* Modal Base Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>

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
// STATUS OPTIONS MANAGEMENT - UPDATED TO PREVENT MULTIPLE RESCHEDULES IN MODAL
// ==========================

// ==========================
// STATUS OPTIONS MANAGEMENT - UPDATED TO PREVENT MULTIPLE RESCHEDULES IN MODAL
// ==========================

class StatusOptionsManager {
    static getAvailableOptions(currentStatus, isForDisplayOnly = false) {
        const allOptions = [
            { value: 'Pending', label: '⏳ Pending', disabled: false },
            { value: 'Scheduled', label: '📅 Scheduled', disabled: false },
            { value: 'Rescheduled', label: '🔄 Rescheduled', disabled: false },
            { value: 'Completed', label: '✅ Completed', disabled: false },
            { value: 'Cancelled', label: '❌ Cancelled', disabled: false }
        ];

        // Apply rules based on current status
        switch(currentStatus) {
            case 'Pending':
                // When pending, can ONLY go to: Scheduled (per your requirement)
                // Cannot go to: Rescheduled, Completed, Cancelled, or stay as Pending
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: isForDisplayOnly ? false :
                        (opt.value !== 'Scheduled') // Only "Scheduled" is NOT disabled
                }));

            case 'Scheduled':
                // When scheduled, can go to: Rescheduled, Completed, Cancelled
                // Cannot go back to Pending, cannot select same status
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: isForDisplayOnly ? false :
                        (opt.value === 'Pending' || opt.value === 'Scheduled')
                }));

            case 'Rescheduled':
                // When already rescheduled, CANNOT RESCHEDULE AGAIN
                // Can only go to: Completed, Cancelled
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: isForDisplayOnly ? false :
                        (opt.value === 'Pending' || opt.value === 'Scheduled' || opt.value === 'Rescheduled')
                }));

            case 'Completed':
                // When completed, cannot change status anymore
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: isForDisplayOnly ? false : true
                }));

            case 'Cancelled':
                // When cancelled, can only reschedule (go to Rescheduled)
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: isForDisplayOnly ? false : (opt.value !== 'Rescheduled')
                }));

            default:
                return allOptions.map(opt => ({
                    ...opt,
                    disabled: false
                }));
        }
    }

    static populateDropdown(selectElement, currentStatus) {
        const selectOptions = this.getAvailableOptions(currentStatus, false);

        selectElement.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Select New Status --';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        selectElement.appendChild(defaultOption);

        selectOptions.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            option.disabled = opt.disabled;

            // Add helpful text for disabled options
            if (opt.disabled) {
                if (opt.value === currentStatus) {
                    option.textContent += ' (Cannot select same status)';
                } else if (currentStatus === 'Rescheduled' && opt.value === 'Rescheduled') {
                    option.textContent += ' (Cannot reschedule multiple times)';
                } else if (currentStatus === 'Pending' && opt.value === 'Pending') {
                    option.textContent += ' (Cannot stay as Pending)';
                }
            }

            // Add special notes for specific statuses
            if (currentStatus === 'Cancelled' && opt.value === 'Rescheduled') {
                option.textContent += ' (Reschedule cancelled appointment)';
            }

            // Add special note for Pending → Scheduled
            if (currentStatus === 'Pending' && opt.value === 'Scheduled' && !opt.disabled) {
                option.textContent += ' (Only option for Pending status)';
            }

            selectElement.appendChild(option);
        });

        selectElement.dataset.currentStatus = currentStatus;
    }

    static shouldDisableForm(currentStatus) {
        return currentStatus === 'Completed';
    }

    static toggleFormFields(container, currentStatus) {
        const shouldDisable = this.shouldDisableForm(currentStatus);
        const form = container.closest('form');

        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            const submitBtn = form.querySelector('button[type="submit"]');

            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    if (currentStatus === 'Cancelled') {
                        input.disabled = false;
                    } else {
                        input.disabled = shouldDisable;
                    }

                    if (input.id === 'bulk_appointment_date' || input.id === 'bulk_appointment_time') {
                        const statusSelect = form.querySelector('#bulk_new_status, #new_status');
                        if (statusSelect) {
                            const selectedStatus = statusSelect.value;
                            if (selectedStatus === 'Scheduled' || selectedStatus === 'Rescheduled') {
                                input.required = true;
                            } else {
                                input.required = false;
                            }
                        }
                    }
                }
            });

            if (submitBtn) {
                submitBtn.disabled = shouldDisable;
                if (shouldDisable) {
                    if (currentStatus === 'Completed') {
                        submitBtn.innerHTML = '<span>🔒 Locked (Completed)</span>';
                    }
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                } else {
                    submitBtn.innerHTML = '<span>💾 Update Status</span>';
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                }
            }

            const formHint = form.querySelector('.form-hint');
            if (formHint) {
                if (currentStatus === 'Completed') {
                    formHint.innerHTML = '⚠️ This appointment is marked as completed and cannot be edited.';
                    formHint.style.color = '#856404';
                } else if (currentStatus === 'Cancelled') {
                    formHint.innerHTML = 'Note: Cancelled appointments can only be rescheduled. Select "Rescheduled" to reschedule.';
                    formHint.style.color = '#0c5460';
                } else if (currentStatus === 'Scheduled') {
                    formHint.innerHTML = 'Note: Cannot update to same status. Select "Rescheduled", "Completed", or "Cancelled".';
                    formHint.style.color = '#0c5460';
                } else if (currentStatus === 'Rescheduled') {
                    formHint.innerHTML = 'Note: Cannot reschedule multiple times. Select "Completed" or "Cancelled" only.';
                    formHint.style.color = '#0c5460';
                } else if (currentStatus === 'Pending') {
                    // Updated hint for Pending status
                    formHint.innerHTML = 'Note: Pending appointments can only be changed to "Scheduled". Other statuses are not available.';
                    formHint.style.color = '#0c5460';
                } else {
                    formHint.innerHTML = 'Cannot select the same status. Please choose a different status.';
                    formHint.style.color = '#6c757d';
                }
            }
        }
    }

    static validateStatusChange(currentStatus, selectedStatus) {
        // For pending appointments, only allow Scheduled
        if (currentStatus === 'Pending' && selectedStatus !== 'Scheduled') {
            return {
                valid: false,
                message: 'Pending appointments can only be changed to "Scheduled". Please select "Scheduled".'
            };
        }

        // For cancelled appointments, only allow Rescheduled
        if (currentStatus === 'Cancelled' && selectedStatus !== 'Rescheduled') {
            return {
                valid: false,
                message: 'Cancelled appointments can only be rescheduled. Please select "Rescheduled".'
            };
        }

        // For completed appointments, no changes allowed
        if (currentStatus === 'Completed') {
            return {
                valid: false,
                message: 'Completed appointments cannot be changed.'
            };
        }

        // Prevent selecting same status (including Rescheduled now)
        if (currentStatus === selectedStatus) {
            return {
                valid: false,
                message: `Cannot update to same status (${currentStatus}). Please select a different status.`
            };
        }

        // NEW: Prevent multiple reschedules - THIS HAPPENS IN THE MODAL WHEN USER SELECTS STATUS
        if (currentStatus === 'Rescheduled' && selectedStatus === 'Rescheduled') {
            return {
                valid: false,
                message: 'Cannot reschedule multiple times. Please select "Completed" or "Cancelled".'
            };
        }

        // Validate status transitions based on rules
        const allowedOptions = this.getAvailableOptions(currentStatus, false);
        const isAllowed = allowedOptions.find(opt => opt.value === selectedStatus && !opt.disabled);

        if (!isAllowed) {
            return {
                valid: false,
                message: `Invalid status transition: ${currentStatus} → ${selectedStatus}`
            };
        }

        return { valid: true };
    }
}
// ==========================
// BULK UPDATE STATUS FUNCTIONALITY
// ==========================

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken);

    // ✅ Select All - Main Table
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.appointment-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
        console.log('Select All checkbox event listener attached');
    }

    // Helper function to check if all selected appointments have the same status
    function checkIfAllSelectedHaveSameStatus(selectedCheckboxes) {
        if (selectedCheckboxes.length === 0) return true;

        const firstStatus = selectedCheckboxes[0].closest('tr').getAttribute('data-status');

        for (let i = 1; i < selectedCheckboxes.length; i++) {
            const currentStatus = selectedCheckboxes[i].closest('tr').getAttribute('data-status');
            if (currentStatus !== firstStatus) {
                return false;
            }
        }
        return true;
    }

    // Helper function to get unique statuses from selected appointments
    function getUniqueStatusesFromSelected(selectedCheckboxes) {
        const statuses = new Set();
        selectedCheckboxes.forEach(cb => {
            const status = cb.closest('tr').getAttribute('data-status');
            statuses.add(status);
        });
        return Array.from(statuses);
    }

    // Bulk Update Status Button
    const bulkUpdateBtn = document.getElementById('bulkUpdateStatusBtn');
    if (bulkUpdateBtn) {
        bulkUpdateBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.appointment-checkbox:checked');

            if (!selectedCheckboxes.length) {
                notifications.showNotification('Please select at least one appointment to update.', 'warning');
                return;
            }

            // Check if all selected appointments have the same status
            const allSameStatus = checkIfAllSelectedHaveSameStatus(selectedCheckboxes);
            if (!allSameStatus) {
                const uniqueStatuses = getUniqueStatusesFromSelected(selectedCheckboxes);
                const statusList = uniqueStatuses.join(', ');

                notifications.showNotification(
                    `Cannot update appointments with different statuses. Selected appointments have: ${statusList}. ` +
                    'Please select appointments with the same status only.',
                    'warning'
                );
                return;
            }

            // Collect selected appointment IDs and data
            const selectedAppointments = [];
            let hasCompletedAppointments = false;

            selectedCheckboxes.forEach(cb => {
                const row = cb.closest('tr');
                const status = row.getAttribute('data-status');
                selectedAppointments.push({
                    id: cb.value,
                    studentName: row.getAttribute('data-student-name'),
                    status: status,
                    date: row.getAttribute('data-date'),
                    time: row.getAttribute('data-time'),
                    notes: row.getAttribute('data-notes')
                });

                if (status === 'Completed') hasCompletedAppointments = true;
            });

            // Check if any appointments are completed
            if (hasCompletedAppointments) {
                const completedCount = selectedAppointments.filter(a => a.status === 'Completed').length;
                let message = 'Cannot update appointments that are already marked as Completed: ';
                message += `(${completedCount} appointments)`;

                notifications.showNotification(message, 'warning');
                return;
            }

            // Update selected count
            document.getElementById('selectedCount').textContent = selectedAppointments.length;

            // Store in modal data attribute
            document.getElementById('bulkUpdateStatusModal').dataset.selectedAppointments = JSON.stringify(selectedAppointments);

            // Populate the selected appointments list
            const selectedList = document.getElementById('selectedAppointmentsList');
            selectedList.innerHTML = '';

            selectedAppointments.forEach(appointment => {
                const item = document.createElement('div');
                item.className = 'selected-violation-item';
                item.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${appointment.studentName}</strong>
                            <br>
                            <small style="color: #666;">
                                Current Status: <span class="current-status">${appointment.status}</span>
                            </small>
                        </div>
                    </div>
                `;
                selectedList.appendChild(item);
            });

            // Get the common status (all are same now)
            const commonStatus = selectedAppointments[0].status;

            // Auto-fill form with first selected appointment's data
            const firstAppointment = selectedAppointments[0];
            if (firstAppointment) {
                // Format date for input field
                if (firstAppointment.date && firstAppointment.date !== 'N/A') {
                    const dateObj = new Date(firstAppointment.date);
                    const formattedDate = dateObj.toISOString().split('T')[0];
                    document.getElementById('bulk_appointment_date').value = formattedDate;
                } else {
                    document.getElementById('bulk_appointment_date').value = '';
                }

                // Format time for input field
                if (firstAppointment.time && firstAppointment.time !== 'N/A') {
                    const timeStr = firstAppointment.time;
                    if (timeStr.includes('AM') || timeStr.includes('PM')) {
                        const [timePart, period] = timeStr.split(' ');
                        let [hours, minutes] = timePart.split(':');
                        hours = parseInt(hours);
                        minutes = parseInt(minutes);

                        if (period === 'PM' && hours < 12) {
                            hours += 12;
                        } else if (period === 'AM' && hours === 12) {
                            hours = 0;
                        }

                        const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                        document.getElementById('bulk_appointment_time').value = formattedTime;
                    } else {
                        document.getElementById('bulk_appointment_time').value = timeStr;
                    }
                } else {
                    document.getElementById('bulk_appointment_time').value = '';
                }

                // Set notes
                if (firstAppointment.notes && firstAppointment.notes !== 'N/A') {
                    document.getElementById('bulk_appointment_notes').value = firstAppointment.notes;
                } else {
                    document.getElementById('bulk_appointment_notes').value = '';
                }

                // Set status dropdown
                const statusSelect = document.getElementById('bulk_new_status');
                StatusOptionsManager.populateDropdown(statusSelect, commonStatus);
                statusSelect.value = '';

                // Check if we need to disable form
                StatusOptionsManager.toggleFormFields(statusSelect, commonStatus);
            }

            // Show modal
            document.getElementById('bulkUpdateStatusModal').style.display = 'flex';
        });
    }

    // Handle bulk form submission
    const bulkForm = document.getElementById('bulkUpdateStatusForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const saveBtn = this.querySelector('.btn-primary');
            const originalText = saveBtn.innerHTML;

            // Show loading state
            saveBtn.innerHTML = '<span>⏳ Updating...</span>';
            saveBtn.disabled = true;

            try {
                // Get selected appointments data
                const selectedAppointmentsJson = document.getElementById('bulkUpdateStatusModal').dataset.selectedAppointments;
                const selectedAppointments = JSON.parse(selectedAppointmentsJson || '[]');

                if (selectedAppointments.length === 0) {
                    notifications.showNotification('No appointments selected for update.', 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                const appointmentIds = selectedAppointments.map(app => app.id);
                const newStatus = document.getElementById('bulk_new_status').value;
                const appointmentDate = document.getElementById('bulk_appointment_date').value;
                const appointmentTime = document.getElementById('bulk_appointment_time').value;
                const appointmentNotes = document.getElementById('bulk_appointment_notes').value;

                // Validation
                if (!newStatus) {
                    notifications.showNotification('Please select a status.', 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                // Validate status transitions for each appointment
                const invalidTransitions = [];
                selectedAppointments.forEach(app => {
                    const validation = StatusOptionsManager.validateStatusChange(app.status, newStatus);

                    if (!validation.valid) {
                        invalidTransitions.push({
                            student: app.studentName,
                            from: app.status,
                            to: newStatus,
                            message: validation.message
                        });
                    }
                });

                if (invalidTransitions.length > 0) {
                    const firstError = invalidTransitions[0];
                    let message = `${firstError.message}`;

                    if (invalidTransitions.length > 1) {
                        message += ` (and ${invalidTransitions.length - 1} more appointment${invalidTransitions.length > 2 ? 's' : ''})`;
                    }

                    notifications.showNotification(message, 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                // Validate date/time for Scheduled or Rescheduled status
                if ((newStatus === 'Scheduled' || newStatus === 'Rescheduled') && (!appointmentDate || !appointmentTime)) {
                    notifications.showNotification('Date and time are required when setting status to Scheduled or Rescheduled.', 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', csrfToken);

                // Add appointment IDs as array
                appointmentIds.forEach(id => {
                    formData.append('appointment_ids[]', id);
                });

                // Add update fields
                formData.append('new_status', newStatus);
                formData.append('appointment_date', appointmentDate);
                formData.append('appointment_time', appointmentTime);
                formData.append('appointment_notes', appointmentNotes);

                // Send the request
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification('✅ ' + result.message, 'success');
                    document.getElementById('bulkUpdateStatusModal').style.display = 'none';

                    // Refresh the page after 1.5 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } else {
                    notifications.showNotification('❌ ' + (result.message || 'Update failed'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('❌ An error occurred. Please try again.', 'error');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });
    }

    // ==========================
    // INDIVIDUAL UPDATE STATUS MODAL FUNCTIONALITY
    // ==========================

    // View button functionality
    function initializeViewModal() {
        const infoModal = document.getElementById('infoModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const exportSinglePdfBtn = document.getElementById('exportSinglePdfBtn');

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                infoModal.style.display = 'none';
            });
        }



        if (exportSinglePdfBtn) {
            exportSinglePdfBtn.addEventListener('click', function() {
                exportSingleAppointmentInfo();
            });
        }

        if (infoModal) {
            infoModal.addEventListener('click', function(event) {
                if (event.target === infoModal) {
                    infoModal.style.display = 'none';
                }
            });
        }

        // View button functionality
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();

                const row = this.closest('tr');
                const appointmentId = row.getAttribute('data-app-id');
                const studentName = row.getAttribute('data-student-name');
                const studentId = row.getAttribute('data-student-id');
                const gradeSection = row.getAttribute('data-grade-section');
                const adviser = row.getAttribute('data-adviser');
                const parentName = row.getAttribute('data-parent-name');
                const incident = row.getAttribute('data-incident');
                const offense = row.getAttribute('data-offense');
                const violationDate = row.getAttribute('data-violation-date');
                const notes = row.getAttribute('data-notes');
                const date = row.getAttribute('data-date');
                const time = row.getAttribute('data-time');
                const status = row.getAttribute('data-status');

                // Format dates
                let formattedDate = 'N/A';
                if (date && date !== 'null') {
                    formattedDate = new Date(date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                let formattedViolationDate = 'N/A';
                if (violationDate && violationDate !== 'null') {
                    formattedViolationDate = new Date(violationDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                // Fill info modal
                document.getElementById('info_student_name').textContent = studentName || 'N/A';
                document.getElementById('info_appointment_id').textContent = appointmentId || 'N/A';
                document.getElementById('info_student_id').textContent = studentId || 'N/A';
                document.getElementById('info_grade_section').textContent = gradeSection || 'N/A';
                document.getElementById('info_adviser').textContent = adviser || 'N/A';
                document.getElementById('info_parent').textContent = parentName || 'N/A';
                document.getElementById('info_incident').textContent = incident || 'N/A';
                document.getElementById('info_offense').textContent = offense || 'N/A';
                document.getElementById('info_violation_date').textContent = formattedViolationDate;
                document.getElementById('info_notes').textContent = notes || 'No notes available';
                document.getElementById('info_date').textContent = formattedDate;
                document.getElementById('info_time').textContent = time || 'N/A';

                // Set status
                const statusElement = document.getElementById('info_status');
                statusElement.textContent = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
                statusElement.className = 'info-value';
                statusElement.style.color = getStatusColor(status);
                statusElement.style.fontWeight = '600';

                // Store appointment data for export
                if (exportSinglePdfBtn) {
                    exportSinglePdfBtn.dataset.appointmentId = appointmentId;
                    exportSinglePdfBtn.dataset.appointmentData = JSON.stringify({
                        studentName,
                        studentId,
                        gradeSection,
                        adviser,
                        parentName,
                        incident,
                        offense,
                        violationDate: formattedViolationDate,
                        notes,
                        date: formattedDate,
                        time,
                        status
                    });
                }

                // Show modal
                infoModal.style.display = 'flex';

                // Also setup the individual update modal when viewing
                setupIndividualUpdateModal(appointmentId, studentName, status, date, time);
            });
        });

       function getStatusColor(status) {
    switch(status?.toLowerCase()) {
        case 'scheduled': return '#004085'; // Blue
        case 'pending': return '#856404';
        case 'completed': return '#155724';
        case 'rescheduled': return '#721c24'; // Red
        case 'cancelled': return '#721c24';
        default: return '#495057';
    }
}
    }

    // Setup individual update modal
    function setupIndividualUpdateModal(appointmentId, studentName, currentStatus, date, time) {
        // Set hidden fields
        document.getElementById('update_appointment_id').value = appointmentId;
        document.getElementById('update_current_status').value = currentStatus;

        // Set display fields
        document.getElementById('update_student_name').textContent = studentName;
        document.getElementById('update_current_status_display').textContent = currentStatus;

        // Format date and time for display
        let formattedDate = 'N/A';
        if (date && date !== 'null') {
            formattedDate = new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        let formattedTime = 'N/A';
        if (time && time !== 'null') {
            formattedTime = time;
        }

        document.getElementById('update_date_time').textContent = `${formattedDate} at ${formattedTime}`;

        // Populate status dropdown with appropriate options
        const statusSelect = document.getElementById('new_status');
        StatusOptionsManager.populateDropdown(statusSelect, currentStatus);
        statusSelect.value = '';

        // Disable form if status is Completed
        StatusOptionsManager.toggleFormFields(statusSelect, currentStatus);
    }

    // Handle individual form submission
    const individualForm = document.getElementById('updateStatusForm');
    if (individualForm) {
        individualForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const saveBtn = this.querySelector('.btn-primary');
            const originalText = saveBtn.innerHTML;

            // Show loading state
            saveBtn.innerHTML = '<span>⏳ Updating...</span>';
            saveBtn.disabled = true;

            try {
                const appointmentId = document.getElementById('update_appointment_id').value;
                const currentStatus = document.getElementById('update_current_status').value;
                const newStatus = document.getElementById('new_status').value;

                // Validation
                if (!newStatus) {
                    notifications.showNotification('Please select a new status.', 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                // Validate status change using new validation method
                const validation = StatusOptionsManager.validateStatusChange(currentStatus, newStatus);
                if (!validation.valid) {
                    notifications.showNotification(validation.message, 'warning');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }

                // Prepare form data
                const formData = new FormData(this);

                // Send the request
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification('✅ ' + result.message, 'success');
                    document.getElementById('updateStatusModal').style.display = 'none';

                    // Refresh the page after 1.5 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } else {
                    notifications.showNotification('❌ ' + (result.message || 'Update failed'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('❌ An error occurred. Please try again.', 'error');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });
    }

    // ==========================
    // MODAL CLOSE BUTTONS (FIXED)
    // ==========================

    // Close Bulk Status Modal - X button
    const closeBulkStatusModalBtn = document.getElementById('closeBulkStatusModal');
    if (closeBulkStatusModalBtn) {
        closeBulkStatusModalBtn.addEventListener('click', function() {
            document.getElementById('bulkUpdateStatusModal').style.display = 'none';
        });
    }

    // Close Bulk Status Modal - Cancel button
    const cancelBulkStatusBtn = document.getElementById('cancelBulkStatusBtn');
    if (cancelBulkStatusBtn) {
        cancelBulkStatusBtn.addEventListener('click', function() {
            document.getElementById('bulkUpdateStatusModal').style.display = 'none';
        });
    }

    // Close Individual Status Modal - X button
    const closeStatusModalBtn = document.getElementById('closeStatusModal');
    if (closeStatusModalBtn) {
        closeStatusModalBtn.addEventListener('click', function() {
            document.getElementById('updateStatusModal').style.display = 'none';
        });
    }

    // Close Individual Status Modal - Cancel button
    const cancelStatusBtn = document.getElementById('cancelStatusBtn');
    if (cancelStatusBtn) {
        cancelStatusBtn.addEventListener('click', function() {
            document.getElementById('updateStatusModal').style.display = 'none';
        });
    }

    // Close modal when clicking outside
    const bulkModal = document.getElementById('bulkUpdateStatusModal');
    if (bulkModal) {
        bulkModal.addEventListener('click', function(event) {
            if (event.target === this) {
                this.style.display = 'none';
            }
        });
    }

    // Close individual status modal when clicking outside
    const individualStatusModal = document.getElementById('updateStatusModal');
    if (individualStatusModal) {
        individualStatusModal.addEventListener('click', function(event) {
            if (event.target === this) {
                this.style.display = 'none';
            }
        });
    }

    // ==========================
    // Export Buttons - UPDATED WITH OLD PDF EXPORT STYLE
    // ==========================

    const exportPdfBtn = document.getElementById('exportPdfBtn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function() {
            exportAllAppointments('pdf');
        });
    }

    const exportExcelBtn = document.getElementById('exportExcelBtn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            exportAllAppointments('excel');
        });
    }

    // ==========================
    // Search Functionality
    // ==========================

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // ==========================
    // Status Filter
    // ==========================

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        const savedStatus = localStorage.getItem('appointmentStatusFilter');
        if (savedStatus) {
            statusFilter.value = savedStatus;
            filterByStatus(savedStatus);
        }

        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;
            localStorage.setItem('appointmentStatusFilter', selectedStatus);
            filterByStatus(selectedStatus);
        });

        function filterByStatus(status) {
            const rows = document.querySelectorAll('#tableBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.classList.contains('no-data')) return;

                const rowStatus = row.getAttribute('data-status');

                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateVisibleCount(visibleCount);
        }

        function updateVisibleCount(count) {
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo) {
                const currentStatus = document.getElementById('statusFilter').value;
                const totalText = currentStatus === 'all'
                    ? `Showing ${count} of {{ $vappointments->total() }} entries`
                    : `Showing ${count} filtered record(s)`;
                paginationInfo.textContent = totalText;
            }
        }
    }

    // Initialize view modal
    initializeViewModal();

    console.log('All JavaScript initialized successfully');
});

// ==========================
// EXPORT FUNCTIONS - USING OLD PDF STYLE
// ==========================

// Export All Appointments
function exportAllAppointments(format) {
    const appointmentRows = document.querySelectorAll('#tableBody tr:not(.no-data)');

    if (appointmentRows.length === 0) {
        notifications.showNotification('No appointments found to export.', 'warning');
        return;
    }

    // Collect all appointment data
    let appointmentData = [];
    appointmentRows.forEach(row => {
        if (row.classList.contains('no-data')) return;

        appointmentData.push({
            id: row.getAttribute('data-app-id'),
            student_name: row.getAttribute('data-student-name'),
            student_id: row.getAttribute('data-student-id'),
            offense: row.getAttribute('data-offense'),
            incident: row.getAttribute('data-incident'),
            date: row.getAttribute('data-date'),
            time: row.getAttribute('data-time'),
            notes: row.getAttribute('data-notes'),
            status: row.getAttribute('data-status'),
            grade_section: row.getAttribute('data-grade-section'),
            adviser: row.getAttribute('data-adviser'),
            parent_name: row.getAttribute('data-parent-name'),
            violation_date: row.getAttribute('data-violation-date')
        });
    });

    if (appointmentData.length === 0) {
        notifications.showNotification('No appointments found to export.', 'warning');
        return;
    }

    if (format === 'pdf') {
        exportAppointmentsToPDF(appointmentData, true);
    } else if (format === 'excel') {
        exportAppointmentsToExcel(appointmentData, true);
    }
}

// Export Single Appointment
function exportSingleAppointmentInfo() {
    const exportSinglePdfBtn = document.getElementById('exportSinglePdfBtn');
    if (!exportSinglePdfBtn) return;

    const appointmentData = JSON.parse(exportSinglePdfBtn.dataset.appointmentData || '{}');

    if (!appointmentData.studentName) {
        notifications.showNotification('No appointment data available for export.', 'warning');
        return;
    }

    // Convert single appointment to array format
    const singleAppointmentData = [{
        id: exportSinglePdfBtn.dataset.appointmentId,
        student_name: appointmentData.studentName,
        student_id: appointmentData.studentId,
        grade_section: appointmentData.gradeSection,
        adviser: appointmentData.adviser,
        parent_name: appointmentData.parentName,
        offense: appointmentData.offense,
        incident: appointmentData.incident,
        violation_date: appointmentData.violationDate,
        date: appointmentData.date,
        time: appointmentData.time,
        status: appointmentData.status,
        notes: appointmentData.notes
    }];

    exportSingleAppointmentToPDF(singleAppointmentData[0]);
}

// Export to Excel Function (Using old style)
function exportAppointmentsToExcel(appointmentData, isSelectAll = false) {
    // Create worksheet data
    const worksheetData = [
        // Headers
        ['Appointment ID', 'Student Name', 'Student ID', 'Offense', 'Incident', 'Appointment Date', 'Appointment Time', 'Status', 'Grade & Section', 'Adviser', 'Parent/Guardian', 'Violation Date', 'Notes'],
        // Data rows
        ...appointmentData.map(appointment => {
            // Format date
            let formattedDate = 'N/A';
            if (appointment.date && appointment.date !== 'null') {
                formattedDate = new Date(appointment.date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            // Format violation date
            let formattedViolationDate = 'N/A';
            if (appointment.violation_date && appointment.violation_date !== 'null') {
                formattedViolationDate = new Date(appointment.violation_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            return [
                appointment.id,
                appointment.student_name || 'N/A',
                appointment.student_id || 'N/A',
                appointment.offense || 'N/A',
                appointment.incident || 'N/A',
                formattedDate,
                appointment.time || 'N/A',
                appointment.status || 'N/A',
                appointment.grade_section || 'N/A',
                appointment.adviser || 'N/A',
                appointment.parent_name || 'N/A',
                formattedViolationDate,
                appointment.notes || 'N/A'
            ]
        })
    ];

    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(worksheetData);

    // Set column widths
    const colWidths = [
        { wch: 15 }, // Appointment ID
        { wch: 20 }, // Student Name
        { wch: 12 }, // Student ID
        { wch: 20 }, // Offense
        { wch: 30 }, // Incident
        { wch: 15 }, // Appointment Date
        { wch: 15 }, // Appointment Time
        { wch: 12 }, // Status
        { wch: 15 }, // Grade & Section
        { wch: 20 }, // Adviser
        { wch: 20 }, // Parent/Guardian
        { wch: 15 }, // Violation Date
        { wch: 30 }  // Notes
    ];
    ws['!cols'] = colWidths;

    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Appointments');

    // Generate Excel file
    const fileName = `Appointments_Export_${new Date().toISOString().slice(0,10)}.xlsx`;
    XLSX.writeFile(wb, fileName);

    notifications.showNotification('Excel file exported successfully', 'success');
}

// Export Multiple Appointments to PDF (Using old style)
function exportAppointmentsToPDF(appointmentData, isSelectAll = false) {
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

    const reportTitle = isSelectAll ? 'Complete Appointment Registry' : 'Selected Appointment Registry';
    const rowCount = appointmentData.length;

    // Create simple table HTML
    let tableHTML = `
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; table-layout: fixed; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Appointment ID</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Student Name</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Student ID</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Offense</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Incident</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Appointment Date</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Appointment Time</th>
                    <th style="background: #1e3a8a; color: white; padding: 8px 6px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                </tr>
            </thead>
            <tbody>
    `;

    // Add each appointment's data to the table
    appointmentData.forEach((appointment, index) => {
        // Format date
        let formattedDate = 'N/A';
        if (appointment.date && appointment.date !== 'null') {
            formattedDate = new Date(appointment.date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Format time
        let formattedTime = appointment.time || 'N/A';

        // Truncate incident if too long
        let truncatedIncident = appointment.incident || 'N/A';
        if (truncatedIncident.length > 50) {
            truncatedIncident = truncatedIncident.substring(0, 50) + '...';
        }

        // Alternate row colors
        const rowColor = index % 2 === 0 ? '#ffffff' : '#f7fafc';

        tableHTML += `
            <tr style="background-color: ${rowColor};">
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${appointment.id}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${appointment.student_name || 'N/A'}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${appointment.student_id || 'N/A'}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${appointment.offense || 'N/A'}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${truncatedIncident}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${formattedDate}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${formattedTime}</td>
                <td style="padding: 6px 4px; border: 1px solid #e2e8f0; font-size: 8px; color: #000000; word-wrap: break-word;">${appointment.status || 'N/A'}</td>
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
                    <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Violation Appointment Management System</h2>
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
                            Total Records: <strong style="color: #000000;">${rowCount} Appointment(s)</strong>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #000000;">Document ID</div>
                        <div style="font-size: 14px; font-weight: 600; color: #000000;">APP-${Date.now().toString().slice(-6)}</div>
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
                pdf.text('Tagoloan Senior High School - Violation Appointment System',
                    pdf.internal.pageSize.getWidth() / 2 - 70,
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

// Export Single Appointment to PDF (Using old style)
function exportSingleAppointmentToPDF(appointmentData) {
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

    // Create PDF content
    const pdfContent = `
        <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
            <!-- Professional Header with Logo on Right -->
            <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                <div style="flex: 1;">
                    <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                    <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Violation Appointment System</h2>
                    <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Individual Appointment Record</p>
                </div>
                <div style="text-align: right;">
                    <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </div>
            </div>

            <!-- Report Summary -->
            <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">Appointment Record</h3>
                        <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                            Student: <strong style="color: #000000;">${appointmentData.student_name}</strong>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #000000;">Document ID</div>
                        <div style="font-size: 14px; font-weight: 600; color: #000000;">APP-RECORD-${Date.now().toString().slice(-6)}</div>
                    </div>
                </div>
            </div>

            <!-- Appointment Information -->
            <div style="margin: 0 25px 25px 25px;">
                <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Appointment Details</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; margin-top: 10px; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr>
                            <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; width: 30%;">Field</th>
                            <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; width: 70%;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Student Name</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.student_name}</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Student ID</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.student_id}</td>
                        </tr>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Grade & Section</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.grade_section}</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Adviser</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.adviser}</td>
                        </tr>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Parent/Guardian</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.parent_name}</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Offense Type</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.offense}</td>
                        </tr>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Incident</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.incident}</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Violation Date</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.violation_date}</td>
                        </tr>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Appointment Date</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.date}</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Appointment Time</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${appointmentData.time}</td>
                        </tr>
                        <tr style="background-color: #ffffff;">
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Status</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000; font-weight: 600; color: ${getStatusColor(appointmentData.status)}">${appointmentData.status}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes Section -->
            <div style="margin: 0 25px 25px 25px;">
                <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Appointment Notes</h4>
                <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; min-height: 80px;">
                    <p style="margin: 0; color: #4a5568; font-size: 14px; line-height: 1.5;">${appointmentData.notes}</p>
                </div>
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
        script.onload = () => generateSinglePDF(pdfContent, appointmentData.student_name);
        document.head.appendChild(script);
    } else {
        generateSinglePDF(pdfContent, appointmentData.student_name);
    }

    function generateSinglePDF(content, studentName) {
        // Create a temporary element for PDF generation
        const element = document.createElement('div');
        element.innerHTML = content;

        // PDF options for new tab preview
        const options = {
            margin: [10, 15, 25, 15],
            filename: `Appointment_${studentName.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
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
                pdf.text('Tagoloan Senior High School - Violation Appointment System',
                    pdf.internal.pageSize.getWidth() / 2 - 70,
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

function getStatusColor(status) {
    switch(status?.toLowerCase()) {
        case 'scheduled': return '#004085';
        case 'pending': return '#856404';
        case 'completed': return '#155724';
        case 'rescheduled': return '#0c5460';
        case 'cancelled': return '#721c24';
        default: return '#495057';
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = [
        'infoModal', 'bulkUpdateStatusModal', 'updateStatusModal',
        'notificationModal', 'confirmationModal'
    ];

    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && event.target === modal) {
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
@endsection
