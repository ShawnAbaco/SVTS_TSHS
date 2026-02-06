@extends('adviser.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- ✅ Toolbar -->
        <div class="toolbar">
            <h2>Violation Management</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
                <a href="{{ route('adviser.violations.create') }}" class="btn-primary" id="createBtn">
                    <i class="fas fa-plus"></i> Add Violation
                </a>
            </div>
        </div>

        <!-- Bulk Action / Dropdown -->
        <div class="select-options">
            <div class="dropdown">
                <button class="btn-info dropdown-btn">⬇️ Violation Anecdotals</button>
                <div class="dropdown-content">
                    <a href="{{ route('adviser.violation') }}"
                        class="route-link {{ Request::is('prefect/violations*') ? 'active' : '' }}"
                        data-table="violationRecords">Violation Records</a>

                    <a href="{{ route('adviser.violationAnecdotal') }}"
                        class="route-link {{ Request::is('prefect/violation-appointments*') ? 'active' : '' }}"
                        data-table="violationAppointments">Violation Anecdotals</a>

                    <a href="{{ route('adviser.violationAppointment') }}"
                        class="route-link {{ Request::is('prefect/violation-anecdotals*') ? 'active' : '' }}"
                        data-table="violationAnecdotals">Violation Appointments</a>
                </div>
            </div>

            <div class="right-controls">
                <!-- Violation Records Buttons -->
                <div id="violationRecordsActions" class="action-buttons">

                </div>

                <!-- Violation Appointments Buttons -->
                <div id="violationAppointmentsActions" class="action-buttons" style="display:none;">
                    <button class="btn-cleared" id="markAppointmentCompletedBtn">Mark as Completed</button>
                    <button class="btn-danger" id="moveAppointmentToTrashBtn">🗑️ Move Selected to Trash</button>
                </div>

                <!-- Violation Anecdotals Buttons -->
                <div id="violationAnecdotalsActions" class="action-buttons" style="display:none;">
                    <button class="btn-cleared" id="markAnecdotalCompletedBtn">Mark as Completed</button>
                    <button class="btn-danger" id="moveAnecdotalToTrashBtn">🗑️ Move Selected to Trash</button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <!-- 📝 VIOLATION ANECDOTALS TABLE (ONLY SHOW THIS ONE) -->
            <div id="violationAnecdotalsTable" class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                    data-adviser="{{ $anec->violation->student->adviser->name ?? 'N/A' }}"
                                    data-parent-name="{{ $anec->violation->student->parent->parent_fname ?? 'N/A' }} {{ $anec->violation->student->parent->parent_lname ?? 'N/A' }}"
                                    data-violation-date="{{ $anec->violation->violation_date ?? 'N/A' }}"
                                    class="clickable-row">
                                    <td>{{ $anec->violation_anec_id }}</td>
                                    <td>
                                        {{ $anec->violation->student->student_fname ?? 'N/A' }}
                                        {{ $anec->violation->student->student_lname ?? '' }}
                                    </td>
                                    <td>{{ $anec->violation_anec_date }}</td>
                                    <td>{{ \Carbon\Carbon::parse($anec->violation_anec_time)->format('h:i A') }}</td>
                                    <td>
                                        <span class="status-badge status-completed">
                                            Completed
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-primary editAnecdotalBtn">✏️ Edit</button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;">No completed anecdotal records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    <div class="pagination-summary">
                        @if ($anecdotals instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            @php
                                $activeCount = $anecdotals->whereIn('status', ['active', 'in_progress'])->count();
                            @endphp
                            Showing {{ $activeCount > 0 ? '1' : '0' }} to {{ $activeCount }} of {{ $activeCount }}
                            record(s)
                        @endif
                    </div>
                    <div class="pagination-links">
                        {{ $anecdotals->links() }}
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

        <!-- 👁️ Anecdotal Details Modal -->
        <div class="modal" id="anecdotalDetailsModal">
            <div class="modal-content">
                <button class="close-btn" id="closeAnecdotalDetailsModal">✖</button>
                <h2>Anecdotal Details</h2>
                <div class="anecdotal-details-container">
                    <div class="detail-section">
                        <h3>Student Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Student Name:</label>
                                <span id="detail-anecdotal-student-name">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Student ID:</label>
                                <span id="detail-anecdotal-student-id">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="detail-section">
                        <h3>Violation Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Incident:</label>
                                <span id="detail-anecdotal-incident">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Offense Type:</label>
                                <span id="detail-anecdotal-offense">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="detail-section">
                        <h3>Anecdotal Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Anecdotal ID:</label>
                                <span id="detail-anecdotal-id">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Date:</label>
                                <span id="detail-anecdotal-date">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Time:</label>
                                <span id="detail-anecdotal-time">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span id="detail-anecdotal-status" class="status-badge">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn-print" id="exportPdfBtn">📄 Export PDF</button>
                </div>
            </div>
        </div>


        <!-- ✏️ Edit Anecdotal Modal -->
        <div class="modal" id="editAnecdotalModal">
            <div class="modal-content">
                <button class="close-btn" id="closeAnecdotalEditModal">✖</button>
                <h2>Edit Anecdotal Record</h2>
                <form id="editAnecdotalForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="record_id" id="edit_anecdotal_record_id">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Solution</label>
                            <textarea id="edit_solution" name="solution" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Recommendation</label>
                            <textarea id="edit_recommendation" name="recommendation" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="edit_anecdotal_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" id="edit_anecdotal_time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="edit_anecdotal_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn-primary">💾 Save Changes</button>
                        <button type="button" class="btn-secondary" id="cancelAnecdotalEditBtn">❌ Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 📝 Create Anecdotal Modal -->
        <div class="modal" id="createAnecdotalModal">
            <div class="modal-content">
                <button class="close-btn" id="closeAnecdotalModal">✖</button>
                <h2>Create Anecdotal Record for Selected Violations</h2>
                <form id="createAnecdotalForm" method="POST" action="{{ route('adviser.storeMultipleAnecdotals') }}">
                    @csrf
                    <div class="selected-violations">
                        <h3>Selected Violations:</h3>
                        <div id="selectedViolationsForAnecdotal" class="selected-list"></div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="anecdotal_date">Anecdotal Date</label>
                            <input type="date" id="anecdotal_date" name="anecdotal_date" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="anecdotal_time">Anecdotal Time</label>
                            <input type="time" id="anecdotal_time" name="anecdotal_time" required
                                value="{{ date('H:i') }}">
                        </div>
                        <div class="form-group full-width">
                            <label for="violation_anec_solution">Solution</label>
                            <textarea id="violation_anec_solution" name="violation_anec_solution"
                                placeholder="Describe the solution implemented..." required rows="4"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="violation_anec_recommendation">Recommendation</label>
                            <textarea id="violation_anec_recommendation" name="violation_anec_recommendation"
                                placeholder="Provide recommendations for future prevention..." required rows="4"></textarea>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn-primary">📝 Create Anecdotal Records</button>
                        <button type="button" class="btn-secondary" id="cancelAnecdotalBtn">❌ Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ✅ Anecdotal Success Modal -->
        <div class="modal" id="anecdotalSuccessModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>✅ Anecdotal Records Created Successfully</h2>
                </div>
                <div class="modal-body">
                    <p id="successMessage"></p>
                    <div class="success-actions">
                        <button class="btn-primary" id="closeSuccessModal">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Include html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        .bbttn-danger {
            background-color: #dc3545;
            /* red background */
            color: white;
            /* white text */
            border: none;
            /* no border */
            padding: 8px 16px;
            /* spacing inside button */
            border-radius: 4px;
            /* rounded corners */
            cursor: pointer;
            /* pointer on hover */
            font-weight: bold;
            /* bold text */
            transition: background-color 0.2s ease;
            /* smooth hover effect */
            margin-right: 10px;
            /* spacing to next element */
        }

        .bbttn-danger:hover {
            background-color: #e53935;
            /* slightly darker red on hover */
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: #f5f5f5;
        }

        .violation-details-container,
        .anecdotal-details-container,
        .sr-details-container {
            margin: 20px 0;
        }

        .detail-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fafafa;
        }

        .detail-section h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .detail-item span {
            color: #333;
            padding: 8px 12px;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .solution-content,
        .recommendation-content {
            background: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .solution-content p,
        .recommendation-content p {
            margin: 0;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-sms {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-sms:hover {
            background-color: #218838;
        }

        .btn-schedule {
            background-color: #ffc107;
            color: #212529;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-schedule:hover {
            background-color: #e0a800;
        }

        .btn-anecdotal {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-anecdotal:hover {
            background-color: #138496;
        }

        .btn-print {
            background-color: #6f42c1;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-print:hover {
            background-color: #5a2d91;
        }

        .selected-violations {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .selected-list {
            max-height: 150px;
            overflow-y: auto;
            margin-top: 10px;
        }

        .selected-violation-item {
            padding: 8px 12px;
            margin-bottom: 5px;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            resize: vertical;
        }

        .status-in-progress {
            background-color: #ffc107;
            color: #212529;
        }

        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .status-closed {
            background-color: #6c757d;
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group textarea,
        .form-group input,
        .form-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close-btn:hover {
            color: #000;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-scheduled {
            background-color: #cce7ff;
            color: #004085;
        }

        .status-cleared {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-in-progress {
            background-color: #fff3cd;
            color: #856404;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-cleared {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-cleared:hover {
            background-color: #218838;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        tbody tr:hover {
            background-color: #f5f5f5;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .pagination-links {
            display: flex;
            gap: 5px;
        }

        .pagination-links a,
        .pagination-links span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination-links a:hover {
            background-color: #007bff;
            color: white;
        }

        .pagination-links .current {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .toolbar h2 {
            margin: 0;
            color: #333;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .actions input[type="search"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }

        .select-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .left-controls,
        .right-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .select-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 1;
            margin-top: 0px;

        }

        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
        }

        .dropdown-content a:hover {
            background-color: #f5f5f5;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .no-data-row {
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .rowCheckbox {
            cursor: pointer;
        }

        .archive-table-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 15px 0;
        }

        .archive-table {
            width: 100%;
            border-collapse: collapse;
        }

        .archive-table th,
        .archive-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .archive-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .modal-header {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-container,
        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-select,
        .search-input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .select-all-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .success-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        /* Select Options Styling */
        .select-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .right-controls {
            display: flex;
            gap: 10px;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px 30px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            min-width: 300px;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .notification.success {
            background: #27ae60;
        }

        .notification.error {
            background: #e74c3c;
        }

        .notification.info {
            background: #3498db;
        }

        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
    </style>

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

        // ==================== ROW CLICK FUNCTIONALITY ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Make table rows clickable
            const tableRows = document.querySelectorAll('#violationAnecdotalsTable tbody tr.clickable-row');

            tableRows.forEach(row => {
                // Make the row clickable (excluding action buttons)
                row.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (e.target.classList.contains('editAnecdotalBtn') ||
                        e.target.classList.contains('exportPdfBtn') ||
                        e.target.closest('.editAnecdotalBtn') ||
                        e.target.closest('.exportPdfBtn')) {
                        return;
                    }

                    openSRModal(this);
                });
            });
        });

        // Function to open SR modal
        function openSRModal(row) {
            const studentName = row.dataset.studentName;
            const studentId = row.dataset.studentId;
            const solution = row.dataset.solution;
            const recommendation = row.dataset.recommendation;

            // Populate SR modal with data
            document.getElementById('sr-student-name').textContent = studentName;
            document.getElementById('sr-student-id').textContent = studentId;
            document.getElementById('sr-solution').textContent = solution;
            document.getElementById('sr-recommendation').textContent = recommendation;

            // Show modal
            document.getElementById('SR-modal').style.display = 'flex';
        }

        // Function to open anecdotal details modal
        // Function to open anecdotal details modal
        function openAnecdotalDetailsModal(row) {
            const anecdotalId = row.dataset.anecId;
            const studentName = row.dataset.studentName;
            const studentId = row.dataset.studentId;
            const solution = row.dataset.solution;
            const recommendation = row.dataset.recommendation;
            const date = row.dataset.date;
            const time = row.dataset.time;
            const status = row.dataset.status;
            const incident = row.dataset.incident;
            const offense = row.dataset.offense;
            const gradeSection = row.dataset.gradeSection;
            const adviser = row.dataset.adviser;
            const parentName = row.dataset.parentName;
            const violationDate = row.dataset.violationDate;

            // Populate modal with data
            document.getElementById('detail-anecdotal-student-name').textContent = studentName;
            document.getElementById('detail-anecdotal-student-id').textContent = studentId;
            document.getElementById('detail-anecdotal-id').textContent = anecdotalId;
            document.getElementById('detail-anecdotal-date').textContent = date;
            document.getElementById('detail-anecdotal-time').textContent = time;
            document.getElementById('detail-anecdotal-incident').textContent = incident;
            document.getElementById('detail-anecdotal-offense').textContent = offense;

            // Set status badge
            const statusBadge = document.getElementById('detail-anecdotal-status');
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            statusBadge.className = 'status-badge ' + getStatusClass(status);

            // Store all data for PDF export
            const exportBtn = document.getElementById('exportPdfBtn');
            exportBtn.dataset.anecdotalId = anecdotalId;
            exportBtn.dataset.studentName = studentName;
            exportBtn.dataset.studentId = studentId;
            exportBtn.dataset.gradeSection = gradeSection;
            exportBtn.dataset.adviser = adviser;
            exportBtn.dataset.parentName = parentName;
            exportBtn.dataset.incident = incident;
            exportBtn.dataset.offense = offense;
            exportBtn.dataset.violationDate = violationDate;
            exportBtn.dataset.solution = solution;
            exportBtn.dataset.recommendation = recommendation;
            exportBtn.dataset.anecdotalDate = date;
            exportBtn.dataset.anecdotalTime = time;
            exportBtn.dataset.status = status;

            // Show modal
            document.getElementById('anecdotalDetailsModal').style.display = 'flex';
        }

        // Add click event listeners to table rows
        document.addEventListener('DOMContentLoaded', function() {
            // Make table rows clickable for modal
            const tableRows = document.querySelectorAll('#violationAnecdotalsTable tbody tr.clickable-row');

            tableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (e.target.classList.contains('editAnecdotalBtn') ||
                        e.target.closest('.editAnecdotalBtn')) {
                        return;
                    }
                    openAnecdotalDetailsModal(this);
                });
            });
        });

        // Function to get status class
        function getStatusClass(status) {
            const statusMap = {
                'active': 'status-active',
                'in_progress': 'status-in-progress',
                'completed': 'status-completed',
                'closed': 'status-closed'
            };
            return statusMap[status] || 'status-active';
        }

        // ==================== ENHANCED PDF GENERATION ====================
        // Direct PDF Export from table button
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('exportPdfBtn')) {
                e.stopPropagation();
                const anecdotalId = e.target.dataset.anecId;
                const row = e.target.closest('tr');
                generateAndShowPDF(row);
            }
        });

        // PDF Export from details modal
        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            const anecdotalId = this.dataset.anecdotalId;
            const row = document.querySelector(`tr[data-anec-id="${anecdotalId}"]`);
            if (row) {
                generateAndShowPDF(row);
            }
        });

        function generateAndShowPDF(row) {
            // Get all data from the row
            const data = {
                anecdotalId: row.dataset.anecId,
                studentName: row.dataset.studentName,
                studentId: row.dataset.studentId,
                gradeSection: row.dataset.gradeSection,
                adviser: row.dataset.adviser,
                prefect: row.dataset.prefect,
                parentName: row.dataset.parentName,
                incident: row.dataset.incident,
                offense: row.dataset.offense,
                violationDate: row.dataset.violationDate,
                solution: row.dataset.solution,
                recommendation: row.dataset.recommendation,
                anecdotalDate: formatReadableDate(row.dataset.date),
                anecdotalTime: row.dataset.time,
                status: row.dataset.status
            };

            // Generate PDF content
            const pdfContent = generatePDFContent(data);

            // Generate and open PDF in new tab
            generatePDFPreview(pdfContent, data);
        }

        function formatReadableDate(dateString) {
            if (!dateString) return "____________";

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return "____________";

            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        }


        function generatePDFContent(data) {
            return `
        <div style="font-family: 'Times New Roman', serif; color: #000; padding: 40px;">

            <!-- DEPED HEADER -->
            <div style="text-align: center; line-height: 1.4;">
                <div>Republic of Philippines</div>
                <div>Department of Education</div>
                <div><b>REGION X - NORTHERN MINDANAO</b></div>
                <div><b>SCHOOLS DIVISION OF MISAMIS ORIENTAL</b></div>
                <div><b>TAGOLOAN SENIOR HIGH SCHOOL</b></div>
            </div>

            <!-- HEADER LINE -->
            <div style="margin: 15px 0 20px 0; border-top: 1px solid #000;"></div>

            <!-- TITLE -->
            <div style="text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: 700;">
                ANECDOTAL RECORD
            </div>

            <div style="text-align: center; font-size: 14px;">
                Prefect Of Discipline
            </div>

            <div style="text-align: center; margin-bottom: 30px; font-size: 14px;">
                Date: ${data.anecdotalDate ?? "____________"}
            </div>

            <!-- INCIDENT -->
            <div style="margin-bottom: 50px; font-size: 14px; text-align: justify;">
                <strong>INCIDENT:</strong> ${data.incident}
            </div>

            <!-- RECOMMENDATION -->
            <div style="margin-bottom: 50px; font-size: 14px; text-align: justify;">
                <strong>RECOMMENDATION:</strong> ${data.recommendation}
            </div>

            <!-- SOLUTION -->
            <div style="margin-bottom: 50px; font-size: 14px; text-align: justify;">
                <strong>SOLUTION:</strong> ${data.solution}
            </div>

            <!-- SIGNATURES -->
            <div style="margin-top: 40px;">

                <div style="margin-bottom: 35px;">
                    <div style="font-size: 15px;">${data.studentName}</div>
                    <div style="border-top: 1px solid #000; width: 170px;"></div>
                    <div style="font-size: 13px;">Student's name and signature</div>
                </div>

                <div style="margin-bottom: 35px;">
                    <div style="font-size: 15px;">${data.parentName}</div>
                    <div style="border-top: 1px solid #000; width: 170px;"></div>
                    <div style="font-size: 13px;">Parent's name and signature</div>
                </div>

                <div style="margin-bottom: 35px;">
                    <div style="font-size: 15px;">
                        {{ Auth::user()->adviser_fname }} {{ Auth::user()->adviser_lname }}
                    </div>
                    <div style="border-top: 1px solid #000; width: 170px;"></div>
                    <div style="font-size: 13px;">Adviser's name and signature</div>
                </div>

                <div style="width: 100%; display: flex; justify-content: flex-end;">
                <div style="margin-bottom: 35px; text-align: right;">
                    <div style="font-size: 15px;">
                            {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                    </div>
                    <div style="border-top: 1px solid #000; width: 170px; margin-left: auto; margin-top: 5px;"></div>
                    <div style="font-size: 13px; margin-top: 5px;">
                        Prefect of Discipline Incharge
                    </div>
            </div>
        </div>
    `;
        }


        function generatePDFPreview(content, data) {
            // Create a temporary element for PDF generation
            const element = document.createElement('div');
            element.innerHTML = content;

            const reportTitle = `Anecdotal_Record_${data.studentName.replace(/[^a-zA-Z0-9]/g, '_')}`;

            // PDF options for new tab preview
            const options = {
                margin: [10, 15, 25, 15],
                filename: `${reportTitle}_${new Date().toISOString().slice(0,10)}.pdf`,
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

                }

                // Open PDF in new tab
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);
                window.open(pdfUrl, '_blank');

                notifications.showNotification('PDF opened in new tab', 'success');

                // Close the details modal if open
                document.getElementById('anecdotalDetailsModal').style.display = 'none';
            }).catch(error => {
                console.error('PDF generation error:', error);
                notifications.showNotification('PDF generation failed. Please try again.', 'error');
            });
        }

        // ==================== EDIT ANECDOTAL MODAL FUNCTIONALITY ====================
        document.addEventListener('DOMContentLoaded', () => {
            // Edit Anecdotal Modal
            const editAnecdotalModal = document.getElementById('editAnecdotalModal');
            const editAnecdotalForm = document.getElementById('editAnecdotalForm');
            const closeAnecdotalModal = document.getElementById('closeAnecdotalEditModal');
            const cancelAnecdotalBtn = document.getElementById('cancelAnecdotalEditBtn');

            function openAnecdotalModal(action, data) {
                editAnecdotalForm.action = action;
                document.getElementById('edit_anecdotal_record_id').value = data.id || '';
                document.getElementById('edit_solution').value = data.solution || '';
                document.getElementById('edit_recommendation').value = data.recommendation || '';
                document.getElementById('edit_anecdotal_date').value = data.date || '';
                document.getElementById('edit_anecdotal_time').value = convertTo24Hour(data.time || '');
                document.getElementById('edit_anecdotal_status').value = data.status || 'active';
                editAnecdotalModal.style.display = 'flex';
            }

            function convertTo24Hour(timeStr) {
                if (!timeStr || !timeStr.includes(' ')) return timeStr;

                const [time, mod] = timeStr.split(' ');
                let [h, m] = time.split(':');
                h = parseInt(h);
                if (mod === 'PM' && h !== 12) h += 12;
                if (mod === 'AM' && h === 12) h = 0;
                return `${h.toString().padStart(2, '0')}:${m}`;
            }

            // Edit Anecdotal Button
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('editAnecdotalBtn')) {
                    e.stopPropagation();
                    const row = e.target.closest('tr');
                    if (row) {
                        openAnecdotalModal(`/adviser/violation-anecdotals/update/${row.dataset.anecId}`, {
                            id: row.dataset.anecId,
                            solution: row.dataset.solution,
                            recommendation: row.dataset.recommendation,
                            date: row.dataset.date,
                            time: row.dataset.time,
                            status: row.dataset.status
                        });
                    }
                }
            });

            // Close modal events
            [closeAnecdotalModal, cancelAnecdotalBtn].forEach(btn => {
                if (btn) btn.addEventListener('click', () => editAnecdotalModal.style.display = 'none');
            });

            // Handle form submission
            if (editAnecdotalForm) {
                editAnecdotalForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await handleFormSubmission(this, 'Anecdotal');
                });
            }

            async function handleFormSubmission(form, type) {
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                try {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    submitBtn.disabled = true;

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        notifications.showNotification(`${type} updated successfully!`, 'success');
                        editAnecdotalModal.style.display = 'none';
                        location.reload();
                    } else {
                        if (result.errors) {
                            let messages = Object.values(result.errors).flat().join('\n');
                            notifications.showNotification('Validation failed:\n' + messages, 'error');
                        } else {
                            notifications.showNotification('Error: ' + (result.message || 'Unknown error'),
                                'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    notifications.showNotification(`Error updating ${type.toLowerCase()}.`, 'error');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        });

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = [
                'editAnecdotalModal',
                'anecdotalDetailsModal',
                'SR-modal',
                'violationAnecdotalsArchiveModal',
                'notificationModal',
                'confirmationModal'
            ];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Close anecdotal details modal
        document.getElementById('closeAnecdotalDetailsModal').addEventListener('click', function() {
            document.getElementById('anecdotalDetailsModal').style.display = 'none';
        });

        // Close SR modal
        document.getElementById('closeSRModal').addEventListener('click', function() {
            document.getElementById('SR-modal').style.display = 'none';
        });

        // 🔍 Search Functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const currentTable = document.getElementById('violationAnecdotalsTable');
            if (currentTable) {
                const rows = currentTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            }
        });

        // ==================== VIOLATION ANECDOTALS FUNCTIONALITY ====================

        // Show dropdown on hover
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownContent = document.querySelector('.dropdown-content');

            dropdown.addEventListener('mouseenter', function() {
                dropdownContent.style.display = 'block';
            });

            dropdown.addEventListener('mouseleave', function() {
                dropdownContent.style.display = 'none';
            });
        });
    </script>
@endsection
