@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Parent Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by parent name or ID..." id="searchInput">
      <a href="{{ route('create.parent') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Parent
      </a>
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

  <!-- Parent Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th></th>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Sex</th>
          <th>Birthdate</th>
          <th>Email</th>
          <th>Contact Info</th>
          <th>Relationship</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($parents as $parent)
          <tr data-details="{{ $parent->parent_fname }} {{ $parent->parent_lname }}|{{ $parent->parent_sex }}|{{ $parent->parent_birthdate }}|{{ $parent->parent_email }}|{{ $parent->parent_contactinfo }}|{{ $parent->parent_relationship }}|{{ $parent->status }}">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>{{ $parent->parent_id }}</td>
            <td>{{ $parent->parent_fname }}</td>
            <td>{{ $parent->parent_lname }}</td>
            <td>{{ ucfirst($parent->parent_sex) }}</td>
            <td>{{ $parent->parent_birthdate }}</td>
            <td>{{ $parent->parent_email ?? 'N/A' }}</td>
            <td>{{ $parent->parent_contactinfo }}</td>
            <td>{{ $parent->parent_relationship ?? 'N/A' }}</td>
            <td>{{ ucfirst($parent->status) }}</td>
            <td>
              <button class="btn-primary editBtn">✏️ Edit</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11" style="text-align:center;">No parents found</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      {{ $parents->links() }}
    </div>
  </div>

</div>

<!-- 📝 Parent Details Modal -->
<div class="modal" id="detailsModal">
  <div class="modal-content">
    <div class="modal-header">
      📄 Parent Details
    </div>
    <div class="modal-body" id="detailsBody">
      <!-- Content filled dynamically via JS -->
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
    <div class="modal-header">
      🗃️ Archived Parents
    </div>

    <div class="modal-body">
      <!-- 🔍 Search & Bulk Actions -->
      <div class="modal-actions">
        <label class="select-all-label">
          <input type="checkbox" id="selectAllArchived" class="select-all-checkbox">
          <span>Select All</span>
        </label>

        <div class="search-container">
          <input type="search" id="archiveSearch" placeholder="🔍 Search archived..." class="search-input">
        </div>
      </div>

      <!-- 📋 Archive Table -->
      <div class="archive-table-container">
        <table class="archive-table">
          <thead>
            <tr>
              <th>✔</th>
              <th>ID</th>
              <th>Parent Name</th>
              <th>Relationship</th>
              <th>Contact Info</th>
              <th>Date Archived</th>
            </tr>
          </thead>
          <tbody id="archiveTableBody">
            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>P003</td>
              <td>Juan Dela Cruz</td>
              <td>Father</td>
              <td>09123456789</td>
              <td>2025-09-22</td>
            </tr>
            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>P004</td>
              <td>Maria Reyes</td>
              <td>Mother</td>
              <td>09198765432</td>
              <td>2025-09-23</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ⚠️ Note -->
      <div class="modal-note">
        ⚠️ Note: Deleting records will permanently remove them.
      </div>

      <!-- 🧭 Footer Buttons -->
      <div class="modal-footer">
        <button class="btn-secondary" id="restoreArchivedBtn">🔄 Restore</button>
        <button class="btn-danger" id="deleteArchivedBtn">🗑️ Delete</button>
        <button class="btn-close" id="closeArchive">❌ Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// Search filter for main parent table
document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const tableBody = document.getElementById('tableBody');
  const rows = tableBody.querySelectorAll('tr');

  let visibleCount = 0;

  rows.forEach(row => {
    const firstName = row.cells[2].innerText.toLowerCase();
    const lastName = row.cells[3].innerText.toLowerCase();
    const parentID = row.cells[1].innerText.toLowerCase();
    
    if(firstName.includes(filter) || lastName.includes(filter) || parentID.includes(filter)) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Remove existing "No records found" row
  const noDataRow = tableBody.querySelector('.no-data-row');
  if(visibleCount === 0) {
    if(!noDataRow) {
      const newRow = document.createElement('tr');
      newRow.classList.add('no-data-row');
      newRow.innerHTML = `<td colspan="11" style="text-align:center; padding:15px;">⚠️ No parents found</td>`;
      tableBody.appendChild(newRow);
    }
  } else {
    if(noDataRow) noDataRow.remove();
  }
});

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// Move to Trash
document.getElementById('moveToTrashBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.rowCheckbox:checked')];
  if (selected.length === 0) {
    alert('Please select at least one record.');
  } else {
    alert(selected.length + ' record(s) moved to Trash.');
    // Add AJAX call here to move to trash in backend
  }
});

// Row click -> Parent Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    // Ignore if checkbox or edit button is clicked
    if(e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

    const data = row.dataset.details.split('|');

    const detailsBody = `
      <p><strong>Parent Name:</strong> ${data[0]}</p>
      <p><strong>Sex:</strong> ${data[1]}</p>
      <p><strong>Birthdate:</strong> ${data[2]}</p>
      <p><strong>Email:</strong> ${data[3]}</p>
      <p><strong>Contact Info:</strong> ${data[4]}</p>
      <p><strong>Relationship:</strong> ${data[5]}</p>
      <p><strong>Status:</strong> ${data[6]}</p>
    `;

    document.getElementById('detailsBody').innerHTML = detailsBody;
    document.getElementById('detailsModal').style.display = 'flex';
  });
});

// Close Details Modal
document.querySelectorAll('#detailsModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

// Set Schedule Button
document.getElementById('setScheduleBtn').addEventListener('click', () => {
  alert('Open schedule setup form or modal here.');
});

// Send SMS Button
document.getElementById('sendSmsBtn').addEventListener('click', () => {
  alert('Trigger SMS sending here.');
});

// Edit button
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    const row = btn.closest('tr');
    const data = row.dataset.details.split('|');
    alert('Edit parent: ' + data[0]);
    // TODO: Implement actual edit functionality
  });
});

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// Dropdown functionality
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const dropdown = btn.parentElement;
    dropdown.classList.toggle('show');
  });
});

// Close dropdown if clicked outside
window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Close archive modal
document.getElementById('closeArchive').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'none';
});

// ================= ARCHIVE MODAL FUNCTIONALITY =================

// Select all checkboxes in archive modal
const selectAllArchived = document.getElementById('selectAllArchived');
const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');

selectAllArchived.addEventListener('change', () => {
  const isChecked = selectAllArchived.checked;
  archivedCheckboxes.forEach(checkbox => {
    checkbox.checked = isChecked;
  });
});

// Individual checkbox change handler
archivedCheckboxes.forEach(checkbox => {
  checkbox.addEventListener('change', () => {
    if (!checkbox.checked) {
      selectAllArchived.checked = false;
    } else {
      const allChecked = Array.from(archivedCheckboxes).every(cb => cb.checked);
      selectAllArchived.checked = allChecked;
    }
  });
});

// Search filter for archive table
document.getElementById('archiveSearch').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const archiveRows = document.querySelectorAll('#archiveTableBody tr');
  
  archiveRows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// Restore selected archived records
document.getElementById('restoreArchivedBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(selected.length === 0) {
    alert('Please select at least one record to restore.');
    return;
  }
  alert(`${selected.length} record(s) restored.`);
  // TODO: Add AJAX call to restore records
});

// Delete selected archived records
document.getElementById('deleteArchivedBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(selected.length === 0) {
    alert('Please select at least one record to delete.');
    return;
  }
  if(confirm('This will permanently delete the selected record(s). Are you sure?')) {
    alert(`${selected.length} record(s) deleted permanently.`);
    // TODO: Add AJAX call to delete records
  }
});

// Close modals when clicking outside
window.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal')) {
    e.target.style.display = 'none';
  }
});
</script>
@endsection