<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Offenses & Sanctions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/prefect/cards.css') }}">

</head>
<body>
<!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
    <ul>
      <div class="section-title">Main</div>
      <li ><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
      <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
      <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
      <li><a href="{{ route('violation.records') }}"><i class="fas fa-book"></i> Violation Record</a></li>
        <li><a href="{{ route('people.complaints') }}"><i class="fas fa-comments"></i>Complaints</a></li>
      <li class="active"><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>


<div class="main-content">
  <!-- HEADER -->
  <header class="main-header">
    <div class="header-left"><h2>Offenses & Sanctions</h2></div>
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
      <p>{{ $offenses->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>{{ $offenses->where('status', 'active')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>{{ $offenses->where('status', 'Cleared')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>{{ $offenses->count() }}</p>
    </div>
  </div>
</div>
<!-- Table Container -->
  <div class="table-container">
    <div class="table-header">
      <div class="table-controls">
         <h3>Offenses&Sanctions Table</h3>

        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin:12px 0;">
      
      <div style="display:flex; gap:10px;">
        <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Create</button>
        <button id="archiveBtn" class="btn-warning"><i class="fas fa-archive"></i> Archive</button>
        <button id="PrntBtn" class="btn-print"><i class="fas fa-print"></i> Print</button>
      </div>
    </div>
    </div>

    <div class="student-table-wrapper">
      <table id="offenseTable" class="fixed-header">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>ID</th>
            <th>Offense Name</th>
            <th>Category</th>
            <th>Points</th>
            <th>Status</th>
            <td>Action</td>
          </tr>
        </thead>
        <!-- Add "Action" column and Edit buttons in table rows -->
<tbody>
  <tr>
    <td><input type="checkbox" class="rowCheckbox"></td>
    <td>1</td>
    <td>Late Submission</td>
    <td>Minor</td>
    <td>1</td>
    <td>Active</td>
    <td>
      <button class="btn-edit" onclick="editOffenseRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>
  </tr>
  <tr>
    <td><input type="checkbox" class="rowCheckbox"></td>
    <td>2</td>
    <td>Cheating</td>
    <td>Major</td>
    <td>5</td>
    <td>Active</td>
    <td>
      <button class="btn-edit" onclick="editOffenseRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>
  </tr>
</tbody>


      </table>
    </div>
  </div>
</div>
<!-- ARCHIVES MODAL -->
<div class="modal" id="archivesModal">
  <div class="archive-modal-content">
    <div class="archive-modal-header">
      <span>Archived Offenses</span>
      <span class="close-btn" id="closeArchivesModal">&times;</span>
    </div>

    <div class="archive-modal-body">
      <!-- Search & Actions -->
      <div class="toolbar-actions" style="display:flex; gap:10px; margin-bottom:10px;">
        <input id="archiveSearch" type="search" placeholder="Search archived offenses..." style="flex:1;">
        <button id="restoreBtn" class="btn-primary"><i class="fas fa-undo"></i> Restore</button>
        <button id="deleteBtn" class="btn-primary"><i class="fas fa-trash"></i> Delete</button>
      </div>

      <!-- Archived Offenses Table -->
      <div class="archive-table-container">
        <table id="archiveTable" class="archive-table">
          <thead>
            <tr>
              <th><input type="checkbox" id="archiveSelectAll"></th>
              <th>ID</th>
              <th>Offense Name</th>
              <th>Category</th>
              <th>Points</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Archived offenses will be dynamically populated by JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- EDIT OFFENSE MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <span class="close" id="closeEditModal">&times;</span>
    <form id="editForm" class="form-grid">
      <div class="form-column">
        <div class="form-group"><label>Offense Name</label><input type="text" name="offenseName" required></div>
        <div class="form-group"><label>Category</label>
          <select name="category" required>
            <option>Minor</option>
            <option>Major</option>
            <option>Severe</option>
          </select>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
      </div>
      <div class="form-column">
        <div class="form-group"><label>Sanction Name</label><input type="text" name="sanctionName" required></div>
        <div class="form-group"><label>Sanction Type</label>
          <select name="sanctionType" required>
            <option>Warning</option>
            <option>Detention</option>
            <option>Suspension</option>
          </select>
        </div>
        <div class="form-group"><label>Points/Severity</label><input type="number" name="points" min="1" required></div>
        <div class="form-group"><label>Status</label>
          <select name="status">
            <option>Active</option>
            <option>Inactive</option>
            <option>Cleared</option>
          </select>
        </div>
      </div>
      <div class="form-actions" style="margin-top:10px;">
        <button type="button" class="btn-cancel" id="closeEditModalBtn">Cancel</button>
        <button type="submit" class="btn-create">Save Changes</button>
      </div>
    </form>
  </div>
</div>


<!-- CREATE MODAL -->
<div class="modal" id="createModal">
  <div class="modal-content">
    <span class="close" id="closeCreate">&times;</span>
    <form id="createForm" class="form-grid">
      <div class="form-column">
        <div class="form-group"><label>Offense Name</label><input type="text" name="offenseName" required></div>
        <div class="form-group"><label>Category</label><select name="category" required><option>Minor</option><option>Major</option><option>Severe</option></select></div>
        <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
      </div>
      <div class="form-column">
        <div class="form-group"><label>Sanction Name</label><input type="text" name="sanctionName" required></div>
        <div class="form-group"><label>Sanction Type</label><select name="sanctionType" required><option>Warning</option><option>Detention</option><option>Suspension</option></select></div>
        <div class="form-group"><label>Points/Severity</label><input type="number" name="points" min="1" required></div>
      </div>
      <div class="form-actions" style="margin-top:10px;">
        <button type="button" class="btn-cancel" id="closeCreateBtn">Cancel</button>
        <button type="submit" class="btn-create">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- INFO MODAL -->
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

  // PROFILE DROPDOWN
  function toggleProfileDropdown(){
    document.getElementById('profileDropdown').classList.toggle('show');
  }
  document.addEventListener('click', e=>{
    const dropdown=document.getElementById('profileDropdown');
    const userInfo=document.querySelector('.user-info');
    if(dropdown && userInfo && !userInfo.contains(e.target)) dropdown.classList.remove('show');
  });

  // PRINT TABLE
  document.getElementById('PrntBtn').addEventListener('click', ()=> window.print());

  // SELECT ALL CHECKBOX
  function updateRowCheckboxes(){
    const selectAll=document.getElementById('selectAll');
    const rowCheckboxes=document.querySelectorAll('.rowCheckbox');
    selectAll?.addEventListener('change', ()=> rowCheckboxes.forEach(cb=>cb.checked=selectAll.checked));
  }
  updateRowCheckboxes();

  // SHOW INFO MODAL
  const infoModal=document.getElementById('infoModal');
  const infoModalBody=document.getElementById('infoModalBody');
  document.getElementById('closeInfoModalBtn').addEventListener('click', ()=> infoModal.classList.remove('show-modal'));
  function showOffenseInfo(row){
    const cells=row.children;
    infoModalBody.innerHTML=`
      <p><strong>ID:</strong> ${cells[1].innerText}</p>
      <p><strong>Offense Name:</strong> ${cells[2].innerText}</p>
      <p><strong>Category:</strong> ${cells[3].innerText}</p>
      <p><strong>Points:</strong> ${cells[4].innerText}</p>
      <p><strong>Status:</strong> ${cells[5].innerText}</p>
      <p><strong>Description:</strong> ${row.dataset.description||'N/A'}</p>
      <p><strong>Sanction:</strong> ${row.dataset.sanction||'N/A'}</p>
    `;
    infoModal.classList.add('show-modal');
  }
  window.addEventListener('click', e=>{if(e.target===infoModal) infoModal.classList.remove('show-modal');});

  // CREATE MODAL
  const createModal=document.getElementById('createModal');
  const createBtn=document.getElementById('createBtn');
  const closeCreateBtn=document.getElementById('closeCreateBtn');
  createBtn.addEventListener('click', ()=> createModal.classList.add('show-modal'));
  document.getElementById('closeCreate')?.addEventListener('click', ()=> createModal.classList.remove('show-modal'));
  closeCreateBtn.addEventListener('click', ()=> createModal.classList.remove('show-modal'));
  window.addEventListener('click', e=>{if(e.target===createModal) createModal.classList.remove('show-modal');});

  // CREATE FORM SUBMIT
  const createForm=document.getElementById('createForm');
  let offenseId=document.querySelectorAll('#offenseTable tbody tr').length+1;
  createForm?.addEventListener('submit', e=>{
    e.preventDefault();
    const data=Object.fromEntries(new FormData(createForm).entries());
    const row=document.createElement('tr');
    row.dataset.description=data.description;
    row.dataset.sanction=`${data.sanctionName} - ${data.points} points`;
    row.innerHTML=`
      <td><input type="checkbox" class="rowCheckbox"></td>
      <td>${offenseId}</td>
      <td>${data.offenseName}</td>
      <td>${data.category}</td>
      <td>${data.points}</td>
      <td>Active</td>
    `;
    row.addEventListener('click', ()=> showOffenseInfo(row));
    document.querySelector('#offenseTable tbody').appendChild(row);
    offenseId++;
    createForm.reset();
    createModal.classList.remove('show-modal');
    updateRowCheckboxes();
  });

  // SEARCH TABLE
  document.getElementById('searchInput')?.addEventListener('input', e=>{
    const q=e.target.value.toLowerCase();
    document.querySelectorAll('#offenseTable tbody tr').forEach(row=>{
      row.style.display=row.innerText.toLowerCase().includes(q)?'':'none';
    });
  });

  // LOGOUT
  function logout(){
    if(!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('prefect.logout') }}",{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
      .then(()=> window.location.href="{{ route('auth.login') }}");
  }
  // EDIT MODAL FUNCTIONALITY
const editModal = document.getElementById('editModal');
const closeEditModal = document.getElementById('closeEditModal');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');
const editForm = document.getElementById('editForm');
let currentEditRow = null;

function editOffenseRow(btn) {
  const row = btn.closest('tr');
  currentEditRow = row;

  // Prefill edit form
  editForm.elements.offenseName.value = row.children[2].innerText;
  editForm.elements.category.value = row.children[3].innerText;
  editForm.elements.points.value = row.children[4].innerText;
  editForm.elements.status.value = row.children[5].innerText;

  const [sanctionName] = (row.dataset.sanction || '').split(' - ');
  editForm.elements.sanctionName.value = sanctionName || '';
  editForm.elements.description.value = row.dataset.description || '';

  editModal.classList.add('show-modal');
}

// Close Edit Modal
closeEditModal.addEventListener('click', () => editModal.classList.remove('show-modal'));
closeEditModalBtn.addEventListener('click', () => editModal.classList.remove('show-modal'));
window.addEventListener('click', e => { if(e.target === editModal) editModal.classList.remove('show-modal'); });

// Save changes
editForm.addEventListener('submit', e => {
  e.preventDefault();
  if(!currentEditRow) return;

  const data = Object.fromEntries(new FormData(editForm).entries());

  currentEditRow.children[2].innerText = data.offenseName;
  currentEditRow.children[3].innerText = data.category;
  currentEditRow.children[4].innerText = data.points;
  currentEditRow.children[5].innerText = data.status;
  currentEditRow.dataset.description = data.description;
  currentEditRow.dataset.sanction = `${data.sanctionName} - ${data.points} points`;

  editModal.classList.remove('show-modal');
});
// ARCHIVES MODAL LOGIC
const archivesModal = document.getElementById('archivesModal');
const closeArchivesModal = document.getElementById('closeArchivesModal');
const archiveSelectAll = document.getElementById('archiveSelectAll');
const archiveTableBody = document.querySelector('#archiveTable tbody');

// Open archives modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  archivesModal.classList.add('show-modal');
  populateArchiveTable();
});

// Close archives modal
closeArchivesModal.addEventListener('click', () => archivesModal.classList.remove('show-modal'));
window.addEventListener('click', e => { if(e.target === archivesModal) archivesModal.classList.remove('show-modal'); });

// Populate archived offenses into modal table
function populateArchiveTable() {
  archiveTableBody.innerHTML = '';
  document.querySelectorAll('#offenseTable tbody tr').forEach(row => {
    if(row.children[5].innerText === 'Cleared') {
      const clone = row.cloneNode(true);
      clone.querySelector('td:last-child').remove(); // Remove action buttons in modal
      clone.querySelector('input.rowCheckbox').classList.add('archive-checkbox');
      clone.querySelector('input.rowCheckbox').classList.remove('rowCheckbox');
      archiveTableBody.appendChild(clone);
    }
  });
}

// Archive select all checkbox
archiveSelectAll.addEventListener('change', () => {
  archiveTableBody.querySelectorAll('.archive-checkbox').forEach(cb => cb.checked = archiveSelectAll.checked);
});

// Restore archived offenses
document.getElementById('restoreBtn').addEventListener('click', () => {
  const selectedRows = Array.from(archiveTableBody.querySelectorAll('.archive-checkbox'))
    .filter(cb => cb.checked)
    .map(cb => cb.closest('tr'));

  if(selectedRows.length === 0) return alert('Select at least one offense to restore.');

  if(!confirm(`Restore ${selectedRows.length} offense(s)?`)) return;

  selectedRows.forEach(row => {
    const offenseId = row.children[1].innerText;
    const originalRow = Array.from(document.querySelectorAll('#offenseTable tbody tr'))
      .find(r => r.children[1].innerText === offenseId);
    if(originalRow) {
      originalRow.children[5].innerText = 'Active';
      originalRow.classList.remove('archived-row');
    }
  });

  populateArchiveTable();
});

// Delete archived offenses permanently
document.getElementById('deleteBtn').addEventListener('click', () => {
  const selectedRows = Array.from(archiveTableBody.querySelectorAll('.archive-checkbox'))
    .filter(cb => cb.checked)
    .map(cb => cb.closest('tr'));

  if(selectedRows.length === 0) return alert('Select at least one offense to delete.');

  if(!confirm(`Permanently delete ${selectedRows.length} offense(s)?`)) return;

  selectedRows.forEach(row => {
    const offenseId = row.children[1].innerText;
    const originalRow = Array.from(document.querySelectorAll('#offenseTable tbody tr'))
      .find(r => r.children[1].innerText === offenseId);
    if(originalRow) originalRow.remove();
    row.remove();
  });
});

</script>
</body>
</html>
