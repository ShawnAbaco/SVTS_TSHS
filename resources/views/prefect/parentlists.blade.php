@extends('prefect.layout')

@section('content')
<div class="main-container">


  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Student Management</h2>
    <div class="actions">
<input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <button class="btn-primary" id="createBtn">➕ Add Student</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

<div class="summary">
  <div class="card">
    <h2>{{ $totalStudents }}</h2>
    <p>Total Students</p>
  </div>
  <div class="card">
    <h2>{{ $activeStudents }}</h2>
    <p>Active Students</p>
  </div>

  <div class="card">
    <h2>{{ $maleStudents }}</h2>
    <p>Male Students</p>
  </div>
  <div class="card">
    <h2>{{ $femaleStudents }}</h2>
    <p>Female Students</p>
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
  <!-- Section Filter Dropdown -->
<div class="section-filter">
  <select id="sectionSelect" class="btn-info">
    <option value="">All Sections</option>
    @foreach($sections as $section)
      <option value="{{ $section }}">{{ $section }}</option>
    @endforeach
  </select>
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
        <th>Sex</th>
        <th>Birthdate</th>
        <th>Address</th>
        <th>Contact Info</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="tableBody">
      @forelse($students as $student)
      <tr data-details="{{ $student->student_fname }} {{ $student->student_lname }}|{{ $student->student_sex }}|{{ $student->student_birthdate }}|{{ $student->student_address }}|{{ $student->student_contactinfo }}|{{ $student->status }}">
        <td><input type="checkbox" class="rowCheckbox"></td>
        <td>{{ $student->student_id }}</td>
        <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
        <td>{{ ucfirst($student->student_sex) }}</td>
        <td>{{ \Carbon\Carbon::parse($student->student_birthdate)->format('Y-m-d') }}</td>
        <td>{{ $student->student_address }}</td>
        <td>{{ $student->student_contactinfo }}</td>
        <td>{{ ucfirst($student->status) }}</td>
        <td>
          <button class="btn-primary editBtn" data-id="{{ $student->student_id }}">✏️ Edit</button>
        </td>
      </tr>
      @empty
      <tr class="no-data-row">
        <td colspan="9" style="text-align:center; padding:15px;">⚠️ No students found</td>
      </tr>
      @endforelse
    </tbody>
  </table>

<!-- Pagination -->
<div class="pagination-wrapper">
  <div class="pagination-summary">
    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
  </div>

  <div class="pagination-links">
    {{ $students->links() }}
  </div>
</div>



<!-- 📝 Details Modal -->
<div class="modal" id="detailsModal">
  <div class="modal-content">
    <div class="modal-header">
      📄 Violation Details
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
      🗃️ Archived Violations
    </div>

    <div class="modal-body">

      <!-- 🔍 Search & Bulk Actions -->
      <div class="modal-actions">
        <label class="select-all-label">
          <input type="checkbox" id="selectAllArchived" class="select-all-checkbox">
          <span>Select All</span>
        </label>

        <div class="search-container">
          <input type="search" placeholder="🔍 Search archived..." class="search-input">
        </div>
      </div>

      <!-- 📋 Archive Table -->
      <div class="archive-table-container">
        <table class="archive-table">
          <thead>
            <tr>
              <th>✔</th>
              <th>ID</th>
              <th>Student Name</th>
              <th>Offense</th>
              <th>Date</th>
            </tr>
          </thead>
<tbody id="archiveTableBody">

            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>3</td>
              <td>Mark Dela Cruz</td>
              <td>Tardiness</td>
              <td>2025-09-22</td>
            </tr>
            <tr>
              <td><input type="checkbox" class="archivedCheckbox"></td>
              <td>4</td>
              <td>Anna Reyes</td>
              <td>Cutting Classes</td>
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
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr:not(.no-data-row)');

    let visibleCount = 0;

    rows.forEach(row => {
        const studentName = row.cells[2].innerText.toLowerCase();
        const studentID = row.cells[1].innerText.toLowerCase();
        if(studentName.includes(filter) || studentID.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noDataRow = tableBody.querySelector('.no-data-row');
    if(visibleCount === 0) {
        if(!noDataRow) {
            const newRow = document.createElement('tr');
            newRow.classList.add('no-data-row');
            newRow.innerHTML = `<td colspan="9" style="text-align:center; padding:15px;">⚠️ No students found</td>`;
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

// Row click -> Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
    row.addEventListener('click', e => {
        // Ignore if checkbox or edit button is clicked
        if(e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

        const data = row.dataset.details.split('|');

        const detailsBody = `
            <p><strong>Student Name:</strong> ${data[0]}</p>
            <p><strong>Sex:</strong> ${data[1]}</p>
            <p><strong>Birthdate:</strong> ${data[2]}</p>
            <p><strong>Address:</strong> ${data[3]}</p>
            <p><strong>Contact Info:</strong> ${data[4]}</p>
            <p><strong>Status:</strong> ${data[5]}</p>
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
        const studentId = btn.getAttribute('data-id');
        alert('Edit student with ID: ' + studentId);
        // TODO: Implement edit functionality
    });
});

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
    document.getElementById('archiveModal').style.display = 'flex';
});

// Close modals
document.querySelectorAll('.btn-close').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.closest('.modal').style.display = 'none';
    });
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