<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violation Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
    <li class="active"><a href="{{ route('violation.records') }}"><i class="fas fa-book"></i> Violation Record</a></li>
    <li><a href="{{ route('people.complaints') }}"><i class="fas fa-comments"></i> Complaints</a></li>
    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

<!-- Main Content -->
<div class="main-content">
  <header class="main-header">
    <div class="header-left"><h2>Violation Management</h2></div>
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
      <p>123</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>13</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>12</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>12</p>
    </div>
  </div>
</div>

   <!-- Table Switcher -->
    <div style="margin-bottom:15px;">
      <label for="tableSwitch" style="font-weight:600; margin-right:10px;">Show:</label>
      <select id="tableSwitch" style="padding:8px 12px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
        <option value="records" selected>Violation Records</option>
        <option value="appointments">Violation Appointments</option>
        <option value="anecdotal">Violation Anecdotal</option>
      </select>
    </div>
  <!-- Table Controls -->
  <div class="table-container">
    <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
      <div>
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search..." class="form-control">
      </div>
      <div style="display:flex; gap:10px;">
        <a href="{{ route('violations.create') }}" class="btn-create"><i class="fas fa-plus"></i> Add Violation</a>
        <button>Create Anecdotal</button>
        <button id="archiveBtn" class="btn-warning"><i class="fas fa-archive"></i> Archive</button>
        <button id="PrntBtn" class="btn-primary"><i class="fas fa-print"></i> Print</button>
      </div>
    </div>



    <!-- Student Table Wrapper -->
    <div class="student-table-wrapper">
      <table id="violationTable" class="fixed-header">

        <!-- Violation Records Table -->
        <thead id="recordsHead">
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>ID</th>
            <th>Student</th>
            <th>Offense</th>
            <th>Incident</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>

          </tr>
        </thead>
        <tbody id="recordsBody">
          <tr>
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1</td>
            <td>Juan Dela Cruz</td>
            <td>Tardiness</td>
            <td>Late arrival to class</td>
            <td>2025-09-28</td>
            <td>07:45 AM</td>
            <td>Active</td>
                   <td>
      <button class="btn-edit" onclick="editStudentRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>

          </tr>
        </tbody>

        <!-- Violation Appointments Table -->
        <thead id="appointmentsHead" style="display:none;">
          <tr>
            <th><input type="checkbox" id="selectAll2"></th>
            <th>Appointment ID</th>
            <th>Violation ID</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Appointment Status</th>
            <th>Status</th>
                        <th>Action</th>
          </tr>
        </thead>
        <tbody id="appointmentsBody" style="display:none;">
          <tr>
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1</td>
            <td>1</td>
            <td>2025-09-30</td>
            <td>10:00 AM</td>
            <td>Pending</td>
            <td>Active</td>
                   <td>
      <button class="btn-edit" onclick="editStudentRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>
            <td>
      <button class="btn-edit" onclick="editStudentRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>
          </tr>
        </tbody>

        <!-- Violation Anecdotal Table -->
        <thead id="anecdotalHead" style="display:none;">
          <tr>
            <th><input type="checkbox" id="selectAll3"></th>
            <th>Anecdotal ID</th>
            <th>Violation ID</th>
            <th>Solution</th>
            <th>Recommendation</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
                        <th>Action</th>
          </tr>
        </thead>
        <tbody id="anecdotalBody" style="display:none;">
          <tr>
            <td><input type="checkbox" class="rowCheckbox"></td>
            <td>1</td>
            <td>1</td>
            <td>Counseling session conducted</td>
            <td>Monitor student punctuality</td>
            <td>2025-09-28</td>
            <td>08:30 AM</td>
            <td>Active</td>
            <td>
      <button class="btn-edit" onclick="editStudentRow(this)">
        <i class="fas fa-edit"></i> Edit
      </button>
    </td>
          </tr>
        </tbody>

      </table>
    </div>
  </div>
</div>


  <!-- Modal -->
  <!-- Details Modal -->

  <div class="modal" id="detailsModal">
    <div class="modal-content">
      <h3>Violation Details</h3>
      <p id="violationDetails">Details about the selected violation...</p>
      <div class="modal-footer">
        <button class="btn-info" id="setScheduleBtn">📅 Set Schedule</button>
        <button class="btn-secondary">📨 Send SMS</button>
        <button class="btn-close" id="closeDetails">❌ Close</button>
      </div>
    </div>
  </div>



<!-- JS SCRIPT -->
<script>
// PROFILE DROPDOWN
function toggleProfileDropdown() {
  document.getElementById('profileDropdown').classList.toggle('show');
}

// PRINT
document.getElementById('PrntBtn').addEventListener('click', () => window.print());

// TABLE SWITCH
const switcher = document.getElementById('tableSwitch');
const recordsHead = document.getElementById('recordsHead');
const recordsBody = document.getElementById('recordsBody');
const appointmentsHead = document.getElementById('appointmentsHead');
const appointmentsBody = document.getElementById('appointmentsBody');
const anecdotalHead = document.getElementById('anecdotalHead');
const anecdotalBody = document.getElementById('anecdotalBody');

switcher.addEventListener('change', () => {
  const value = switcher.value;

  // Hide all
  [recordsHead, recordsBody, appointmentsHead, appointmentsBody, anecdotalHead, anecdotalBody]
    .forEach(el => el.style.display = 'none');

  // Show selected
  if (value === 'records') {
    recordsHead.style.display = 'table-header-group';
    recordsBody.style.display = 'table-row-group';
  } else if (value === 'appointments') {
    appointmentsHead.style.display = 'table-header-group';
    appointmentsBody.style.display = 'table-row-group';
  } else {
    anecdotalHead.style.display = 'table-header-group';
    anecdotalBody.style.display = 'table-row-group';
  }
});

 // Clickable rows (Violation Records only)
    const violationRows = violationTable.querySelectorAll('tbody tr');
    const modal = document.getElementById('detailsModal');
    const closeBtn = document.getElementById('closeDetails');
    const detailText = document.getElementById('violationDetails');

    violationRows.forEach(row => {
      row.addEventListener('click', () => {
        const data = Array.from(row.children).map(td => td.textContent);
        detailText.textContent = `Violation ID: ${data[0]}, Student: ${data[1]}, Offense: ${data[2]}, Date: ${data[3]}, Time: ${data[4]}`;
        modal.style.display = 'flex';
      });
    });

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    window.addEventListener('click', e => {
      if (e.target === modal) modal.style.display = 'none';
    });
</script>
</body>
</html>
