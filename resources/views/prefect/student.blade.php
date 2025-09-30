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
  </div>
</div>

<!-- 📝 Student Details Modal -->
<div class="modal" id="detailsModal">
  <div class="modal-content">
    <div class="modal-header">
      📄 Student Details
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
      🗃️ Archived Students
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



<script>
// Search filter for student table
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

// Section filter
document.getElementById('sectionSelect').addEventListener('change', function() {
  const selectedSection = this.value;
  showNotification('📚 Section Filter', `Filtering by section: ${selectedSection || 'All Sections'}`, 'info', {
    yesText: 'OK',
    noText: null,
    onYes: () => {
      document.getElementById('notificationModal').style.display = 'none';
      // TODO: Implement actual section filtering
    }
  });
});

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// Move to Trash - Now shows confirmation modal
document.getElementById('moveToTrashBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.rowCheckbox:checked')];
  if (selected.length === 0) {
    showNotification('⚠️ No Selection', 'Please select at least one student record to move to trash.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
  } else {
    showNotification('🗑️ Move to Trash', `Are you sure you want to move ${selected.length} student record(s) to trash?`, 'confirm', {
      yesText: 'Yes, Move',
      noText: 'Cancel',
      onYes: () => {
        // AJAX call to move to trash
        setTimeout(() => {
          showNotification('✅ Success', `${selected.length} student record(s) moved to trash successfully.`, 'success', {
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
  }
});

// Row click -> Student Details Modal
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
  showNotification('📅 Set Schedule', 'Open schedule setup form or modal here.', 'info', {
    yesText: 'OK',
    noText: null,
    onYes: () => {
      document.getElementById('notificationModal').style.display = 'none';
    }
  });
});

// Send SMS Button
document.getElementById('sendSmsBtn').addEventListener('click', () => {
  showNotification('📩 Send SMS', 'Are you sure you want to send an SMS to this student/guardian?', 'confirm', {
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

// Edit button
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    const studentId = btn.getAttribute('data-id');
    showNotification('✏️ Edit Student', `Edit student with ID: ${studentId}`, 'info', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
        // TODO: Implement actual edit functionality
      }
    });
  });
});

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
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
    showNotification('⚠️ No Selection', 'Please select at least one record to restore.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
    return;
  }
  
  showNotification('🔄 Restore Records', `Are you sure you want to restore ${selected.length} record(s)?`, 'confirm', {
    yesText: 'Yes, Restore',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to restore records
      setTimeout(() => {
        showNotification('✅ Success', `${selected.length} record(s) restored successfully.`, 'success', {
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

// Delete selected archived records
document.getElementById('deleteArchivedBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(selected.length === 0) {
    showNotification('⚠️ No Selection', 'Please select at least one record to delete.', 'warning', {
      yesText: 'OK',
      noText: null,
      onYes: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
    return;
  }
  
  showNotification('🗑️ Delete Records', `This will permanently delete ${selected.length} record(s). This action cannot be undone. Are you sure?`, 'danger', {
    yesText: 'Yes, Delete',
    noText: 'Cancel',
    onYes: () => {
      // AJAX call to delete records
      setTimeout(() => {
        showNotification('✅ Success', `${selected.length} record(s) deleted permanently.`, 'success', {
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

// Close modals when clicking outside
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