<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violation Logging</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
 :root {
  --primary-color: #000000;
  --secondary-color: #ffffff;
  --text-color: #000000;
  --background-color: #ffffff;
  --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Global Styles */
* {
  box-sizing: border-box;
  font-family: "Arial", sans-serif;
}

body {
  margin: 0;
  background-color: var(--background-color);
  min-height: 100vh;
  display: flex;
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

/* Main Content */
.main-content {
  margin-left: 260px;
  padding: 2rem;
  flex-grow: 1;
  background-color: var(--background-color);
}

h2 {
  color: var(--primary-color);
}
.btn-archive {
  background: linear-gradient( #e55300); /* Coral to Deep Orange */
  color: #fff !important;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  padding: 10px 16px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

.btn-archive:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.25);
  opacity: 0.95;
}

/* --- High Resolution Buttons --- */
.btn {
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
  background: linear-gradient(135deg, #2563EB, #1E40AF);
  color: #fff !important;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.2);
  opacity: 0.95;
}

.btn-info {
  background: linear-gradient(135deg, #17a2b8, #117a8b);
  color: #fff !important;
}

.btn-edit {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  color: #000 !important;
}

.btn-delete {
  background: linear-gradient(135deg, #dc3545, #b02a37);
  color: #fff !important;
}

.btn-edit:hover,
.btn-delete:hover,
.btn-info:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.25);
  opacity: 0.95;
}

/* --- High Resolution Table --- */
.violation-table-container {
  margin-top: 1rem;
  background-color: var(--secondary-color);
  padding: 1rem;
  border-radius: 10px;
  box-shadow: var(--shadow);
  overflow-x: auto;
}

.violation-table-container table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.9rem;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.violation-table-container th,
.violation-table-container td {
  padding: 12px 14px;
  border-bottom: 1px solid #ddd;
  text-align: center;
}

.violation-table-container thead {
  background-color: rgb(0, 0, 0);
  color: #fff;
  text-transform: uppercase;
  font-size: 14px;
  letter-spacing: 0.5px;
}

.violation-table-container tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.violation-table-container tbody tr:hover {
  background-color: #f1f1f1;
  transition: background 0.3s ease;
}

/* --- Modal Overlay --- */
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0,0,0,0.6);
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.modal.show {
  display: flex;
  opacity: 1;
}

/* --- Modal Content --- */
.modal-content {
  background-color: var(--secondary-color);
  padding: 1rem;
  border-radius: 10px;
  width: 80%;
  max-width: 500px;
  position: relative;
  box-shadow: var(--shadow);
  transform: translateY(-20px);
  transition: transform 0.3s ease;
}

.modal.show .modal-content {
  transform: translateY(0);
}

/* Close Button */
.close-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.75rem;
  font-size: 1.2rem;
  background: none;
  border: none;
  color: #000;
  cursor: pointer;
}

/* Form inside modal */
.form-group {
  margin-bottom: 0.75rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.3rem;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 0.45rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #fff;
  color: #000;
  font-size: 0.9rem;
}

.form-group button {
  background: linear-gradient(135deg, #2563EB, #1E40AF);
  color: #fff;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: bold;
  transition: all 0.3s ease;
}

.form-group button:hover {
  background: linear-gradient(135deg, #1E40AF, #1e3a8a);
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Header Layout */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}
.header h2 {
  margin: 0;
  font-size: 22px;
}
.header .actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
/* Match search box with button size */
.search-box input {
  padding: 10px 16px;         /* same as .btn */
  border: 1px solid #ccc;
  border-radius: 8px;         /* match button radius */
  font-size: 15px;            /* match font size */
  font-weight: 600;           /* same text weight */
  height: 42px;               /* force equal height */
  min-width: 200px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15); /* consistent shadow */
}


    </style>
</head>
<body>

 <!-- SIDEBAR -->

<nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
        <img src="/images/Logo.png" alt="Logo">
        <p>ADVISER</p>
    </div>
    <ul>
        <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
        <li><a href="{{ route('parent.list') }}" ><i class="fas fa-user-friends"></i> Parent List</a></li>

        <!-- Violations Dropdown -->
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
            <ul class="dropdown-container">
                 <li><a href="{{ route('violation.record') }}" class="active">Violation Records</a></li>
                <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
                <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
            </ul>
        </li>

        <!-- Complaints Dropdown -->
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

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="header">
    <h2>Violation Logging</h2>
    <div class="actions">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search records...">
      </div>
      <button class="btn btn-info" onclick="openAddModal()">
        <i class="fas fa-plus-circle"></i> Add Violation Record
        <button class="btn-archive" id="archivesBtn">
  <i class="fas fa-archive"></i> Archives
</button>

    </div>
  </div>

  <!-- VIOLATION RECORDS TABLE -->
  <div class="violation-table-container">
    <table id="violationTable">
      <thead>
        <tr>
          <th>Student</th>
          <th>Violation</th>
          <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Sanction</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($violations as $v)
        <tr data-id="{{ $v->violation_id }}" data-student-id="{{ $v->student->student_id }}">
          <td>{{ $v->student->student_fname }} {{ $v->student->student_lname }}</td>
          <td>{{ $v->offense->offense_type }}</td>
          <td>{{ $v->violation_incident }}</td>
          <td>{{ $v->violation_date }}</td>
          <td>{{ $v->violation_time }}</td>
          <td>{{ $v->offense->sanction_consequences }}</td>

          <td>
            <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- ADD VIOLATION MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Violation Record</h2>
    <form id="addViolationForm" method="POST" action="{{ route('adviser.storeViolation') }}">
      @csrf

      <!-- Student Selection -->
      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <!-- Offense Selection -->
      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <!-- Incident -->
      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" placeholder="Incident Details" required
               pattern="^[A-Za-z0-9\s.,#()!?-]+$"
               title="Only letters, numbers, spaces, commas, periods, dashes, (), !, and ? are allowed">
      </div>

      <!-- Date -->
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date"
               max="<?php echo date('Y-m-d'); ?>" required
               title="Date cannot be in the future">
      </div>

      <!-- Time -->
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" required>
      </div>

      <!-- Save Button -->
      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT VIOLATION MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Violation Record</h2>
    <form id="editViolationForm">
      @csrf
      @method('PUT')
      <input type="hidden" name="violation_id" id="editViolationId">

      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" id="editStudent" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" id="editOffense" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" id="editIncident" required>
      </div>

      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date" id="editDate" required>
      </div>

      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" id="editTime" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Update</button>
      </div>
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

document.addEventListener("DOMContentLoaded", function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  // --- Modal controls ---
  function openModal(id) { document.getElementById(id).classList.add("show"); }
  function closeModal(id) { document.getElementById(id).classList.remove("show"); }
  window.onclick = function(event) {
    let addModal = document.getElementById("addModal");
    let editModal = document.getElementById("editModal");
    if (event.target === addModal) closeModal("addModal");
    if (event.target === editModal) closeModal("editModal");
  };

  // --- Open Add Modal ---
  window.openAddModal = function() { openModal("addModal"); };

  // --- Live Search ---
  document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#violationTable tbody tr").forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // --- Logout ---
  window.logout = function() {
    if (confirm("Are you sure you want to log out?")) {
      window.location.href = "/adviser/login";
    }
  };

  // --- Handle Add (AJAX POST) ---
  document.getElementById("addViolationForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const v = data.violation;
          let tbody = document.querySelector("#violationTable tbody");

          let newRow = document.createElement("tr");
          newRow.setAttribute("data-id", v.violation_id);
          newRow.setAttribute("data-student-id", v.violator_id);

          newRow.innerHTML = `
            <td>${v.student ? v.student.student_fname + " " + v.student.student_lname : "N/A"}</td>
            <td>${v.offense ? v.offense.offense_type : "N/A"}</td>
            <td>${v.violation_incident}</td>
            <td>${v.violation_date}</td>
            <td>${v.violation_time}</td>
            <td>${v.offense ? v.offense.sanction_consequences : "N/A"}</td>
            <td>
              <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </td>
          `;
          tbody.appendChild(newRow);

          closeModal("addModal");
          document.getElementById("addViolationForm").reset();
          alert("Violation added successfully!");
        }
      })
      .catch(err => console.error("Error:", err));
  });

  // --- Event Delegation for Edit & Delete ---
  document.querySelector("#violationTable tbody").addEventListener("click", function(e) {
    const row = e.target.closest("tr");
    if (!row) return;

    // Handle Edit
    if (e.target.closest(".btn-edit")) {
      const id = row.getAttribute("data-id");
      const studentId = row.getAttribute("data-student-id");
      const cells = row.querySelectorAll("td");

      document.getElementById("editViolationId").value = id;
      document.getElementById("editStudent").value = studentId;
      document.getElementById("editIncident").value = cells[2].innerText;
      document.getElementById("editDate").value = cells[3].innerText;
      document.getElementById("editTime").value = cells[4].innerText;

      openModal("editModal");
    }

    // Handle Delete
    if (e.target.closest(".btn-delete")) {
      if (!confirm("Are you sure you want to delete this violation?")) return;
      const id = row.getAttribute("data-id");

      fetch(`/adviser/violations/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrfToken }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation deleted successfully!");
          row.remove();
        }
      })
      .catch(err => console.error("Error:", err));
    }
  });

  // --- Handle Update (AJAX PUT) ---
  document.getElementById("editViolationForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const id = document.getElementById("editViolationId").value;
    const formData = new FormData(this);

    fetch(`/adviser/violation/${id}`, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken, "X-HTTP-Method-Override": "PUT" },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation updated successfully!");
          location.reload();
        }
      });
  });
});
</script>


</body>
</html>
