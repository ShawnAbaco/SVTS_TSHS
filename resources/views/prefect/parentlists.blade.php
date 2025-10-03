@extends('prefect.layout')

@section('content')
<div class="main-container">
<meta name="csrf-token" content="{{ csrf_token() }}">

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
        <h2>{{ $totalParents }}</h2>
        <p>Total Parents</p>
    </div>
    <div class="card">
        <h2>{{ $activeParents }}</h2>
        <p>Active Parents</p>
    </div>
    <div class="card">
        <h2 id="archivedCount">{{ $archivedParents }}</h2>
        <p>Archived Parents</p>
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
        <div class="dropdown-content">
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
        <tr data-parent-id="{{ $parent->parent_id }}" data-details="{{ $parent->parent_fname }} {{ $parent->parent_lname }}|{{ $parent->parent_relationship ?? 'N/A' }}|{{ $parent->parent_contactinfo ?? 'N/A' }}|{{ $parent->parent_birthdate ?? 'N/A' }}|{{ $parent->parent_email ?? 'N/A' }}">
          <td><input type="checkbox" class="rowCheckbox" value="{{ $parent->parent_id }}"></td>
          <td>{{ $parent->parent_id }}</td>
          <td>{{ $parent->parent_fname }}</td>
          <td>{{ $parent->parent_lname }}</td>
          <td>{{ ucfirst($parent->parent_sex) }}</td>
          <td>{{ $parent->parent_birthdate ? \Carbon\Carbon::parse($parent->parent_birthdate)->format('F j, Y') : 'N/A' }}</td>
          <td>{{ $parent->parent_email ?? 'N/A' }}</td>
          <td>{{ $parent->parent_contactinfo }}</td>
          <td>{{ $parent->parent_relationship ?? 'N/A' }}</td>
          <td>
            <span class="status-badge {{ $parent->status === 'active' ? 'status-active' : 'status-inactive' }}">
              {{ ucfirst($parent->status) }}
            </span>
          </td>
          <td>
            <button class="btn-primary edit-btn">✏️ Edit</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="11" style="text-align:center;">No active parents found</td>
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
      <form id="editParentForm" method="POST" action="">
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

  <!-- 🗃️ Archive Modal -->
  <div class="modal" id="archiveModal">
    <div class="modal-content">
      <div class="modal-header">
        🗃️ Archived Parents
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
                <th>First Name</th>
                <th>Last Name</th>
                <th>Sex</th>
                <th>Birthdate</th>
                <th>Email</th>
                <th>Contact Info</th>
                <th>Relationship</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <!-- Archived parents will be loaded here via AJAX -->
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
  const rows = document.querySelectorAll('#tableBody tr');
  
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// ==========================
// Select all checkboxes
// ==========================
document.getElementById('selectAll').addEventListener('change', function() {
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// ==========================
// Move to Archive (Trash)
// ==========================
document.getElementById('moveToTrashBtn').addEventListener('click', function() {
  const selected = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
    .map(cb => cb.value);
  
  if (!selected.length) {
    alert('Please select at least one parent.');
    return;
  }

  if (confirm(`Are you sure you want to move ${selected.length} parent(s) to archive?`)) {
    fetch('{{ route("parents.archive") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ parent_ids: selected })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload(); // Reload to update the table and counts
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while archiving parents.');
    });
  }
});

// ==========================
// Load Archived Parents
// ==========================
function loadArchivedParents() {
  fetch('{{ route("parents.archived") }}')
    .then(response => response.json())
    .then(parents => {
      const archiveTableBody = document.getElementById('archiveTableBody');
      archiveTableBody.innerHTML = '';

      if (parents.length === 0) {
        archiveTableBody.innerHTML = `
          <tr>
            <td colspan="10" style="text-align:center; padding:15px;">No archived parents found</td>
          </tr>
        `;
        return;
      }

      parents.forEach(parent => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td><input type="checkbox" class="archiveCheckbox" value="${parent.parent_id}"></td>
          <td>${parent.parent_id}</td>
          <td>${parent.parent_fname}</td>
          <td>${parent.parent_lname}</td>
          <td>${parent.parent_sex}</td>
          <td>${parent.parent_birthdate ? new Date(parent.parent_birthdate).toLocaleDateString() : 'N/A'}</td>
          <td>${parent.parent_email || 'N/A'}</td>
          <td>${parent.parent_contactinfo}</td>
          <td>${parent.parent_relationship || 'N/A'}</td>
          <td><span class="status-badge status-inactive">Inactive</span></td>
        `;
        archiveTableBody.appendChild(row);
      });

      // Update select all functionality for archived items
      updateArchiveSelectAll();
    })
    .catch(error => {
      console.error('Error loading archived parents:', error);
    });
}

// ==========================
// Update Archive Select All
// ==========================
function updateArchiveSelectAll() {
  const selectAllArchived = document.getElementById('selectAllArchived');
  const archivedCheckboxes = document.querySelectorAll('.archiveCheckbox');

  selectAllArchived.addEventListener('change', function() {
    archivedCheckboxes.forEach(cb => cb.checked = this.checked);
  });

  // Update select all when individual checkboxes change
  archivedCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const allChecked = Array.from(archivedCheckboxes).every(cb => cb.checked);
      selectAllArchived.checked = allChecked;
    });
  });
}

// ==========================
// Restore Archived Parents
// ==========================
document.getElementById('restoreArchiveBtn').addEventListener('click', function() {
  const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
    .map(cb => cb.value);
  
  if (!selected.length) {
    alert('Please select at least one parent to restore.');
    return;
  }

  if (confirm(`Are you sure you want to restore ${selected.length} parent(s)?`)) {
    fetch('{{ route("parents.restore") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ parent_ids: selected })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        loadArchivedParents(); // Reload the archived list
        location.reload(); // Reload main page to update counts
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while restoring parents.');
    });
  }
});

// ==========================
// Delete Archived Parents Permanently
// ==========================
document.getElementById('deleteArchiveBtn').addEventListener('click', function() {
  const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
    .map(cb => cb.value);
  
  if (!selected.length) {
    alert('Please select at least one parent to delete permanently.');
    return;
  }

  if (confirm(`WARNING: This will permanently delete ${selected.length} parent(s). This action cannot be undone!`)) {
    fetch('{{ route("parents.destroy.permanent") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ parent_ids: selected })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        loadArchivedParents(); // Reload the archived list
        // Update archived count
        fetch('{{ route("parents.archived.count") }}')
          .then(response => response.json())
          .then(data => {
            document.getElementById('archivedCount').innerText = data.count;
          });
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting parents.');
    });
  }
});

// ==========================
// Open Archive Modal
// ==========================
document.getElementById('archiveBtn').addEventListener('click', function() {
  loadArchivedParents();
  document.getElementById('archiveModal').style.display = 'flex';
});

// ==========================
// Close Archive Modal
// ==========================
document.getElementById('closeArchive').addEventListener('click', function() {
  document.getElementById('archiveModal').style.display = 'none';
});

// ==========================
// Archive Search
// ==========================
document.getElementById('archiveSearch').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#archiveTableBody tr');
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// ==========================
// Edit Modal Functionality
// ==========================
document.addEventListener('DOMContentLoaded', function() {
  const editButtons = document.querySelectorAll('.edit-btn');
  const editModal = document.getElementById('editModal');
  const closeEditModal = document.getElementById('closeEditModal');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const editForm = document.getElementById('editParentForm');

  editButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const row = this.closest('tr');
      const parentId = row.children[1].innerText.trim();
      const fname = row.children[2].innerText.trim();
      const lname = row.children[3].innerText.trim();
      const sex = row.children[4].innerText.trim();
      const birthdate = row.children[5].innerText.trim();
      const email = row.children[6].innerText.trim();
      const contact = row.children[7].innerText.trim();
      const relationship = row.children[8].innerText.trim();
      const status = row.children[9].innerText.trim();

      // Convert birthdate to YYYY-MM-DD format if not N/A
      let birthdateInput = '';
      if (birthdate !== 'N/A') {
        birthdateInput = new Date(birthdate).toISOString().split('T')[0];
      }

      // Fill form
      document.getElementById('edit_parent_id').value = parentId;
      document.getElementById('edit_parent_fname').value = fname;
      document.getElementById('edit_parent_lname').value = lname;
      document.getElementById('edit_parent_sex').value = sex.toLowerCase();
      document.getElementById('edit_parent_birthdate').value = birthdateInput;
      document.getElementById('edit_parent_email').value = email === 'N/A' ? '' : email;
      document.getElementById('edit_parent_contactinfo').value = contact;
      document.getElementById('edit_parent_relationship').value = relationship === 'N/A' ? '' : relationship;
      document.getElementById('edit_parent_status').value = status.toLowerCase();

      // Set form action
      editForm.action = `{{ url('prefect/parents/update') }}/${parentId}`;

      // Show modal
      editModal.style.display = 'flex';
    });
  });

  // Close modal
  [closeEditModal, cancelEditBtn].forEach(btn => {
    btn.addEventListener('click', function() {
      editModal.style.display = 'none';
    });
  });
});

// ==========================
// Load archived count on page load
// ==========================
document.addEventListener('DOMContentLoaded', function() {
  fetch('{{ route("parents.archived.count") }}')
    .then(response => response.json())
    .then(data => {
      document.getElementById('archivedCount').innerText = data.count;
    })
    .catch(error => {
      console.error('Error loading archived count:', error);
    });
});
</script>

@endsection