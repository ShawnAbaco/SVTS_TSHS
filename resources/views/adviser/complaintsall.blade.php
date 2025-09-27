<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
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
    <li ><a href="{{ route('student.list') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-book"></i>Violation Record</a></li>
    <li class="active"><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i>Complaints</a></li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>
  <!-- Main Content -->
<div class="main-content">
  <header class="main-header">
    <div class="header-left"><h2>Violation Records</h2></div>
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
      <p>{{ $complaints->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>{{ $complaints->where('status', 'active')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>{{ $complaints->where('status', 'Cleared')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>{{ $complaints->count() }}</p>
    </div>
  </div>
</div>

  <!-- Table Controls -->
  <div class="table-container">
    <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
      <div>
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search students..." class="form-control">
      </div>
      <div style="display:flex; gap:10px;">
        <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Add Violation</button>
        <button id="archiveBtn" class="btn-warning"><i class="fas fa-archive"></i> Archive</button>
        <button id="PrntBtn" class="btn-primary"><i class="fas fa-print"></i> Print</button>
      </div>
    </div>


    <!-- Violation Table -->
    <div class="student-table-wrapper">
      <table id="violationTable" class="fixed-header">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>ID</th>
            <th>Student Name</th>
            <th>Grade</th>
            <th>Section</th>
            <th>Offense</th>
            <th>Category</th>
            <th>Points</th>
            <th>Sanction</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Sample row -->
          <tr data-description="Late submission" data-sanction="Warning - 1 point" onclick="showViolationInfo(this)">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1</td><td>Juan Dela Cruz</td><td>10</td><td>A</td>
            <td>Late Submission</td><td>Minor</td><td>1</td><td>Warning</td><td>Active</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CREATE VIOLATION MODAL -->
<div class="modal" id="createModal">
  <div class="modal-content">
    <span class="close" id="closeCreate">&times;</span>
    <form id="createForm" class="form-grid">
      <div class="form-column">
        <div class="form-group"><label>Student Name</label><input type="text" name="studentName" required></div>
        <div class="form-group"><label>Grade</label><input type="number" name="grade" min="1" max="12" required></div>
        <div class="form-group"><label>Section</label><input type="text" name="section" required></div>
        <div class="form-group"><label>Offense</label><input type="text" name="offense" required></div>
      </div>
      <div class="form-column">
        <div class="form-group"><label>Category</label>
          <select name="category"><option>Minor</option><option>Major</option><option>Severe</option></select>
        </div>
        <div class="form-group"><label>Points</label><input type="number" name="points" min="1" required></div>
        <div class="form-group"><label>Sanction</label><input type="text" name="sanction" required></div>
        <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
      </div>
      <div class="form-actions" style="margin-top:10px;">
        <button type="button" class="btn-secondary" id="closeCreateBtn">Cancel</button>
        <button type="submit" class="btn-create">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- INFO MODAL -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <h3>Violation Info</h3>
    <div id="infoModalBody"></div>
    <div style="text-align:right; margin-top:10px;">
      <button class="btn-secondary" onclick="closeInfoModal()">Close</button>
    </div>
  </div>
</div>

<script>
// ------------------ SIDEBAR DROPDOWN ------------------
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.classList.toggle('active');
    const dropdown = btn.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  });
});

// ------------------ PROFILE DROPDOWN ------------------
function toggleProfileDropdown() {
  document.getElementById('profileDropdown').classList.toggle('show');
}

// ------------------ PRINT TABLE ------------------
document.getElementById('PrntBtn').addEventListener('click', () => window.print());

// ------------------ SELECT ALL CHECKBOX ------------------
function updateRowCheckboxes() {
  const selectAll = document.getElementById('selectAll');
  const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
  if (selectAll) selectAll.addEventListener('change', () => rowCheckboxes.forEach(cb => cb.checked = selectAll.checked));
}
updateRowCheckboxes();

// ------------------ SHOW INFO MODAL ------------------
function showViolationInfo(row) {
  const cells = row.children;
  document.getElementById('infoModalBody').innerHTML = `
    <p><strong>ID:</strong> ${cells[1].innerText}</p>
    <p><strong>Student:</strong> ${cells[2].innerText}</p>
    <p><strong>Grade:</strong> ${cells[3].innerText}</p>
    <p><strong>Section:</strong> ${cells[4].innerText}</p>
    <p><strong>Offense:</strong> ${cells[5].innerText}</p>
    <p><strong>Category:</strong> ${cells[6].innerText}</p>
    <p><strong>Points:</strong> ${cells[7].innerText}</p>
    <p><strong>Sanction:</strong> ${cells[8].innerText}</p>
    <p><strong>Description:</strong> ${row.dataset.description}</p>
  `;
  document.getElementById('infoModal').classList.add('show-modal');
}
function closeInfoModal() { document.getElementById('infoModal').classList.remove('show-modal'); }

// ------------------ CREATE MODAL ------------------
const createBtn = document.getElementById('createBtn');
const createModal = document.getElementById('createModal');
const closeCreateBtn = document.getElementById('closeCreateBtn');
createBtn.addEventListener('click', () => createModal.classList.add('show-modal'));
closeCreateBtn.addEventListener('click', () => createModal.classList.remove('show-modal'));
document.getElementById('closeCreate').addEventListener('click', () => createModal.classList.remove('show-modal'));

// ------------------ SUBMIT CREATE FORM ------------------
const createForm = document.getElementById('createForm');
let violationId = document.querySelectorAll('#violationTable tbody tr').length + 1;
createForm.addEventListener('submit', e => {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(createForm).entries());
  const row = document.createElement('tr');
  row.dataset.description = data.description;
  row.innerHTML = `
    <td><input type="checkbox" class="rowCheckbox"></td>
    <td>${violationId}</td><td>${data.studentName}</td><td>${data.grade}</td><td>${data.section}</td>
    <td>${data.offense}</td><td>${data.category}</td><td>${data.points}</td><td>${data.sanction}</td><td>Active</td>
  `;
  row.addEventListener('click', () => showViolationInfo(row));
  document.querySelector('#violationTable tbody').appendChild(row);
  violationId++;
  createForm.reset();
  createModal.classList.remove('show-modal');
  updateRowCheckboxes();
});
</script>
</body>
</html>