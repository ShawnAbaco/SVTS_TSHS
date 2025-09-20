<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Appointment</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">


</head>
<body>
  <style>

    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --hover-bg: rgb(0, 88, 240);
      --shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
      --btn-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

    body {
      display: flex;
      min-height: 100vh;
      background-color: #f5f5f5;
    }
/* --- Sidebar --- */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100%;
  /* Gradient background */
  background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
  font-family: "Segoe UI", Tahoma, sans-serif;
  z-index: 1000;
  overflow-y: auto;
  transition: all 0.3s ease;
  color: #ffffff;
  font-weight: bold;
  -webkit-font-smoothing: antialiased; /* smooth fonts for high-res */
  -moz-osx-font-smoothing: grayscale;
  image-rendering: optimizeQuality; /* high-res image rendering */
}

/* Sidebar scroll */
.sidebar::-webkit-scrollbar { width: 8px; }
.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}
.sidebar::-webkit-scrollbar-track {
  background-color: rgba(255, 255, 255, 0.05);
}

/* Logo */
.sidebar img {
  width: 180px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}

/* Sidebar Title */
.sidebar p {
  font-size: 1.6rem;
  font-weight: 900;
  margin: 0 0 1rem;
  color: #ffffff;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0,0,0,0.4);
}

/* Sidebar Links */
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 22px;
  color: #ffffff;
  text-decoration: none;
  font-size: 1rem;
  font-weight: bold;
  border-left: 4px solid transparent;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.sidebar ul li a i {
  font-size: 1.2rem;
  min-width: 22px;
  text-align: center;
  color: #ffffff;
  transition: color 0.3s ease;
}

/* Hover & Active */
.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: rgba(255,255,255,0.15);
  border-left-color: #FFD700;
  color: #ffffff !important;
}

/* Dropdown */
.dropdown-container {
  max-height: 0;
  overflow: hidden;
  background-color: rgba(255,255,255,0.05);
  transition: max-height 0.4s ease, padding 0.4s ease;
  border-left: 2px solid rgba(255,255,255,0.1);
  border-radius: 0 8px 8px 0;
}
.dropdown-container.show {
  max-height: 400px;
  padding-left: 12px;
}
.dropdown-container li a {
  font-size: 0.9rem;
  padding: 10px 20px;
  color: #ffffff;
  font-weight: bold;
}
.dropdown-container li a:hover {
  background-color: rgba(255,255,255,0.15);
  color: #ffffff;
}
.dropdown-btn .fa-caret-down {
  margin-left: auto;
  transition: transform 0.3s ease;
  color: #ffffff;
}


    /* Main content */
    .main-content {
      margin-left: 260px;
      padding: 2.5rem;
      flex-grow: 1;
      background: #fff;
      min-height: 100vh;
    }
    h1 { margin-bottom: 25px; font-size: 2rem; }
.toolbar {
  display: flex;
  justify-content: space-between; /* left title, right buttons */
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.toolbar-left h1 {
  margin: 0;
  font-size: 2rem;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

#searchInput {
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  max-width: 250px;
}

.btn-primary {
  padding: 12px 22px;
  font-size: 1rem;
  font-weight: 700;
  background: rgb(0, 88, 240);
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  box-shadow: var(--btn-shadow);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-primary:hover { background: rgb(0, 70, 196); transform: translateY(-2px); }

.btn-archive {
  background: #e63946;
}

.btn-archive:hover { background: #c1273b; }



    .btn-edit { background: #3498db; color: #fff; }
    .btn-delete { background: #e74c3c; color: #fff; }
    .btn-edit, .btn-delete {
      padding: 8px 12px; border: none; border-radius: 6px;
      cursor: pointer; transition: all 0.3s ease;
    }
    .btn-edit:hover, .btn-delete:hover { transform: translateY(-2px); }

    /* Card */
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-bottom: 25px;
    }
    .card-header { font-size: 1.3rem; margin-bottom: 15px; }

    /* Table */
    table {
      width: 100%; border-collapse: separate; border-spacing: 0;
      background: #fff; border-radius: 12px; overflow: hidden;
      box-shadow: var(--shadow); font-size: 16px;
    }
    th, td {
      padding: 14px 18px;
      text-align: center;
      vertical-align: middle;
    }
    th {
      background: #000; color: #fff;
      font-size: 1rem;
    }
    tr:nth-child(even) { background: #f7f7f7; }
    tr:hover { background: rgba(0,0,0,0.05); }

    /* Modal */
    .modal {
      display: none; position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center; align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 25px 35px;
      border-radius: 12px;
      max-width: 550px;
      width: 100%;
      position: relative;
      box-shadow: var(--shadow);
    }
    .modal-content h2 { margin-bottom: 25px; font-size: 1.5rem; }
    .form-group { margin-bottom: 18px; }
    label { display: block; margin-bottom: 8px; font-weight: 600; }
    input, select {
      width: 100%; padding: 10px 12px;
      border: 1px solid #ccc; border-radius: 6px;
    }
    .close {
      position: absolute; top: 12px; right: 15px;
      font-size: 1.5rem; cursor: pointer; color: #000;
    }

    </style>
  <!-- Sidebar -->
  <nav class="sidebar">
    <div style="text-align:center; margin-bottom:1rem;">
      <img src="/images/Logo.png" alt="Logo"><p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}" class="active">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
          <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
          <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
        </ul>
      </li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
<li>
    <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>    </ul>
  </nav>

  <div class="main-content">
  <div class="toolbar">
    <!-- Left side: title -->
    <div class="toolbar-left">
      <h1>Violation Appointments</h1>
    </div>
   <div class="toolbar">
  <div class="toolbar-right">
    <input type="text" id="searchInput" placeholder="Search appointments...">
    <button class="btn-primary" onclick="openModal('createModal')"><i class="fas fa-plus"></i> Create </button>
    <button class="btn-primary" style="background:#e63946;"><i class="fas fa-archive"></i> Archive</button>
  </div>
</div>



    <div class="card">
      <div class="card-header">Scheduled Appointments</div>
      <div class="card-body">
        <table id="appointmentTable">
          <thead>
            <tr>
              <th>#</th><th>Student</th><th>Parent</th><th>Contact</th>
              <th>Incident</th><th>Offense</th><th>Date</th><th>Time</th>
              <th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="appointmentList">
            @foreach($appointments as $index => $app)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $app->violation->student->student_fname }} {{ $app->violation->student->student_lname }}</td>
              <td>{{ $app->violation->student->parent->parent_fname ?? 'N/A' }} {{ $app->violation->student->parent->parent_lname ?? '' }}</td>
              <td>{{ $app->violation->student->parent->parent_contactinfo ?? 'N/A' }}</td>
              <td>{{ $app->violation->violation_incident }}</td>
              <td>{{ $app->violation->offense->offense_type ?? 'N/A' }}</td>
              <td>{{ $app->violation_app_date }}</td>
              <td>{{ \Carbon\Carbon::parse($app->violation_app_time)->format('h:i A') }}</td>
              <td>{{ $app->violation_app_status }}</td>
              <td>
                <button class="btn-edit"><i class="fas fa-edit"></i></button>
                <button class="btn-delete"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="createModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('createModal')">&times;</span>
      <h2>Create Schedule Appointment</h2>
      <form method="POST" action="{{ route('violation.appointment.store') }}">
        @csrf
        <div class="form-group">
          <label for="studentSelect">Select Student</label>
          <select id="studentSelect" name="violation_id" required>
            @foreach($students as $student)
              @foreach($student->violations as $violation)
                <option value="{{ $violation->violation_id }}">
                  {{ $student->student_fname }} {{ $student->student_lname }} - {{ $violation->offense->offense_type ?? '' }}
                </option>
              @endforeach
            @endforeach
          </select>
        </div>
        <div class="form-group"><label for="date">Date</label><input type="date" name="violation_app_date" id="date" required></div>
        <div class="form-group"><label for="time">Time</label><input type="time" name="violation_app_time" id="time" required></div>
        <div class="form-group">
          <label for="status">Status</label>
          <select name="violation_app_status" id="status" required>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Appointment</button>
      </form>
    </div>
  </div>

  <script>
      // Dropdown functionality - auto close others & scroll
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // close all other dropdowns
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this) {
                otherBtn.nextElementSibling.classList.remove('show');
                otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
            }
        });

        // toggle clicked dropdown
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
            container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

        // scroll into view if dropdown is opened
        if(container.classList.contains('show')){
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});



// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function logout() {
      fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      }).then(() => window.location.href = '/admin/login');
    }

    // Live search
    document.getElementById("searchInput").addEventListener("keyup", function() {
      let filter = this.value.toLowerCase();
      document.querySelectorAll("#appointmentTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
      });
    });
  </script>
</body>
</html>
