<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Complaints Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
/* --- Reset --- */
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

/* --- Sidebar (untouched) --- */
.sidebar {
  width: 230px;
  background: rgb(73, 0, 0);
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
.sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
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

/* --- Main Content --- */
.content {
  margin-left: 270px;
  padding: 30px;
}

h1 {
  font-size: 26px;
  margin-bottom: 20px;
  color: #007bff;
}

/* --- Top Controls --- */
.top-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.top-controls input {
  padding: 10px 15px;
  width: 300px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.top-controls button {
  border: none;
  padding: 10px 16px;
  font-size: 15px;
  border-radius: 8px;
  cursor: pointer;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #007bff, #00aaff);
  transition: all 0.3s ease;
}
.top-controls button:hover {
  background: linear-gradient(135deg, #0056b3, #007bbf);
  transform: translateY(-2px);
}

/* --- Table Styling (Big & Modern) --- */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  background: #ffffff;
  font-size: 16px;
}

th {
  background: linear-gradient(135deg, #007bff, #00aaff);
  color: #fff;
  padding: 16px 14px;
  text-align: left;
  position: sticky;
  top: 0;
  z-index: 2;
}

td {
  padding: 14px 12px;
  border-bottom: 1px solid #e3e3e3;
  vertical-align: middle;
}

tr:nth-child(even) { background: #f5f8ff; }
tr:hover {
  background: #d0e7ff;
  transform: scale(1.01);
  transition: all 0.2s ease-in-out;
}

/* Status labels */
.status-pending {
  color: #e67e22;
  background: #fff4e5;
  padding: 6px 12px;
  border-radius: 14px;
  font-weight: 600;
  text-align: center;
  display: inline-block;
}

.status-resolved {
  color: #27ae60;
  background: #e6f9f0;
  padding: 6px 12px;
  border-radius: 14px;
  font-weight: 600;
  text-align: center;
  display: inline-block;
}

/* Action buttons */
.btn-action {
  padding: 8px 14px;
  font-size: 15px;
  border-radius: 10px;
  cursor: pointer;
  color: #fff;
  margin-right: 6px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  transition: all 0.2s ease-in-out;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-action i { margin-right: 5px; }

.btn-edit { background: #28a745; }
.btn-edit:hover { background: #218838; transform: translateY(-2px) scale(1.05); }

.btn-delete { background: #dc3545; }
.btn-delete:hover { background: #c82333; transform: translateY(-2px) scale(1.05); }

/* Responsive adjustments */
@media screen and (max-width: 1200px) {
  table { font-size: 15px; }
  th, td { padding: 12px 10px; }
}

@media screen and (max-width: 768px) {
  .content { margin-left: 0; padding: 15px; }
  table { font-size: 14px; }
  th, td { padding: 10px 8px; }
  .btn-action { padding: 6px 10px; font-size: 13px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
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
      <li class="active"><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

<!-- Content -->
<div class="content">
  <h1>Complaints Management</h1>

  <div class="top-controls">
    <input type="text" id="searchInput" onkeyup="searchComplainant()" placeholder="Search Complainant Name...">
    <button class="btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Add Complainant</button>
  </div>

  <table id="complaintsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Offense</th>
        <th>Sanction</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($complaints as $complaint)
      <tr>
        <td>{{ $complaint->complaints_id }}</td>
        <td>{{ $complaint->complainant->student_fname }} {{ $complaint->complainant->student_lname }}</td>
        <td>{{ $complaint->respondent->student_fname }} {{ $complaint->respondent->student_lname }}</td>
        <td>{{ $complaint->offense->offense_type }}</td>
        <td>{{ $complaint->offense->sanction_consequences }}</td>
        <td>{{ $complaint->complaints_date }}</td>
        <td>{{ \Carbon\Carbon::parse($complaint->complaints_time)->format('h:i A') }}</td>
        <td class="{{ $complaint->status == 'Resolved' ? 'status-resolved' : 'status-pending' }}">
            {{ $complaint->status ?? 'Pending' }}
        </td>
        <td>
          <button class="btn-action btn-edit"><i class="fas fa-edit"></i>Edit</button>
          <button class="btn-action btn-delete"><i class="fas fa-trash"></i>Delete</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
// Dropdown functionality
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
  });
});

// Logout
function logout() {
  fetch("/logout", {
    method: "POST",
    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
  }).then(() => window.location.href = "/prefect/login");
}

// Search filter
function searchComplainant() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let table = document.getElementById("complaintsTable");
  let tr = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    let td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      let textValue = td.textContent || td.innerText;
      tr[i].style.display = textValue.toLowerCase().includes(input) ? "" : "none";
    }
  }
}

function openModal() {
  alert("Open Add Complainant form/modal here.");
}
</script>

</body>
</html>
