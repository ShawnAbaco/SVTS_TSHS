<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Anecdotal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
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
  background:rgb(0, 0, 0); 
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

/* --- Main Content --- */
.main-content {
  margin-left: 260px;
  padding: 30px;
}

h2 {
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
  width: 250px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.top-controls button,
.btn-action {
  border: none;
  padding: 10px 16px;
  font-size: 15px;
  border-radius: 8px;
  cursor: pointer;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

.top-controls button {
  background: linear-gradient(135deg, #007bff, #00aaff);
  display: flex;
  align-items: center;
  gap: 6px;
}

.top-controls button:hover {
  background: linear-gradient(135deg, #0056b3, #007bbf);
  transform: translateY(-2px);
}

/* --- Table Styling --- */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 25px rgba(0,0,0,0.1);
  background: #ffffff;
  font-size: 15px;
}

th {
  background: linear-gradient(135deg, #007bff, #00aaff);
  color: #fff;
  padding: 14px 12px;
  text-align: left;
  position: sticky;
  top: 0;
  z-index: 2;
}

td {
  padding: 12px 10px;
  border-bottom: 1px solid #e3e3e3;
  vertical-align: middle;
}

tr:nth-child(even) {
  background: #f9faff;
}

tr:hover {
  background: #d0e7ff;
  transform: scale(1.01);
  transition: all 0.2s ease-in-out;
}

/* --- Status Labels --- */
.status-pending {
  color: #e67e22;
  background: #fff4e5;
  padding: 4px 10px;
  border-radius: 12px;
  font-weight: 600;
  text-align: center;
}

.status-resolved {
  color: #27ae60;
  background: #e6f9f0;
  padding: 4px 10px;
  border-radius: 12px;
  font-weight: 600;
  text-align: center;
}

/* --- Action Buttons --- */
.btn-action {
  padding: 6px 12px;
  font-size: 14px;
  border-radius: 8px;
  cursor: pointer;
  color: #fff;
  margin-right: 5px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: all 0.2s ease-in-out;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-action i {
  margin-right: 4px;
}

.btn-edit {
  background: #28a745;
}
.btn-edit:hover {
  background: #218838;
  transform: translateY(-2px) scale(1.05);
}

.btn-delete {
  background: #dc3545;
}
.btn-delete:hover {
  background: #c82333;
  transform: translateY(-2px) scale(1.05);
}

/* --- Responsive --- */
@media screen and (max-width: 768px) {
  .main-content { margin-left: 0; padding: 15px; }
  th, td { padding: 10px; font-size: 14px; }
  .top-controls { flex-direction: column; align-items: flex-start; gap: 10px; }
  .top-controls input { width: 100%; }
  .btn-action { padding: 6px 10px; font-size: 13px; }
}
</style>
</head>
<body>
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
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li class="active" ><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
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
  <h2>Violation Anecdotal Reports</h2>

  <div class="top-controls">
    <input type="text" id="searchInput" placeholder="Search student name...">
    <button onclick="openCreateForm()"><i class="fas fa-plus"></i> Create Violation Anecdotal</button>
  </div>

  <table id="anecTable">
    <thead>
      <tr>
        <th>Student Name</th>
        <th>Violation</th>
        <th>Respondent</th>
        <th>Solution</th>
        <th>Punishment</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($violation_anecdotals as $anec)
      <tr>
        <td>{{ $anec->violation->student->student_fname }} {{ $anec->violation->student->student_lname }}</td>
        <td>{{ $anec->violation->offense->offense_type }}</td>
        <td>{{ $anec->violation->student->adviser->adviser_fname ?? 'Prefect' }}</td>
        <td>{{ $anec->violation_anec_solution }}</td>
        <td>{{ $anec->violation->offense->sanction_consequences }}</td>
        <td>{{ $anec->violation_anec_date }}</td>
        <td>{{ \Carbon\Carbon::parse($anec->violation_anec_time)->format('h:i A') }}</td>
        <td class="{{ $anec->violation->status == 'Resolved' ? 'status-resolved' : 'status-pending' }}">
            {{ $anec->violation->status ?? 'Pending' }}
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
  document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#anecTable tbody tr");
    rows.forEach(row => {
      let studentName = row.cells[0].innerText.toLowerCase();
      row.style.display = studentName.includes(filter) ? "" : "none";
    });
  });

  function openCreateForm() {
    alert("Open Create Violation Anecdotal form/modal here.");
  }
</script>

</body>
</html>
