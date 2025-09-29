@extends('adviser.layout')

@section('content')
<div class="main-container">


  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Student Management</h2>
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
        <a href="#" id="violaitonAppointments">Violation Appointments</a>
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
          <tbody>
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

    // Search filter for main violation table
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr:not(.no-data-row)'); // Ignore the "No records found" row

    let visibleCount = 0;

    rows.forEach(row => {
        const studentName = row.cells[2].innerText.toLowerCase(); // Student Name column
        const studentID = row.cells[1].innerText.toLowerCase();   // ID column
        if(studentName.includes(filter) || studentID.includes(filter)) {
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
            newRow.innerHTML = `<td colspan="8" style="text-align:center; padding:15px;">⚠️ No records found</td>`;
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
// Row click -> Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    // Ignore if checkbox or edit button is clicked
    if(e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

    const data = row.dataset.details.split('|');

    const detailsBody = `
      <p><strong>Student:</strong> ${data[0]}</p>
      <p><strong>Offense:</strong> ${data[1]}</p>
      <p><strong>Sanction:</strong> ${data[2]}</p>
      <p><strong>Date:</strong> ${data[3]}</p>
      <p><strong>Time:</strong> ${data[4]}</p>
    `;

    document.getElementById('detailsBody').innerHTML = detailsBody;
    document.getElementById('detailsModal').style.display = 'flex';
    document.getElementById('detailsModal').classList.add('show');
    btn.closest('.modal').classList.remove('show');


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
  // TODO: open your schedule modal or redirect to schedule setup
});

// Send SMS Button
document.getElementById('sendSmsBtn').addEventListener('click', () => {
  alert('Trigger SMS sending here.');
  // TODO: implement SMS sending via backend
});


  // Close modals
  document.querySelectorAll('.btn-close').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.modal').style.display = 'none';
    });
  });

  // Edit button
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const row = btn.closest('tr');
      const data = row.dataset.details.split('|');
      document.getElementById('editStudentName').value = data[0];
      document.getElementById('editOffense').value = data[1];
      document.getElementById('editSanction').value = data[2];
      document.getElementById('editDate').value = data[3];
      document.getElementById('editTime').value = data[4];
      document.getElementById('editModal').style.display = 'flex';
    });
  });

  // Open modals
  document.getElementById('createAnecBtn').addEventListener('click', () => {
    document.getElementById('anecModal').style.display = 'flex';
  });
  document.getElementById('archiveBtn').addEventListener('click', () => {
    document.getElementById('archiveModal').style.display = 'flex';
  });

  document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent row click event
    const dropdown = btn.parentElement;
    dropdown.classList.toggle('show');
  });
});

// Close dropdown if clicked outside
window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// Close modal
document.querySelectorAll('#archiveModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

// Select all checkboxes
  // Get the select all checkbox and all individual checkboxes
  const selectAllArchived = document.getElementById('selectAllArchived');
  const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');

  // When the select all checkbox changes
  selectAllArchived.addEventListener('change', () => {
    const isChecked = selectAllArchived.checked;
    archivedCheckboxes.forEach(checkbox => {
      checkbox.checked = isChecked;
    });
  });

  // Optional: If any individual checkbox is unchecked, uncheck "Select All"
  archivedCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      if (!checkbox.checked) {
        selectAllArchived.checked = false;
      } else {
        // If all checkboxes are checked, check the "Select All" box
        const allChecked = Array.from(archivedCheckboxes).every(cb => cb.checked);
        selectAllArchived.checked = allChecked;
      }
    });
  });

// Search filter
document.getElementById('archiveSearch').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#archiveTableBody tr').forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// Restore selected
document.getElementById('restoreArchiveBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archiveCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to restore.');
  alert(`${selected.length} record(s) restored.`);
  // TODO: Add AJAX call to restore records
});

// Delete selected
document.getElementById('deleteArchiveBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archiveCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to delete.');
  if(confirm('This will permanently delete the selected record(s). Are you sure?')) {
    alert(`${selected.length} record(s) deleted permanently.`);
    // TODO: Add AJAX call to delete records
  }
});



</script>
@endsection
