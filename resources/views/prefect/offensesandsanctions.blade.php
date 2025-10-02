@extends('prefect.layout')

@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Offense and Sanctions</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by offense type or description..." id="searchInput">
      <button class="btn-primary" id="createBtn">➕ Add Offense with sanction</button>
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
            <td>{{ $loop->iteration }}</td>
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
      <div class="modal-header">🗃️ Archived Offenses</div>
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
                <th>Offense Type</th>
                <th>Description</th>
                <th>Sanctions</th>
                <th>Date Archived</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <tr>
                <td><input type="checkbox" class="archivedCheckbox"></td>
                <td>O001</td>
                <td>Tardiness</td>
                <td>Late to class</td>
                <td>Warning, Parent Notification</td>
                <td>2025-09-22</td>
              </tr>
              <tr>
                <td><input type="checkbox" class="archivedCheckbox"></td>
                <td>O002</td>
                <td>Cutting Classes</td>
                <td>Unauthorized absence</td>
                <td>Conference with Adviser</td>
                <td>2025-09-23</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-note">⚠️ Note: Deleting records will permanently remove them.</div>

        <div class="modal-footer">
          <button class="btn-secondary" id="restoreArchivedBtn">🔄 Restore</button>
          <button class="btn-danger" id="deleteArchivedBtn">🗑️ Delete</button>
          <button class="btn-close" id="closeArchive">❌ Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 📝 Create Anecdotal Modal (if needed) -->
  <div class="modal" id="anecModal">
    <div class="modal-content">
      <div class="modal-header">📝 Create Anecdotal Record</div>
      <div class="modal-body">
        <p>Anecdotal record form would go here...</p>
      </div>
      <div class="modal-footer">
        <button class="btn-primary">Save</button>
        <button class="btn-close">❌ Close</button>
      </div>
    </div>
  </div>

  <!-- 🔔 Notification Modal -->
  <div class="modal" id="notificationModal">
    <div class="modal-content notification-modal-content">
      <div class="modal-header notification-modal-header">
        <div class="notification-header-content">
          <span id="notificationIcon">🔔</span>
          <span id="notificationTitle">Notification</span>
        </div>
      </div>
      <div class="modal-body notification-modal-body" id="notificationBody">
        <!-- Content filled dynamically via JS -->
      </div>
      <div class="modal-footer notification-modal-footer">
        <div class="notification-buttons-container">
          <button class="btn-primary" id="notificationYesBtn">Yes</button>
          <button class="btn-secondary" id="notificationNoBtn">No</button>
          <button class="btn-close" id="notificationCloseBtn">Close</button>
        </div>
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

// ✅ Move to Trash - Now shows confirmation modal
document.getElementById('moveToTrashBtn')?.addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.rowCheckbox:checked')];
  if(!selected.length) {
    showNotification('⚠️ No Selection', 'Please select at least one offense record to move to trash.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
    return;
  }

  showNotification('🗑️ Move to Trash', `Are you sure you want to move ${selected.length} offense record(s) to trash?`, 'confirm', {
    yesText: 'Yes, Move',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to move to trash
      setTimeout(() => {
        showNotification('✅ Success', `${selected.length} offense record(s) moved to trash successfully.`, 'success', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
            // Optionally refresh the page or update the table
          }
        });
      }, 500);
    },
    onNo: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
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

// 🔹 Set Schedule Button
document.getElementById('setScheduleBtn')?.addEventListener('click', () => {
  showNotification('📅 Set Schedule', 'Open schedule setup form or modal here.', 'info', {
    yesText: 'OK',
    noText: null,
    onYes: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// 🔹 Send SMS Button
document.getElementById('sendSmsBtn')?.addEventListener('click', () => {
  showNotification('📩 Send SMS', 'Are you sure you want to send an SMS about this offense?', 'confirm', {
    yesText: 'Send SMS',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to send SMS
      setTimeout(() => {
        showNotification('✅ Success', 'SMS sent successfully!', 'success', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
          }
        });
      }, 500);
    },
    onNo: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// 🔹 Edit Button
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    const [offenseType, offenseDescription, sanctions] = btn.closest('tr').dataset.details.split('|');
    showNotification('✏️ Edit Offense', `Edit offense: ${offenseType}\nDescription: ${offenseDescription}\nSanctions: ${sanctions}`, 'info', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
        // TODO: Implement actual edit modal functionality
      }
    });
  });
});

// 🔹 Add Violation Button
document.getElementById('createBtn')?.addEventListener('click', () => {
  showNotification('➕ Add Violation', 'Open violation creation form.', 'info', {
    yesText: 'OK',
    noText: null,
    onYes: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// 🔹 Open Create Anecdotal Modal
document.getElementById('createAnecBtn')?.addEventListener('click', () => {
  document.getElementById('anecModal').style.display = 'flex';
});

// 🔹 Open Archive Modal
document.getElementById('archiveBtn')?.addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// 🔹 Close Archive Modal
document.getElementById('closeArchive')?.addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'none';
});

// 🔹 Dropdown toggle
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    btn.parentElement.classList.toggle('show');
  });
});

window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

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

// 🔹 Restore Archived - Now shows confirmation modal
document.getElementById('restoreArchivedBtn')?.addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(!selected.length) {
    showNotification('⚠️ No Selection', 'Please select at least one record to restore.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
    return;
  }

  showNotification('🔄 Restore Records', `Are you sure you want to restore ${selected.length} offense record(s)?`, 'confirm', {
    yesText: 'Yes, Restore',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to restore records
      setTimeout(() => {
        showNotification('✅ Success', `${selected.length} offense record(s) restored successfully.`, 'success', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
            // Optionally refresh the page or update the table
          }
        });
      }, 500);
    },
    onNo: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// 🔹 Delete Archived - Now shows confirmation modal
document.getElementById('deleteArchivedBtn')?.addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(!selected.length) {
    showNotification('⚠️ No Selection', 'Please select at least one record to delete.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
    return;
  }

  showNotification('🗑️ Delete Records', `This will permanently delete ${selected.length} offense record(s). This action cannot be undone. Are you sure?`, 'danger', {
    yesText: 'Yes, Delete',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to delete records
      setTimeout(() => {
        showNotification('✅ Success', `${selected.length} offense record(s) deleted permanently.`, 'success', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
            // Optionally refresh the page or update the table
          }
        });
      }, 500);
    },
    onNo: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// 🔹 Close modals when clicking outside
window.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal')) {
    e.target.style.display = 'none';
  }
});

// ================= NOTIFICATION MODAL FUNCTIONALITY =================

// Notification modal function
function showNotification(title, message, type = 'info', options = {}) {
  const modal = document.getElementById('notificationModal');
  const notificationTitle = document.getElementById('notificationTitle');
  const notificationBody = document.getElementById('notificationBody');
  const notificationIcon = document.getElementById('notificationIcon');
  const yesBtn = document.getElementById('notificationYesBtn');
  const noBtn = document.getElementById('notificationNoBtn');
  const closeBtn = document.getElementById('notificationCloseBtn');

  // Set title and message
  notificationTitle.textContent = title;
  notificationBody.textContent = message;

  // Set icon based on type
  let icon = '🔔';
  if (type === 'success') icon = '✅';
  else if (type === 'warning') icon = '⚠️';
  else if (type === 'danger') icon = '❌';
  else if (type === 'confirm') icon = '❓';
  notificationIcon.textContent = icon;

  // Configure buttons
  yesBtn.textContent = options.yesText || 'Yes';
  yesBtn.onclick = options.onYes || (() => modal.style.display = 'none');

  if (options.noText) {
    noBtn.textContent = options.noText;
    noBtn.style.display = 'inline-block';
    noBtn.onclick = options.onNo || (() => modal.style.display = 'none');
  } else {
    noBtn.style.display = 'none';
  }

  closeBtn.onclick = () => modal.style.display = 'none';

  // Show the modal
  modal.style.display = 'flex';
}

// Close notification modal with close button
document.getElementById('notificationCloseBtn').addEventListener('click', () => {
  document.getElementById('notificationModal').style.display = 'none';
});
</script>

@endsection
