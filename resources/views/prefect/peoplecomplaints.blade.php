<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Complaints Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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

.sidebar {
  width: 220px;
  background: #000; 
  color: #fff;
  height: 100vh;
  position: fixed;
  padding: 25px 15px;
  border-radius: 0 15px 15px 0;
  box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}

.sidebar h2 {
  margin-bottom: 30px;
  text-align: center;
  font-size: 20px;
  letter-spacing: 1px;
  color:#fff;
}
.sidebar ul { list-style: none; }
.sidebar ul li {
  padding: 12px 10px;
  display: flex;
  align-items: center;
  cursor: pointer;
  border-radius: 8px;
  font-size: 14px;
  color: #fff;
  transition: 0.3s;
}
.sidebar ul li i {
  margin-right: 12px;
  color:#fff;
  min-width: 20px;
}
.sidebar ul li:hover {
  background:rgb(0, 247, 239);
  color: #111;
}
.sidebar ul li:hover i { color: #111; }
.sidebar ul li.active {
  background:rgb(11, 255, 235);
  color: #111;
}
.sidebar ul li.active i { color: #111; }
.sidebar ul li a {
  text-decoration: none;
  color: inherit;
  flex: 1;
}
.section-title {
  margin: 15px 10px 5px;
  font-size: 11px;
  text-transform: uppercase;
  color: #bbb;
}

/* Dropdown */
.dropdown-container {
  display: none;
  list-style: none;
  padding-left: 20px;
}
.dropdown-container li {
  padding: 10px;
  font-size: 13px;
  border-radius: 6px;
  cursor: pointer;
}
.dropdown-container li:hover {
  background:#fff;
  color: #111;
}
.dropdown-btn .arrow {
  margin-left: auto;
  transition: transform 0.3s;
}
.dropdown-btn.active .arrow { transform: rotate(180deg); }

/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-thumb {
  background:#fff;
  border-radius: 3px;
}

.content {
  margin-left: 270px;
  padding: 20px;
}

h1 {
  text-align: center;
  margin-bottom: 20px;
}

.btn-primary {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  margin-bottom: 15px;
}
.btn-primary:hover { background: #0056b3; }

.btn-edit {
  background: #ffc107;
  color: #fff;
  padding: 6px 12px;
  border-radius: 5px;
  text-decoration: none;
}
.btn-edit:hover { background: #e0a800; }

.btn-delete {
  background: #dc3545;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
}
.btn-delete:hover { background: #c82333; }

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
table thead {
  background: #007bff;
  color: #fff;
}
table th, table td {
  padding: 12px 15px;
  border-bottom: 1px solid #ddd;
  text-align: center;
  font-size: 14px;
}
table tbody tr:hover { background: #f1f1f1; }

/* Search bar styling */
.search-container {
  margin: 15px 0;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
.search-container input {
  padding: 8px 12px;
  width: 250px;
  border: 1px solid #ccc;
  border-radius: 5px;
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
  <button class="btn-primary" onclick="openModal()">+ Add Complainant</button>

  <!-- Search Bar -->
  <div class="search-container">
    <input type="text" id="searchInput" onkeyup="searchComplainant()" placeholder="Search Complainant Name...">
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
        <td>
          <a href="{{ route('complaints.edit', $complaint->complaints_id) }}" class="btn-edit">Edit</a>
          <form action="{{ route('complaints.destroy', $complaint->complaints_id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn-delete" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
// Dropdown functionality with sidebar scroll and only one open at a time
  const sidebar = document.querySelector('.sidebar');
  const dropdowns = document.querySelectorAll('.dropdown-btn');

  dropdowns.forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;

      // Close other dropdowns
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) {
          otherBtn.classList.remove('active');
          otherContainer.style.display = 'none';
        }
      });

      // Toggle current dropdown
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';

      // Sidebar scrollable when at least 1 dropdown is open
      const openDropdowns = document.querySelectorAll('.dropdown-container[style*="block"]').length;
      sidebar.style.overflowY = openDropdowns >= 1 ? 'auto' : 'hidden';
    });
  });

// ✅ Logout
function logout() {
  fetch('/logout', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => window.location.href = '/prefect/login');
}

// ✅ Search complainant
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
</script>

</body>
</html>
