<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Appointment</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
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

/* Sidebar */
 .sidebar {
      width: 230px;
background: linear-gradient(135deg, #002200, #004400, #006600, #008800);

background-repeat: no-repeat;
background-attachment: fixed;
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

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px 14px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 10px;
      font-size: 15px;
      color:rgb(255, 255, 255);
      transition: background 0.3s, transform 0.2s;
    }

    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
      font-size: 16px;
    }

    .sidebar ul li:hover {
      background: #2d3f55;
      transform: translateX(5px);
      color: #fff;
    }

    .sidebar ul li:hover i {
      color: #00e0ff;
    }

    .sidebar ul li.active {
      background: #00aaff;
      color: #fff;
      border-left: 4px solid #ffffff;
    }

    .sidebar ul li.active i {
      color: #fff;
    }

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

    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 25px;
    }

    .dropdown-container li {
      padding: 10px;
      font-size: 14px;
      border-radius: 8px;
      color: #ddd;
    }

    .dropdown-container li:hover {
      background: #3a4c66;
      color: #fff;
    }

    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.25);
      border-radius: 3px;
    }
/* Main Content */
.container {
  margin-left: 250px;
  padding: 30px;
  width: calc(100% - 250px);
}

/* Heading */
h1 { font-size: 28px; margin-bottom: 20px; color: #2c3e50; }

/* Search + Buttons */
.search-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}
.search-buttons input[type="text"] {
    padding: 10px;
    width: 300px;
    border-radius: 6px;
    border: 1px solid #ccc;
    height: 40px;
    font-size: 14px;
}

/* Buttons */
.btn, .btn-create, .btn-archive, .btn-edit {
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    gap: 6px;
    border: none;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #fff;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.btn-create {
    background: linear-gradient(90deg, #007bff, #00aaff);
}
.btn-create:hover {
    background: linear-gradient(90deg, #0056b3, #0088ff);
    transform: translateY(-2px) scale(1.03);
}
.btn-archive {
    background-color: orange;
}
.btn-archive:hover {
    background-color: darkorange;
    transform: translateY(-1px) scale(1.02);
}
.btn-edit {
  background-color: orange;
  color: #fff;
  font-weight: bold;
  padding: 8px 14px;
  border-radius: 6px;
  transition: all 0.3s ease;
}
.btn-edit:hover {
  background-color: darkorange;
  transform: translateY(-1px) scale(1.05);
}

/* Cards */
.card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}
.card-header { font-size: 18px; font-weight: bold; padding: 12px 16px; background-color: #007bff; color: #fff; }
.card-body { padding: 15px; }

/* Table */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  font-size: 14px;
}
table th, table td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #e0e0e0;
  font-weight: 500;
}
table thead {
  background: #000000;
  color: #fff;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 13px;
}
table thead tr:hover { background: #000000; transform: none; }
table tbody tr:hover {
  background-color: #e0f0ff;
  transform: scale(1.01);
  transition: all 0.2s ease-in-out;
}
table tr:nth-child(even) { background-color: #f4f7fa; }

/* Form Inputs */
input, select, textarea { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; font-weight: normal; }

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background-color: #fff;
  margin: auto;
  padding: 25px;
  border-radius: 10px;
  width: 100%;
  max-width: 500px;
  position: relative;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.close {
  position: absolute;
  top: 10px;
  right: 20px;
  font-size: 24px;
  font-weight: bold;
  color: #aaa;
  cursor: pointer;
}
.close:hover { color: #000; }

/* Responsive */
@media screen and (max-width: 768px) {
  .container { margin-left: 0; padding: 15px; }
  table th, table td { padding: 8px; }
  .btn, .btn-primary { font-size: 12px; padding: 6px 12px; }
  .search-buttons { flex-direction: column; align-items: stretch; gap: 8px; }
  .search-buttons input { width: 100%; }
}

/* Logo */
.sidebar img {
  width: 150px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}
</style>
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
    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li class="active"><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
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
<div class="container">
  <h1>VIOLATION APPOINTMENTS</h1>

  <!-- Search + Buttons -->
  <div class="search-buttons">
      <input type="text" id="searchInput" placeholder="Search student name...">
      <button class="btn-create" onclick="openModal('createModal')">
          <i class="fas fa-plus"></i> Create Appointment
      </button>
      <button class="btn-archive" onclick="bulkArchive()">
          <i class="fas fa-archive"></i> Archive
      </button>
  </div>

  <div class="card-body">
    <table>
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>#</th>
          <th>Student</th>
          <th>Incident</th>
          <th>Offense</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="appointmentList">
@foreach($violation_appointments as $index => $app)
<tr data-parent="{{ $app->violation->student->parent->parent_fname }} {{ $app->violation->student->parent->parent_lname }}" 
    data-contact="{{ $app->violation->student->parent->parent_contactinfo }}">
    <td><input type="checkbox" class="student-checkbox"></td>
    <td>{{ $index + 1 }}</td>
    <td>{{ $app->violation->student->student_fname }} {{ $app->violation->student->student_lname }}</td>
    <td>{{ $app->violation->violation_incident }}</td>
    <td>{{ $app->violation->offense->offense_type }}</td>
    <td>{{ $app->violation_app_date }}</td>
    <td>{{ \Carbon\Carbon::parse($app->violation_app_time)->format('h:i A') }}</td>
    <td>{{ $app->violation_app_status }}</td>
    <td>
        <button class="btn-edit"><i class="fas fa-edit"></i> Update</button>
    </td>
</tr>
@endforeach
</tbody>

    </table>
  </div>
</div>

<!-- Create Modal -->
<div class="modal" id="createModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('createModal')">&times;</span>
    <h2>Create Schedule Appointment</h2>
    <form id="createAppointmentForm" method="POST" action="{{ route('violation.appointments.store') }}">
      @csrf
      <div class="form-group">
          <label for="studentSelect">Select Student</label>
          <select id="studentSelect" name="violation_id" required>
              <option value="">-- Select Student --</option>
              @foreach($violations as $violation)
                  <option value="{{ $violation->violation_id }}">
                      {{ $violation->student->student_fname }} {{ $violation->student->student_lname }} — {{ $violation->violation_incident }}
                  </option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="date">Date</label>
          <input type="date" name="date" required>
      </div>
      <div class="form-group">
          <label for="time">Time</label>
          <input type="time" name="time" required>
      </div>
      <div class="form-group">
          <label for="status">Status</label>
          <select name="status" required>
              <option value="Pending">Pending</option>
              <option value="Confirmed">Confirmed</option>
              <option value="Completed">Completed</option>
          </select>
      </div>
      <button type="submit" class="btn-create"><i class="fas fa-save"></i> Save Appointment</button>
    </form>
  </div>
</div>

<!-- Info Modal -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('infoModal')">&times;</span>
    <h2>Appointment Info</h2>
    <div id="infoContent"></div>
  </div>
</div>

<script>
 // Select All functionality
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.student-checkbox');
selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
});
checkboxes.forEach(cb => {
  cb.addEventListener('change', () => {
    if (!cb.checked) selectAll.checked = false;
    else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) selectAll.checked = true;
  });
});

// Dropdown sidebar
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

// Modal functions
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

// Logout
function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (!confirmLogout) return;
    fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if(response.ok) window.location.href = "{{ route('auth.login') }}";
        else console.error('Logout failed:', response.statusText);
    })
    .catch(error => console.error('Logout failed:', error));
}

// Search filter
document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  document.querySelectorAll('#appointmentList tr').forEach(row => {
    let student = row.cells[2].textContent.toLowerCase();
    row.style.display = student.includes(filter) ? '' : 'none';
  });
});

// Row click to show info modal (Parent + Contact only)
document.querySelectorAll('#appointmentList tr').forEach(row => {
  row.addEventListener('click', (e) => {
    if (e.target.tagName === 'INPUT' || e.target.closest('.btn-edit')) return;
    const parentName = row.dataset.parent;
    const contact = row.dataset.contact;
    document.getElementById('infoContent').innerHTML = `
      <p><strong>Parent:</strong> ${parentName}</p>
      <p><strong>Contact:</strong> ${contact}</p>
    `;
    openModal('infoModal');
  });
});


// Placeholder bulk archive
function bulkArchive() {
  const checkedRows = document.querySelectorAll('.student-checkbox:checked');
  if (!checkedRows.length) { alert('No appointments selected'); return; }
  alert(`${checkedRows.length} appointment(s) moved to archive!`);
}
const bulkActionBtn = document.getElementById('bulkActionBtn');
const bulkActionMenu = document.getElementById('bulkActionMenu');

bulkActionBtn.addEventListener('click', () => {
  bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
});

// Trash functionality
bulkActionMenu.querySelector('.bulk-action-item').addEventListener('click', () => {
  document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
    cb.closest('tr').remove();
  });
  bulkActionMenu.style.display = 'none';
});

// Close dropdown if clicked outside
document.addEventListener('click', (e) => {
  if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
    bulkActionMenu.style.display = 'none';
  }
});

</script>

</body>
</html>
