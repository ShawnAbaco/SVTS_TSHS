<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Appointments</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
</head>
<body>
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --hover-bg: rgb(0, 88, 240);
      --hover-active-bg: rgb(0, 120, 255);
      --shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      --button-blue: #007BFF;
      --button-red: #dc3545;
      --button-orange: #fd7e14;
      --table-header-bg: #f5f5f5;
    }

/* --- Body --- */
body {
  background-color: var(--secondary-color);
  min-height: 100vh;
  display: flex;

 color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;

}

p, span, a, li, label {
  color: rgb(255, 255, 255);
  font-weight: bold;
  font-family: "Arial", sans-serif;
}

h1, h2, h3, h4, h5, h6 {
  color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;
}

button, input, textarea, select, th, td {
  color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;
}
/* --- Reset Margin & Padding --- */
body, div, p {
  margin: 0;
  padding: 0;
}

ul, ol, li {
  margin: 0;
  padding: 0;
}

h1, h2, h3, h4, h5, h6 {
  margin: 0;
  padding: 0;
}

table, th, td {
  margin: 0;
  padding: 0;
}

form {
  margin: 0;
  padding: 0;
}

/* --- Box Sizing --- */
body, div, p {
  box-sizing: border-box;
}

ul, ol, li {
  box-sizing: border-box;
}

h1, h2, h3, h4, h5, h6 {
  box-sizing: border-box;
}

table, th, td, form {
  box-sizing: border-box;
}

input, textarea, select, button {
  box-sizing: border-box;
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
      padding: 2rem;
      flex-grow: 1;
    }

   /* Toolbar - title left, actions right */
.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between; /* title left, actions right */
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.toolbar h1 {
  font-size: 1.8rem;
  font-weight: bold;
  margin: 0;
  color: #000;
}

.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 12px; /* space between search, create, archive */
}

.toolbar-actions input,
.toolbar-actions button {
  height: 46px; /* same height for all elements */
  font-size: 0.95rem;
  border-radius: 8px;
  padding: 0 14px;
  font-weight: bold;
  box-shadow: var(--shadow);
}

#searchInput {
  width: 280px;
  border: 1px solid #ccc;
}


    .btn-primary { background-color: var(--button-blue); color: #fff; border: none; }
    .btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
    .btn-red { background-color: var(--button-red); color: #fff; border: none; }
    .btn-red:hover { background-color: #b52a37; transform: translateY(-2px); }
    .btn-orange { background-color: var(--button-orange); color: #fff; border: none; }
    .btn-orange:hover { background-color: #e76a05; transform: translateY(-2px); }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      font-size: 0.95rem;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: var(--shadow);
    }
    table th, table td {
      padding: 14px 12px;
      text-align: left;
    }
    table th {
      background-color: #000000;
      color: #ffffff !important;
      font-size: 1rem;
    }
    table tbody tr {
      background: #fff;
      transition: background 0.3s ease;
    }
    table tbody tr:nth-child(even) {
      background: #f9f9f9;
    }
    table tbody tr:hover {
      background: #eef5ff;
    }
    table td {
      border-bottom: 1px solid #ddd;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; width: 100%; height: 100%;
      overflow: auto; background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #fff;
      margin: 5% auto;
      padding: 20px;
      border-radius: 10px;
      width: 450px;
      box-shadow: var(--shadow);
    }
    .close { float: right; font-size: 1.5rem; cursor: pointer; }
    .modal-content form { display: flex; flex-direction: column; }
    .modal-content label { margin-top: 10px; margin-bottom: 5px; }
    .modal-content input, .modal-content select {
      padding: 8px; border-radius: 6px;
      border: 1px solid #ccc; margin-bottom: 10px;
      box-shadow: var(--shadow);
    }
    .modal-content button { align-self: flex-end; }
  </style>

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div style="text-align:center;margin-bottom:1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
          <li><a href="{{ route('complaints.appointment') }}" class="active">Complaints Appointment</a></li>
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

  <!-- MAIN -->
  <div class="main-content">
    <div class="toolbar">
  <h1>Complaints Appointments</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search appointments...">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create</button>
    <button class="btn-orange" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <!-- Modal -->
    <div id="appointmentModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Create Appointment</h2>
        <form id="appointmentForm">
          <label for="complaint">Complaint:</label>
          <input list="complaintList" id="complaint" name="complaint_id" required>
          <datalist id="complaintList">
            @foreach ($comp_appointments as $comp_appointment)
              <option value="{{ $comp_appointment->complaint->id }}">
                {{ $comp_appointment->complaint->offense->offense_type }} -
                {{ $comp_appointment->complaint->respondent->student_fname }} {{ $comp_appointment->complaint->respondent->student_lname }}
              </option>
            @endforeach
          </datalist>

          <label for="date">Date:</label>
          <input type="date" id="date" name="appointment_date" required>

          <label for="time">Time:</label>
          <input type="time" id="time" name="appointment_time" required>

          <label for="status">Status:</label>
          <select id="status" name="appointment_status" required>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>

          <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save</button>
        </form>
      </div>
    </div>

    <!-- Table -->
    <table id="appointmentTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Incident</th>
          <th>Offense</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($comp_appointments as $comp_appointment)
        <tr>
          <td>{{ $comp_appointment->comp_app_id }}</td>
          <td>{{ $comp_appointment->complaint->complainant->student_fname ?? 'N/A' }} {{ $comp_appointment->complaint->complainant->student_lname ?? '' }}</td>
          <td>{{ $comp_appointment->complaint->respondent->student_fname ?? 'N/A' }} {{ $comp_appointment->complaint->respondent->student_lname ?? '' }}</td>
          <td>{{ $comp_appointment->complaint->complaints_incident ?? 'N/A' }}</td>
          <td>{{ $comp_appointment->complaint->offense->offense_type ?? 'N/A' }}</td>
          <td>{{ $comp_appointment->comp_app_date }}</td>
          <td>{{ $comp_appointment->comp_app_time }}</td>
          <td>{{ $comp_appointment->comp_app_status }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <script>
   // Sidebar dropdown toggle
  const dropdowns = document.querySelectorAll('.dropdown-btn');
  dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      dropdowns.forEach(otherBtn => {
        if (otherBtn !== this) {
          otherBtn.nextElementSibling.classList.remove('show');
          otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
        }
      });
      const container = this.nextElementSibling;
      container.classList.toggle('show');
      this.querySelector('.fa-caret-down').style.transform =
        container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    });
  });

    function logout(){ alert('Logging out...'); }

    // --- Live Search ---
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#appointmentTable tbody');

    searchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    // --- Modal & Form Logic ---
    const modal = document.getElementById("appointmentModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModal = document.querySelector(".close");
    const form = document.getElementById('appointmentForm');
    let editingRow = null;

    openModalBtn.onclick = () => {
      modal.style.display = "block";
      form.reset();
      editingRow = null;
    };
    closeModal.onclick = () => modal.style.display = "none";
    window.onclick = e => { if(e.target == modal) modal.style.display="none"; }

    // --- Edit/Delete Buttons ---
    function createActionButtons(row) {
      const actionsCell = row.insertCell(-1);

      const editBtn = document.createElement('button');
      editBtn.className = 'btn-orange';
      editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      editBtn.onclick = () => editRow(row);

      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'btn-red';
      deleteBtn.style.marginLeft = '5px';
      deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
      deleteBtn.onclick = () => row.remove();

      actionsCell.appendChild(editBtn);
      actionsCell.appendChild(deleteBtn);
    }

    document.querySelectorAll('.btn-edit').forEach((btn,i)=>{
      btn.onclick = () => editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow = row;
      modal.style.display = "block";
      form.complaint.value = row.cells[0].textContent; // you may adjust mapping
      form.date.value = row.cells[5].textContent;
      form.time.value = row.cells[6].textContent;
      form.status.value = row.cells[7].textContent;
    }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      if(editingRow){
        editingRow.cells[0].textContent = form.complaint.value;
        editingRow.cells[5].textContent = form.date.value;
        editingRow.cells[6].textContent = form.time.value;
        editingRow.cells[7].textContent = form.status.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = form.complaint.value;
        row.insertCell(1).textContent = 'Complainant'; // placeholder
        row.insertCell(2).textContent = 'Respondent'; // placeholder
        row.insertCell(3).textContent = 'Incident'; // placeholder
        row.insertCell(4).textContent = 'Offense'; // placeholder
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        row.insertCell(7).textContent = form.status.value;
        createActionButtons(row);
      }
      modal.style.display = "none";
      form.reset();
    });
  </script>
</body>
</html>
