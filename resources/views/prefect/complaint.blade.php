@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Complaint Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <a href="{{ route('complaints.create') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Complaint
      </a>
      <button class="btn-secondary" id="createAnecBtn">📝 Create Anecdotal</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="summary">
    <div class="card">
      <h2>55</h2>
      <p>Monthly Complaints</p>
    </div>
    <div class="card">
      <h2>12</h2>
      <p>Weekly Complaints</p>
    </div>
    <div class="card">
      <h2>11</h2>
      <p> Daily Complaints</p>
    </div>
    <div class="card">
      <h2>11</h2>
      <p>Scheduled</p>
    </div>
    <div class="card">
      <h2>11</h2>
      <p>Completed</p>
    </div>
    </div>


  <!-- Bulk Action / Select Options -->
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
          <a href="#" id="violaitonAppointments">Violation Appointments</a>
          <a href="#" id="violationAnecdotals">Violation Anecdotals</a>
        </div>
      </div>
    </div>

    <div class="right-controls">
      <button class="btn-danger" id="moveToTrashBtn">🗑️ Move Selected to Trash</button>
    </div>
  </div>

  <!-- Complaint Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th></th>
          <th>ID</th>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Offense Type</th>
          <th>Sanction</th>
                    <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody id="tableBody">
        @forelse($complaints as $complaint)
        <tr
            data-complainant="{{ $complaint->complainant->student_fname }} {{ $complaint->complainant->student_lname }}"
            data-respondent="{{ $complaint->respondent->student_fname }} {{ $complaint->respondent->student_lname }}"
            data-offense="{{ $complaint->offense->offense_type }}"
            data-offense-id="{{ $complaint->offense_sanc_id }}"
            data-sanction="{{ $complaint->offense->sanction_consequences }}"
            data-incident="{{ $complaint->complaints_incident }}"
            data-date="{{ $complaint->complaints_date }}"
            data-time="{{ \Carbon\Carbon::parse($complaint->complaints_time)->format('H:i') }}"
        >
          <td><input type="checkbox" class="rowCheckbox"></td>
          <td>{{ $complaint->complaints_id }}</td>
          <td>{{ $complaint->complainant->student_fname }} {{ $complaint->complainant->student_lname }}</td>
          <td>{{ $complaint->respondent->student_fname }} {{ $complaint->respondent->student_lname }}</td>
          <td>{{ $complaint->offense->offense_type }}</td>
          <td>{{ $complaint->offense->sanction_consequences }}</td>
          <td>{{ $complaint->complaints_incident }}</td>
          <td>{{ $complaint->complaints_date }}</td>
          <td>{{ \Carbon\Carbon::parse($complaint->complaints_time)->format('h:i A') }}</td>
          <td>
            <button class="btn-primary editComplaintBtn">✏️ Edit</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="text-align:center;">No complaints found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>


    <!-- Pagination -->
  <div class="pagination-wrapper">
    <div class="pagination-summary">
      Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of {{ $complaints->total() }} results
    </div>
    <div class="pagination-links">
      {{ $complaints->links() }}
    </div>
  </div>


<!-- Edit Complaint Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            ✏️ Edit Complaint
        </div>
        <form id="editComplaintForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editComplaintId" name="complaints_id">
            <div class="modal-body">

                <label>Complainant Name</label>
                <input type="text" id="editComplainant" name="complainant_name" placeholder="Search complainant..." autocomplete="off" required>
                <input type="hidden" id="editComplainantId" name="complainant_id">
                <div id="complainantResults" class="search-results"></div>

                <label>Respondent Name</label>
                <input type="text" id="editRespondent" name="respondent_name" placeholder="Search respondent..." autocomplete="off" required>
                <input type="hidden" id="editRespondentId" name="respondent_id">
                <div id="respondentResults" class="search-results"></div>

                <label>Offense Type</label>
                <input type="text" id="editOffense" name="offense_name" placeholder="Search offense..." autocomplete="off" required>
                <input type="hidden" id="editOffenseId" name="offense_sanc_id">
                <div id="offenseResults" class="search-results"></div>

                <label>Sanction</label>
                <input type="text" id="editSanction" name="sanction_consequences" readonly>

                <label>Incident Details</label>
                <textarea id="editIncident" name="complaints_incident" required></textarea>

                <label>Date</label>
                <input type="date" id="editDate" name="complaints_date" required>

                <label>Time</label>
                <input type="time" id="editTime" name="complaints_time" required>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary">💾 Save Changes</button>
                <button type="button" class="btn-close">❌ Close</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
   // =======================
    // Open Edit Modal
    // =======================
    document.querySelectorAll('.editComplaintBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');

            const complaintId = row.cells[1].innerText;
            const complainantName = row.dataset.complainant;
            const respondentName = row.dataset.respondent;
            const offenseName = row.dataset.offense;
            const offenseId = row.dataset.offenseId;
            const sanction = row.dataset.sanction;
            const incident = row.dataset.incident;
            const date = row.dataset.date;
            const time = row.dataset.time;

            document.getElementById('editComplaintId').value = complaintId;
            document.getElementById('editComplainant').value = complainantName;
            document.getElementById('editRespondent').value = respondentName;
            document.getElementById('editOffense').value = offenseName;
            document.getElementById('editOffenseId').value = offenseId;
            document.getElementById('editSanction').value = sanction;
            document.getElementById('editIncident').value = incident;
            document.getElementById('editDate').value = date;
            document.getElementById('editTime').value = time;

            document.getElementById('editComplaintForm').action = `/prefect/complaints/${complaintId}`;
            document.getElementById('editModal').style.display = 'flex';
        });
    });

    // Close Modal
    document.querySelectorAll('#editModal .btn-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal').style.display = 'none';
        });
    });

    // =======================
    // Live Search Function
    // =======================
    function liveSearch(inputId, resultsId, url, hiddenInputId = null, callback = null) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        const hiddenInput = hiddenInputId ? document.getElementById(hiddenInputId) : null;

        input.addEventListener('input', () => {
            const query = input.value;
            if (query.length < 1) {
                results.innerHTML = '';
                return;
            }

            fetch(`${url}?query=${query}`)
                .then(res => res.text())
                .then(html => {
                    results.innerHTML = html;

                    results.querySelectorAll('div').forEach(item => {
                        item.addEventListener('click', () => {
                            input.value = item.innerText;
                            if (hiddenInput) hiddenInput.value = item.dataset.id;


                                   // For complainant/respondent
                            if(inputId === 'editComplainant') document.getElementById('editComplainantId').value = item.dataset.id;
                            if(inputId === 'editRespondent') document.getElementById('editRespondentId').value = item.dataset.id;

                            results.innerHTML = '';

                            if (callback) callback(item);
                        });
                    });
                });
        });
    }
// Student live search
liveSearch('editComplainant', 'complainantResults', '{{ route("prefect.students.search") }}', 'editComplainantId');
liveSearch('editRespondent', 'respondentResults', '{{ route("prefect.students.search") }}', 'editRespondentId');

// Offense live search
liveSearch('editOffense', 'offenseResults', '{{ route("prefect.offenses.search") }}', 'editOffenseId', (item) => {
    // Get sanction automatically
    fetch(`{{ route("complaints.get-sanction") }}?offense_id=${item.dataset.id}`)
        .then(res => res.text())
        .then(sanction => {
            document.getElementById('editSanction').value = sanction;
        });
});

</script>
@endsection
