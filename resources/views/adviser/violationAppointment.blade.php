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
  <button class="btn-info dropdown-btn">⬇️ Violation Appointments</button>
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
<!-- 📅 VIOLATION APPOINTMENTS TABLE -->
<div id="violationAppointmentsTable" class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Student Name</th>
        <th>Offense</th>
        <th>Appointment Date</th>
        <th>Appointment Time</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
     @forelse($vappointments as $appt)
    <tr
        data-app-id="{{ $appt->violation_app_id }}"
        data-status="{{ $appt->violation_app_status }}"
        data-date="{{ $appt->violation_app_date }}"
        data-time="{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}"
        data-notes="{{ $appt->violation_app_notes }}"
        data-incident="{{ $appt->violation->violation_incident ?? 'No incident details' }}"
        class="clickable-row"
    >
        <td>{{ $appt->violation_app_id }}</td>
        <td>
            {{ $appt->violation->student->student_fname ?? 'N/A' }}
            {{ $appt->violation->student->student_lname ?? '' }}
        </td>
        <td>
            {{ $appt->violation->offense->offense_type ?? 'N/A' }}
        </td>
        <td>{{ $appt->violation_app_date }}</td>
        <td>{{ \Carbon\Carbon::parse($appt->violation_app_time)->format('h:i A') }}</td>
        <td>
            <span class="status-badge
                @if($appt->violation_app_status === 'Scheduled') status-scheduled
                @elseif($appt->violation_app_status === 'Completed') status-cleared
                @elseif($appt->violation_app_status === 'Pending') status-pending
                @elseif($appt->violation_app_status === 'Cancelled') status-inactive
                @endif">
                {{ $appt->violation_app_status }}
            </span>
        </td>
        <td>
            <button class="btn-primary editAppointmentBtn">✏️ Edit</button>
        </td>
    </tr>
@empty
<tr><td colspan="7" style="text-align:center;">No appointments found</td></tr>
@endforelse
    </tbody>
  </table>
      <div class="pagination-wrapper">
  <div class="pagination-summary">
    @if($vappointments->count() > 0)
      Showing 1 to {{ $vappointments->count() }} of {{ $vappointments->count() }} record(s)
    @else
      Showing 0 to 0 of 0 record(s)
    @endif
  </div>
  <div class="pagination-links">
    {{ $vappointments->links() }}
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

  <!-- 👁️ Info Modal -->
  <div class="modal" id="inNoModalInfo">
    <div class="modal-content">
      <button class="close-btn" id="closeInfoModal">✖</button>
      <h2>Appointment Information</h2>
      <div class="info-details-container">
        <div class="detail-section">
          <h3>Incident Details</h3>
          <div class="detail-item">
            <p id="info-incident" class="info-text"></p>
          </div>
        </div>
        <div class="detail-section">
          <h3>Appointment Notes</h3>
          <div class="detail-item">
            <p id="info-notes" class="info-text"></p>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" id="closeInfoBtn">Close</button>
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
                <div class="form-group full-width">
                    <label>Appointment Notes</label>
                    <textarea id="edit_appointment_notes" name="violation_app_notes" rows="4" placeholder="Enter appointment notes..."></textarea>
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

</div>

<style>
.clickable-row {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.clickable-row:hover {
  background-color: #f5f5f5;
}

.violation-details-container, .anecdotal-details-container, .info-details-container {
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

.info-text {
  color: #333;
  padding: 15px;
  background-color: white;
  border-radius: 4px;
  border: 1px solid #ddd;
  white-space: pre-wrap;
  word-wrap: break-word;
  line-height: 1.5;
  max-height: 300px;
  overflow-y: auto;
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

// ==================== INFO MODAL FUNCTIONALITY ====================
document.addEventListener('DOMContentLoaded', () => {
    const infoModal = document.getElementById('inNoModalInfo');
    const closeInfoModal = document.getElementById('closeInfoModal');
    const closeInfoBtn = document.getElementById('closeInfoBtn');
    const infoIncident = document.getElementById('info-incident');
    const infoNotes = document.getElementById('info-notes');

    // Open info modal when clicking anywhere on the row (except edit button)
    document.addEventListener('click', function(e) {
        const row = e.target.closest('tr.clickable-row');
        if (row && !e.target.closest('.editAppointmentBtn')) {
            // Get incident and notes from data attributes
            const incident = row.dataset.incident || 'No incident details available';
            const notes = row.dataset.notes || 'No notes available';
            
            // Set the content in the modal
            infoIncident.textContent = incident;
            infoNotes.textContent = notes;
            
            // Show the modal
            infoModal.style.display = 'flex';
        }
    });

    // Close modal events
    [closeInfoModal, closeInfoBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => {
            infoModal.style.display = 'none';
        });
    });

    // Close modal when clicking outside
    infoModal.addEventListener('click', (e) => {
        if (e.target === infoModal) {
            infoModal.style.display = 'none';
        }
    });
});

// ==================== UPDATED EDIT APPOINTMENT MODAL ====================
document.addEventListener('DOMContentLoaded', () => {
    // Edit Appointment Modal - Updated to include notes
    const editAppointmentModal = document.getElementById('editAppointmentModal');
    const editAppointmentForm = document.getElementById('editAppointmentForm');
    const closeAppointmentModal = document.getElementById('closeAppointmentEditModal');
    const cancelAppointmentBtn = document.getElementById('cancelAppointmentEditBtn');

    function openAppointmentModal(action, data) {
        editAppointmentForm.action = action;
        document.getElementById('edit_appointment_record_id').value = data.id || '';
        document.getElementById('edit_appointment_date').value = data.date || '';
        document.getElementById('edit_appointment_time').value = convertTo24Hour(data.time || '');
        document.getElementById('edit_appointment_status').value = data.status || 'Scheduled';
        document.getElementById('edit_appointment_notes').value = data.notes || '';

        editAppointmentModal.style.display = 'flex';
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

    // Edit Appointment Button - Updated to include notes
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('editAppointmentBtn')) {
            e.stopPropagation();
            const row = e.target.closest('tr');
            if (row) {
                openAppointmentModal(`/adviser/violation-appointments/update/${row.dataset.appId}`, {
                    id: row.dataset.appId,
                    date: row.dataset.date,
                    time: row.dataset.time,
                    status: row.dataset.status,
                    notes: row.dataset.notes || ''
                });
            }
        }
    });

    // Close modal events
    [closeAppointmentModal, cancelAppointmentBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => editAppointmentModal.style.display = 'none');
    });

    // Handle form submission
    if (editAppointmentForm) {
        editAppointmentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'Appointment');
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
                editAppointmentModal.style.display = 'none';
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
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = [
        'editAppointmentModal',
        'inNoModalInfo',
        'violationAppointmentsArchiveModal',
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

// 🔍 Search Functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const currentTable = document.getElementById('violationAppointmentsTable');
    if (currentTable) {
        const rows = currentTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
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