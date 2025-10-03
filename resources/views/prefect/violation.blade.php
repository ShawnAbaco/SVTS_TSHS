@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Violation Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <a href="{{ route('violations.create') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Violation
      </a>
      <button class="btn-secondary" id="createAnecBtn">📝 Create Anecdotal</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

  <!-- Summary Cards -->
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


  <!-- Bulk Action / Select Options -->
  <div class="select-options">
    <div class="left-controls">
      <label for="selectAll" class="select-label">
        <input type="checkbox" id="selectAll">
        <span>Select All</span>
      </label>

      <!-- Dropdown Button -->
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
      <button class="btn-danger" id="moveToTrashBtn">🗑️ Move Selected to Trash</button>
    </div>
  </div>

  <!-- Violation Table -->
  <div class="table-container">
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
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($violations as $violation)
          <tr
            data-student-id="{{ $violation->student->student_id }}"
            data-offense-id="{{ $violation->offense->offense_sanc_id }}"
            data-student-name="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
            data-offense-type="{{ $violation->offense->offense_type }}"
            data-sanction="{{ $violation->offense->sanction_consequences }}"
            data-incident="{{ $violation->violation_incident }}"
            data-date="{{ $violation->violation_date }}"
            data-time="{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}"
          >
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>{{ $violation->violation_id }}</td>
            <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
            <td>{{ $violation->violation_incident }}</td>
            <td><span title="{{ $violation->offense->offense_type }}">{{ $violation->offense->offense_type }}</span></td>
            <td><span title="{{ $violation->offense->sanction_consequences }}">{{ $violation->offense->sanction_consequences }}</span></td>
            <td>{{ $violation->violation_date }}</td>
            <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
            <td>
              <button class="btn-primary editViolationBtn">✏️ Edit</button>
            </td>
          </tr>
        @empty
          <tr class="no-data-row">
            <td colspan="9" style="text-align:center;">No violations found</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination-wrapper">
      <div class="pagination-summary">
        Showing {{ $violations->firstItem() ?? 0 }} to {{ $violations->lastItem() ?? 0 }} of {{ $violations->total() ?? 0 }} violation{{ $violations->total() == 1 ? '' : 's' }}
      </div>
      <div class="pagination-links">
        {{ $violations->links() }}
      </div>
    </div>
  </div>

  <!-- ✏️ Edit Violation Modal -->
  <div class="modal" id="editViolationModal">
    <div class="modal-content">
      <button class="close-btn" id="closeViolationEditModal">✖</button>
      <h2>Edit Violation</h2>

      <form id="editViolationForm" method="POST" action="">
        @csrf
        @method('PUT')

        <input type="hidden" name="violation_id" id="edit_violation_id">
        <input type="hidden" name="violator_id" id="edit_student_id">
        <input type="hidden" name="offense_sanc_id" id="edit_offense_sanc_id">

        <div class="form-grid">
          <!-- Student -->
          <div class="form-group">
            <label>Student Name</label>
            <input type="text" id="studentSearch" placeholder="Search student name..." autocomplete="off" required>
            <ul id="studentResults" class="search-results"></ul>
          </div>

          <!-- Offense & Sanction -->
          <div class="form-group">
            <label>Offense & Sanction</label>
            <input type="text" id="edit_offense_search" placeholder="Search offense..." autocomplete="off" required>
            <ul id="offenseResults" class="search-results"></ul>
          </div>

          <!-- Incident -->
          <div class="form-group">
            <label>Incident</label>
            <input type="text" name="violation_incident" id="edit_violation_incident" required>
          </div>

          <!-- Date -->
          <div class="form-group">
            <label>Date</label>
            <input type="date" name="violation_date" id="edit_violation_date" required>
          </div>

          <!-- Time -->
          <div class="form-group">
            <label>Time</label>
            <input type="time" name="violation_time" id="edit_violation_time" required>
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn-primary">💾 Save Changes</button>
          <button type="button" class="btn-secondary" id="cancelViolationEditBtn">❌ Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const editButtons = document.querySelectorAll('.editViolationBtn');
  const editModal = document.getElementById('editViolationModal');
  const closeEditModal = document.getElementById('closeViolationEditModal');
  const cancelEditBtn = document.getElementById('cancelViolationEditBtn');
  const editForm = document.getElementById('editViolationForm');

  const studentSearch = document.getElementById('studentSearch');
  const studentResults = document.getElementById('studentResults');
  const studentIdInput = document.getElementById('edit_student_id');

  const offenseSearch = document.getElementById('edit_offense_search');
  const offenseResults = document.getElementById('offenseResults');
  const offenseIdInput = document.getElementById('edit_offense_sanc_id');

  // Open edit modal
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const row = this.closest('tr');
      const violationId = row.children[1].innerText.trim();

      // Populate hidden inputs from row data attributes
      studentIdInput.value = row.dataset.studentId;
      offenseIdInput.value = row.dataset.offenseId;

      // Populate form fields
      studentSearch.value = row.dataset.studentName;
      offenseSearch.value = `${row.dataset.offenseType} - ${row.dataset.sanction}`;
      editForm.querySelector('#edit_violation_incident').value = row.dataset.incident;
      editForm.querySelector('#edit_violation_date').value = row.dataset.date;
      editForm.querySelector('#edit_violation_time').value = convertTo24Hour(row.dataset.time);
      editForm.querySelector('#edit_violation_id').value = violationId;

      editForm.action = `/prefect/violations/update/${violationId}`; // Must match your route
      editModal.style.display = 'flex';
    });
  });

  function convertTo24Hour(timeStr) {
    const [time, modifier] = timeStr.split(' ');
    let [hours, minutes] = time.split(':');
    hours = parseInt(hours, 10);
    if (modifier === 'PM' && hours !== 12) hours += 12;
    if (modifier === 'AM' && hours === 12) hours = 0;
    return `${hours.toString().padStart(2, '0')}:${minutes}`;
  }

  // Close modal
  [closeEditModal, cancelEditBtn].forEach(btn =>
    btn.addEventListener('click', () => editModal.style.display = 'none')
  );

  // Live student search
  studentSearch.addEventListener('input', async function() {
    const query = this.value.trim();
    studentResults.innerHTML = '';
    if (query.length < 2) return;

    try {
      const res = await fetch(`/prefect/students/search?query=${query}`);
      const students = await res.json();
      students.forEach(s => {
        const li = document.createElement('li');
        li.textContent = `${s.student_fname} ${s.student_lname}`;
        li.addEventListener('click', () => {
          studentSearch.value = li.textContent;
          studentIdInput.value = s.student_id;
          studentResults.innerHTML = '';
        });
        studentResults.appendChild(li);
      });
    } catch (err) { console.error(err); }
  });

  // Live offense search
  offenseSearch.addEventListener('input', async function() {
    const query = this.value.trim();
    offenseResults.innerHTML = '';
    if (query.length < 2) return;

    try {
      const res = await fetch(`/prefect/offenses/search?query=${query}`);
      const offenses = await res.json();
      offenses.forEach(o => {
        const li = document.createElement('li');
        li.textContent = `${o.offense_type} - ${o.sanction_consequences}`;
        li.addEventListener('click', () => {
          offenseSearch.value = li.textContent;
          offenseIdInput.value = o.offense_sanc_id;
          offenseResults.innerHTML = '';
        });
        offenseResults.appendChild(li);
      });
    } catch (err) { console.error(err); }
  });
});
</script>
@endsection
