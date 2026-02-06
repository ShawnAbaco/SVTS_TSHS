@extends('adviser.layout')

@section('content')
<div class="main-container">
<meta name="csrf-token" content="{{ csrf_token() }}">


  <!-- Notification Modal -->
  <div class="notification-modal" id="notificationModal">
    <div class="notification-content" id="notificationContent">
      <div class="notification-icon" id="notificationIcon"></div>
      <div class="notification-message" id="notificationMessage"></div>
      <div class="notification-actions" id="notificationActions">
        <button class="btn-confirm" id="notificationConfirm">OK</button>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="notification-modal" id="confirmationModal">
    <div class="notification-content">
      <div class="notification-icon" id="confirmationIcon">⚠️</div>
      <div class="notification-message" id="confirmationMessage"></div>
      <div class="notification-actions">
        <button class="btn-confirm" id="confirmAction">Confirm</button>
        <button class="btn-cancel" id="cancelAction">Cancel</button>
      </div>
    </div>
  </div>


  <!-- ✅ Toolbar -->
  <div class="toolbar">
    <h2>Violation Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <a href="{{ route('adviser.violations.create') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Violation
      </a>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

  <!-- ✅ Summary Cards -->
  <div class="summary">
    <div class="card">
      <h2>{{ $monthlyViolations }}</h2>
      <p>This Month</p>
    </div>
    <div class="card">
      <h2>{{ $weeklyViolations }}</h2>
      <p>This Week</p>
    </div>
    <div class="card">
      <h2>{{ $dailyViolations }}</h2>
      <p>Today</p>
    </div>
  </div>

  <!-- ✅ Bulk Action / Dropdown -->
  <div class="select-options">
    <div class="left-controls">
      <label for="selectAll" class="select-label">
        <input type="checkbox" id="selectAll">
        <span>Select All</span>
      </label>

      <div class="dropdown">
        <button class="btn-info dropdown-btn">⬇️ View Records</button>
        <div class="dropdown-content">
          <a href="#" id="violationRecords">Violation Records</a>
          <a href="#" id="violationAppointments">Violation Appointments</a>
          <a href="#" id="violationAnecdotals">Violation Anecdotals</a>
        </div>
      </div>
    </div>

    <div class="right-controls">
      <!-- Violation Records Buttons -->
      <div id="violationRecordsActions" class="action-buttons">
        <button class="btn-cleared" id="markAsClearedBtn">✅ Mark as Cleared</button>
        <button class="btn-schedule" id="setScheduleBtn">📅 Set Appointment</button>
        <button class="btn-anecdotal" id="createAnecdotalBtn">📝 Create Anecdotal</button>
        <button class="btn-danger" id="moveToTrashBtn">🗑️ Move to Trash</button>
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

    <!-- 📋 VIOLATION RECORDS TABLE -->
    <div id="violationRecordsTable" class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Student Name</th>
            <th>Incident</th>
            <th>Offense Type</th>
            <th>Sanction</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @forelse($violations as $violation)
            @if($violation->status === 'active')
            <tr
              data-violation-id="{{ $violation->violation_id }}"
              data-student-id="{{ $violation->student->student_id }}"
              data-student-name="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
              data-offense-id="{{ $violation->offense->offense_sanc_id }}"
              data-offense-type="{{ $violation->offense->offense_type }}"
              data-sanction="{{ $violation->offense->sanction_consequences }}"
              data-incident="{{ $violation->violation_incident }}"
              data-date="{{ $violation->violation_date }}"
              data-status="{{ $violation->status }}"
              data-time="{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}"
              class="clickable-row"
            >
              <td><input type="checkbox" class="rowCheckbox violationCheckbox" value="{{ $violation->violation_id }}"></td>
              <td>{{ $violation->violation_id }}</td>
              <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
              <td>{{ $violation->violation_incident }}</td>
              <td>{{ $violation->offense->offense_type }}</td>
              <td>{{ $violation->offense->sanction_consequences }}</td>
              <td>{{ $violation->violation_date }}</td>
              <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
              <td>
                <span class="status-badge status-active">
                  Active
                </span>
              </td>
              <td><button class="btn-primary editViolationBtn">✏️ Edit</button></td>
            </tr>
            @endif
          @empty
          <tr class="no-data-row">
            <td colspan="10" style="text-align:center;">No active violations found</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      <div class="pagination-wrapper">
        <div class="pagination-summary">
          @if($violations instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php
              $activeCount = $violations->where('status', 'active')->count();
            @endphp
            Showing {{ $activeCount > 0 ? '1' : '0' }} to {{ $activeCount }} of {{ $activeCount }} record(s)
          @endif
        </div>
        <div class="pagination-links">
          {{ $violations->links() }}
        </div>
      </div>
    </div>

    <!-- 📅 VIOLATION APPOINTMENTS TABLE -->
    <div id="violationAppointmentsTable" class="table-wrapper" style="display:none;">
      <table>
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Student Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($vappointments as $appt)
            @if($appt->violation_app_status === 'Pending' || $appt->violation_app_status === 'Scheduled')
            <tr
              data-app-id="{{ $appt->violation_app_id }}"
              data-status="{{ $appt->violation_app_status }}"
              data-date="{{ $appt->violation_app_date }}"
              data-time="{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}"
            >
              <td><input type="checkbox" class="rowCheckbox appointmentCheckbox" value="{{ $appt->violation_app_id }}"></td>
              <td>{{ $appt->violation_app_id }}</td>
              <td>
                {{ $appt->violation->student->student_fname ?? 'N/A' }}
                {{ $appt->violation->student->student_lname ?? '' }}
              </td>
              <td>{{ $appt->violation_app_date }}</td>
              <td>{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}</td>
              <td>
                <span class="status-badge {{ $appt->violation_app_status === 'Pending' ? 'status-pending' : 'status-scheduled' }}">
                  {{ $appt->violation_app_status }}
                </span>
              </td>
              <td><button class="btn-primary editAppointmentBtn">✏️ Edit</button></td>
            </tr>
            @endif
          @empty
          <tr><td colspan="7" style="text-align:center;">No active appointments found</td></tr>
          @endforelse
        </tbody>
      </table>
      <div class="pagination-wrapper">
        <div class="pagination-summary">
          @if($appointments instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php
              $activeCount = $appointments->whereIn('status', ['active', 'in_progress'])->count();
            @endphp
            Showing {{ $activeCount > 0 ? '1' : '0' }} to {{ $activeCount }} of {{ $activeCount }} record(s)
          @endif
        </div>
        <div class="pagination-links">
          {{ $appointments->links() }}
        </div>
      </div>
    </div>

    <!-- 📝 VIOLATION ANECDOTALS TABLE -->
    <div id="violationAnecdotalsTable" class="table-wrapper" style="display:none;">
      <table>
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Student Name</th>
            <th>Solution</th>
            <th>Recommendation</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($vanecdotals as $anec)
            @if($anec->status === 'active' || $anec->status === 'in_progress')
            <tr
              data-anec-id="{{ $anec->violation_anec_id }}"
              data-solution="{{ $anec->violation_anec_solution }}"
              data-recommendation="{{ $anec->violation_anec_recommendation }}"
              data-date="{{ $anec->violation_anec_date }}"
              data-time="{{ \Carbon\Carbon::parse($anec->violation_anec_time)->format('h:i A') }}"
              data-status="{{ $anec->status }}"
            >
              <td><input type="checkbox" class="rowCheckbox anecdotalCheckbox" value="{{ $anec->violation_anec_id }}"></td>
              <td>{{ $anec->violation_anec_id }}</td>
              <td>
                {{ $anec->violation->student->student_fname ?? 'N/A' }}
                {{ $anec->violation->student->student_lname ?? '' }}
              </td>
              <td>{{ $anec->violation_anec_solution }}</td>
              <td>{{ $anec->violation_anec_recommendation }}</td>
              <td>{{ $anec->violation_anec_date }}</td>
              <td>{{ \Carbon\Carbon::parse($anec->violation_anec_time)->format('h:i A') }}</td>
              <td>
                <span class="status-badge {{ $anec->status === 'active' ? 'status-active' : 'status-in-progress' }}">
                  {{ ucfirst($anec->status) }}
                </span>
              </td>
              <td><button class="btn-primary editAnecdotalBtn">✏️ Edit</button></td>
            </tr>
            @endif
          @empty
          <tr><td colspan="9" style="text-align:center;">No active anecdotal records found</td></tr>
          @endforelse
        </tbody>
      </table>

      <div class="pagination-wrapper">
        <div class="pagination-summary">
          @if($anecdotals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php
              $activeCount = $anecdotals->whereIn('status', ['active', 'in_progress'])->count();
            @endphp
            Showing {{ $activeCount > 0 ? '1' : '0' }} to {{ $activeCount }} of {{ $activeCount }} record(s)
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

  <!-- 👁️ Violation Details Modal -->
  <div class="modal" id="violationDetailsModal">
    <div class="modal-content">
      <button class="close-btn" id="closeViolationDetailsModal">✖</button>
      <h2>Violation Details</h2>
      <div class="violation-details-container">
        <div class="detail-section">
          <h3>Student Information</h3>
          <div class="detail-grid">
            <div class="detail-item">
              <label>Student ID:</label>
              <span id="detail-student-id">-</span>
            </div>
            <div class="detail-item">
              <label>Student Name:</label>
              <span id="detail-student-name">-</span>
            </div>
          </div>
        </div>
        <div class="detail-section">
          <h3>Violation Information</h3>
          <div class="detail-grid">
            <div class="detail-item">
              <label>Violation ID:</label>
              <span id="detail-violation-id">-</span>
            </div>
            <div class="detail-item">
              <label>Incident:</label>
              <span id="detail-incident">-</span>
            </div>
            <div class="detail-item">
              <label>Offense Type:</label>
              <span id="detail-offense-type">-</span>
            </div>
            <div class="detail-item">
              <label>Sanction:</label>
              <span id="detail-sanction">-</span>
            </div>
            <div class="detail-item">
              <label>Date:</label>
              <span id="detail-date">-</span>
            </div>
            <div class="detail-item">
              <label>Time:</label>
              <span id="detail-time">-</span>
            </div>
            <div class="detail-item">
              <label>Status:</label>
              <span id="detail-status" class="status-badge">-</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-sms" id="sendSmsBtn">📱 SEND SMS</button>
        <button class="btn-primary" id="viewAppointmentsBtn">📅 VIEW APPOINTMENTS</button>
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
              <label>Solution:</label>
              <span id="detail-solution">-</span>
            </div>
            <div class="detail-item">
              <label>Recommendation:</label>
              <span id="detail-recommendation">-</span>
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
        <button class="btn-primary" id="viewRelatedViolationBtn">📋 VIEW RELATED VIOLATION</button>
      </div>
    </div>
  </div>

 <!-- ✏️ Edit Violation Modal -->
<div class="modal" id="editViolationModal">
    <div class="modal-content">
      <button class="close-btn" id="closeViolationEditModal">✖</button>
      <h2>Edit Violation Record</h2>
      <form id="editViolationForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="record_id" id="edit_violation_record_id">
        <!-- Add these hidden fields that your controller requires -->
        <input type="hidden" name="violator_id" id="edit_violator_id">
        <input type="hidden" name="offense_sanc_id" id="edit_offense_sanc_id">

        <div class="form-grid">
          <div class="form-group full-width">
            <label>Incident Details</label>
            <textarea id="edit_violation_incident" name="violation_incident" required></textarea>
          </div>
          <div class="form-group">
            <label>Violation Date</label>
            <input type="date" id="edit_violation_date" name="violation_date" required>
          </div>
          <div class="form-group">
            <label>Violation Time</label>
            <input type="time" id="edit_violation_time" name="violation_time" required>
          </div>
          <div class="form-group">
            <label>Offense Type</label>
            <select id="edit_offense_type" name="offense_type" required>
              <option value="">Select Offense Type</option>
              @foreach($offenses as $offense)
                <option value="{{ $offense->offense_sanc_id }}" data-sanction="{{ $offense->sanction_consequences }}">
                  {{ $offense->offense_type }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Sanction (Auto-filled)</label>
            <input type="text" id="edit_sanction" readonly style="background-color: #f8f9fa;">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="edit_violation_status" name="status" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="cleared">Cleared</option>
            </select>
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn-primary">💾 Save Changes</button>
          <button type="button" class="btn-secondary" id="cancelViolationEditBtn">❌ Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ✏️ Edit Appointment Modal -->
  <div class="modal" id="editAppointmentModal">
    <div class="modal-content">
      <button class="close-btn" id="closeAppointmentEditModal">✖</button>
      <h2>Edit Appointment</h2>
      <form id="editAppointmentForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="record_id" id="edit_appointment_record_id">
        <div class="form-grid">
          <div class="form-group">
            <label>Appointment Date</label>
            <input type="date" id="edit_appointment_date" name="appointment_date" required>
          </div>
          <div class="form-group">
            <label>Appointment Time</label>
            <input type="time" id="edit_appointment_time" name="appointment_time" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="edit_appointment_status" name="appointment_status" required>
              <option value="Pending">Pending</option>
              <option value="Scheduled">Scheduled</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn-primary">💾 Save Changes</button>
          <button type="button" class="btn-secondary" id="cancelAppointmentEditBtn">❌ Cancel</button>
        </div>
      </form>
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

  <!-- 📅 Set Schedule Modal -->
  <div class="modal" id="setScheduleModal">
    <div class="modal-content">
        <button class="close-btn" id="closeScheduleModal">✖</button>
        <h2>Set Schedule for Selected Violations</h2>
        <form id="setScheduleForm" method="POST" action="{{ route('adviser.storeMultipleAppointments') }}">
            @csrf
            <div class="selected-violations">
                <h3>Selected Violations:</h3>
                <div id="selectedViolationsList" class="selected-list"></div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="schedule_date">Appointment Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="schedule_time">Appointment Time</label>
                    <input type="time" id="schedule_time" name="schedule_time" required>
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="btn-primary">📅 Create Appointments</button>
                <button type="button" class="btn-secondary" id="cancelScheduleBtn">❌ Cancel</button>
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
                    <input type="date" id="anecdotal_date" name="anecdotal_date" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="anecdotal_time">Anecdotal Time</label>
                    <input type="time" id="anecdotal_time" name="anecdotal_time" required value="{{ date('H:i') }}">
                </div>
                <div class="form-group full-width">
                    <label for="violation_anec_solution">Solution</label>
                    <textarea id="violation_anec_solution" name="violation_anec_solution" placeholder="Describe the solution implemented..." required rows="4"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="violation_anec_recommendation">Recommendation</label>
                    <textarea id="violation_anec_recommendation" name="violation_anec_recommendation" placeholder="Provide recommendations for future prevention..." required rows="4"></textarea>
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
                <button class="btn-print" id="printAnecdotalBtn">🖨️ Print Records</button>
                <button class="btn-primary" id="closeSuccessModal">OK</button>
            </div>
        </div>
    </div>
  </div>

  <!-- 🗃️ VIOLATION RECORDS ARCHIVE MODAL -->
  <div class="modal" id="violationRecordsArchiveModal">
    <div class="modal-content">
      <div class="modal-header">🗃️ Archived Violation Records</div>
      <div class="modal-body">
        <div class="modal-actions">
          <label class="select-all-label">
            <input type="checkbox" id="selectAllViolationRecordsArchived">
            <span>Select All</span>
          </label>
          <div class="filter-container">
            <select id="violationRecordsStatusFilter" class="filter-select">
              <option value="all">All Status</option>
              <option value="cleared">Cleared</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="search-container">
            <input type="search" id="violationRecordsArchiveSearch" placeholder="🔍 Search archived violation records..." class="search-input">
          </div>
        </div>

        <div class="archive-table-container">
          <div id="archiveViolationRecordsTable" class="archive-table-wrapper">
            <table class="archive-table">
              <thead>
                <tr>
                  <th>✔</th>
                  <th>ID</th>
                  <th>Student Name</th>
                  <th>Incident</th>
                  <th>Offense Type</th>
                  <th>Sanction</th>
                  <th>Status</th>
                  <th>Date Archived</th>
                </tr>
              </thead>
              <tbody id="archiveViolationRecordsBody">
                <!-- Archived violation records will be loaded here via AJAX -->
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" id="restoreViolationRecordsBtn">🔄 Restore</button>
          <button class="btn-danger" id="deleteViolationRecordsBtn">🗑️ Delete</button>
          <button class="btn-close" id="closeViolationRecordsArchive">❌ Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 🗃️ VIOLATION APPOINTMENTS ARCHIVE MODAL -->
  <div class="modal" id="violationAppointmentsArchiveModal">
    <div class="modal-content">
      <div class="modal-header">🗃️ Archived Violation Appointments</div>
      <div class="modal-body">
        <div class="modal-actions">
          <label class="select-all-label">
            <input type="checkbox" id="selectAllViolationAppointmentsArchived">
            <span>Select All</span>
          </label>
          <div class="filter-container">
            <select id="violationAppointmentsStatusFilter" class="filter-select">
              <option value="all">All Status</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div class="search-container">
            <input type="search" id="violationAppointmentsArchiveSearch" placeholder="🔍 Search archived appointments..." class="search-input">
          </div>
        </div>

        <div class="archive-table-container">
          <div id="archiveViolationAppointmentsTable" class="archive-table-wrapper">
            <table class="archive-table">
              <thead>
                <tr>
                  <th>✔</th>
                  <th>ID</th>
                  <th>Student Name</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Status</th>
                  <th>Date Archived</th>
                </tr>
              </thead>
              <tbody id="archiveViolationAppointmentsBody">
                <!-- Archived violation appointments will be loaded here via AJAX -->
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" id="restoreViolationAppointmentsBtn">🔄 Restore</button>
          <button class="btn-danger" id="deleteViolationAppointmentsBtn">🗑️ Delete</button>
          <button class="btn-close" id="closeViolationAppointmentsArchive">❌ Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 🗃️ VIOLATION ANECDOTALS ARCHIVE MODAL -->
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
            <input type="search" id="violationAnecdotalsArchiveSearch" placeholder="🔍 Search archived anecdotals..." class="search-input">
          </div>
        </div>

        <div class="archive-table-container">
          <div id="archiveViolationAnecdotalsTable" class="archive-table-wrapper">
            <table class="archive-table">
              <thead>
                <tr>
                  <th>✔</th>
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

</div>

<style>
.clickable-row {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.clickable-row:hover {
  background-color: #f5f5f5;
}

.violation-details-container, .anecdotal-details-container {
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
  background-color: rgba(0,0,0,0.5);
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

.table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
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

.pagination-links a, .pagination-links span {
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

.summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  text-align: center;
}

.card h2 {
  margin: 0;
  font-size: 2em;
  color: #007bff;
}

.card p {
  margin: 5px 0 0 0;
  color: #666;
}

.select-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 15px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.left-controls, .right-controls {
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
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 4px;
  z-index: 1;
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

.archive-table th, .archive-table td {
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

.filter-container, .search-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-select, .search-input {
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
            this.notificationActions.innerHTML = '<button class="btn-confirm" id="notificationConfirm">OK</button>';

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

// Current active table type
let currentTableType = 'violationRecords';

// ==================== EDIT MODAL FUNCTIONALITY ====================
document.addEventListener('DOMContentLoaded', () => {
    // Edit Violation Modal
    const editViolationModal = document.getElementById('editViolationModal');
    const editViolationForm = document.getElementById('editViolationForm');
    const closeViolationModal = document.getElementById('closeViolationEditModal');
    const cancelViolationBtn = document.getElementById('cancelViolationEditBtn');

    // Edit Appointment Modal
    const editAppointmentModal = document.getElementById('editAppointmentModal');
    const editAppointmentForm = document.getElementById('editAppointmentForm');
    const closeAppointmentModal = document.getElementById('closeAppointmentEditModal');
    const cancelAppointmentBtn = document.getElementById('cancelAppointmentEditBtn');

    // Edit Anecdotal Modal
    const editAnecdotalModal = document.getElementById('editAnecdotalModal');
    const editAnecdotalForm = document.getElementById('editAnecdotalForm');
    const closeAnecdotalModal = document.getElementById('closeAnecdotalEditModal');
    const cancelAnecdotalBtn = document.getElementById('cancelAnecdotalEditBtn');

    // ✅ FIXED: Updated openViolationModal function
    function openViolationModal(action, data) {
        editViolationForm.action = action;
        document.getElementById('edit_violation_record_id').value = data.id || '';

        // Set the required fields for your controller
        document.getElementById('edit_violator_id').value = data.studentId || '';
        document.getElementById('edit_offense_sanc_id').value = data.offenseId || '';

        // Set the form fields
        document.getElementById('edit_violation_incident').value = data.incident || '';
        document.getElementById('edit_violation_date').value = data.date || '';
        document.getElementById('edit_violation_time').value = convertTo24Hour(data.time || '');
        document.getElementById('edit_offense_type').value = data.offenseId || '';
        document.getElementById('edit_violation_status').value = data.status || 'active';

        // Auto-populate sanction based on selected offense
        const selectedOption = document.querySelector(`#edit_offense_type option[value="${data.offenseId}"]`);
        if (selectedOption) {
            document.getElementById('edit_sanction').value = selectedOption.dataset.sanction || '';
        }

        editViolationModal.style.display = 'flex';
    }

    function openAppointmentModal(action, data) {
        editAppointmentForm.action = action;
        document.getElementById('edit_appointment_record_id').value = data.id || '';
        document.getElementById('edit_appointment_date').value = data.date || '';
        document.getElementById('edit_appointment_time').value = convertTo24Hour(data.time || '');
        document.getElementById('edit_appointment_status').value = data.status || 'Pending';
        editAppointmentModal.style.display = 'flex';
    }

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

    // ✅ FIXED: Use event delegation for all edit buttons
    document.addEventListener('click', function(e) {
        // Edit Violation Button
        if (e.target.classList.contains('editViolationBtn')) {
            e.stopPropagation();
            const row = e.target.closest('tr');
            if (row) {
                openViolationModal(`/adviser/violations/update/${row.dataset.violationId}`, {
                    id: row.dataset.violationId,
                    studentId: row.dataset.studentId, // Add this for violator_id
                    incident: row.dataset.incident,
                    date: row.dataset.date,
                    time: row.dataset.time,
                    offenseId: row.dataset.offenseId,
                    status: row.dataset.status
                });
            }
        }

        // Edit Appointment Button
        else if (e.target.classList.contains('editAppointmentBtn')) {
            e.stopPropagation();
            const row = e.target.closest('tr');
            if (row) {
                openAppointmentModal(`/adviser/violation-appointments/update/${row.dataset.appId}`, {
                    id: row.dataset.appId,
                    date: row.dataset.date,
                    time: row.dataset.time,
                    status: row.dataset.status
                });
            }
        }

        // Edit Anecdotal Button
        else if (e.target.classList.contains('editAnecdotalBtn')) {
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

    // ✅ FIXED: Add event listener for offense type change to auto-update sanction and offense_sanc_id
    const offenseTypeSelect = document.getElementById('edit_offense_type');
    if (offenseTypeSelect) {
        offenseTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('edit_sanction').value = selectedOption.dataset.sanction || '';
            document.getElementById('edit_offense_sanc_id').value = this.value;
        });
    }

    // Close modal events
    [closeViolationModal, cancelViolationBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => editViolationModal.style.display = 'none');
    });

    [closeAppointmentModal, cancelAppointmentBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => editAppointmentModal.style.display = 'none');
    });

    [closeAnecdotalModal, cancelAnecdotalBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => editAnecdotalModal.style.display = 'none');
    });

    // Handle form submissions
    if (editViolationForm) {
        editViolationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'Violation');
        });
    }

    if (editAppointmentForm) {
        editAppointmentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'Appointment');
        });
    }

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
                // Close the appropriate modal
                if (type === 'Violation') editViolationModal.style.display = 'none';
                if (type === 'Appointment') editAppointmentModal.style.display = 'none';
                if (type === 'Anecdotal') editAnecdotalModal.style.display = 'none';

                // Reload to show updated data
                location.reload();
            } else {
                if (result.errors) {
                    let messages = Object.values(result.errors).flat().join('\n');
                    notifications.showNotification('Validation failed:\n' + messages, 'error');
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
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

    // Table switching functionality
    const sections = {
        violationRecords: document.getElementById('violationRecordsTable'),
        violationAppointments: document.getElementById('violationAppointmentsTable'),
        violationAnecdotals: document.getElementById('violationAnecdotalsTable')
    };

    const actionButtons = {
        violationRecords: document.getElementById('violationRecordsActions'),
        violationAppointments: document.getElementById('violationAppointmentsActions'),
        violationAnecdotals: document.getElementById('violationAnecdotalsActions')
    };

    // Setup table view links
    const violationRecordsLink = document.getElementById('violationRecords');
    const violationAppointmentsLink = document.getElementById('violationAppointments');
    const violationAnecdotalsLink = document.getElementById('violationAnecdotals');

    if (violationRecordsLink) {
        violationRecordsLink.addEventListener('click', function(e) {
            e.preventDefault();
            switchTableView('violationRecords');
        });
    }

    if (violationAppointmentsLink) {
        violationAppointmentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            switchTableView('violationAppointments');
        });
    }

    if (violationAnecdotalsLink) {
        violationAnecdotalsLink.addEventListener('click', function(e) {
            e.preventDefault();
            switchTableView('violationAnecdotals');
        });
    }

    function switchTableView(tableType) {
        // Hide all tables and action buttons
        Object.values(sections).forEach(sec => {
            if (sec) sec.style.display = 'none';
        });
        Object.values(actionButtons).forEach(btn => {
            if (btn) btn.style.display = 'none';
        });

        // Show selected table and action buttons
        if (sections[tableType]) {
            sections[tableType].style.display = 'block';
        }
        if (actionButtons[tableType]) {
            actionButtons[tableType].style.display = 'flex';
        }

        // Update current table type
        currentTableType = tableType;

        // Reset select all checkbox
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.checked = false;
        }
    }
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = [
        'editViolationModal',
        'editAppointmentModal',
        'editAnecdotalModal',
        'violationDetailsModal',
        'anecdotalDetailsModal',
        'violationRecordsArchiveModal',
        'violationAppointmentsArchiveModal',
        'violationAnecdotalsArchiveModal',
        'setScheduleModal',
        'createAnecdotalModal',
        'anecdotalSuccessModal',
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

// 🔍 Search Functionality for Main Tables
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const currentTable = document.querySelector('.table-wrapper:not([style*="display: none"])');
    if (currentTable) {
        const rows = currentTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
});

// ✅ Select All - Main Table
document.getElementById('selectAll').addEventListener('change', function() {
    const currentTable = document.querySelector('.table-wrapper:not([style*="display: none"])');
    if (currentTable) {
        const checkboxes = currentTable.querySelectorAll('.rowCheckbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
    }
});

// ==================== SET SCHEDULE FUNCTIONALITY ====================
document.getElementById('setScheduleBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.violationCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one violation to schedule.', 'warning');
        return;
    }

    const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
        const row = cb.closest('tr');
        return {
            violation_id: row.dataset.violationId,
            student_name: row.dataset.studentName,
            incident: row.dataset.incident
        };
    });

    const selectedList = document.getElementById('selectedViolationsList');
    selectedList.innerHTML = '';

    selectedViolations.forEach(violation => {
        const item = document.createElement('div');
        item.className = 'selected-violation-item';
        item.innerHTML = `
            <strong>${violation.student_name}</strong> - ${violation.incident}
            <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
        `;
        selectedList.appendChild(item);
    });

    document.getElementById('setScheduleModal').style.display = 'flex';
});

// Close Set Schedule Modal
document.getElementById('closeScheduleModal').addEventListener('click', function() {
    document.getElementById('setScheduleModal').style.display = 'none';
});

document.getElementById('cancelScheduleBtn').addEventListener('click', function() {
    document.getElementById('setScheduleModal').style.display = 'none';
});

// Handle schedule form submission
document.getElementById('setScheduleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scheduling...';
        submitBtn.disabled = true;

        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            notifications.showNotification('Appointments scheduled successfully!', 'success');
            document.getElementById('setScheduleModal').style.display = 'none';
            location.reload();
        } else {
            notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        notifications.showNotification('Error scheduling appointments.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// ==================== CREATE ANECDOTAL FUNCTIONALITY ====================
document.getElementById('createAnecdotalBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.violationCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one violation to create anecdotal record.', 'warning');
        return;
    }

    const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
        const row = cb.closest('tr');
        return {
            violation_id: row.dataset.violationId,
            student_name: row.dataset.studentName,
            incident: row.dataset.incident,
            offense_type: row.dataset.offenseType,
            sanction: row.dataset.sanction
        };
    });

    const selectedList = document.getElementById('selectedViolationsForAnecdotal');
    selectedList.innerHTML = '';

    selectedViolations.forEach(violation => {
        const item = document.createElement('div');
        item.className = 'selected-violation-item';
        item.innerHTML = `
            <strong>${violation.student_name}</strong><br>
            <small>Incident: ${violation.incident}</small><br>
            <small>Offense: ${violation.offense_type}</small>
            <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
        `;
        selectedList.appendChild(item);
    });

    document.getElementById('createAnecdotalModal').style.display = 'flex';
});

// Close Create Anecdotal Modal
document.getElementById('closeAnecdotalModal').addEventListener('click', function() {
    document.getElementById('createAnecdotalModal').style.display = 'none';
});

document.getElementById('cancelAnecdotalBtn').addEventListener('click', function() {
    document.getElementById('createAnecdotalModal').style.display = 'none';
});

// Handle anecdotal form submission
document.getElementById('createAnecdotalForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;

        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('createAnecdotalModal').style.display = 'none';
            document.getElementById('successMessage').textContent = result.message;
            document.getElementById('anecdotalSuccessModal').style.display = 'flex';
            window.lastCreatedAnecdotals = result.data;
        } else {
            if (result.errors) {
                let messages = Object.values(result.errors).flat().join('\n');
                notifications.showNotification('Validation failed:\n' + messages, 'error');
            } else {
                notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        notifications.showNotification('Error creating anecdotal records.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Print Anecdotal Records
 document.getElementById('printAnecdotalBtn').addEventListener('click', function() {
     if (!window.lastCreatedAnecdotals || window.lastCreatedAnecdotals.length === 0) {
         notifications.showNotification('No anecdotal records to print.', 'warning');
         return;
     }

     const printWindow = window.open('', '_blank');
    const printContent = generateAnecdotalPrintContent(window.lastCreatedAnecdotals);

     printWindow.document.write(`
        <!DOCTYPE html>
        <html>
         <head>
             <title>Anecdotal Records</title>
             <style>
                 body { font-family: Arial, sans-serif; margin: 20px; }
                 .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                 .anecdotal-record { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; page-break-inside: avoid; }
                 .record-header { background: #f5f5f5; padding: 10px; margin: -15px -15px 15px -15px; border-bottom: 1px solid #ddd; }
                 .field { margin-bottom: 10px; }
                 .field label { font-weight: bold; display: inline-block; width: 150px; }
                 @media print {
                     .no-print { display: none; }
                     .anecdotal-record { page-break-inside: avoid; }
                 }
            </style>
         </head>
         <body>
            ${printContent}
             <div class="no-print" style="margin-top: 20px; text-align: center;">
                 <button onclick="window.print()">Print</button>
                 <button onclick="window.close()">Close</button>
             </div>
         </body>
         </html>
     `);

     printWindow.document.close();
 });

// Close Success Modal
document.getElementById('closeSuccessModal').addEventListener('click', function() {
    document.getElementById('anecdotalSuccessModal').style.display = 'none';
    location.reload();
});

// Function to generate print content
function generateAnecdotalPrintContent(anecdotals) {
    let content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Violation Anecdotal Record</title>
            <style>
                body {
                    font-family: 'Times New Roman', serif;
                    margin: 0;
                    padding: 0;
                    line-height: 1.4;
                    color: #000;
                }
                .container {
                    max-width: 8.5in;
                    margin: 0 auto;
                    padding: 0.5in;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 15px;
                }
                .header h1 {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 5px 0;
                    text-transform: uppercase;
                }
                .header p {
                    font-size: 12px;
                    margin: 2px 0;
                }
                .title {
                    text-align: center;
                    font-size: 18px;
                    font-weight: bold;
                    margin: 25px 0;
                    text-decoration: underline;
                }
                .section {
                    margin-bottom: 25px;
                }
                .section-title {
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 8px;
                    border-bottom: 1px solid #000;
                    padding-bottom: 3px;
                }
                .incident-content {
                    min-height: 100px;
                    border: 1px solid #000;
                    padding: 10px;
                    margin-bottom: 15px;
                    font-size: 12px;
                    line-height: 1.6;
                }
                .signature-section {
                    margin-top: 60px;
                }
                .signature-line {
                    border-top: 1px solid #000;
                    margin-top: 40px;
                    padding-top: 5px;
                    font-size: 12px;
                }
                .signature-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 40px;
                    margin-top: 20px;
                }
                .signature-item {
                    text-align: center;
                }
                .date-section {
                    margin-top: 30px;
                    font-size: 12px;
                }
                .no-print {
                    display: none;
                }
                @media print {
                    body { margin: 0; padding: 0; }
                    .container { padding: 0.5in; }
                    .no-print { display: none !important; }
                    .page-break { page-break-before: always; }
                }
                @page {
                    margin: 0.5in;
                    size: letter;
                }
            </style>
        </head>
        <body>
    `;

    anecdotals.forEach((anecdotal, index) => {
        const studentName = anecdotal.student_name || 'Unknown Student';
        const incident = anecdotal.incident || 'No incident details available.';
        const prefectName = 'Prefect of Discipline';

        content += `
            <div class="container">
                <div class="header">
                    <h1>Republic of the Philippines</h1>
                    <h1>Department of Education</h1>
                    <p>RECORD'S DIVISION OF MIRANDA ORIGINAL</p>
                    <h1>TABORQAM SENIOR HIGH SCHOOL</h1>
                    <h1>ANECDOTAL RECORD</h1>
                    <p>Prefect of Discipline</p>
                    <div class="date-section">
                        Date: ${new Date(anecdotal.violation_anec_date).toLocaleDateString()}
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">STUDENT INFORMATION:</div>
                    <div class="incident-content">
                        <strong>Student Name:</strong> ${studentName}<br>
                        <strong>Incident:</strong> ${incident}
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">SOLUTION IMPLEMENTED:</div>
                    <div class="incident-content">
                        ${anecdotal.violation_anec_solution}
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">RECOMMENDATION:</div>
                    <div class="incident-content">
                        ${anecdotal.violation_anec_recommendation}
                    </div>
                </div>

                <div class="signature-section">
                    <div class="signature-grid">
                        <div class="signature-item">
                            <div class="signature-line"></div>
                            <div>Student's name and signature</div>
                            <div style="margin-top: 5px; font-weight: bold;">${studentName}</div>
                        </div>
                        <div class="signature-item">
                            <div class="signature-line"></div>
                            <div>Parent/Guardian's signature</div>
                            <div style="margin-top: 5px; font-weight: bold;">Parent/Guardian</div>
                        </div>
                        <div class="signature-item">
                            <div class="signature-line"></div>
                            <div>Prefect of Discipline In-charge</div>
                            <div style="margin-top: 5px; font-weight: bold;">${prefectName}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add page break for multiple records
        if (index < anecdotals.length - 1) {
            content += `<div class="page-break"></div>`;
        }
    });

    content += `
            <div class="no-print" style="position: fixed; top: 20px; right: 20px; background: white; padding: 10px; border: 1px solid #000;">
                <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Print</button>
                <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Close</button>
            </div>
        </body>
        </html>
    `;

    return content;
}

// 👁️ Violation Details Modal Functionality
const violationDetailsModal = document.getElementById('violationDetailsModal');
const closeViolationDetailsModal = document.getElementById('closeViolationDetailsModal');
const sendSmsBtn = document.getElementById('sendSmsBtn');
const viewAppointmentsBtn = document.getElementById('viewAppointmentsBtn');

// Function to open violation details modal
function openViolationDetailsModal(violationData) {
    document.getElementById('detail-student-id').textContent = violationData.studentId || '-';
    document.getElementById('detail-student-name').textContent = violationData.studentName || '-';
    document.getElementById('detail-violation-id').textContent = violationData.violationId || '-';
    document.getElementById('detail-incident').textContent = violationData.incident || '-';
    document.getElementById('detail-offense-type').textContent = violationData.offenseType || '-';
    document.getElementById('detail-sanction').textContent = violationData.sanction || '-';
    document.getElementById('detail-date').textContent = violationData.date || '-';
    document.getElementById('detail-time').textContent = violationData.time || '-';

    const statusElement = document.getElementById('detail-status');
    statusElement.textContent = violationData.status || '-';
    statusElement.className = 'status-badge ' + (violationData.status === 'active' ? 'status-active' : 'status-inactive');

    violationDetailsModal.style.display = 'flex';
}

// 👁️ Anecdotal Details Modal Functionality
const anecdotalDetailsModal = document.getElementById('anecdotalDetailsModal');
const closeAnecdotalDetailsModal = document.getElementById('closeAnecdotalDetailsModal');
const viewRelatedViolationBtn = document.getElementById('viewRelatedViolationBtn');

// Function to open anecdotal details modal
function openAnecdotalDetailsModal(anecdotalData) {
    document.getElementById('detail-anecdotal-student-name').textContent = anecdotalData.studentName || '-';
    document.getElementById('detail-anecdotal-id').textContent = anecdotalData.anecdotalId || '-';
    document.getElementById('detail-solution').textContent = anecdotalData.solution || '-';
    document.getElementById('detail-recommendation').textContent = anecdotalData.recommendation || '-';
    document.getElementById('detail-anecdotal-date').textContent = anecdotalData.date || '-';
    document.getElementById('detail-anecdotal-time').textContent = anecdotalData.time || '-';

    const statusElement = document.getElementById('detail-anecdotal-status');
    statusElement.textContent = anecdotalData.status ? anecdotalData.status.charAt(0).toUpperCase() + anecdotalData.status.slice(1) : '-';
    statusElement.className = 'status-badge ' +
        (anecdotalData.status === 'active' ? 'status-active' :
         anecdotalData.status === 'in_progress' ? 'status-in-progress' :
         anecdotalData.status === 'completed' ? 'status-completed' : 'status-closed');

    anecdotalDetailsModal.style.display = 'flex';
}

// Add click event listeners to violation record rows
document.addEventListener('DOMContentLoaded', function() {
    const violationRows = document.querySelectorAll('#violationRecordsTable tbody tr.clickable-row');

    violationRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' || e.target.classList.contains('editViolationBtn')) {
                return;
            }

            const violationData = {
                studentId: this.dataset.studentId,
                studentName: this.dataset.studentName,
                violationId: this.dataset.violationId,
                incident: this.dataset.incident,
                offenseType: this.dataset.offenseType,
                sanction: this.dataset.sanction,
                date: this.dataset.date,
                time: this.dataset.time,
                status: this.dataset.status
            };

            openViolationDetailsModal(violationData);
        });
    });
});

// Add click event listeners to anecdotal rows
document.addEventListener('DOMContentLoaded', function() {
    const anecdotalRows = document.querySelectorAll('#violationAnecdotalsTable tbody tr');

    anecdotalRows.forEach(row => {
        if (!row.classList.contains('no-data-row')) {
            row.addEventListener('click', function(e) {
                if (e.target.type === 'checkbox' || e.target.classList.contains('editAnecdotalBtn')) {
                    return;
                }

                const anecdotalData = {
                    studentName: this.cells[2].textContent.trim(),
                    anecdotalId: this.dataset.anecId,
                    solution: this.dataset.solution,
                    recommendation: this.dataset.recommendation,
                    date: this.dataset.date,
                    time: this.dataset.time,
                    status: this.dataset.status
                };

                openAnecdotalDetailsModal(anecdotalData);
            });
        }
    });
});

// Close violation details modal
closeViolationDetailsModal.addEventListener('click', function() {
    violationDetailsModal.style.display = 'none';
});

// Close anecdotal details modal
closeAnecdotalDetailsModal.addEventListener('click', function() {
    anecdotalDetailsModal.style.display = 'none';
});

// Send SMS button functionality
sendSmsBtn.addEventListener('click', function() {
    const studentName = document.getElementById('detail-student-name').textContent;
    const violationId = document.getElementById('detail-violation-id').textContent;
    notifications.showNotification(`SMS would be sent for violation ${violationId} - ${studentName}`, 'info');
});

// View Appointments button functionality
viewAppointmentsBtn.addEventListener('click', function() {
    const violationId = document.getElementById('detail-violation-id').textContent;
    const studentName = document.getElementById('detail-student-name').textContent;
    notifications.showNotification(`Viewing appointments for violation ${violationId} - ${studentName}`, 'info');
});

// View Related Violation button functionality
viewRelatedViolationBtn.addEventListener('click', function() {
    const anecdotalId = document.getElementById('detail-anecdotal-id').textContent;
    const studentName = document.getElementById('detail-anecdotal-student-name').textContent;
    notifications.showNotification(`Viewing related violation for anecdotal ${anecdotalId} - ${studentName}`, 'info');
});

// ==================== VIOLATION RECORDS FUNCTIONALITY ====================
// 🗑️ Move to Trash (Archive as Inactive)
document.getElementById('moveToTrashBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.violationCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one violation.', 'warning');
        return;
    }

    const violationIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to move ${violationIds.length} violation(s) to archive as Inactive?`,
        async function() {
            try {
                const response = await fetch('/adviser/violations/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        violation_ids: violationIds,
                        status: 'inactive'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${violationIds.length} violation(s) moved to archive as Inactive.`, 'success');
                    violationIds.forEach(id => {
                        const row = document.querySelector(`tr[data-violation-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error moving violations to archive.', 'error');
            }
        }
    );
});

// ✅ Mark as Cleared (Archive as Cleared)
document.getElementById('markAsClearedBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.violationCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one violation.', 'warning');
        return;
    }

    const violationIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to mark ${violationIds.length} violation(s) as Cleared?`,
        async function() {
            try {
                const response = await fetch('/adviser/violations/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        violation_ids: violationIds,
                        status: 'cleared'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${violationIds.length} violation(s) marked as Cleared and moved to archive.`, 'success');
                    violationIds.forEach(id => {
                        const row = document.querySelector(`tr[data-violation-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error marking violations as cleared.', 'error');
            }
        }
    );
});

// ==================== VIOLATION APPOINTMENTS FUNCTIONALITY ====================
// ✅ Mark Appointment as Completed
document.getElementById('markAppointmentCompletedBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.appointmentCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one appointment.', 'warning');
        return;
    }

    const appointmentIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to mark ${appointmentIds.length} appointment(s) as Completed?`,
        async function() {
            try {
                const response = await fetch('/adviser/violation-appointments/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        appointment_ids: appointmentIds,
                        status: 'Completed'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${appointmentIds.length} appointment(s) marked as Completed and moved to archive.`, 'success');
                    appointmentIds.forEach(id => {
                        const row = document.querySelector(`tr[data-app-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error marking appointments as completed.', 'error');
            }
        }
    );
});

// 🗑️ Move Appointment to Trash (Archive as Cancelled)
document.getElementById('moveAppointmentToTrashBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.appointmentCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one appointment.', 'warning');
        return;
    }

    const appointmentIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to move ${appointmentIds.length} appointment(s) to archive as Cancelled?`,
        async function() {
            try {
                const response = await fetch('/adviser/violation-appointments/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        appointment_ids: appointmentIds,
                        status: 'Cancelled'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${appointmentIds.length} appointment(s) moved to archive as Cancelled.`, 'success');
                    appointmentIds.forEach(id => {
                        const row = document.querySelector(`tr[data-app-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error moving appointments to archive.', 'error');
            }
        }
    );
});

// ==================== VIOLATION ANECDOTALS FUNCTIONALITY ====================
// ✅ Mark Anecdotal as Completed
document.getElementById('markAnecdotalCompletedBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.anecdotalCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one anecdotal record.', 'warning');
        return;
    }

    const anecdotalIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to mark ${anecdotalIds.length} anecdotal record(s) as Completed?`,
        async function() {
            try {
                const response = await fetch('/adviser/violation-anecdotals/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        anecdotal_ids: anecdotalIds,
                        status: 'completed'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${anecdotalIds.length} anecdotal record(s) marked as Completed and moved to archive.`, 'success');
                    anecdotalIds.forEach(id => {
                        const row = document.querySelector(`tr[data-anec-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error marking anecdotal records as completed.', 'error');
            }
        }
    );
});

// 🗑️ Move Anecdotal to Trash (Archive as Closed)
document.getElementById('moveAnecdotalToTrashBtn').addEventListener('click', async function() {
    const selectedCheckboxes = document.querySelectorAll('.anecdotalCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one anecdotal record.', 'warning');
        return;
    }

    const anecdotalIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    notifications.showConfirmation(
        `Are you sure you want to move ${anecdotalIds.length} anecdotal record(s) to archive as Closed?`,
        async function() {
            try {
                const response = await fetch('/adviser/violation-anecdotals/archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        anecdotal_ids: anecdotalIds,
                        status: 'closed'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${anecdotalIds.length} anecdotal record(s) moved to archive as Closed.`, 'success');
                    anecdotalIds.forEach(id => {
                        const row = document.querySelector(`tr[data-anec-id="${id}"]`);
                        if (row) row.remove();
                    });

                    document.getElementById('selectAll').checked = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error moving anecdotal records to archive.', 'error');
            }
        }
    );
});

// ==================== ARCHIVE MODAL FUNCTIONALITY ====================

// 🗃️ Archive Button - Opens appropriate archive modal based on current table
document.getElementById('archiveBtn').addEventListener('click', async function() {
    try {
        console.log('Loading archived data for:', currentTableType);

        if (currentTableType === 'violationRecords') {
            const violationResponse = await fetch('/adviser/violations/archived');
            console.log('Violation response status:', violationResponse.status);
            const archivedViolations = await violationResponse.json();
            console.log('Archived violations:', archivedViolations);

            populateArchiveTable('archiveViolationRecordsBody', archivedViolations, 'violation');
            document.getElementById('violationRecordsArchiveModal').style.display = 'flex';

        } else if (currentTableType === 'violationAppointments') {
            const appointmentResponse = await fetch('/adviser/violation-appointments/archived');
            console.log('Appointment response status:', appointmentResponse.status);
            const archivedAppointments = await appointmentResponse.json();
            console.log('Archived appointments:', archivedAppointments);

            populateArchiveTable('archiveViolationAppointmentsBody', archivedAppointments, 'appointment');
            document.getElementById('violationAppointmentsArchiveModal').style.display = 'flex';
        } else if (currentTableType === 'violationAnecdotals') {
            const anecdotalResponse = await fetch('/adviser/violation-anecdotals/archived');
            console.log('Anecdotal response status:', anecdotalResponse.status);
            const archivedAnecdotals = await anecdotalResponse.json();
            console.log('Archived anecdotals:', archivedAnecdotals);

            populateArchiveTable('archiveViolationAnecdotalsBody', archivedAnecdotals, 'anecdotal');
            document.getElementById('violationAnecdotalsArchiveModal').style.display = 'flex';
        }
    } catch (error) {
        console.error('Error loading archived data:', error);
        notifications.showNotification('Error loading archived data. Check console for details.', 'error');
    }
});

// Function to populate archive tables
function populateArchiveTable(tableBodyId, data, type) {
    const tableBody = document.getElementById(tableBodyId);
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center;">⚠️ No archived ${type} records found</td></tr>`;
        return;
    }

    data.forEach(item => {
        const row = document.createElement('tr');

        if (type === 'violation') {
            row.setAttribute('data-record-id', item.violation_id);
            row.setAttribute('data-record-type', 'violation');
            row.innerHTML = `
                <td><input type="checkbox" class="archiveCheckbox" value="${item.violation_id}" data-type="violation"></td>
                <td>${item.violation_id}</td>
                <td>${item.student_fname} ${item.student_lname}</td>
                <td>${item.violation_incident}</td>
                <td>${item.offense_type}</td>
                <td>${item.sanction_consequences || 'N/A'}</td>
                <td><span class="status-badge ${item.status === 'cleared' ? 'status-cleared' : 'status-inactive'}">${item.status}</span></td>
                <td>${new Date(item.updated_at).toLocaleDateString()}</td>
            `;
        } else if (type === 'appointment') {
            row.setAttribute('data-record-id', item.violation_app_id);
            row.setAttribute('data-record-type', 'appointment');
            row.innerHTML = `
                <td><input type="checkbox" class="archiveCheckbox" value="${item.violation_app_id}" data-type="appointment"></td>
                <td>${item.violation_app_id}</td>
                <td>${item.student_fname} ${item.student_lname}</td>
                <td>${item.violation_app_date}</td>
                <td>${item.violation_app_time}</td>
                <td><span class="status-badge ${item.violation_app_status === 'Completed' ? 'status-cleared' : 'status-inactive'}">${item.violation_app_status}</span></td>
                <td>${new Date(item.updated_at).toLocaleDateString()}</td>
            `;
        } else if (type === 'anecdotal') {
            row.setAttribute('data-record-id', item.violation_anec_id);
            row.setAttribute('data-record-type', 'anecdotal');
            row.innerHTML = `
                <td><input type="checkbox" class="archiveCheckbox" value="${item.violation_anec_id}" data-type="anecdotal"></td>
                <td>${item.violation_anec_id}</td>
                <td>${item.student_fname} ${item.student_lname}</td>
                <td>${item.violation_anec_solution}</td>
                <td>${item.violation_anec_recommendation}</td>
                <td><span class="status-badge ${item.status === 'completed' ? 'status-completed' : 'status-closed'}">${item.status}</span></td>
                <td>${new Date(item.updated_at).toLocaleDateString()}</td>
            `;
        }

        tableBody.appendChild(row);
    });
}

// Archive Search for each modal
document.getElementById('violationRecordsArchiveSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

document.getElementById('violationAppointmentsArchiveSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('archiveViolationAppointmentsBody');
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

document.getElementById('violationAnecdotalsArchiveSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('archiveViolationAnecdotalsBody');
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Archive Status Filters
document.getElementById('violationRecordsStatusFilter').addEventListener('change', function() {
    const filter = this.value;
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const rows = tableBody.querySelectorAll('tr');

    if (filter !== 'all') {
        rows.forEach(row => {
            const status = row.querySelector('.status-badge').innerText.toLowerCase();
            row.style.display = status === filter.toLowerCase() ? '' : 'none';
        });
    } else {
        rows.forEach(row => row.style.display = '');
    }
});

document.getElementById('violationAppointmentsStatusFilter').addEventListener('change', function() {
    const filter = this.value;
    const tableBody = document.getElementById('archiveViolationAppointmentsBody');
    const rows = tableBody.querySelectorAll('tr');

    if (filter !== 'all') {
        rows.forEach(row => {
            const status = row.querySelector('.status-badge').innerText.toLowerCase();
            row.style.display = status === filter.toLowerCase() ? '' : 'none';
        });
    } else {
        rows.forEach(row => row.style.display = '');
    }
});

document.getElementById('violationAnecdotalsStatusFilter').addEventListener('change', function() {
    const filter = this.value;
    const tableBody = document.getElementById('archiveViolationAnecdotalsBody');
    const rows = tableBody.querySelectorAll('tr');

    if (filter !== 'all') {
        rows.forEach(row => {
            const status = row.querySelector('.status-badge').innerText.toLowerCase();
            row.style.display = status === filter.toLowerCase() ? '' : 'none';
        });
    } else {
        rows.forEach(row => row.style.display = '');
    }
});

// Select All for each archive modal
document.getElementById('selectAllViolationRecordsArchived').addEventListener('change', function() {
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const checkboxes = tableBody.querySelectorAll('.archiveCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

document.getElementById('selectAllViolationAppointmentsArchived').addEventListener('change', function() {
    const tableBody = document.getElementById('archiveViolationAppointmentsBody');
    const checkboxes = tableBody.querySelectorAll('.archiveCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

document.getElementById('selectAllViolationAnecdotalsArchived').addEventListener('change', function() {
    const tableBody = document.getElementById('archiveViolationAnecdotalsBody');
    const checkboxes = tableBody.querySelectorAll('.archiveCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

// 🔄 Restore Archived Records for each type
document.getElementById('restoreViolationRecordsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to restore.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        `Are you sure you want to restore ${records.length} record(s)?`,
        async function() {
            try {
                const response = await fetch('/adviser/violations/restore-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) restored successfully.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });
                    location.reload();
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error restoring records.', 'error');
            }
        }
    );
});

document.getElementById('restoreViolationAppointmentsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationAppointmentsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to restore.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        `Are you sure you want to restore ${records.length} record(s)?`,
        async function() {
            try {
                const response = await fetch('/adviser/violations/restore-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) restored successfully.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });
                    location.reload();
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error restoring records.', 'error');
            }
        }
    );
});

document.getElementById('restoreViolationAnecdotalsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationAnecdotalsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to restore.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        `Are you sure you want to restore ${records.length} record(s)?`,
        async function() {
            try {
                const response = await fetch('/adviser/violations/restore-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) restored successfully.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });
                    location.reload();
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error restoring records.', 'error');
            }
        }
    );
});

// 🗑️ Delete Archived Records Permanently for each type
document.getElementById('deleteViolationRecordsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to delete permanently.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        'WARNING: This will permanently delete these records. This action cannot be undone!',
        async function() {
            try {
                const response = await fetch('/adviser/violations/destroy-multiple-archived', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) deleted permanently.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });

                    const remainingRows = tableBody.querySelectorAll('tr');
                    if (remainingRows.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">⚠️ No archived records found</td></tr>';
                    }
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error deleting records.', 'error');
            }
        }
    );
});

document.getElementById('deleteViolationAppointmentsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationAppointmentsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to delete permanently.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        'WARNING: This will permanently delete these records. This action cannot be undone!',
        async function() {
            try {
                const response = await fetch('/adviser/violations/destroy-multiple-archived', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) deleted permanently.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });

                    const remainingRows = tableBody.querySelectorAll('tr');
                    if (remainingRows.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">⚠️ No archived records found</td></tr>';
                    }
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error deleting records.', 'error');
            }
        }
    );
});

document.getElementById('deleteViolationAnecdotalsBtn').addEventListener('click', async function() {
    const tableBody = document.getElementById('archiveViolationAnecdotalsBody');
    const selectedCheckboxes = tableBody.querySelectorAll('.archiveCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one record to delete permanently.', 'warning');
        return;
    }

    const records = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        type: cb.dataset.type
    }));

    notifications.showConfirmation(
        'WARNING: This will permanently delete these records. This action cannot be undone!',
        async function() {
            try {
                const response = await fetch('/adviser/violations/destroy-multiple-archived', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ records: records })
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(`${records.length} record(s) deleted permanently.`, 'success');
                    records.forEach(record => {
                        const row = document.querySelector(`tr[data-record-id="${record.id}"][data-record-type="${record.type}"]`);
                        if (row) row.remove();
                    });

                    const remainingRows = tableBody.querySelectorAll('tr');
                    if (remainingRows.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">⚠️ No archived records found</td></tr>';
                    }
                } else {
                    notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('Error deleting records.', 'error');
            }
        }
    );
});

// Close Archive Modals
document.getElementById('closeViolationRecordsArchive').addEventListener('click', function() {
    document.getElementById('violationRecordsArchiveModal').style.display = 'none';
});

document.getElementById('closeViolationAppointmentsArchive').addEventListener('click', function() {
    document.getElementById('violationAppointmentsArchiveModal').style.display = 'none';
});

document.getElementById('closeViolationAnecdotalsArchive').addEventListener('click', function() {
    document.getElementById('violationAnecdotalsArchiveModal').style.display = 'none';
});

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
