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
      <h2>{{ $parents->total() }}</h2>
      <p>Total Parents</p>
    </div>
    <div class="card">
      <h2>{{ $parents->where('status', 'active')->count() }}</h2>
      <p>Active Parents</p>
    </div>
    <div class="card">
    <h2 id="archivedCount">0</h2>
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
      @php
        $activeParents = $parents->where('status', 'active');
      @endphp
      
      @forelse($activeParents as $parent)
      <tr data-parent-id="{{ $parent->parent_id }}" data-details="{{ $parent->parent_fname }} {{ $parent->parent_lname }}|{{ $parent->parent_relationship ?? 'N/A' }}|{{ $parent->parent_contactinfo ?? 'N/A' }}|{{ $parent->parent_birthdate ?? 'N/A' }}|{{ $parent->parent_email ?? 'N/A' }}">
        <td><input type="checkbox" class="rowCheckbox" value="{{ $parent->parent_id }}"></td>
        <td>{{ $parent->parent_id }}</td>
        <td>{{ $parent->parent_fname }}</td>
        <td>{{ $parent->parent_lname }}</td>
        <td>{{ ucfirst($parent->parent_sex) }}</td>
        <td>{{ \Carbon\Carbon::parse($parent->parent_birthdate)->format('F j, Y') }}</td>
        <td>{{ $parent->parent_email ?? 'N/A' }}</td>
        <td>{{ $parent->parent_contactinfo }}</td>
        <td>{{ $parent->parent_relationship ?? 'N/A' }}</td>
        <td>
          <span class="status-badge status-active">
            Active
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
      Showing {{ $activeParents->count() ? '1' : '0' }} to {{ $activeParents->count() }} of {{ $activeParents->count() }} results
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
        📄 Parent Details
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
  const tableBody = document.getElementById('tableBody');
  const rows = tableBody.querySelectorAll('tr:not(.no-data-row)');

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

  const noDataRow = tableBody.querySelector('.no-data-row');
  if(visibleCount === 0) {
    if(!noDataRow) {
      const newRow = document.createElement('tr');
      newRow.classList.add('no-data-row');
      newRow.innerHTML = `<td colspan="11" style="text-align:center; padding:15px;">⚠️ No records found</td>`;
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

// ==========================
// Move to Archive (Trash)
// ==========================
document.getElementById('moveToTrashBtn').addEventListener('click', () => {
  const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
  const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

  if (selectedIds.length === 0) {
    alert('Please select at least one record.');
    return;
  }

  if(confirm(`Are you sure you want to move ${selectedIds.length} parent(s) to archive?`)) {
    // Send AJAX request to archive parents
    fetch("{{ route('parents.archive') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        parent_ids: selectedIds
      })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        alert(data.message);
        // Remove the archived rows from the table without reloading
        selectedCheckboxes.forEach(checkbox => {
          const row = checkbox.closest('tr');
          row.remove();
        });
        
        // Update the table if no rows left
        const tableBody = document.getElementById('tableBody');
        const remainingRows = tableBody.querySelectorAll('tr:not(.no-data-row)');
        if (remainingRows.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="11" style="text-align:center;">No active parents found</td></tr>';
        }
        
        // Update summary cards
        updateSummaryCards(-selectedIds.length);
      } else {
        alert('Error moving parents to archive.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error moving parents to archive.');
    });
  }
});

// Function to update summary cards
function updateSummaryCards(change) {
  // This would ideally be done via AJAX to get updated counts
  // For now, we'll just reload the page to get accurate counts
  setTimeout(() => {
    location.reload();
  }, 1000);
}

// ==========================
// Load Archived Parents
// ==========================
function loadArchivedParents() {
  fetch("{{ route('parents.archived') }}")
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
        row.setAttribute('data-parent-id', parent.parent_id);
        row.innerHTML = `
          <td><input type="checkbox" class="archiveCheckbox" value="${parent.parent_id}"></td>
          <td>${parent.parent_id}</td>
          <td>${parent.parent_fname}</td>
          <td>${parent.parent_lname}</td>
          <td>${parent.parent_sex}</td>
          <td>${parent.parent_birthdate}</td>
          <td>${parent.parent_email || 'N/A'}</td>
          <td>${parent.parent_contactinfo}</td>
          <td>${parent.parent_relationship || 'N/A'}</td>
          <td><span class="status-badge status-inactive">${parent.status}</span></td>
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
}

// ==========================
// Row click -> Details Modal
// ==========================
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    if(e.target.type === 'checkbox' || e.target.classList.contains('edit-btn')) return;

    const data = row.dataset.details.split('|');

    const detailsBody = `
      <p><strong>Parent Name:</strong> ${data[0]}</p>
      <p><strong>Relationship:</strong> ${data[1]}</p>
      <p><strong>Contact Info:</strong> ${data[2]}</p>
      <p><strong>Birthdate:</strong> ${data[3]}</p>
      <p><strong>Email:</strong> ${data[4]}</p>
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
// Open Archive Modal
// ==========================
document.getElementById('archiveBtn').addEventListener('click', () => {
  loadArchivedParents();
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
// Archive modal functionality
// ==========================
document.querySelectorAll('#archiveModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
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
  const selectedCheckboxes = document.querySelectorAll('.archiveCheckbox:checked');
  const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

  if(selectedIds.length === 0) {
    alert('Please select at least one record to restore.');
    return;
  }

  if(confirm(`Are you sure you want to restore ${selectedIds.length} parent(s)?`)) {
    fetch("{{ route('parents.restore') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        parent_ids: selectedIds
      })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        alert(data.message);
        // Remove restored rows from archive modal
        selectedCheckboxes.forEach(checkbox => {
          const row = checkbox.closest('tr');
          row.remove();
        });
        
        // Update archive table if no rows left
        const archiveTableBody = document.getElementById('archiveTableBody');
        const remainingRows = archiveTableBody.querySelectorAll('tr');
        if (remainingRows.length === 0) {
          archiveTableBody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:15px;">No archived parents found</td></tr>';
        }
      } else {
        alert('Error restoring parents.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error restoring parents.');
    });
  }
});

// Delete archive permanently
document.getElementById('deleteArchiveBtn').addEventListener('click', () => {
  const selectedCheckboxes = document.querySelectorAll('.archiveCheckbox:checked');
  const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

  if(selectedIds.length === 0) {
    alert('Please select at least one record to delete.');
    return;
  }

  if(confirm('This will permanently delete the selected parent(s). Are you sure?')) {
    fetch("{{ route('parents.destroy.permanent') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        parent_ids: selectedIds
      })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        alert(data.message);
        // Remove deleted rows from archive modal
        selectedCheckboxes.forEach(checkbox => {
          const row = checkbox.closest('tr');
          row.remove();
        });
        
        // Update archive table if no rows left
        const archiveTableBody = document.getElementById('archiveTableBody');
        const remainingRows = archiveTableBody.querySelectorAll('tr');
        if (remainingRows.length === 0) {
          archiveTableBody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:15px;">No archived parents found</td></tr>';
        }
      } else {
        alert('Error deleting parents.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error deleting parents.');
    });
  }
});

// ==========================
// ✏️ Edit Modal Logic
// ==========================
document.querySelectorAll('#tableBody .edit-btn').forEach(editBtn => {
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

fetch('/parents/archived/count')
  .then(response => response.json())
  .then(data => {
    document.getElementById('archivedCount').innerText = data.count;
  });

</script>

@endsection