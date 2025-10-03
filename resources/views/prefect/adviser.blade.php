@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Adviser Management</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by adviser name or ID..." id="searchInput">
      <a href="{{ route('create.adviser') }}" class="btn-primary" id="createBtn">
        <i class="fas fa-plus"></i> Add Adviser
      </a>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="summary">
    <div class="card">
        <h2>{{ $totalAdvisers }}</h2>
        <p>Total Adviser</p>
    </div>
    <div class="card">
        <h2>{{ $grade11Advisers }}</h2>
        <p>Grade 11 Advisers</p>
    </div>
    <div class="card">
        <h2>{{ $grade12Advisers }}</h2>
        <p>Grade 12 Advisers</p>
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

  <!-- Adviser Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th></th>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Section</th>
          <th>Grade Level</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($advisers as $adviser)
        <tr data-details="{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}|{{ $adviser->adviser_section }}|{{ $adviser->adviser_gradelevel }}|{{ $adviser->adviser_email }}|{{ $adviser->adviser_contactinfo }}">
          <td><input type="checkbox" class="rowCheckbox"></td>
          <td>{{ $adviser->adviser_id }}</td>
          <td>{{ $adviser->adviser_fname }}</td>
          <td>{{ $adviser->adviser_lname }}</td>
          <td>{{ $adviser->adviser_section }}</td>
          <td>{{ $adviser->adviser_gradelevel }}</td>
          <td>{{ $adviser->adviser_email }}</td>
          <td>{{ $adviser->adviser_contactinfo }}</td>
          <td>
            <button class="btn-primary">✏️ Edit</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="text-align:center;">No advisers found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="pagination-wrapper">
    <div class="pagination-summary">
      Showing {{ $advisers->firstItem() }} to {{ $advisers->lastItem() }} of {{ $advisers->total() }} results
    </div>
    <div class="pagination-links">
      {{ $advisers->links() }}
    </div>
  </div>


  <!-- ✏️ Edit Adviser Modal -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <button class="close-btn" id="closeEditModal">✖</button>
      <h2>Edit Adviser</h2>

      <form id="editAdviserForm" method="POST" action="{{ route('advisers.update', ['id' => '__id__']) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="adviser_id" id="edit_adviser_id">

        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="adviser_fname" id="edit_adviser_fname" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="adviser_lname" id="edit_adviser_lname" required>
          </div>

          <div class="form-group">
            <label>Section</label>
            <input type="text" name="adviser_section" id="edit_adviser_section" required>
          </div>

          <div class="form-group">
            <label>Grade Level</label>
            <input type="text" name="adviser_gradelevel" id="edit_adviser_gradelevel" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="adviser_email" id="edit_adviser_email">
          </div>

          <div class="form-group">
            <label>Contact Info</label>
            <input type="text" name="adviser_contactinfo" id="edit_adviser_contactinfo">
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
      <div class="modal-header">🗃️ Archived Advisers</div>
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
                <th>Adviser Name</th>
                <th>Section</th>
                <th>Grade Level</th>
                <th>Date Archived</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>A003</td>
                <td>Maria Santos</td>
                <td>7-A</td>
                <td>Grade 7</td>
                <td>2025-09-22</td>
              </tr>
              <tr>
                <td><input type="checkbox" class="archiveCheckbox"></td>
                <td>A004</td>
                <td>Juan Reyes</td>
                <td>8-B</td>
                <td>Grade 8</td>
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
// ==========================
// ✏️ Edit Adviser Modal Logic
// ==========================
document.querySelectorAll('#tableBody .btn-primary').forEach(editBtn => {
  editBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    const row = e.target.closest('tr');
    const cells = row.querySelectorAll('td');

    const id = cells[1].innerText.trim();

    // ✅ Update form action
    const form = document.getElementById('editAdviserForm');
    form.action = "{{ route('advisers.update', ['id' => '__id__']) }}".replace('__id__', id);

    // ✅ Fill form fields
    document.getElementById('edit_adviser_id').value = id;
    document.getElementById('edit_adviser_fname').value = cells[2].innerText.trim();
    document.getElementById('edit_adviser_lname').value = cells[3].innerText.trim();
    document.getElementById('edit_adviser_section').value = cells[4].innerText.trim();
    document.getElementById('edit_adviser_gradelevel').value = cells[5].innerText.trim();
    document.getElementById('edit_adviser_email').value = cells[6].innerText.trim();
    document.getElementById('edit_adviser_contactinfo').value = cells[7].innerText.trim();

    // ✅ Show modal
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
  });
});

// ✅ Close / Cancel Modal
document.getElementById('closeEditModal').addEventListener('click', closeEditModal);
document.getElementById('cancelEditBtn').addEventListener('click', closeEditModal);

function closeEditModal() {
  const modal = document.getElementById('editModal');
  modal.style.display = 'none';
  modal.classList.remove('show');
}
</script>

@endsection
