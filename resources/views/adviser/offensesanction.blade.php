@extends('adviser.layout')
@section('content')
<div class="main-container">


  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Offense and Sanctions</h2>
    <div class="actions">
<input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <button class="btn-primary" id="createBtn">➕ Add Violation</button>
      <button class="btn-secondary" id="createAnecBtn">📝 Create Anecdotal</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="summary">
    <div class="card">
      <h2>55</h2>
      <p>Total Students</p>
    </div>
    <div class="card">
      <h2>12</h2>
      <p>Violations Today</p>
    </div>
    <div class="card">
      <h2>11</h2>
      <p>Pending Appointments</p>
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

<!-- Offense & Sanction Table -->
<!-- Offense & Sanction Table -->
<div class="table-container">
  <table>
    <thead>
      <tr>
        <th></th>
        <th>#</th>
        <th>Offense Type</th>
        <th>Offense Description</th>
        <th>Sanction(s)</th>
        <th>Action</th>
      </tr>
    </thead>
<tbody id="tableBody">
  @forelse ($offenses as $offense)
    <tr data-details="{{ $offense->offense_type }}|{{ $offense->offense_description }}|{{ $offense->sanctions }}">
      <td><input type="checkbox" class="rowCheckbox"></td>
      {{-- <td>{{ $offense->min_id }}</td> <!-- Display ID --> --}}
            <td>{{ $loop->iteration }}</td> <!-- Display index + 1 -->

      <td><span title="{{ $offense->offense_type }}">{{ $offense->offense_type }}</span></td>
      <td>{{ $offense->offense_description }}</td>
      <td>{{ $offense->sanctions }}</td>
      <td><button class="btn-primary editBtn">✏️ Edit</button></td>
    </tr>
  @empty
    <tr>
      <td colspan="6" style="text-align:center; padding:15px;">⚠️ No offenses found.</td>
    </tr>
  @endforelse
</tbody>

  </table>


  <!-- Pagination -->
  <div class="pagination">
    {{-- {{ $offenses->links() }} --}}
  </div>
</div>


<!-- 📝 Details Modal -->
<!-- 📝 Details Modal -->
<div class="modal" id="detailsModal">
  <div class="modal-content">
    <div class="modal-header">📄 Offense & Sanction Details</div>
    <div class="modal-body" id="detailsBody">
      <!-- Content will be filled dynamically via JS -->
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" id="setScheduleBtn">📅 Set Schedule</button>
      <button class="btn-info" id="sendSmsBtn">📩 Send SMS</button>
      <button class="btn-close">❌ Close</button>
    </div>
  </div>
</div>


<!-- 🗃️ Archive Modal -->
<div class="modal" id="archiveModal">
  <div class="modal-content">
    <div class="modal-header">🗃️ Archived Violations</div>
    <div class="modal-body">
      <div class="modal-actions">
        <label class="select-all-label">
          <input type="checkbox" id="selectAllArchived" class="select-all-checkbox">
          <span>Select All</span>
        </label>

        <div class="search-container">
          <input type="search" placeholder="🔍 Search archived..." id="archiveSearch" class="search-input">
        </div>
      </div>

      <div class="archive-table-container">
        <table class="archive-table">
          <thead>
            <tr>
              <th>✔</th>
              <th>ID</th>
              <th>Student Name</th>
              <th>Offense</th>
              <th>Group #</th>
              <th>Stage #</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody id="archiveTableBody">
            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>3</td>
              <td>Mark Dela Cruz</td>
              <td>Tardiness</td>
              <td>1</td>
              <td>1</td>
              <td>2025-09-22</td>
            </tr>
            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>4</td>
              <td>Anna Reyes</td>
              <td>Cutting Classes</td>
              <td>1</td>
              <td>1</td>
              <td>2025-09-23</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="modal-note">⚠️ Note: Deleting records will permanently remove them.</div>

      <div class="modal-footer">
        <button class="btn-secondary" id="restoreArchiveBtn">🔄 Restore</button>
        <button class="btn-danger" id="deleteArchiveBtn">🗑️ Delete</button>
        <button class="btn-close" id="closeArchive">❌ Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  // 🔍 Search filter for main table
  document.getElementById('searchInput')?.addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  });

  // ✅ Select All Main Table
  document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
  });

  // ✅ Move to Trash
  document.getElementById('moveToTrashBtn')?.addEventListener('click', () => {
    const selected = [...document.querySelectorAll('.rowCheckbox:checked')];
    if(!selected.length) return alert('Please select at least one record.');
    alert(`${selected.length} record(s) moved to Trash.`);
    // TODO: AJAX call to backend
  });
// 🔹 Row click for Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    // Ignore clicks on checkboxes or Edit button
    if (e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

    // Split the dataset details: offense_type | offense_description | sanctions
    const [offenseType, offenseDescription, sanctions] = row.dataset.details.split('|');

    // Convert sanctions string into a bullet list
    const sanctionList = sanctions.split(',').map(s => `<li>${s.trim()}</li>`).join('');

    document.getElementById('detailsBody').innerHTML = `
      <p><strong>Offense Type:</strong> ${offenseType}</p>
      <p><strong>Description:</strong> ${offenseDescription}</p>
      <p><strong>Sanctions:</strong></p>
      <ul>${sanctionList}</ul>
    `;

    document.getElementById('detailsModal').style.display = 'flex';
  });
});

// 🔹 Close Modals
document.querySelectorAll('.btn-close').forEach(btn => {
  btn.addEventListener('click', () => btn.closest('.modal').style.display = 'none');
});


  // 🔹 Edit Button
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const [student, offense, sanction, group, stage, date, time] = btn.closest('tr').dataset.details.split('|');
      // Fill edit modal fields (make sure edit modal inputs exist)
      document.getElementById('editStudentName').value = student;
      document.getElementById('editOffense').value = offense;
      document.getElementById('editSanction').value = sanction;
      document.getElementById('editGroup').value = group;
      document.getElementById('editStage').value = stage;
      document.getElementById('editDate').value = date;
      document.getElementById('editTime').value = time;
      document.getElementById('editModal').style.display = 'flex';
    });
  });

  // 🔹 Dropdown toggle
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      btn.parentElement.classList.toggle('show');
    });
  });
  window.addEventListener('click', () => document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show')));

  // 🔹 Archive Select All
  const selectAllArchived = document.getElementById('selectAllArchived');
  const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');
  selectAllArchived?.addEventListener('change', () => {
    archivedCheckboxes.forEach(cb => cb.checked = selectAllArchived.checked);
  });
  archivedCheckboxes.forEach(cb => cb.addEventListener('change', () => {
    selectAllArchived.checked = Array.from(archivedCheckboxes).every(c => c.checked);
  }));

  // 🔹 Archive Search
  document.getElementById('archiveSearch')?.addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#archiveTableBody tr').forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
  });

  // 🔹 Restore / Delete Archive
  document.getElementById('restoreArchiveBtn')?.addEventListener('click', () => {
    const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
    if(!selected.length) return alert('Please select at least one record to restore.');
    alert(`${selected.length} record(s) restored.`);
  });
  document.getElementById('deleteArchiveBtn')?.addEventListener('click', () => {
    const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
    if(!selected.length) return alert('Please select at least one record to delete.');
    if(confirm('This will permanently delete the selected record(s). Are you sure?')) {
      alert(`${selected.length} record(s) deleted permanently.`);
    }
  });
</script>

@endsection
