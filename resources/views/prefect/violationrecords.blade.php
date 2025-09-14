<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Violation Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
  font-weight: bold;
  transition: all 0.2s ease-in-out;
}

body {
  display: flex;
  background: #f9f9f9;
  color: #111;
}

/* Sidebar (untouched) */
.sidebar {
  width: 230px;
  background:rgb(73, 0, 0);
  color: #fff;
  height: 100vh;
  position: fixed;
  padding: 25px 15px;
  border-radius: 0 15px 15px 0;
  box-shadow: 2px 0 15px rgba(0,0,0,0.5);
  overflow-y: auto;
}

.sidebar h2 {
  margin-bottom: 30px;
  text-align: center;
  font-size: 22px;
  letter-spacing: 1px;
  color: #ffffff;
  text-transform: uppercase;
  border-bottom: 2px solid rgba(255, 255, 255, 0.15);
  padding-bottom: 10px;
}

.sidebar ul { list-style: none; }
.sidebar ul li {
  padding: 12px 14px;
  display: flex;
  align-items: center;
  cursor: pointer;
  border-radius: 10px;
  font-size: 15px;
  color: #e0e0e0;
  transition: background 0.3s, transform 0.2s;
}
.sidebar ul li i {
  margin-right: 12px;
  color: #cfcfcf;
  min-width: 20px;
  font-size: 16px;
}
.sidebar ul li:hover {
  background: #2d3f55;
  transform: translateX(5px);
  color: #fff;
}
.sidebar ul li:hover i { color: #00e0ff; }
.sidebar ul li.active {
  background: #00aaff;
  color: #fff;
  border-left: 4px solid #ffffff;
}
.sidebar ul li.active i { color: #fff; }
.sidebar ul li a {
  text-decoration: none;
  color: inherit;
  flex: 1;
}
.section-title {
  margin: 20px 10px 8px;
  font-size: 11px;
  text-transform: uppercase;
  font-weight: bold;
  color: rgba(255, 255, 255, 0.6);
  letter-spacing: 1px;
}

/* Dropdown */
.dropdown-container { display: none; list-style: none; padding-left: 25px; }
.dropdown-container li { padding: 10px; font-size: 14px; border-radius: 8px; color: #ddd; }
.dropdown-container li:hover { background: #3a4c66; color: #fff; }
.dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
.dropdown-btn.active .arrow { transform: rotate(180deg); }

/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 3px; }

/* Main Content */
.main-content {
  margin-left: 250px;
  padding: 30px;
  width: calc(100% - 250px);
}

.crud-container {
  background: #fff;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.crud-container h2 {
  font-size: 28px;
  margin-bottom: 20px;
  color: #0a1e2d;
}

/* Search + Create */
.search-create {
  display: flex;
  margin-bottom: 20px;
  align-items: center;
}

.search-create input[type="text"] {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  outline: none;
  font-size: 14px;
  width: 250px;
  margin-right: 12px;
  transition: all 0.2s;
}
.search-create input[type="text"]:focus {
  border-color: #007BFF;
  box-shadow: 0 0 8px rgba(0,123,255,0.2);
}

/* Improved Table */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  font-size: 14px;
}

table th, table td {
  padding: 14px 12px;
  text-align: center;
  border-bottom: 1px solid #e0e0e0;
  font-weight: 500;
  color: #333;
}

table thead {
  background: linear-gradient(90deg, #007BFF, #00aaff);
  color: #fff;
  text-transform: uppercase;
  font-size: 13px;
}

table tr:nth-child(even) { background-color: #f4f7fa; }
table tr:hover {
  background-color: #e0f0ff;
  transform: scale(1.01);
  transition: all 0.2s ease-in-out;
}

/* Buttons */
.btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease-in-out;
}

.btn i { font-size: 14px; }

/* Info Button */
.btn-info {
  background-color: #17a2b8;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.btn-info:hover {
  background-color: #138496;
  transform: translateY(-2px) scale(1.02);
}

/* Edit Button */
.btn-edit {
  background-color: #ffc107;
  color: #000;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.btn-edit:hover {
  background-color: #e0a800;
  transform: translateY(-2px) scale(1.02);
}

/* Delete Button */
.btn-delete {
  background-color: #dc3545;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.btn-delete:hover {
  background-color: #c82333;
  transform: translateY(-2px) scale(1.02);
}

/* Create Button */
.btn-create {
  background-color: #28a745;
  color: #fff;
  border-radius: 8px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: all 0.2s ease-in-out;
}
.btn-create:hover {
  background-color: #218838;
  transform: translateY(-2px) scale(1.02);
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal.show { display: flex; animation: fadeIn 0.3s ease-in-out; }

@keyframes fadeIn { from{opacity:0} to{opacity:1} }

.modal-content {
  background: #fff;
  padding: 25px;
  width: 100%;
  max-width: 500px;
  border-radius: 10px;
  position: relative;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.modal-content h5 { margin-bottom: 15px; color: #007bff; }

.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  cursor: pointer;
  font-size: 20px;
  color: #555;
  transition: 0.2s;
}

.modal-content .close:hover { color: #000; }

.info-box p { margin: 8px 0; font-size: 15px; }

/* Responsive */
@media screen and (max-width:768px) {
  .main-content { margin-left: 0; padding: 15px; }
  table th, table td { font-size: 12px; padding: 8px; }
  .btn { font-size: 12px; padding: 4px 8px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li class="active"><a href="{{ route('violation.records') }}">Violation Record</a></li>
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

<!-- Main Content -->
<div class="main-content">
  <div class="crud-container">
    <h2>Student Violations</h2>
    <div class="search-create">
      <input type="text" id="searchInput" placeholder="Search student name...">
      <button class="btn-create"><i class="fas fa-plus"></i> Create Violation</button>
    </div>
    <table id="violationTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Student Name</th>
          <th>Violation</th>
          <th>Adviser</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($violations as $index => $violation)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
          <td>{{ $violation->offense->offense_type }}</td>
          <td>{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}</td>
          <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F d, Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
          <td>
            <button class="btn btn-info"
              onclick="showInfo('{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}', '{{ $violation->student->parent->parent_fname }} {{ $violation->student->parent->parent_lname }}', '{{ $violation->student->parent->parent_contactinfo }}', '{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}')">
              <i class="fas fa-info-circle"></i> Info
            </button>
            <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;">No violations found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Info Modal -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('infoModal').classList.remove('show')">&times;</span>
    <h5>Violation Details</h5>
    <div class="info-box">
      <p><strong>Student Name:</strong> <span id="modalStudent">N/A</span></p>
      <p><strong>Parent Name:</strong> <span id="modalParent">N/A</span></p>
      <p><strong>Parent Contact:</strong> <span id="modalNumber">N/A</span></p>
      <p><strong>Adviser:</strong> <span id="modalAdviser">N/A</span></p>
    </div>
  </div>
</div>

<script>
// Dropdown functionality
const sidebar = document.querySelector('.sidebar');
const dropdowns = document.querySelectorAll('.dropdown-btn');

dropdowns.forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.nextElementSibling;
    dropdowns.forEach(otherBtn => {
      const otherContainer = otherBtn.nextElementSibling;
      if (otherBtn !== btn) {
        otherBtn.classList.remove('active');
        otherContainer.style.display = 'none';
      }
    });
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
    const openDropdowns = document.querySelectorAll('.dropdown-container[style*="block"]').length;
    sidebar.style.overflowY = openDropdowns >= 1 ? 'auto' : 'hidden';
  });
});

// Modal Info
function showInfo(student, parent, number, adviser) {
  document.getElementById('modalStudent').innerText = student;
  document.getElementById('modalParent').innerText = parent;
  document.getElementById('modalNumber').innerText = number;
  document.getElementById('modalAdviser').innerText = adviser;
  document.getElementById('infoModal').classList.add('show');
}
</script>

</body>
</html>
