@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Student Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      <a href="{{ route('create.student') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Student
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

  <!-- Student Table -->
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
          <th>Address</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($students as $student)
        <tr>
          <td><input type="checkbox" class="rowCheckbox"></td>
          <td>{{ $student->student_id }}</td>
          <td>{{ $student->student_fname }}</td>
          <td>{{ $student->student_lname }}</td>
          <td>{{ ucfirst($student->student_sex) }}</td>
          <td>{{ \Carbon\Carbon::parse($student->student_birthdate)->format('Y-m-d') }}</td>
          <td>{{ $student->student_address }}</td>
          <td>{{ $student->student_contactinfo }}</td>
          <td>{{ ucfirst($student->status) }}</td>
          <td>
            <button class="btn-primary">✏️ Edit</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align:center;">⚠️ No students found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="pagination-wrapper">
    <div class="pagination-summary">
      Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
    </div>
    <div class="pagination-links">
      {{ $students->links() }}
    </div>
  </div>

  <!-- ✏️ Edit Student Modal -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <button class="close-btn" id="closeEditModal">✖</button>
      <h2>Edit Student</h2>

      <form id="editStudentForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="student_id" id="edit_student_id">

        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="student_fname" id="edit_student_fname" required>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="student_lname" id="edit_student_lname" required>
          </div>
          <div class="form-group">
            <label>Sex</label>
            <input type="text" name="student_sex" id="edit_student_sex" required>
          </div>
          <div class="form-group">
            <label>Birthdate</label>
            <input type="date" name="student_birthdate" id="edit_student_birthdate" required>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="student_address" id="edit_student_address" required>
          </div>
          <div class="form-group">
            <label>Contact Info</label>
            <input type="text" name="student_contactinfo" id="edit_student_contactinfo" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <input type="text" name="status" id="edit_student_status" required>
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn-primary">💾 Save Changes</button>
          <button type="button" class="btn-secondary" id="cancelEditBtn">❌ Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 🗃️ Archive Modal -->
  <div class="modal" id="archiveModal">
    <div class="modal-content">
      <div class="modal-header">🗃️ Archived Students</div>
      <div class="modal-body">
        <div class="modal-actions">
          <label class="select-all-label">
            <input type="checkbox" id="selectAllArchived">
            <span>Select All</span>
          </label>
          <div class="search-container">
            <input type="search" id="archiveSearch" placeholder="🔍 Search archived..." class="search-input">
          </div>
        </div>

        <div class="archive-table-container">
          <table class="archive-table">
            <thead>
              <tr>
                <th>✔</th>
                <th>ID</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Date Archived</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>001</td>
                <td>Mark Dela Cruz</td>
                <td>Inactive</td>
                <td>2025-09-22</td>
              </tr>
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>002</td>
                <td>Anna Reyes</td>
                <td>Inactive</td>
                <td>2025-09-23</td>
              </tr>
            </tbody>
          </table>
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
// 🔍 Search
document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#tableBody tr');
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// ✅ Select All
document.getElementById('selectAll').addEventListener('change', function() {
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// 🗑️ Move to Trash
document.getElementById('moveToTrashBtn').addEventListener('click', () => {
  const selected = document.querySelectorAll('.rowCheckbox:checked');
  if (!selected.length) return alert('Please select at least one student.');
  alert(`${selected.length} student(s) moved to trash.`);
});

// 🗃️ Archive Modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});
document.getElementById('closeArchive').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'none';
});
</script>
<script>
// ✏️ Edit Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
  const editButtons = document.querySelectorAll('.btn-primary:not(#createBtn)');
  const editModal = document.getElementById('editModal');
  const closeEditModal = document.getElementById('closeEditModal');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const editForm = document.getElementById('editStudentForm');

  // 🎯 When "Edit" Button Clicked
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const row = this.closest('tr');
      const studentId = row.children[1].innerText.trim();
      const fname = row.children[2].innerText.trim();
      const lname = row.children[3].innerText.trim();
      const sex = row.children[4].innerText.trim();
      const birthdate = row.children[5].innerText.trim();
      const address = row.children[6].innerText.trim();
      const contact = row.children[7].innerText.trim();
      const status = row.children[8].innerText.trim();

      // 📝 Fill Form
      document.getElementById('edit_student_id').value = studentId;
      document.getElementById('edit_student_fname').value = fname;
      document.getElementById('edit_student_lname').value = lname;
      document.getElementById('edit_student_sex').value = sex.toLowerCase();
      document.getElementById('edit_student_birthdate').value = birthdate;
      document.getElementById('edit_student_address').value = address;
      document.getElementById('edit_student_contactinfo').value = contact;
      document.getElementById('edit_student_status').value = status.toLowerCase();

      // Set form action dynamically
      editForm.action = `/prefect/students/update/${studentId}`;

      // Show modal
      editModal.style.display = 'flex';
    });
  });

  // ❌ Close / Cancel Modal
  [closeEditModal, cancelEditBtn].forEach(btn => {
    btn.addEventListener('click', () => {
      editModal.style.display = 'none';
    });
  });
});
</script>

@endsection
