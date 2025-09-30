@extends('prefect.layout')
@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Parent Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
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
          <a href="#" id="violaitonAppointments">Violation Appointments</a>
          <a href="#" id="violationAnecdotals">Violation Anecdotals</a>
        </div>
      </div>
    </div>

    <div class="right-controls">
      <button class="btn-danger" id="moveToTrashBtn">🗑️ Move Selected to Trash</button>
    </div>
  </div>

  <!-- Table -->
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
        <tr data-details="{{ $parent->parent_fname }} {{ $parent->parent_lname }}|{{ $parent->parent_relationship ?? 'N/A' }}|{{ $parent->parent_contactinfo ?? 'N/A' }}|{{ $parent->parent_birthdate ?? 'N/A' }}|{{ $parent->parent_email ?? 'N/A' }}">
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
            <button class="btn-primary">✏️ Edit</button>
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
    <div class="pagination-wrapper">
      <div class="pagination-summary">
        Showing {{ $parents->firstItem() }} to {{ $parents->lastItem() }} of {{ $parents->total() }} results
      </div>
      <div class="pagination-links">
        {{ $parents->links() }}
      </div>
    </div>
  </div>


  <!-- ✏️ Edit Parent Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <button class="close-btn" id="closeEditModal">✖</button>
    <h2>Edit Parent</h2>
 <form id="editParentForm" method="POST" action="{{ route('parents.update', ['id' => '__id__']) }}">
  @csrf
  @method('PUT')
  <input type="hidden" name="parent_id" id="edit_parent_id">

  <div class="form-grid">
    <div class="form-group">
      <label>First Name</label>
      <input type="text" name="parent_fname" id="edit_parent_fname" required>
    </div>

    <div class="form-group">
      <label>Last Name</label>
      <input type="text" name="parent_lname" id="edit_parent_lname" required>
    </div>

    <div class="form-group">
      <label>Sex</label>
      <select name="parent_sex" id="edit_parent_sex" required>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
    </div>

    <div class="form-group">
      <label>Birthdate</label>
      <input type="date" name="parent_birthdate" id="edit_parent_birthdate" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="parent_email" id="edit_parent_email">
    </div>

    <div class="form-group">
      <label>Contact Info</label>
      <input type="text" name="parent_contactinfo" id="edit_parent_contactinfo">
    </div>

    <div class="form-group">
      <label>Relationship</label>
      <input type="text" name="parent_relationship" id="edit_parent_relationship">
    </div>

    <div class="form-group">
      <label>Status</label>
      <select name="status" id="edit_parent_status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  </div>

  <div class="actions">
    <button type="submit" class="btn-primary">💾 Save Changes</button>
    <button type="button" class="btn-secondary" id="cancelEditBtn">❌ Cancel</button>
  </div>
</form>

  </div>
</div>



  <!-- 📝 Details Modal -->
  <div class="modal" id="detailsModal">
    <div class="modal-content">
      <div class="modal-header">
        📄 Violation Details
      </div>
      <div class="modal-body" id="detailsBody">
        <!-- Filled via JS -->
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
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>3</td>
                <td>Mark Dela Cruz</td>
                <td>Tardiness</td>
                <td>2025-09-22</td>
              </tr>
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>4</td>
                <td>Anna Reyes</td>
                <td>Cutting Classes</td>
                <td>2025-09-23</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-note">
          ⚠️ Note: Deleting records will permanently remove them.
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" id="restoreArchiveBtn">🔄 Restore</button>
          <button class="btn-danger" id="deleteArchiveBtn">🗑️ Delete</button>
          <button class="btn-close" id="closeArchive">❌ Close</button>
        </div>

      </div>
    </div>
  </div>

</div>


<script>
// ==========================
// Search filter for main table
// ==========================
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
      newRow.innerHTML = `<td colspan="8" style="text-align:center; padding:15px;">⚠️ No records found</td>`;
      tableBody.appendChild(newRow);
    }
  } else {
    if(noDataRow) noDataRow.remove();
  }
});

// ==========================
// Select all checkboxes
// ==========================
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
  }
});

// ==========================
// Row click -> Details Modal
// ==========================
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    if(e.target.type === 'checkbox') return;

    const data = row.dataset.details.split('|');

    const detailsBody = `
      <p><strong>Student:</strong> ${data[0]}</p>
      <p><strong>Offense:</strong> ${data[1]}</p>
      <p><strong>Sanction:</strong> ${data[2]}</p>
      <p><strong>Date:</strong> ${data[3]}</p>
      <p><strong>Time:</strong> ${data[4]}</p>
    `;

    document.getElementById('detailsBody').innerHTML = detailsBody;
    const detailsModal = document.getElementById('detailsModal');
    detailsModal.style.display = 'flex';
    detailsModal.classList.add('show');
  });
});

// Close Details Modal
document.querySelectorAll('#detailsModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
    btn.closest('.modal').classList.remove('show');
  });
});

// ==========================
// Toolbar buttons
// ==========================
document.getElementById('setScheduleBtn').addEventListener('click', () => {
  alert('Open schedule setup form or modal here.');
});

document.getElementById('sendSmsBtn').addEventListener('click', () => {
  alert('Trigger SMS sending here.');
});

// Close modals (generic)
document.querySelectorAll('.btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
    btn.closest('.modal').classList.remove('show');
  });
});

// ==========================
// Open modals
// ==========================
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// ==========================
// Dropdown
// ==========================
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const dropdown = btn.parentElement;
    dropdown.classList.toggle('show');
  });
});

window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// ==========================
// Archive modal
// ==========================
document.querySelectorAll('#archiveModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

// Select all archived checkboxes
const selectAllArchived = document.getElementById('selectAllArchived');
const archivedCheckboxes = document.querySelectorAll('.archiveCheckbox');

selectAllArchived.addEventListener('change', () => {
  const isChecked = selectAllArchived.checked;
  archivedCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
});

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

// Archive search
document.getElementById('archiveSearch').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#archiveTableBody tr').forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// Restore archive
document.getElementById('restoreArchiveBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archiveCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to restore.');
  alert(`${selected.length} record(s) restored.`);
});

// Delete archive
document.getElementById('deleteArchiveBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archiveCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to delete.');
  if(confirm('This will permanently delete the selected record(s). Are you sure?')) {
    alert(`${selected.length} record(s) deleted permanently.`);
  }
});


// ==========================
// ✏️ Edit Modal Logic (Fixed)
// ==========================
document.querySelectorAll('#tableBody .btn-primary').forEach(editBtn => {
  editBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent row click opening details modal
    const row = e.target.closest('tr');
    const cells = row.querySelectorAll('td');

    // Get Parent ID (assuming column 1 = ID)
    const id = cells[1].innerText.trim();

    // ✅ Update form action dynamically
    const form = document.getElementById('editParentForm');
    form.action = "{{ route('parents.update', ['id' => '__id__']) }}".replace('__id__', id);

    // ✅ Fill form fields from row
    document.getElementById('edit_parent_id').value = id;
    document.getElementById('edit_parent_fname').value = cells[2].innerText.trim();
    document.getElementById('edit_parent_lname').value = cells[3].innerText.trim();
    document.getElementById('edit_parent_sex').value = cells[4].innerText.toLowerCase();
    document.getElementById('edit_parent_birthdate').value = cells[5].innerText.trim();

    const email = cells[6].innerText.trim();
    document.getElementById('edit_parent_email').value = (email === 'N/A' ? '' : email);

    document.getElementById('edit_parent_contactinfo').value = cells[7].innerText.trim();

    const relationship = cells[8].innerText.trim();
    document.getElementById('edit_parent_relationship').value = (relationship === 'N/A' ? '' : relationship);

    document.getElementById('edit_parent_status').value = cells[9].innerText.toLowerCase();

    // ✅ Show modal
    const editModal = document.getElementById('editModal');
    editModal.style.display = 'flex';
    editModal.classList.add('show');
  });
});

// ✅ Close / Cancel Edit Modal
document.getElementById('closeEditModal').addEventListener('click', closeEditModal);
document.getElementById('cancelEditBtn').addEventListener('click', closeEditModal);

function closeEditModal() {
  const modal = document.getElementById('editModal');
  modal.style.display = 'none';
  modal.classList.remove('show');
}


</script>

@endsection
