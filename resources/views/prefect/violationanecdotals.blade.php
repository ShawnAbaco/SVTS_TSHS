<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Anecdotal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
<style>
/* Base Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-weight: bold;
}

body {
  font-family: 'Roboto', Arial, sans-serif;
  background-color: #f4f6f8;
  color: #2c3e50;
  line-height: 1.6;
}

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
    background:rgb(0, 0, 0); 
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
      color: rgb(255, 255, 255);
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      padding: 12px 10px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 8px;
      font-size: 14px;
      color: #fff;
      transition: 0.3s;
      position: relative;
    }
    .sidebar ul li i {
      margin-right: 12px;
      color: rgb(255, 255, 255);
      min-width: 20px;
    }
    .sidebar ul li:hover {
      background: rgb(0, 221, 255);
      color: #111;
    }
    .sidebar ul li:hover i {
      color: #111;
    }
    .sidebar ul li.active {
      background: rgb(11, 222, 255);
      color: #111;
    }
    .sidebar ul li.active i {
      color: #111;
    }

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
      transition: max-height 0.3s ease;
    }
    .dropdown-container li {
      padding: 10px;
      font-size: 13px;
      border-radius: 6px;
      cursor: pointer;
    }
    .dropdown-container li:hover {
      background: rgb(255, 255, 255);
      color: #111;
    }
    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }
    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background: rgb(255, 255, 255);
      border-radius: 3px;
    }
/* Main Content */
.main-content {
  margin-left: 260px;
  padding: 30px;
}

h2 {
  font-size: 26px;
  margin-bottom: 20px;
  color: #007bff;
}

/* Top Controls */
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

.top-controls button {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.top-controls button:hover {
  background: #0056b3;
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.1);
  background: #fff;
}

th {
  background-color: #007bff;
  color: #fff;
  padding: 14px;
  text-align: left;
  font-size: 15px;
}

td {
  padding: 12px;
  border-bottom: 1px solid #eaeaea;
  font-size: 14px;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #eef3fb;
  transition: 0.3s ease;
}

/* Status Labels */
.status-pending {
  color: #e67e22;
}

.status-resolved {
  color: #27ae60;
}

/* Responsive */
@media screen and (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 15px;
  }

  th, td {
    padding: 8px;
    font-size: 13px;
  }

  .top-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .top-controls input {
    width: 100%;
  }
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
    <!-- Active Page -->
    <li ><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <!-- Active Page -->
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

  <!-- Search + Create Button -->
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
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  // Dropdown functionality: only one open at a time, no sidebar scroll
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

  // Placeholder for create form
  function openCreateForm() {
    alert("Open Create Violation Anecdotal form/modal here.");
  }
</script>


</body>
</html>
