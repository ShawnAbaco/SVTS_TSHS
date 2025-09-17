<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
</head>
<body>
 <style>
  :root {
  --primary-color: #000000;
  --secondary-color: #ffffff;
  --hover-bg: rgb(12, 12, 12);
  --hover-active-bg: rgb(0, 120, 255);
  --shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  --button-blue: #007BFF;
  --button-red: #dc3545;
  --button-orange: #fd7e14;
  --table-header-bg: rgb(0, 0, 0);
  --input-border: #ccc;
}

* {
  font-weight: bold !important;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Arial", sans-serif;
  margin: 0;
  background-color: var(--secondary-color);
  min-height: 100vh;
  display: flex;
  color: #000000;
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
  color: #000000;
}

/* --- Actions Section (Search + Buttons) --- */
.actions {
  display: flex;
  align-items: center;
  gap: 12px; /* space between search, create, archive */
  margin-bottom: 1rem; /* space below header */
}

button, .btn-primary, .btn-red, .btn-orange {
  font-size: 0.95rem;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: bold;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  margin: 0 4px; /* small margin between buttons */
}

.btn-primary {
  background-color: var(--button-blue);
  color: var(--secondary-color);
}
.btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }

.btn-red {
  background-color: var(--button-red);
  color: var(--secondary-color);
}
.btn-red:hover { background-color: #b52a37; transform: translateY(-2px); }

.btn-orange {
  background-color: var(--button-orange);
  color: var(--secondary-color);
}
.btn-orange:hover { background-color: #e76a05; transform: translateY(-2px); }

.btn-primary i, .btn-red i, .btn-orange i { margin-right: 6px; }

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
}

#searchInput {
  width: 280px;
  border: 1px solid var(--input-border);
  box-shadow: var(--shadow);
}


/* Table - High Resolution */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1.5rem;
  font-size: 0.95rem;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: var(--shadow);
}

table th,
table td {
  padding: 14px 12px;
  text-align: left;
}

table th {
  background-color: var(--table-header-bg);
  color: #fff;
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
  left: 0; top: 0; 
  width: 100%; height: 100%;
  overflow: auto; 
  background-color: rgba(0,0,0,0.5);
}
.modal-content {
  background-color: var(--secondary-color);
  margin: 5% auto; 
  padding: 20px;
  border-radius: 12px;
  width: 500px;
  box-shadow: var(--shadow);
  color: #000000;
}
.modal-content h3 { margin-bottom: 1rem; }
.modal-content label { display: block; margin: 10px 0 5px; }
.modal-content input, .modal-content textarea {
  width: 100%; 
  padding: 10px 12px;
  border-radius: 8px; 
  border: 1px solid var(--input-border);
  margin-bottom: 12px;
  font-size: 0.95rem; 
  font-weight: normal;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}
.modal-content input:focus, .modal-content textarea:focus {
  border-color: var(--button-blue);
  outline: none;
  box-shadow: 0 0 6px rgba(0,123,255,0.4);
}
.modal-content button {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  background-color: var(--button-blue);
  color: var(--secondary-color);
  font-weight: bold;
  transition: all 0.3s ease;
}
.modal-content button:hover { background-color: #0056b3; transform: translateY(-2px); }
.close { float: right; font-size: 1.5rem; cursor: pointer; }

  </style>

  

  <!-- SIDEBAR -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <!-- Violations Dropdown -->
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
      <!-- Complaints Dropdown -->
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}" class="active">Complaints</a></li>
          <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
          <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
        </ul>
      </li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="main-content">
   <div class="toolbar">
  <h1>Complaints</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search complaints...">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add Complaint</button>
    <button class="btn-archive" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <!-- Table -->
    <table id="complaintsTable">
      <thead>
        <tr>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Offense</th>
          <th>Sanction</th>
          <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($complaints as $c)
        <tr>
          <td>{{ $c->complainant->student_fname ?? 'N/A' }} {{ $c->complainant->student_lname ?? '' }}</td>
          <td>{{ $c->respondent->student_fname ?? 'N/A' }} {{ $c->respondent->student_lname ?? '' }}</td>
          <td>{{ $c->offense->offense_type ?? 'N/A' }}</td>
          <td>{{ $c->offense->sanction_consequences ?? 'N/A' }}</td>
          <td>{{ $c->complaints_incident }}</td>
          <td>{{ $c->complaints_date }}</td>
          <td>{{ $c->complaints_time }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Modal -->
    <div id="complaintModal" class="modal">
      <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h3 id="modalTitle">Create Complaint</h3>
        <form id="complaintForm">
          <label>Complainant Name</label>
          <input type="text" name="complainant" required>
          <label>Respondent</label>
          <input type="text" name="respondent" required>
          <label>Offense</label>
          <input type="text" id="offenseSearch" name="offense" placeholder="Search offense..." list="offenseList" required>
          <datalist id="offenseList">
            @foreach($offenses as $offense)
              <option value="{{ $offense->offense_type }}">
            @endforeach
          </datalist>
          <label>Incident</label>
          <textarea name="incident" rows="3" required></textarea>
          <label>Date</label>
          <input type="date" name="date" required>
          <label>Time</label>
          <input type="time" name="time" required>
          <button type="submit"><i class="fas fa-save"></i> Submit</button>
        </form>
      </div>
    </div>
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

  function logout() { alert('Logging out...'); }

  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#complaintsTable tbody');
    function filterTable(){
      const filter = searchInput.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    }
    searchInput.addEventListener('keyup', filterTable);

    const modal = document.getElementById("complaintModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = document.getElementById("closeModalBtn");
    const form = document.getElementById('complaintForm');
    let editingRow = null;

    openBtn.onclick = () => {
      modal.style.display = "block";
      document.getElementById('modalTitle').textContent = "Create Complaint";
      editingRow = null;
      form.reset();
    };
    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; }

    function createActionButtons(row){
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

    document.querySelectorAll('.btn-edit').forEach((btn, i) => {
      btn.onclick = () => editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow = row;
      modal.style.display = "block";
      document.getElementById('modalTitle').textContent = "Edit Complaint";
      form.complainant.value = row.cells[0].textContent;
      form.respondent.value = row.cells[1].textContent;
      form.offense.value = row.cells[2].textContent;
      form.incident.value = row.cells[4].textContent;
      form.date.value = row.cells[5].textContent;
      form.time.value = row.cells[6].textContent;
    }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      if(editingRow){
        editingRow.cells[0].textContent = form.complainant.value;
        editingRow.cells[1].textContent = form.respondent.value;
        editingRow.cells[2].textContent = form.offense.value;
        editingRow.cells[4].textContent = form.incident.value;
        editingRow.cells[5].textContent = form.date.value;
        editingRow.cells[6].textContent = form.time.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = form.complainant.value;
        row.insertCell(1).textContent = form.respondent.value;
        row.insertCell(2).textContent = form.offense.value;
        row.insertCell(3).textContent = "N/A";
        row.insertCell(4).textContent = form.incident.value;
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        createActionButtons(row);
      }
      modal.style.display = "none";
      form.reset();
      editingRow = null;
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('tr').remove();
      });
    });
  });
</script>
</body>
</html>
