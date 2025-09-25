  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Adviser Dashboard - Student List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="stylesheet" href="{{ asset('css/adviser/studentlist.css') }}">
  </head>
  <body>


  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}" class="active"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
          <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
          <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
        </ul>
      </li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
<li>
    <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>    </ul>
  </nav>


  <!-- MAIN CONTENT -->
<!-- MAIN CONTENT -->

<main class="main-content">
  <div class="toolbar">

    <h1>Student List</h1>
    <div class="toolbar-actions">


  @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

      <input id="tableSearch" type="search" placeholder="Search students...">
      <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create Student</button>
      <button class="btn-archive"><i class="fas fa-archive"></i> Archives</button>

    </div>


  <table id="studentTable">
    <thead>
      <tr>
    <th>
<div style="display: inline-flex; align-items: center; gap: 8px;">
  <input type="checkbox" id="selectAll">

  <!-- BULK ACTION 3 DOTS -->
  <div class="bulk-action-dropdown" style="position: relative;">
    <button id="bulkActionBtn">&#8942;</button>
    <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
      <div class="bulk-action-item" data-action="completed">Completed</div>
      <div class="bulk-action-item" data-action="trash">Trash</div>
    </div>
  </div>
</div>

          </th>
        <th>Name</th>
        <th>Sex</th>
        <th>Birthdate</th>
        <th>Address</th>
        <th>Contact #</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
  @foreach($activeStudents as $student)
      <tr
        data-id="{{ $student->student_id }}"
        data-fname="{{ $student->student_fname }}"
        data-lname="{{ $student->student_lname }}"
        data-sex="{{ $student->student_sex }}"
        data-birthdate="{{ $student->student_birthdate }}"
        data-address="{{ $student->student_address }}"
        data-contact="{{ $student->student_contactinfo }}"
        data-status="{{ $student->status }}"
        data-parent-name="{{ $student->parent ? $student->parent->parent_fname . ' ' . $student->parent->parent_lname : '' }}"
        data-parent-contact="{{ $student->parent ? $student->parent->parent_contactinfo : '' }}"
        data-parent-id="{{ $student->parent ? $student->parent->parent_id : '' }}"
      >
                <td><input type="checkbox" class="student-checkbox" onclick="event.stopPropagation()"></td>
        <td>{{ $student->student_fname . " " . $student->student_lname }}</td>
        <td>{{ $student->student_sex ?? 'N/A' }}</td>
<td>{{ \Carbon\Carbon::parse($student->student_birthdate)->format('M d, Y') }}</td>
        <td>{{ $student->student_address }}</td>
        <td>{{ $student->student_contactinfo }}</td>
        <td>
          <span class="status-badge {{ $student->status === 'active' ? 'active' : 'inactive' }}">
            {{ ucfirst($student->status) }}
          </span>
        </td>
        <td>
          <button class="action-btn info"><i class="fas fa-info-circle"></i> Info</button>
          <button class="action-btn edit"><i class="fas fa-edit"></i> Edit</button>

        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</main>

<!-- ARCHIVES MODAL -->
<div class="modal" id="archivesModal">
  <div class="modal-content" style="max-width: 900px; width: 100%;">
    <span class="close-btn" id="closeArchivesModal">&times;</span>
    <div class="modal-header">Archived Students</div>
@if (session('success'))
  <div class="alert alert-success" id="successAlert">
      {{ session('success') }}
  </div>
@endif


    <!-- Search & Actions -->
    <div class="toolbar-actions" style="margin-bottom: 10px; display: flex; gap: 10px; align-items: center;">
      <input id="archiveSearch" type="search" placeholder="Search archived students..." style="flex:1; padding:6px;">
      <button id="restoreBtn" class="btn-primary"><i class="fas fa-undo"></i> Restore</button>
      <button id="deleteBtn" class="btn-danger"><i class="fas fa-trash"></i> Delete</button>
    </div>

    <!-- Archived Students Table -->
    <table id="archiveTable">
      <thead>
        <tr>
          <th><input type="checkbox" id="archiveSelectAll"></th>
          <th>Name</th>
          <th>Status</th>
        </tr>
      </thead>
<tbody>
  @foreach($archivedStudents as $student)
    <tr
      data-id="{{ $student->student_id }}"
      data-fname="{{ $student->student_fname }}"
      data-lname="{{ $student->student_lname }}"
      data-status="{{ $student->status }}"
    >
      <td><input type="checkbox" class="archive-checkbox"></td>
      <td>{{ $student->student_fname . " " . $student->student_lname }}</td>
      <td>
<span class="status-badge {{ $student->status }}">
    {{ ucfirst($student->status) }}
</span>

      </td>
    </tr>
  @endforeach
</tbody>


    </table>
  </div>
</div>


<!-- CREATE STUDENT MODAL -->
<div class="modal" id="createStudentModal">
  <div class="modal-content">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <div class="modal-header">Create Student Information</div>

    <form id="studentForm" method="POST" action="{{ route('students.store') }}">
      @csrf

      <!-- First Name -->
      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="student_fname" required
              pattern="[A-Za-z\s]+"
              title="Only letters and spaces are allowed">
      </div>

      <!-- Last Name -->
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="student_lname" required
              pattern="[A-Za-z\s]+"
              title="Only letters and spaces are allowed">
      </div>

      <!-- Sex -->
      <div class="form-group">
        <label>Sex</label>
        <select name="student_sex" required>
          <option value="" disabled selected>Select Sex</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <!-- Birthdate -->
      <div class="form-group">
        <label>Birthdate</label>
        <input type="date" name="student_birthdate" required
              max="{{ date('Y-m-d') }}"
              title="Birthdate cannot be in the future">
      </div>

      <!-- Address -->
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="student_address" required
              pattern="^[A-Za-z0-9\s.,#/-]+$"
              title="Only letters, numbers, spaces, commas, periods, hyphens, #, and / are allowed.">
      </div>

      <!-- Contact Info -->
      <div class="form-group">
        <label>Contact Info</label>
        <input type="text" name="student_contactinfo" required
              pattern="[0-9]{11}"
              title="Contact number must be 11 digits (e.g. 09123456789)">
      </div>

      <hr>

      <!-- Parent Search -->
      <div class="form-group">
        <label for="parent_search">Parent</label>
        <input type="text" id="parent_search" class="form-control" placeholder="Type parent name...">
        <input type="hidden" id="parent_id" name="parent_id">
        <div id="parentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label>Status</label>
        <select name="status" required>
          <option value="active" selected>Active</option>

        </select>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="submit" class="btn-submit">Create Student</button>
      </div>
    </form>
  </div>
</div>

  <!-- INFO MODAL -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close-btn" id="closeInfoModalBtn">&times;</span>
      <div class="modal-header">Parent / Guardian Information</div>

      <div class="form-group">
        <label>Parent/Guardian Name:</label>
        <p><strong id="infoGuardianName"></strong></p>
      </div>
      <div class="form-group">
        <label>Contact Number:</label>
        <p><strong id="infoGuardianContact"></strong></p>
      </div>

      <div class="modal-footer">
        <button id="sendSMSBtn" class="btn-submit"><i class="fas fa-sms"></i> Send SMS</button>
      </div>
    </div>
  </div>




<!-- EDIT STUDENT MODAL -->
<div class="modal" id="editStudentModal">
  <div class="modal-content">
    <span class="close-btn" id="closeEditModalBtn">&times;</span>
    <div class="modal-header">Edit Student Information</div>

    <form id="editStudentForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="editStudentId">

      <!-- First Name -->
      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="student_fname" id="editStudentFname" required
              pattern="[A-Za-z\s]+"
              title="Only letters and spaces are allowed">
      </div>

      <!-- Last Name -->
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="student_lname" id="editStudentLname" required
              pattern="[A-Za-z\s]+"
              title="Only letters and spaces are allowed">
      </div>

      <!-- Sex -->
      <div class="form-group">
        <label>Sex</label>
        <select name="student_sex" id="editStudentSex" required>
          <option value="" disabled>Select Sex</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <!-- Birthdate -->
      <div class="form-group">
        <label>Birthdate</label>
        <input type="date" name="student_birthdate" id="editStudentBirthdate" required
              max="{{ date('Y-m-d') }}"
              title="Birthdate cannot be in the future">
      </div>

      <!-- Address -->
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="student_address" id="editStudentAddress" required
              pattern="^[A-Za-z0-9\s.,#/-]+$"
              title="Only letters, numbers, spaces, commas, periods, hyphens, #, and / are allowed.">
      </div>

      <!-- Contact Info -->
      <div class="form-group">
        <label>Contact Info</label>
        <input type="text" name="student_contactinfo" id="editStudentContact" required
              pattern="[0-9]{11}"
              title="Contact number must be 11 digits (e.g. 09123456789)">
      </div>

      <hr>

      <!-- Parent Search -->
      <div class="form-group">
        <label for="edit_parent_search">Parent / Guardian</label>
        <input type="text" id="edit_parent_search" class="form-control" placeholder="Type parent name..." required>
        <input type="hidden" id="edit_parent_id" name="parent_id">
        <div id="editParentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label>Status</label>
        <select name="status" id="editStudentStatus" required>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="submit" class="btn-submit">Update Student</button>
      </div>
    </form>
  </div>
</div>
<script>
        // Select All
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    selectAll.addEventListener('change', () => {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
    checkboxes.forEach(cb => {
      cb.addEventListener('change', () => {
        if (!cb.checked) {
          selectAll.checked = false;
        } else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) {
          selectAll.checked = true;
        }
      });
    });
// Bulk Action Menu
const bulkActionBtn = document.getElementById('bulkActionBtn');
const bulkActionMenu = document.getElementById('bulkActionMenu');

bulkActionBtn.addEventListener('click', () => {
  bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', (e) => {
  if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
    bulkActionMenu.style.display = 'none';
  }
});

document.querySelectorAll('.status-cell').forEach(cell => {
  if (cell.textContent.trim() === 'Trash') {
    cell.classList.add('Trash');
  }
});

// Dropdowns
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.nextElementSibling;
    document.querySelectorAll('.dropdown-btn').forEach(other => {
      if (other !== btn) {
        other.classList.remove('active');
        other.nextElementSibling.style.display = 'none';
      }
    });
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });
});

// Bulk Action Items
document.querySelectorAll('.bulk-action-item').forEach(item => {
  item.addEventListener('click', () => {
    const action = item.dataset.action; // "completed" or "trash"
    const selected = document.querySelectorAll('.student-checkbox:checked');

    if (selected.length === 0) {
      alert("⚠️ Please select at least one student.");
      return;
    }

    // Collect IDs
    const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);

    // Map actions to statuses
    let status;
    if (action === "completed") status = "completed";
    if (action === "trash") status = "inactive";

    fetch(`/adviser/students/bulk-update-status`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ ids, status })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        // Refresh the page to update UI
        location.reload();
      } else {
        alert("❌ Update failed: " + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert("❌ Something went wrong.");
    });
  });
});




  // Dropdown functionality - auto close others & scroll
  const dropdowns = document.querySelectorAll('.dropdown-btn');
  dropdowns.forEach(btn => {
      btn.addEventListener('click', function(e) {
          e.preventDefault();

          // close all other dropdowns
          dropdowns.forEach(otherBtn => {
              if (otherBtn !== this) {
                  otherBtn.nextElementSibling.classList.remove('show');
                  otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
              }
          });

          // toggle clicked dropdown
          const container = this.nextElementSibling;
          container.classList.toggle('show');
          this.querySelector('.fa-caret-down').style.transform =
              container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

          // scroll into view if dropdown is opened
          if(container.classList.contains('show')){
              container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
      });
  });

  document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', function(){
          document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
      }); // Sidebar active link
  });

  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const modal = document.getElementById('createStudentModal');

  const editModal = document.getElementById('editStudentModal');
  const closeEditModalBtn = document.getElementById('closeEditModalBtn');

  const infoModal = document.getElementById('infoModal');
  const closeInfoModalBtn = document.getElementById('closeInfoModalBtn');
  const sendSMSBtn = document.getElementById('sendSMSBtn');

  // TABLE SEARCH (LIVE) - filters student table rows only
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', function() {
      const q = this.value.trim().toLowerCase();
      document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // OPEN / CLOSE MODALS
  openModalBtn.addEventListener('click', () => modal.style.display = 'flex');
  closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
  closeEditModalBtn.addEventListener('click', () => editModal.style.display = 'none');
  closeInfoModalBtn.addEventListener('click', () => infoModal.style.display = 'none');

  // Close modals when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
    if (e.target === editModal) editModal.style.display = 'none';
    if (e.target === infoModal) infoModal.style.display = 'none';
  });

  // ==========================
  // EDIT MODAL (UPDATED)
  // ==========================
  document.querySelectorAll('.edit').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const studentId = row.dataset.id;

      // Fill form fields
      document.getElementById('editStudentId').value = studentId;
      document.getElementById('editStudentFname').value = row.dataset.fname;
      document.getElementById('editStudentLname').value = row.dataset.lname;
      document.getElementById('editStudentBirthdate').value = row.dataset.birthdate;
      document.getElementById('editStudentAddress').value = row.dataset.address;
      document.getElementById('editStudentContact').value = row.dataset.contact;

      // New fields: sex and status
      document.getElementById('editStudentSex').value = row.dataset.sex || '';
      document.getElementById('editStudentStatus').value = row.dataset.status || 'active';

      // Parent info
      document.getElementById('edit_parent_search').value = row.dataset.parentName;
      document.getElementById('edit_parent_id').value = row.dataset.parentId || '';

      // Set form action dynamically
      document.getElementById('editStudentForm').action = `/adviser/students/${studentId}`;

      // Show modal
      editModal.style.display = 'flex';
    });
  });

  // INFO BUTTON
  document.querySelectorAll('.info').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const guardianName = row.dataset.parentName;
      const guardianContact = row.dataset.parentContact;

      document.getElementById('infoGuardianName').innerText = guardianName;
      document.getElementById('infoGuardianContact').innerText = guardianContact;

      infoModal.style.display = 'flex';

      sendSMSBtn.onclick = () => {
        alert(`📩 SMS will be sent to ${guardianName} (${guardianContact})`);
      };
    });
  });

  // LIVE SEARCH PARENT (Create)
  document.getElementById('parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('parentList').style.display = 'none';
      document.getElementById('parentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('parentList').innerHTML = results;
        document.getElementById('parentList').style.display = 'block';
      });
  });

  function selectParent(id, name){
    document.getElementById('parent_id').value = id;
    document.getElementById('parent_search').value = name;
    document.getElementById('parentList').style.display = 'none';
  }

  // LIVE SEARCH for EDIT MODAL
  document.getElementById('edit_parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('editParentList').style.display = 'none';
      document.getElementById('editParentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectEditParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('editParentList').innerHTML = results;
        document.getElementById('editParentList').style.display = 'block';
      });
  });

  function selectEditParent(id, name){
    document.getElementById('edit_parent_id').value = id;
    document.getElementById('edit_parent_search').value = name;
    document.getElementById('editParentList').style.display = 'none';



  }

  // ARCHIVES MODAL
const archivesModal = document.getElementById('archivesModal');
const closeArchivesModal = document.getElementById('closeArchivesModal');
const openArchivesBtn = document.querySelector('.btn-archive');

openArchivesBtn.addEventListener('click', () => archivesModal.style.display = 'flex');
closeArchivesModal.addEventListener('click', () => archivesModal.style.display = 'none');

window.addEventListener('click', (e) => {
  if (e.target === archivesModal) archivesModal.style.display = 'none';
});

// Search archived students
document.getElementById('archiveSearch').addEventListener('input', function() {
  const q = this.value.trim().toLowerCase();
  document.querySelectorAll('#archiveTable tbody tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(q) ? '' : 'none';
  });
});
// Select All in archive
const archiveSelectAll = document.getElementById('archiveSelectAll');

archiveSelectAll.addEventListener('change', () => {
  document.querySelectorAll('.archive-checkbox').forEach(cb => cb.checked = archiveSelectAll.checked);
});

// Restore action
document.getElementById('restoreBtn').addEventListener('click', () => {
  const selected = document.querySelectorAll('.archive-checkbox:checked');
  if (selected.length === 0) {
    alert("⚠️ Please select at least one student to restore.");
    return;
  }

  const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);

  fetch(`/adviser/students/bulk-update-status`, {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ ids, status: 'active' })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload(); // 🔄 refresh page after success
    } else {
      alert("❌ Restore failed: " + data.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert("❌ Something went wrong.");
  });
});

// Delete action
// Delete action — send one DELETE request per id (uses existing /students/{id}/delete route)
document.getElementById('deleteBtn').addEventListener('click', async () => {
  const selected = document.querySelectorAll('.archive-checkbox:checked');
  if (selected.length === 0) {
    alert("⚠️ Please select at least one student to delete.");
    return;
  }

  if (!confirm("Are you sure you want to permanently delete selected students?")) return;

  const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);
  const token = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const requests = ids.map(id =>
      fetch(`/adviser/students/${id}/delete`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': token,
          'Content-Type': 'application/json',
        }
      }).then(res => {
        if (!res.ok) return res.text().then(t => { throw new Error(t || 'Delete failed') });
        return res.json();
      })
    );

    await Promise.all(requests);

    alert("✅ Selected students deleted successfully.");
    location.reload(); // refresh so archived list updates
  } catch (err) {
    console.error(err);
    alert("❌ Something went wrong while deleting: " + (err.message || ''));
  }
});


// Auto-hide success message after 3 seconds
const successAlert = document.getElementById('successAlert');
if (successAlert) {
  setTimeout(() => {
    successAlert.style.transition = "opacity 0.5s";
    successAlert.style.opacity = "0";
    setTimeout(() => successAlert.remove(), 500);
  }, 3000);
}


  function logout(){
    if(confirm('Are you sure you want to log out?')){
      window.location.href = '/adviser/login';
    }
  }
</script>

  </body>
  </html>
