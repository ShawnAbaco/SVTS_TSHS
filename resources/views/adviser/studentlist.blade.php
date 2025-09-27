  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/adviser/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adviser/cards.css') }}">

  </head>
  <body>


 <!-- Sidebar -->
<div class="sidebar">
  <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li class="active"><a href="{{ route('student.list') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-book"></i>Violation Record</a></li>
    <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i>Complaints</a></li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>



  
<div class="main-content">
  <!-- ======= HEADER ======= -->
  <header class="main-header">
    <div class="header-left">
      <h2>Student Management</h2>
    </div>
    <div class="header-right">
      <div class="user-info" onclick="toggleProfileDropdown()">
        <img src="/images/user.jpg" alt="User">
        <span>{{ Auth::user()->name }}</span>
        <i class="fas fa-caret-down"></i>
      </div>
      <div class="profile-dropdown" id="profileDropdown">
        <a href="{{ route('profile.settings') }}">Profile</a>
      </div>
    </div>
  </header>

<!-- Summary Cards -->
<div class="summary-cards">
  <div class="summary-card">
    <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
    <div class="card-content">
      <h3>Total Students</h3>
      <p>{{ $activeStudents->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>{{ $activeStudents->where('status', 'active')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>{{ $activeStudents->where('status', 'Cleared')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>{{ $archivedStudents->count() }}</p>
    </div>
  </div>
</div>

    <!-- Table Container -->
    <div class="table-container">
      <!-- Table Header with Controls -->
      <div class="table-header">
        <div class="table-controls">
          <h3>Student Table</h3>
        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>

         <div style="display: flex; justify-content: flex-end; gap: 5px; margin-bottom: 0px;margin-top: 0px;">
  <select id="sectionFilter" class="form-select" style="max-width:200px;">
    <option value="">All Sections</option>
    @foreach($archivedStudents as $section)
      <option value="{{ $section }}">{{ $section }}</option>
    @endforeach
  </select>
<button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Create</button>  <button id="archiveBtn" class="btn btn-warning">
    <i class="fas fa-archive"></i> Archive
  </button>
</div>

      </div>

     <!-- Wrap your student table in a scrollable container -->
<div class="table-container">

  <table id="studentTable" class="fixed-header">

    <thead>
      <tr>
       <th>
            <input type="checkbox" id="selectAll">
            <!-- 3-dots trash dropdown -->
            <div class="bulk-action-dropdown" style="display:inline-block; position: relative; margin-left:5px;">
                <button id="bulkActionBtn">&#8942;</button>
                <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
                  <div class="bulk-action-item" data-action="completed">Trash</div>
                </div>
              </div>
          </th>
        <th>#</th>
        <th>Name</th>
        <th>Grade Level</th>
        <th>Section</th>
        <th>Status</th>
        <th>Action</th> <!-- New Action column -->
      </tr>
    </thead>
     </table>
     <div class="student-table-wrapper">
    <table id="studentTable">
  <tbody>
  @foreach($archivedStudents as $archivedStudents)
    <tr
      data-id="{{ $student->student_id }}"
      data-name="{{ $student->student_fname }} {{ $student->student_lname }}"
      data-grade="{{ $student->adviser->adviser_gradelevel }}"
      data-section="{{ $student->adviser->adviser_section }}"
      data-status="{{ ucfirst($student->status) }}"
      data-parent-name="{{ $student->parent->name ?? '-' }}"
      data-parent-contact="{{ $student->parent->contact ?? '-' }}"
      data-parent-address="{{ $student->parent->address ?? '-' }}"
      data-adviser-name="{{ $student->adviser->adviser_name ?? '-' }}"
    >
      <td><input type="checkbox" class="student-checkbox"></td>
      <td>{{ $student->student_id }}</td>
      <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
      <td>{{ $student->adviser->adviser_gradelevel }}</td>
      <td>{{ $student->adviser->adviser_section }}</td>
      <td>
        <span class="status-badge {{ $student->status === 'active' ? 'active' : 'inactive' }}">
          {{ ucfirst($student->status) }}
        </span>
      </td>
      <td>
        <button class="btn-edit" onclick="editStudent('{{ $student->student_id }}')">
          <i class="fas fa-edit"></i> Edit
        </button>
      </td>
    </tr>
  @endforeach
</tbody>

  </table>
</div>
  </div>
    </div>
    </div>
    <!-- EDIT MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <div class="modal-header">
      <h5>Edit Student</h5>
      <button class="btn-close" id="closeEditModal">&times;</button>
    </div>
    <div class="modal-body">
      <form id="editForm" class="form-grid">
        <input type="hidden" name="studentId" id="editStudentId">
        <div class="form-column">
          <div class="form-group"><label>Last Name</label><input type="text" name="lastName" id="editLastName" required></div>
          <div class="form-group"><label>First Name</label><input type="text" name="firstName" id="editFirstName" required></div>
          <div class="form-group"><label>Middle Name</label><input type="text" name="middleName" id="editMiddleName"></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" id="editEmail"></div>
          <div class="form-group"><label>Address</label><input type="text" name="address" id="editAddress"></div>
        </div>
        <div class="form-column">
          <div class="form-group"><label>Contact</label><input type="text" name="contact" id="editContact"></div>
          <div class="form-group"><label>Grade Level</label>
            <select name="gradeLevel" id="editGradeLevel" required>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
            </select>
          </div>
          <div class="form-group"><label>Section</label><input type="text" name="section" id="editSection" required></div>
          <div class="form-group"><label>Status</label>
            <select name="status" id="editStatus" required>
              <option value="Active">Active</option>
              <option value="Cleared">Cleared</option>
            </select>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn-cancel" id="closeEditForm">Cancel</button>
          <button type="submit" class="btn-create">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modals: Create, Archives, Info (unchanged from your code) -->
<div class="modal" id="createModal">
  <div class="modal-content">
    <span class="close" id="closeCreate">&times;</span>
    <form id="createForm" class="form-grid">
      <div class="form-column">
        <div class="form-group"><label>Last Name</label><input type="text" name="lastName" required></div>
        <div class="form-group"><label>First Name</label><input type="text" name="firstName" required></div>
        <div class="form-group"><label>Middle Name</label><input type="text" name="middleName"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email"></div>
        <div class="form-group"><label>Address</label><input type="text" name="address"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password"></div>
      </div>
      <div class="form-column">
        <div class="form-group"><label>Contact</label><input type="text" name="contact"></div>
        <div class="form-group"><label>Grade Level</label><select name="gradeLevel" required>
          <option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
        </select></div>
        <div class="form-group"><label>Section</label><input type="text" name="section" required></div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" id="closeCreate">Cancel</button>
        <button type="submit" class="btn-create">Save</button>
      </div>
    </form>
  </div>
</div>
    <!-- ARCHIVES MODAL -->
    <div class="modal" id="archivesModal">
      <div class="archive-modal-content">
        <div class="archive-modal-header">
          <span>Archived Students</span>
          <span class="close-btn" id="closeArchivesModal">&times;</span>
        </div>

        <div class="archive-modal-body">
          <!-- Search & Actions -->
          <div class="toolbar-actions">
            <input id="archiveSearch" type="search" placeholder="Search archived students..." style="flex:1;">
            <select id="archiveSectionFilter" style="max-width:200px;">
              <option value="">All Sections</option>
              @foreach($archivedStudents as $section)
                <option value="{{ $section }}">{{ $section }}</option>
              @endforeach
            </select>
            <button id="restoreBtn" class="btn-primary"><i class="fas fa-undo"></i> Restore</button>
            <button id="deleteBtn" class="btn-primary"><i class="fas fa-trash"></i> Delete</button>
          </div>

          <!-- Archived Students Table -->
          <div class="archive-table-container">
            <table id="archiveTable" class="archive-table">
              <thead>
                <tr>
                  <th><input type="checkbox" id="archiveSelectAll"></th>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Section</th>
                </tr>
              </thead>
              <tbody>
                @foreach($archivedStudents as $student)
                  @if($student->status === 'Cleared')
                    <tr
                      data-id="{{ $student->student_id }}"
                      data-name="{{ $student->student_fname }} {{ $student->student_lname }}"
                      data-status="{{ $student->status }}"
                      data-section="{{ $student->adviser->adviser_section ?? '' }}"
                    >
                      <td><input type="checkbox" class="archive-checkbox"></td>
                      <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
                      <td>
                        <span class="status-badge cleared">{{ $student->status }}</span>
                      </td>
                      <td>{{ $student->adviser->adviser_section ?? '-' }}</td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Modal -->
    <div class="modal" id="infoModal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="infoModalTitle">Student Info</h5>
          <button class="btn-close" onclick="closeInfoModal()">&times;</button>
        </div>
        <div class="modal-body" id="infoModalBody"></div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeInfoModal()">Close</button>
        </div>
      </div>
    </div>

</div>

<script>
/* ==================== SELECT ALL ==================== */
const selectAll = document.getElementById('selectAll');
function updateRowCheckboxes() {
  const checkboxes = document.querySelectorAll('.student-checkbox');
  selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
  });
  checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      if (!cb.checked) selectAll.checked = false;
      else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) {
        selectAll.checked = true;
      }
    });
  });
}
updateRowCheckboxes();

/* ==================== BULK ACTION MENU ==================== */
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

document.querySelectorAll('.bulk-action-item').forEach(item => {
  item.addEventListener('click', () => {
    const action = item.dataset.action;
    const selected = document.querySelectorAll('.student-checkbox:checked');

    if (selected.length === 0) {
      alert("⚠️ Please select at least one student.");
      return;
    }

    if (action === "completed") {
      if (!confirm("Are you sure you want to mark selected students as Cleared?")) return;

      const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);
      fetch(`/prefect/students/bulk-clear-status`, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ids, status: 'Cleared' })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("✅ Selected students marked as Cleared.");
          location.reload();
        } else alert("❌ Failed to update: " + data.message);
      })
      .catch(err => { console.error(err); alert("❌ Something went wrong."); });

      bulkActionMenu.style.display = 'none';
    }
  });
});

/* ==================== ROW CLICK INFO MODAL ==================== */
function attachRowClick(row) {
  row.addEventListener('click', (e) => {
    // Ignore checkboxes
    if (e.target.type === 'checkbox' || e.target.closest('input[type="checkbox"]')) return;
    // Ignore Action column or Edit buttons
    if (e.target.closest('td:last-child') || e.target.closest('.btn-edit')) return;

    const infoHtml = `
      <p><strong>ID:</strong> ${row.dataset.id}</p>
      <p><strong>Name:</strong> ${row.dataset.name}</p>
      <p><strong>Grade Level:</strong> ${row.dataset.grade}</p>
      <p><strong>Section:</strong> ${row.dataset.section}</p>
      <p><strong>Status:</strong> ${row.dataset.status}</p>
      <hr>
      <p><strong>Parent Name:</strong> ${row.dataset.parentName || '-'}</p>
      <p><strong>Parent Contact:</strong> <a href="tel:${row.dataset.parentContact || '-'}">${row.dataset.parentContact || '-'}</a></p>
      <p><strong>Parent Address:</strong> ${row.dataset.parentAddress || '-'}</p>
      <p><strong>Adviser Name:</strong> ${row.dataset.adviserName || '-'}</p>
    `;
    document.getElementById('infoModalBody').innerHTML = infoHtml;
    document.getElementById('infoModal').classList.add('show-modal');
  });
}

// Attach existing rows
document.querySelectorAll('#studentTable tbody tr').forEach(row => attachRowClick(row));

function closeInfoModal() {
  document.getElementById('infoModal').classList.remove('show-modal');
}

/* ==================== SEARCH & FILTER ==================== */
const searchInput = document.getElementById('searchInput');
const sectionFilter = document.getElementById('sectionFilter');

function filterTable() {
  const query = searchInput.value.toLowerCase();
  const section = sectionFilter.value;
  document.querySelectorAll('#studentTable tbody tr').forEach(row => {
    const name = row.dataset.name.toLowerCase();
    const sec = row.dataset.section;
    row.style.display = (name.includes(query) && (section === '' || sec === section)) ? '' : 'none';
  });
}

searchInput.addEventListener('input', filterTable);
sectionFilter.addEventListener('change', filterTable);

/* ==================== CREATE FORM ==================== */
const createBtn = document.getElementById('createBtn');
const createModal = document.getElementById('createModal');
const closeCreateBtns = document.querySelectorAll('#closeCreate');

if (createBtn) createBtn.addEventListener('click', () => createModal.classList.add('show-modal'));
closeCreateBtns.forEach(btn => btn.addEventListener('click', () => createModal.classList.remove('show-modal')));
window.addEventListener('click', e => { if(e.target===createModal) createModal.classList.remove('show-modal'); });

const createForm = document.getElementById('createForm');
if (createForm) {
  let studentId = document.querySelectorAll('#studentTable tbody tr').length + 1;
  createForm.addEventListener('submit', e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(createForm).entries());
    const fullName = `${data.firstName} ${data.middleName} ${data.lastName}`.replace("  ", " ");

    const row = document.createElement('tr');
    row.dataset.id = studentId;
    row.dataset.name = fullName;
    row.dataset.grade = data.gradeLevel;
    row.dataset.section = data.section;
    row.dataset.status = 'Active';
    row.dataset.parentName = data.parentName || '-';
    row.dataset.parentContact = data.parentContact || '-';
    row.dataset.parentAddress = data.parentAddress || '-';
    row.dataset.adviserName = data.adviserName || '-';

    row.innerHTML = `
      <td><input type="checkbox" class="student-checkbox"></td>
      <td>${studentId}</td>
      <td>${fullName}</td>
      <td>${data.gradeLevel}</td>
      <td>${data.section}</td>
      <td><span class="status-badge active">Active</span></td>
      <td><button class="btn-edit" onclick="editStudent('${studentId}')"><i class="fas fa-edit"></i> Edit</button></td>
    `;

    attachRowClick(row); // attach modal click
    document.querySelector("#studentTable tbody").appendChild(row);
    createForm.reset();
    createModal.classList.remove('show-modal');
    updateRowCheckboxes();
    studentId++;
  });
}

/* ==================== ARCHIVES MODAL & RESTORE ==================== */
const archiveBtn = document.getElementById('archiveBtn');
const archivesModal = document.getElementById('archivesModal');
const closeArchivesModal = document.getElementById('closeArchivesModal');
const archiveSearch = document.getElementById('archiveSearch');
const archiveSectionFilter = document.getElementById('archiveSectionFilter');
const archiveSelectAll = document.getElementById('archiveSelectAll');
const restoreBtn = document.getElementById('restoreBtn');

archiveBtn.addEventListener('click', () => archivesModal.classList.add('show-modal'));
closeArchivesModal.addEventListener('click', () => archivesModal.classList.remove('show-modal'));
window.addEventListener('click', e => { if(e.target===archivesModal) archivesModal.classList.remove('show-modal'); });

function filterArchiveTable() {
  const query = archiveSearch.value.toLowerCase();
  const selectedSection = archiveSectionFilter.value;
  document.querySelectorAll('#archiveTable tbody tr').forEach(row => {
    const name = row.dataset.name.toLowerCase();
    const rowSection = row.dataset.section;
    row.style.display = (name.includes(query) && (selectedSection === '' || rowSection === selectedSection)) ? '' : 'none';
  });
}

archiveSearch.addEventListener('input', filterArchiveTable);
archiveSectionFilter.addEventListener('change', filterArchiveTable);

archiveSelectAll.addEventListener('change', () => {
  document.querySelectorAll('.archive-checkbox').forEach(cb => cb.checked = archiveSelectAll.checked);
});

restoreBtn.addEventListener('click', () => {
  const selected = document.querySelectorAll('.archive-checkbox:checked');
  if (selected.length === 0) {
    alert("⚠️ Please select at least one student to restore.");
    return;
  }
  const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);
  fetch(`/prefect/students/bulk-update-status`, {
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
      location.reload();
    } else alert("❌ Restore failed: " + data.message);
  })
  .catch(err => { console.error(err); alert("❌ Something went wrong."); });
});

/* ==================== PROFILE DROPDOWN ==================== */
function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', (e) => {
  const dropdown = document.getElementById('profileDropdown');
  const userInfo = document.querySelector('.user-info');
  if (!userInfo.contains(e.target) && dropdown.style.display === 'block') {
    dropdown.style.display = 'none';
  }
});
/* ==================== EDIT STUDENT ==================== */
const editModal = document.getElementById('editModal');
const closeEditModalBtn = document.getElementById('closeEditModal');
const closeEditFormBtn = document.getElementById('closeEditForm');
const editForm = document.getElementById('editForm');

function editStudent(studentId) {
  const row = document.querySelector(`#studentTable tbody tr[data-id='${studentId}']`);
  if (!row) return;

  // Pre-fill form fields
  document.getElementById('editStudentId').value = row.dataset.id;
  const nameParts = row.dataset.name.split(' ');
  document.getElementById('editFirstName').value = nameParts[0] || '';
  document.getElementById('editMiddleName').value = nameParts.length === 3 ? nameParts[1] : '';
  document.getElementById('editLastName').value = nameParts.length === 3 ? nameParts[2] : nameParts[1] || '';
  document.getElementById('editGradeLevel').value = row.dataset.grade;
  document.getElementById('editSection').value = row.dataset.section;
  document.getElementById('editStatus').value = row.dataset.status;
  document.getElementById('editContact').value = row.dataset.parentContact || '';
  document.getElementById('editAddress').value = row.dataset.parentAddress || '';
  document.getElementById('editEmail').value = row.dataset.email || '';

  editModal.classList.add('show-modal');
}

// Close Edit Modal
closeEditModalBtn.addEventListener('click', () => editModal.classList.remove('show-modal'));
closeEditFormBtn.addEventListener('click', () => editModal.classList.remove('show-modal'));
window.addEventListener('click', e => { if(e.target === editModal) editModal.classList.remove('show-modal'); });

// Handle Edit Form Submission
editForm.addEventListener('submit', e => {
  e.preventDefault();

  const studentId = document.getElementById('editStudentId').value;
  const row = document.querySelector(`#studentTable tbody tr[data-id='${studentId}']`);
  if (!row) return;

  const firstName = document.getElementById('editFirstName').value;
  const middleName = document.getElementById('editMiddleName').value;
  const lastName = document.getElementById('editLastName').value;
  const fullName = `${firstName} ${middleName} ${lastName}`.replace(/\s+/g,' ').trim();
  const grade = document.getElementById('editGradeLevel').value;
  const section = document.getElementById('editSection').value;
  const status = document.getElementById('editStatus').value;

  // Update the row
  row.dataset.name = fullName;
  row.dataset.grade = grade;
  row.dataset.section = section;
  row.dataset.status = status;

  row.querySelector('td:nth-child(3)').textContent = fullName;
  row.querySelector('td:nth-child(4)').textContent = grade;
  row.querySelector('td:nth-child(5)').textContent = section;
  row.querySelector('td:nth-child(6) .status-badge').textContent = status;
  row.querySelector('td:nth-child(6) .status-badge').className = `status-badge ${status.toLowerCase()}`;

  editModal.classList.remove('show-modal');
});

</script>


</body>
</html>