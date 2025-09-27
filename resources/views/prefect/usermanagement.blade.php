<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li class="active"><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
    </ul>

    <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
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

  <!-- Table Container -->
  <div class="table-container">
    <div class="table-header">
      <div class="table-controls">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search students..." class="form-control">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 10px; margin:12px 0;">
        <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Create</button>
        <button id="archiveBtn" class="btn btn-warning"><i class="fas fa-archive"></i> Archive</button>
      </div>
    </div>

    <!-- Student Table -->
    <div class="student-table-wrapper">
      <table id="studentTable" class="fixed-header">
        <thead>
          <tr>
            <th>
              <input type="checkbox" id="selectAll">
              <div class="bulk-action-dropdown" style="display:inline-block; position: relative; margin-left:5px;">
                <button id="bulkActionBtn">&#8942;</button>
                <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
                  <div class="bulk-action-item" data-action="trash">Trash</div>
                </div>
              </div>
            </th>
            <th>ID</th>
            <th>Adviser Name</th>
            <th>Grade Level</th>
            <th>Section</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr data-email="juan@example.com" data-address="123 Street" data-contact="09171234567" onclick="showFullInfo(this)">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1001</td>
            <td>Juan Dela Cruz</td>
            <td>Grade 7</td>
            <td>Section A</td>
            <td>Active</td>
          </tr>
          <tr data-email="maria@example.com" data-address="456 Avenue" data-contact="09179876543" onclick="showFullInfo(this)">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1002</td>
            <td>Maria Santos</td>
            <td>Grade 8</td>
            <td>Section B</td>
            <td>Inactive</td>
          </tr>
        </tbody>
      </table>
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

<div class="modal" id="archivesModal">
  <div class="archive-modal-content">
    <div class="archive-modal-header">
      <span>Archived Students</span>
      <span class="close-btn" id="closeArchivesModal">&times;</span>
    </div>
    <div class="archive-modal-body">
      <div class="toolbar-actions">
        <input id="archiveSearch" type="search" placeholder="Search archived students..." style="flex:1;">
        <button id="restoreBtn" class="btn-primary"><i class="fas fa-undo"></i> Restore</button>
      </div>
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
            <tr>
              <td><input type="checkbox" class="archiveRowCheckbox"></td>
              <td>Pedro Cruz</td>
              <td>Archived</td>
              <td>Section A</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="infoModal">
  <div class="modal-content">
    <span class="close" id="closeInfoModalBtn">&times;</span>
    <div id="infoModalBody"></div>
  </div>
</div>


<script>
    // SIDEBAR DROPDOWN
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.classList.toggle('active');
      const dropdown = btn.nextElementSibling;
      dropdown.style.display = dropdown.style.display==='block'?'none':'block';
    });
  });
    
    
/* ==================== STUDENT CHECKBOX SELECT ALL ==================== */
function updateRowCheckboxes() {
  const selectAll = document.getElementById('selectAll');
  const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });
  }
}
updateRowCheckboxes();

/* ==================== BULK ACTION MENU ==================== */
const bulkActionBtn = document.getElementById('bulkActionBtn');
const bulkActionMenu = document.getElementById('bulkActionMenu');
if (bulkActionBtn) {
  bulkActionBtn.addEventListener('click', () => {
    bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
  });
}
document.addEventListener('click', e => {
  if (bulkActionBtn && !bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
    bulkActionMenu.style.display = 'none';
  }
});

document.querySelectorAll('.bulk-action-item').forEach(item => {
  item.addEventListener('click', () => {
    const selected = document.querySelectorAll('.rowCheckbox:checked');
    if (!selected.length) { alert("⚠️ Please select at least one student."); return; }
    if (item.dataset.action === "trash") {
      if (!confirm("Move selected students to Trash?")) return;
      alert("✅ Students moved to Trash (demo only)");
    }
    bulkActionMenu.style.display = 'none';
  });
});

/* ==================== SHOW INFO MODAL ==================== */
const infoModal = document.getElementById('infoModal');
const infoModalBody = document.getElementById('infoModalBody');
const closeInfoBtn = document.getElementById('closeInfoModalBtn');

function showFullInfo(row) {
  const cells = row.children;
  infoModalBody.innerHTML = `
    <p><strong>ID:</strong> ${cells[1].innerText}</p>
    <p><strong>Name:</strong> ${cells[2].innerText}</p>
    <p><strong>Grade Level:</strong> ${cells[3].innerText}</p>
    <p><strong>Section:</strong> ${cells[4].innerText}</p>
    <p><strong>Status:</strong> ${cells[5].innerText}</p>
    <p><strong>Email:</strong> ${row.dataset.email || 'N/A'}</p>
    <p><strong>Address:</strong> ${row.dataset.address || 'N/A'}</p>
    <p><strong>Contact:</strong> <a href="tel:${row.dataset.contact}">${row.dataset.contact || 'N/A'}</a></p>
  `;
  infoModal.classList.add('show-modal');
}

// Close Info Modal button
closeInfoBtn.addEventListener('click', () => {
  infoModal.classList.remove('show-modal');
});

// Close when clicking outside modal content
window.addEventListener('click', e => {
  if(e.target === infoModal) infoModal.classList.remove('show-modal');
});

/* ==================== SEARCH ==================== */
const searchInput = document.getElementById('searchInput');
if (searchInput) {
  searchInput.addEventListener('input', e => {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
  });
}

/* ==================== ARCHIVES MODAL ==================== */
const archiveBtn = document.getElementById('archiveBtn');
const archivesModal = document.getElementById('archivesModal');
const closeArchivesModal = document.getElementById('closeArchivesModal');
if (archiveBtn) archiveBtn.addEventListener('click', () => archivesModal.classList.add('show-modal'));
if (closeArchivesModal) closeArchivesModal.addEventListener('click', () => archivesModal.classList.remove('show-modal'));
window.addEventListener('click', e => { if(e.target===archivesModal) archivesModal.classList.remove('show-modal'); });

/* ==================== ARCHIVE SELECT ALL ==================== */
const archiveSelectAll = document.getElementById('archiveSelectAll');
if (archiveSelectAll) {
  archiveSelectAll.addEventListener('change', e => {
    document.querySelectorAll('.archiveRowCheckbox').forEach(cb => cb.checked = e.target.checked);
  });
}

/* ==================== RESTORE BUTTON ==================== */
const restoreBtn = document.getElementById('restoreBtn');
if (restoreBtn) {
  restoreBtn.addEventListener('click', () => {
    const selected = document.querySelectorAll('.archiveRowCheckbox:checked');
    if (!selected.length) { alert("⚠️ Select at least one student to restore."); return; }
    alert("✅ Selected students restored (demo only)");
  });
}

/* ==================== PROFILE DROPDOWN ==================== */
function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display==='block'?'none':'block';
}
document.addEventListener('click', e => {
  const dropdown = document.getElementById('profileDropdown');
  const userInfo = document.querySelector('.user-info');
  if (dropdown && userInfo && !userInfo.contains(e.target)) dropdown.style.display = 'none';
});

/* ==================== LOGOUT ==================== */
function logout() {
  if(!confirm("Are you sure you want to logout?")) return;
  fetch("{{ route('prefect.logout') }}", { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} })
  .then(() => window.location.href="{{ route('auth.login') }}");
}

/* ==================== CREATE MODAL ==================== */
const createBtn = document.getElementById('createBtn');
const createModal = document.getElementById('createModal');
const closeCreateBtns = document.querySelectorAll('#closeCreate');
if(createBtn) createBtn.addEventListener('click', () => createModal.classList.add('show-modal'));
closeCreateBtns.forEach(btn => btn.addEventListener('click', () => createModal.classList.remove('show-modal')));
window.addEventListener('click', e => { if(e.target===createModal) createModal.classList.remove('show-modal'); });

/* ==================== CREATE FORM SUBMIT ==================== */
const createForm = document.getElementById('createForm');
if(createForm) {
  let studentId = document.querySelectorAll('#studentTable tbody tr').length + 1;
  createForm.addEventListener('submit', e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(createForm).entries());
    const fullName = `${data.firstName} ${data.middleName} ${data.lastName}`.replace("  ", " ");
    const row = document.createElement("tr");
    row.dataset.email = data.email;
    row.dataset.address = data.address;
    row.dataset.contact = data.contact;
    row.innerHTML = `
      <td><input type="checkbox" class="rowCheckbox"></td>
      <td>${studentId}</td>
      <td>${fullName}</td>
      <td>${data.gradeLevel}</td>
      <td>${data.section}</td>
      <td>Active</td>
    `;
    row.addEventListener('click', () => showFullInfo(row));
    document.querySelector("#studentTable tbody").appendChild(row);
    studentId++;
    createForm.reset();
    createModal.classList.remove('show-modal');
    updateRowCheckboxes();
  });
}

</script>

</body>
</html>
