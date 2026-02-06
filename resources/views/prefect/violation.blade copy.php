@extends('prefect.layout')

@section('content')
<div class="main-container">
<meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- ✅ Toolbar -->
  <div class="toolbar">
    <h2>Violation Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <a href="{{ route('violations.create') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Violation
      </a>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

<!-- Bulk Action / Dropdown -->
<div class="select-options">
  <div class="dropdown">
    <button class="btn-info dropdown-btn">⬇️ Violation Records</button>
    <div class="dropdown-content">
      <a href="{{ route('prefect.violation') }}"
         class="route-link {{ Request::is('prefect/violations*') ? 'active' : '' }}"
         data-table="violationRecords">Violation Records</a>

      <a href="{{ route('prefect.violationAnecdotal') }}"
         class="route-link {{ Request::is('prefect/violation-appointments*') ? 'active' : '' }}"
         data-table="violationAppointments">Violation Anecdotals</a>

      <a href="{{ route('prefect.violationAppointment') }}"
         class="route-link {{ Request::is('prefect/violation-anecdotals*') ? 'active' : '' }}"
         data-table="violationAnecdotals">Violation Appointments</a>
    </div>
  </div>

  <!-- Group/Individual Select - Moved to right side -->
  <div class="left-controls">
    <select class="selection-dropdown" id="viewType">
      <option value="individual" {{ $viewType == 'individual' ? 'selected' : '' }}>By Individual</option>
      <option value="group" {{ $viewType == 'group' ? 'selected' : '' }}>By Group</option>
    </select>
  </div>

  <div class="right-controls">
    <!-- Violation Records Buttons -->
    <div id="violationRecordsActions" class="action-buttons">
      <button class="btn-schedule" id="setScheduleBtn">📅 Set Appointment</button>
      <button class="btn-anecdotal" id="createAnecdotalBtn">📝 Create Anecdotal</button>
      <button class="bbttn-danger" id="moveToTrashBtn">🗑️ Move to Trash</button>
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
            <th>
              <label class="select-label">
                <input type="checkbox" id="selectAll">
              </label>
            </th>
            @if($viewType == 'group')
              <th>Students</th>
            @else
              <th>Student Name</th>
            @endif
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
          @if($viewType == 'group')
    <!-- GROUP VIEW - DISPLAY ALL STATUSES -->
    @forelse($byGroupViolations as $group)
      <tr
          data-group-key="{{ $group->group_key }}"
          data-incident="{{ $group->violation_incident }}"
          data-offense-type="{{ $group->offense_type }}"
          data-sanction="{{ $group->sanction_consequences }}"
          data-date="{{ $group->violation_date }}"
          data-time="{{ \Carbon\Carbon::parse($group->violation_time)->format('h:i A') }}"
          data-status="{{ $group->status ?? 'pending' }}"
          class="clickable-row group-row"
      >
          <td>
            <input type="checkbox" class="rowCheckbox groupCheckbox" value="{{ $group->group_key }}" data-type="group">
          </td>
          <td>
              <div class="student-list">
                  @foreach($group->students as $student)
                      <span class="student-name">{{ $student->student_fname }} {{ $student->student_lname }}</span>
                      @if(!$loop->last), @endif
                  @endforeach
              </div>
          </td>
          <td>{{ $group->violation_incident }}</td>
          <td>{{ $group->offense_type }}</td>
          <td>{{ $group->sanction_consequences }}</td>
          <td>{{ \Carbon\Carbon::parse($group->violation_date)->format('F j, Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($group->violation_time)->format('h:i A') }}</td>
          <td>
              <span class="status-badge status-{{ $group->status ?? 'pending' }}">
                  {{ ucfirst($group->status ?? 'pending') }}
              </span>
          </td>
          <td>
              <button class="btn-primary editGroupBtn" data-group-key="{{ $group->group_key }}">✏️ Edit</button>
          </td>
      </tr>
    @empty
      <tr class="no-data-row">
          <td colspan="9" style="text-align:center;">No violations found</td>
      </tr>
    @endforelse
@else
    <!-- INDIVIDUAL VIEW - DISPLAY ALL STATUSES -->
    @forelse($violations as $violation)
        @php
            // Check if this is a merged violation
            $isMerged = isset($violation->merged_count) && $violation->merged_count > 1;
            $offenseType = $isMerged ? $violation->merged_offense_types : $violation->offense->offense_type;
            $sanction = $isMerged ? $violation->merged_sanctions : $violation->sanction->sanction_consequences;
        @endphp

        <tr
            data-violation-id="{{ $violation->violation_id }}"
            data-violation-ids="{{ $isMerged ? json_encode($violation->merged_violation_ids) : json_encode([$violation->violation_id]) }}"
            data-student-id="{{ $violation->student->student_id }}"
            data-student-name="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
            data-offense-type="{{ $offenseType }}"
            data-sanction="{{ $sanction }}"
            data-incident="{{ $violation->violation_incident }}"
            data-date="{{ $violation->violation_date }}"
            data-status="{{ $violation->status }}"
            data-time="{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}"
            data-is-merged="{{ $isMerged ? 'true' : 'false' }}"
            class="clickable-row individual-row"
        >
            <td>
                <input type="checkbox" class="rowCheckbox violationCheckbox"
                       value="{{ $violation->violation_id }}"
                       data-violation-ids="{{ $isMerged ? json_encode($violation->merged_violation_ids) : json_encode([$violation->violation_id]) }}"
                       data-type="individual"
                       data-is-merged="{{ $isMerged ? 'true' : 'false' }}">
            </td>
            <td>
                {{ $violation->student->student_fname }} {{ $violation->student->student_lname }}
                @if($isMerged)
                    <span class="badge badge-info" style="font-size: 0.7em; margin-left: 5px;">
                        ({{ $violation->merged_count }} violations)
                    </span>
                @endif
            </td>
            <td>{{ $violation->violation_incident }}</td>
            <td>{{ $offenseType }}</td>
            <td>{{ $sanction }}</td>
            <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F j, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
            <td>
                <span class="status-badge status-{{ $violation->status }}">
                    {{ ucfirst($violation->status) }}
                </span>
            </td>
            <td>
                @if($isMerged)
                    <button class="btn-primary editViolationBtn" data-is-merged="true">✏️ Edit All</button>
                @else
                    <button class="btn-primary editViolationBtn" data-is-merged="false">✏️ Edit</button>
                @endif
            </td>
        </tr>
    @empty
        <tr class="no-data-row">
            <td colspan="9" style="text-align:center;">No violations found</td>
        </tr>
    @endforelse
@endif
        </tbody>
      </table>

      <div class="pagination-wrapper">
        <div class="pagination-summary">
          @if($viewType == 'individual' && $violations instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php
              // Count all violations for display (not just pending)
              $totalCount = 0;
              $statusCounts = [
                  'pending' => 0,
                  'active' => 0,
                  'cleared' => 0,
                  'inactive' => 0
              ];

              foreach ($violations as $violation) {
                  $totalCount++;
                  $status = $violation->status ?? 'pending';
                  if (isset($statusCounts[$status])) {
                      $statusCounts[$status]++;
                  }
              }

              $currentPage = $violations->currentPage();
              $perPage = $violations->perPage();
              $total = $violations->total();

              $from = (($currentPage - 1) * $perPage) + 1;
              $to = min($currentPage * $perPage, $total);
            @endphp
            Showing {{ $from }} to {{ $to }} of {{ $total }} record(s)
            <br><small>
                Status:
                @foreach($statusCounts as $status => $count)
                    @if($count > 0)
                        <span class="status-badge status-{{ $status }}" style="font-size: 0.7em; margin: 0 2px;">
                            {{ ucfirst($status) }}: {{ $count }}
                        </span>
                    @endif
                @endforeach
            </small>
          @elseif($viewType == 'group')
            Showing {{ count($byGroupViolations) }} group(s)
          @endif
        </div>
        <div class="pagination-links">
          @if($viewType == 'individual')
            {{ $violations->links() }}
          @endif
        </div>
      </div>
    </div>
  </div>
</div>




<script>
document.addEventListener('DOMContentLoaded', function() {
    // View type change handler
    document.getElementById('viewType').addEventListener('change', function() {
        const viewType = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('view', viewType);
        window.location.href = url.toString();
    });

    // Group view button handler
    document.querySelectorAll('.viewGroupBtn').forEach(button => {
        button.addEventListener('click', function() {
            const groupKey = this.getAttribute('data-group-key');
            // Show individual violations for this group
            const url = new URL(window.location.href);
            url.searchParams.set('view', 'individual');
            url.searchParams.set('group', groupKey);
            window.location.href = url.toString();
        });
    });
});
</script>

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

<!-- ✏️ Edit Group Violation Modal -->
<div class="modal" id="editGroupModal">
    <div class="modal-content" style="max-width: 800px;">
        <button class="close-btn" id="closeGroupEditModal">✖</button>
        <h2>Edit Group Violation</h2>

        <form id="editGroupForm" method="POST" action="{{ route('prefect.violations.group.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="group_key" id="edit_group_key">

            <div class="form-section">
                <h3>Students in this Group</h3>
                <div id="groupStudentsList" class="students-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
                    <!-- Students will be loaded here via AJAX -->
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Incident Details</label>
                    <textarea id="edit_group_incident" name="violation_incident" required></textarea>
                </div>

                <div class="form-group">
                    <label>Violation Date</label>
                    <input type="date" id="edit_group_date" name="violation_date" required>
                </div>

                <div class="form-group">
                    <label>Violation Time</label>
                    <input type="time" id="edit_group_time" name="violation_time" required>
                </div>

                <div class="form-group">
                    <label>Offense Type</label>
                    <select id="edit_group_offense_type" name="offense_type" required>
                        <option value="">Select Offense Type</option>
                        @foreach($offenses as $offense)
                            <option value="{{ $offense->offense_sanc_id }}" data-sanction="{{ $offense->sanction_consequences }}">
                                {{ $offense->offense_type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
    <label>Sanction</label>
    <input type="text" id="edit_group_sanction" name="sanction" required style="background-color: white;">
</div>

                <div class="form-group">
                    <label>Status</label>
                    <select id="edit_group_status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="cleared">Cleared</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary">💾 Update Group Violation</button>
                <button type="button" class="btn-secondary" id="cancelGroupEditBtn">❌ Cancel</button>
            </div>
        </form>
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
        <form id="setScheduleForm" method="POST" action="{{ route('prefect.storeMultipleAppointments') }}">
            @csrf
            <div class="selected-violations">
                <h3>Selected Violations:</h3>
                <div id="selectedViolationsList" class="selected-list"></div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="schedule_date">Appointment Date *</label>
                    <input type="date" id="schedule_date" name="schedule_date" required min="{{ date('Y-m-d') }}">
                    <small class="form-text">Select a future date</small>
                </div>
                <div class="form-group">
                    <label for="schedule_time">Appointment Time *</label>
                    <input type="time" id="schedule_time" name="schedule_time" required>
                    <small class="form-text">Select appointment time</small>
                </div>
                <div class="form-group full-width">
                    <label for="violation_app_notes">Additional Notes (Optional)</label>
                    <textarea id="violation_app_notes" name="violation_app_notes" placeholder="Enter any additional notes or instructions..." rows="3"></textarea>
                </div>
            </div>

            <div class="form-requirements">
                <h4>Requirements:</h4>
                <ul>
                    <li>✅ Date must be today or in the future</li>
                    <li>✅ Time must be within school hours (7:00 AM - 5:00 PM)</li>
                    <li>✅ All selected violations will receive the same appointment</li>
                </ul>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary" id="submitScheduleBtn">
                    <span class="btn-text">📅 Create Appointments</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Creating...
                    </span>
                </button>
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
        <form id="createAnecdotalForm" method="POST" action="{{ route('prefect.storeMultipleAnecdotals') }}">
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
            <h2>✅ Anecdotal Records Processed Successfully</h2>
        </div>
        <div class="modal-body">
            <p id="successMessage"></p>
            <div id="processDetails" style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <!-- Details will be populated by JavaScript -->
            </div>
            <div class="success-actions">
                <button class="btn-print" id="printAnecdotalBtn">🖨️ Export PDF</button>
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
                  <th>
                    <label class="select-label">
                      <input type="checkbox" id="selectAllViolationRecordsArchived">
                      <span>Select All</span>
                    </label>
                  </th>
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
                  <th>
                    <label class="select-label">
                      <input type="checkbox" id="selectAllViolationAppointmentsArchived">
                      <span>Select All</span>
                    </label>
                  </th>
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

</div>

<style>
    /* Group Edit Modal Styles */
.students-container {
    background: #f8f9fa;
    border-radius: 6px;
}

.student-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-bottom: 1px solid #e9ecef;
}

.student-item:last-child {
    border-bottom: none;
}

.student-info {
    flex: 1;
}

.student-name {
    font-weight: 500;
    color: #333;
}

.student-id {
    font-size: 0.85em;
    color: #666;
    margin-left: 10px;
}

.form-section {
    margin-bottom: 20px;
}

.form-section h3 {
    margin-bottom: 10px;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
}

/* Make the modal content scrollable for many students */
.modal-content {
    max-height: 90vh;
    overflow-y: auto;
}
/* Add status badge styles for all status types */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-cleared {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-scheduled {
        background-color: #cce7ff;
        color: #004085;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
.selected-violation-item {
    padding: 10px;
    margin-bottom: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    font-size: 0.9em;
}

.selected-violation-item .group-badge {
    background: #17a2b8;
    color: white;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 0.7em;
    margin-left: 5px;
}

.form-requirements {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 6px;
    padding: 15px;
    margin: 15px 0;
}

.form-requirements h4 {
    margin: 0 0 10px 0;
    color: #0066cc;
    font-size: 0.9em;
}

.form-requirements ul {
    margin: 0;
    padding-left: 20px;
    font-size: 0.85em;
    color: #555;
}

.form-requirements li {
    margin-bottom: 5px;
}

.form-text {
    display: block;
    margin-top: 4px;
    font-size: 0.8em;
    color: #6c757d;
}

.btn-loading {
    display: none;
}

/* Responsive design for modal */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 5% auto;
        padding: 20px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}
.student-list {
    min-width: 250px;
}
.student-name {
    display: inline-block;
    margin-right: 5px;
}
.group-row {
    background-color: #f8f9fa;
}
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

/* Make checkboxes bigger */
.table-container input[type="checkbox"] {
  transform: scale(1.3);
  margin: 0;
  cursor: pointer;
}

/* Select All in table header */
.table-container thead .select-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: bold;
  cursor: pointer;
  margin: 0;
  font-size: 14px;
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

.left-controls {
  display: flex;
  gap: 15px;
  margin-right: 430px;

}
.right-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.select-label {
  display: flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
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

function convertTo24Hour(timeStr) {
        if (!timeStr || !timeStr.includes(' ')) return timeStr;

        const [time, mod] = timeStr.split(' ');
        let [h, m] = time.split(':');
        h = parseInt(h);
        if (mod === 'PM' && h !== 12) h += 12;
        if (mod === 'AM' && h === 12) h = 0;
        return `${h.toString().padStart(2, '0')}:${m}`;
    }

// ==================== SELECT ALL FUNCTIONALITY ====================
// Select All for Violation Records
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.violationCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

// ==================== EDIT VIOLATION MODAL FUNCTIONALITY ====================
document.addEventListener('DOMContentLoaded', () => {
    // Edit Violation Modal
    const editViolationModal = document.getElementById('editViolationModal');
    const editViolationForm = document.getElementById('editViolationForm');
    const closeViolationModal = document.getElementById('closeViolationEditModal');
    const cancelViolationBtn = document.getElementById('cancelViolationEditBtn');

    function openViolationModal(action, data, isGroup = false) {
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

        // If it's a group, show a message or handle differently
        if (isGroup) {
            notifications.showNotification('Editing group violation - this will affect all students in this group', 'info');
        }

        editViolationModal.style.display = 'flex';
    }



    // Edit Violation Button for Individual Rows
    document.addEventListener('click', function(e) {
        // Individual row edit button
        if (e.target.classList.contains('editViolationBtn')) {
            e.stopPropagation();
            const row = e.target.closest('tr');
            if (row && row.classList.contains('individual-row')) {
                openViolationModal(`/prefect/violations/update/${row.dataset.violationId}`, {
                    id: row.dataset.violationId,
                    studentId: row.dataset.studentId,
                    incident: row.dataset.incident,
                    date: row.dataset.date,
                    time: row.dataset.time,
                    offenseId: row.dataset.offenseId,
                    status: row.dataset.status
                }, false);
            }
        }

        // Group row edit button
        if (e.target.classList.contains('editGroupBtn')) {
            e.stopPropagation();
            const row = e.target.closest('tr');
            if (row && row.classList.contains('group-row')) {
                const groupKey = row.dataset.groupKey;
                openGroupEditModal(groupKey, {
                    incident: row.dataset.incident,
                    offenseType: row.dataset.offenseType,
                    sanction: row.dataset.sanction,
                    date: row.dataset.date,
                    time: row.dataset.time,
                    status: row.dataset.status
                });
            }
        }
    });

    // Handle group row clicks for editing
    const groupRows = document.querySelectorAll('#violationRecordsTable tbody tr.group-row');
    groupRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on checkbox or edit button
            if (e.target.type === 'checkbox' || e.target.classList.contains('editGroupBtn')) {
                return;
            }

            const groupKey = this.dataset.groupKey;
            openGroupEditModal(groupKey, {
                incident: this.dataset.incident,
                offenseType: this.dataset.offenseType,
                sanction: this.dataset.sanction,
                date: this.dataset.date,
                time: this.dataset.time,
                status: this.dataset.status
            });
        });
    });

    // Add event listener for offense type change to auto-update sanction and offense_sanc_id
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

    // Handle form submission
    if (editViolationForm) {
        editViolationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'Violation');
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
                editViolationModal.style.display = 'none';
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
        'editViolationModal',
        'violationDetailsModal',
        'violationRecordsArchiveModal',
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

// 🔍 Search Functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const currentTable = document.getElementById('violationRecordsTable');
    if (currentTable) {
        const rows = currentTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
});

// ==================== SET SCHEDULE FUNCTIONALITY ====================
document.getElementById('setScheduleBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.violationCheckbox:checked, .groupCheckbox:checked');

    if (!selectedCheckboxes.length) {
        notifications.showNotification('Please select at least one violation to schedule.', 'warning');
        return;
    }

    const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
        const row = cb.closest('tr');
        const isGroup = cb.classList.contains('groupCheckbox');

        return {
            violation_id: isGroup ? cb.value : row.dataset.violationId,
            student_name: row.dataset.studentName || getStudentNamesFromGroup(row),
            incident: row.dataset.incident,
            is_group: isGroup,
            group_key: isGroup ? cb.value : null
        };
    });

    const selectedList = document.getElementById('selectedViolationsList');
    selectedList.innerHTML = '';

    selectedViolations.forEach(violation => {
        const item = document.createElement('div');
        item.className = 'selected-violation-item';
        item.innerHTML = `
            <strong>${violation.student_name}</strong>
            ${violation.is_group ? '<span class="group-badge">(Group)</span>' : ''}
            <br><small>Incident: ${violation.incident}</small>
            <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
            ${violation.is_group ? `<input type="hidden" name="group_keys[]" value="${violation.group_key}">` : ''}
        `;
        selectedList.appendChild(item);
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('schedule_date').min = today;
    document.getElementById('schedule_date').value = today;

    // Set default time to next hour
    const nextHour = new Date();
    nextHour.setHours(nextHour.getHours() + 1);
    document.getElementById('schedule_time').value = nextHour.toTimeString().substring(0, 5);

    document.getElementById('setScheduleModal').style.display = 'flex';
});

// Helper function to get student names from group rows
function getStudentNamesFromGroup(row) {
    const studentElements = row.querySelectorAll('.student-name');
    const names = Array.from(studentElements).map(el => el.textContent.trim());
    return names.join(', ');
}
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
    const submitBtn = this.querySelector('#submitScheduleBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Validate form
    const scheduleDate = document.getElementById('schedule_date').value;
    const scheduleTime = document.getElementById('schedule_time').value;

    if (!scheduleDate || !scheduleTime) {
        notifications.showNotification('Please fill in all required fields.', 'error');
        return;
    }

    // Validate date is not in the past
    const selectedDateTime = new Date(`${scheduleDate}T${scheduleTime}`);
    if (selectedDateTime < new Date()) {
        notifications.showNotification('Please select a future date and time.', 'error');
        return;
    }

    try {
        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        submitBtn.disabled = true;

        // Get all form data including violation_ids and group_keys
        const formDataObject = Object.fromEntries(formData);

        // Send as form data instead of JSON to handle arrays properly
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
            notifications.showNotification(result.message, 'success');
            document.getElementById('setScheduleModal').style.display = 'none';

            // Clear form
            this.reset();

            // Reload page after short delay to show updated data
            setTimeout(() => {
                location.reload();
            }, 1500);
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
        notifications.showNotification('Error scheduling appointments. Please try again.', 'error');
    } finally {
        // Reset button state
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
    }
});

// Real-time validation for date and time
document.getElementById('schedule_date').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        notifications.showNotification('Please select a date that is not in the past.', 'warning');
        this.value = today.toISOString().split('T')[0];
    }
});

document.getElementById('schedule_time').addEventListener('change', function() {
    const selectedDate = document.getElementById('schedule_date').value;
    const selectedTime = this.value;

    if (selectedDate && selectedTime) {
        const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);
        if (selectedDateTime < new Date()) {
            notifications.showNotification('Please select a time that is not in the past.', 'warning');

            // Set to current time + 30 minutes
            const now = new Date();
            now.setMinutes(now.getMinutes() + 30);
            this.value = now.toTimeString().substring(0, 5);
        }
    }
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

// Store selected violation IDs globally
let selectedViolationIds = [];

// ==================== CREATE ANECDOTAL FUNCTIONALITY ====================
document.getElementById('createAnecdotalBtn').addEventListener('click', function() {
    // Get both individual and group checkboxes
    const selectedIndividual = document.querySelectorAll('.violationCheckbox:checked');
    const selectedGroup = document.querySelectorAll('.groupCheckbox:checked');

    const totalSelected = selectedIndividual.length + selectedGroup.length;

    if (!totalSelected) {
        notifications.showNotification('Please select at least one violation to create anecdotal record.', 'warning');
        return;
    }

    const selectedViolations = [];

    // Process individual violations
    selectedIndividual.forEach(cb => {
        const row = cb.closest('tr');
        if (row && row.classList.contains('individual-row')) {
            selectedViolations.push({
                violation_id: row.dataset.violationId,
                student_name: row.dataset.studentName,
                incident: row.dataset.incident,
                offense_type: row.dataset.offenseType,
                sanction: row.dataset.sanction,
                is_individual: true
            });
        }
    });

    // Process group violations
    selectedGroup.forEach(cb => {
        const row = cb.closest('tr');
        if (row && row.classList.contains('group-row')) {
            selectedViolations.push({
                group_key: cb.value,
                student_name: getStudentNamesFromGroup(row),
                incident: row.dataset.incident,
                offense_type: row.dataset.offenseType,
                sanction: row.dataset.sanction,
                is_group: true
            });
        }
    });

    const selectedList = document.getElementById('selectedViolationsForAnecdotal');
    selectedList.innerHTML = '';

    selectedViolations.forEach(violation => {
        const item = document.createElement('div');
        item.className = 'selected-violation-item';

        if (violation.is_group) {
            item.innerHTML = `
                <strong>${violation.student_name}</strong>
                <span class="group-badge">(Group)</span><br>
                <small>Incident: ${violation.incident}</small><br>
                <small>Offense: ${violation.offense_type}</small>
                <input type="hidden" name="group_keys[]" value="${violation.group_key}">
            `;
        } else {
            item.innerHTML = `
                <strong>${violation.student_name}</strong><br>
                <small>Incident: ${violation.incident}</small><br>
                <small>Offense: ${violation.offense_type}</small>
                <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
            `;
        }

        selectedList.appendChild(item);
    });

    // Set default date and time
    const now = new Date();
    document.getElementById('anecdotal_date').value = now.toISOString().split('T')[0];
    document.getElementById('anecdotal_time').value = now.toTimeString().substring(0, 5);

    document.getElementById('createAnecdotalModal').style.display = 'flex';
});

// Print Anecdotal Record - UPDATED
document.getElementById('printAnecdotalBtn').addEventListener('click', function() {
    if (selectedViolationIds.length === 0) {
        notifications.showNotification('No violations selected for printing.', 'warning');
        return;
    }

    // Generate PDF using the violation IDs
    const printUrl = `/prefect/violations/generate-multiple-anecdotal-pdf?violation_ids=${selectedViolationIds.join(',')}`;

    // Open in new tab for printing
    window.open(printUrl, '_blank');
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

    // Log what we're sending for debugging
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }

    try {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
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
        console.log('Server response:', result);

        if (result.success) {
            document.getElementById('createAnecdotalModal').style.display = 'none';
            document.getElementById('successMessage').textContent = result.message;

            // Show process details
            const processDetails = document.getElementById('processDetails');
            if (result.data) {
                let detailsHtml = `<strong>Process Summary:</strong><br>`;
                detailsHtml += `• Total Records Processed: ${result.data.total_processed}<br>`;
                if (result.data.created && result.data.created.length > 0) {
                    detailsHtml += `• New Records Created: ${result.data.created.length}<br>`;
                }
                if (result.data.updated && result.data.updated.length > 0) {
                    detailsHtml += `• Existing Records Updated: ${result.data.updated.length}<br>`;
                }

                // Show student names
                const allAnecdotals = [...(result.data.created || []), ...(result.data.updated || [])];
                if (allAnecdotals.length > 0) {
                    const studentNames = allAnecdotals.map(a =>
                        a.violation?.student?.student_fname + ' ' + a.violation?.student?.student_lname
                    ).filter(name => name !== 'undefined undefined');

                    if (studentNames.length > 0) {
                        detailsHtml += `• Students Processed: ${studentNames.join(', ')}<br>`;
                    }
                }

                processDetails.innerHTML = detailsHtml;
            }

            document.getElementById('anecdotalSuccessModal').style.display = 'flex';

            // Store the violation IDs for printing
            if (result.data) {
                const allAnecdotals = [...(result.data.created || []), ...(result.data.updated || [])];
                if (allAnecdotals.length > 0) {
                    window.selectedViolationIds = allAnecdotals.map(a => a.violation_id);
                }
            }
        } else {
            console.error('Server returned error:', result);
            if (result.errors) {
                let messages = Object.values(result.errors).flat().join('\n');
                notifications.showNotification('Validation failed:\n' + messages, 'error');
            } else {
                notifications.showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        console.error('Network error:', error);
        notifications.showNotification('Error processing anecdotal records.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Print Anecdotal Record - UPDATED
document.getElementById('printAnecdotalBtn').addEventListener('click', function() {
    if (!window.selectedViolationIds || window.selectedViolationIds.length === 0) {
        notifications.showNotification('No violations selected for printing.', 'warning');
        return;
    }

    // Generate PDF using the violation IDs
    const printUrl = `/prefect/violations/generate-multiple-anecdotal-pdf?violation_ids=${window.selectedViolationIds.join(',')}`;

    // Open in new tab for printing
    window.open(printUrl, '_blank');
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
                 <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Print</button>
                 <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Close</button>
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

// Add click event listeners to violation record rows
document.addEventListener('DOMContentLoaded', function() {
    const violationRows = document.querySelectorAll('#violationRecordsTable tbody tr.clickable-row');

    violationRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' || e.target.classList.contains('editViolationBtn') || e.target.classList.contains('editGroupBtn')) {
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

// Close violation details modal
closeViolationDetailsModal.addEventListener('click', function() {
    violationDetailsModal.style.display = 'none';
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
                const response = await fetch('/prefect/violations/archive', {
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
                const response = await fetch('/prefect/violations/archive', {
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

// ==================== ARCHIVE MODAL FUNCTIONALITY ====================

// 🗃️ Archive Button - Opens violation records archive modal
document.getElementById('archiveBtn').addEventListener('click', async function() {
    try {
        console.log('Loading archived violation data');

        const violationResponse = await fetch('/prefect/violations/archived');
        console.log('Violation response status:', violationResponse.status);
        const archivedViolations = await violationResponse.json();
        console.log('Archived violations:', archivedViolations);

        populateArchiveTable('archiveViolationRecordsBody', archivedViolations, 'violation');
        document.getElementById('violationRecordsArchiveModal').style.display = 'flex';

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
        }

        tableBody.appendChild(row);
    });
}

// Archive Search
document.getElementById('violationRecordsArchiveSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('archiveViolationRecordsBody');
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

// Select All for archive modal
document.getElementById('selectAllViolationRecordsArchived').addEventListener('change', function() {
    const tableBody = document.getElementById('archiveViolationRecordsBody');
    const checkboxes = tableBody.querySelectorAll('.archiveCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

// 🔄 Restore Archived Records
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
                const response = await fetch('/prefect/violations/restore-multiple', {
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

// 🗑️ Delete Archived Records Permanently
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
                const response = await fetch('/prefect/violations/destroy-multiple-archived', {
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

// Close Archive Modal
document.getElementById('closeViolationRecordsArchive').addEventListener('click', function() {
    document.getElementById('violationRecordsArchiveModal').style.display = 'none';
});

// Show dropdown on hover
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.dropdown');
    const dropdownContent = document.querySelector('.dropdown-content');

    dropdown.addEventListener('mouseenter', function() {
        dropdownContent.style.display = 'block';
    });

    dropdown.addEventListener('mouseleave', function() {
        setTimeout(() => {
            if (!dropdownContent.matches(':hover')) {
                dropdownContent.style.display = 'none';
            }
        }, 200);
    });

    dropdownContent.addEventListener('mouseleave', function() {
        this.style.display = 'none';
    });
});

// ==================== EDIT GROUP MODAL FUNCTIONALITY ====================
const editGroupModal = document.getElementById('editGroupModal');
const editGroupForm = document.getElementById('editGroupForm');
const closeGroupModal = document.getElementById('closeGroupEditModal');
const cancelGroupBtn = document.getElementById('cancelGroupEditBtn');

// Function to open group edit modal
async function openGroupEditModal(groupKey, groupData) {
    try {
        console.log('Opening group edit modal for key:', groupKey);
        console.log('Group data:', groupData);

        // Show loading state
        document.getElementById('groupStudentsList').innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading students...</div>';

        // FIX: Get the modal element inside the function instead of using the global variable
        const editGroupModal = document.getElementById('editGroupModal');
        editGroupModal.style.display = 'flex';

        // ... rest of your function code remains the same

        // Fetch students in this group
        const response = await fetch(`/prefect/violations/group/${encodeURIComponent(groupKey)}/students`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Server response:', result);

        if (result.success) {
            // Populate students list
            const studentsList = document.getElementById('groupStudentsList');
            studentsList.innerHTML = '';

            if (result.students && result.students.length > 0) {
                result.students.forEach(student => {
                    const studentItem = document.createElement('div');
                    studentItem.className = 'student-item';
                    studentItem.innerHTML = `
                        <div class="student-info">
                            <span class="student-name">${student.student_fname} ${student.student_lname}</span>
                            <span class="student-id">(ID: ${student.student_id})</span>
                        </div>
                    `;
                    studentsList.appendChild(studentItem);
                });
            } else {
                studentsList.innerHTML = '<div style="text-align: center; padding: 10px; color: #666;">No students found in this group.</div>';
            }

            // Populate form fields
            document.getElementById('edit_group_key').value = groupKey;
            document.getElementById('edit_group_incident').value = groupData.incident || '';
            document.getElementById('edit_group_date').value = groupData.date || '';
            document.getElementById('edit_group_time').value = convertTo24Hour(groupData.time || '');
            document.getElementById('edit_group_status').value = groupData.status || 'pending';

           // Set offense type and auto-populate sanction
if (groupData.offenseType) {
    // Find the offense option that matches the offense type
    const offenseSelect = document.getElementById('edit_group_offense_type');
    const options = offenseSelect.options;
    const sanctionField = document.getElementById('edit_group_sanction');

    let found = false;
    for (let i = 0; i < options.length; i++) {
        if (options[i].text === groupData.offenseType) {
            offenseSelect.value = options[i].value;
            // Set the sanction from the actual data, not from the offense options
            sanctionField.value = groupData.sanction || '';
            found = true;
            break;
        }
    }

    if (!found) {
        console.warn('Could not find offense type:', groupData.offenseType);
        // Still set the sanction from the actual data
        sanctionField.value = groupData.sanction || '';
    }
} else {
    // If no offense type match, still set the sanction from actual data
    document.getElementById('edit_group_sanction').value = groupData.sanction || '';
}

        } else {
            console.error('Server returned error:', result);
            notifications.showNotification('Error loading group data: ' + (result.message || 'Unknown error'), 'error');
            editGroupModal.style.display = 'none';
        }

    } catch (error) {
        console.error('Error loading group data:', error);
        notifications.showNotification('Error loading group data: ' + error.message, 'error');
        editGroupModal.style.display = 'none';
    }
}

// Event listener for group edit buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('editGroupBtn')) {
        e.stopPropagation();
        const row = e.target.closest('tr');
        if (row && row.classList.contains('group-row')) {
            const groupKey = row.dataset.groupKey;
            openGroupEditModal(groupKey, {
                incident: row.dataset.incident,
                offenseType: row.dataset.offenseType,
                sanction: row.dataset.sanction,
                date: row.dataset.date,
                time: row.dataset.time,
                status: row.dataset.status
            });
        }
    }
});

// Auto-suggest sanction when offense type changes, but allow editing
document.getElementById('edit_group_offense_type').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const sanctionField = document.getElementById('edit_group_sanction');

    // Only auto-fill if the field is empty or contains the previous auto-filled value
    if (!sanctionField.value || sanctionField.value === sanctionField.dataset.lastAutoFill) {
        sanctionField.value = selectedOption.dataset.sanction || '';
        sanctionField.dataset.lastAutoFill = selectedOption.dataset.sanction || '';
    }
});

// Close modal events
[closeGroupModal, cancelGroupBtn].forEach(btn => {
    if (btn) btn.addEventListener('click', () => editGroupModal.style.display = 'none');
});

// Handle group form submission
editGroupForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Group...';
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
            notifications.showNotification('Group violation updated successfully!', 'success');
            editGroupModal.style.display = 'none';

            // Reload the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 1000);
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
        notifications.showNotification('Error updating group violation.', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Close modal when clicking outside
editGroupModal.addEventListener('click', function(event) {
    if (event.target === editGroupModal) {
        editGroupModal.style.display = 'none';
    }
});
</script>
@endsection
