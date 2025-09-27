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
<div class="sidebar" id="sidebar">
  <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-right arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
    </ul>

    <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-right arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

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
  <!-- Table Controls -->
  <div class="table-container">
    <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
      <div>
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search offenses..." class="form-control">
      </div>
      <div style="display:flex; gap:10px;">
        <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Create</button>
        <button id="archiveBtn" class="btn-warning"><i class="fas fa-archive"></i> Archive</button>
        <button id="PrntBtn" class="btn-print"><i class="fas fa-print"></i> Print</button>
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
          </tr>
        </thead>
        <tbody>
          <tr data-description="Late submission of assignment" data-sanction="Warning - 1 point" onclick="showOffenseInfo(this)">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1</td>
            <td>Late Submission</td>
            <td>Minor</td>
            <td>1</td>
            <td>Active</td>
          </tr>
          <tr data-description="Cheating on exam" data-sanction="Suspension - 5 points" onclick="showOffenseInfo(this)">
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>2</td>
            <td>Cheating</td>
            <td>Major</td>
            <td>5</td>
            <td>Active</td>
          </tr>
        </tbody>
      </table>
    </div>
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
</script>
</body>
</html>
