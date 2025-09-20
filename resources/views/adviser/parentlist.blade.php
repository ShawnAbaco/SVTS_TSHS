<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <style>
    :root {
  --primary-color: #000000;
  --secondary-color: #ffffff;
  --hover-bg: rgb(0, 88, 240);
  --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

* {
  color: black;
  font-weight: bold;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Arial", sans-serif;
  background-color: var(--secondary-color);
  min-height: 100vh;
  margin: 0;
  padding: 0;
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

 /* --- Main content --- */
    .main-content {
      margin-left: 260px;
      padding: 2rem;
      width: calc(100% - 260px);
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .header h1 {
      font-size: 22px;
      margin: 0;
    }

    .actions {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .actions input,
    .actions button {
      height: 44px;
      font-size: 15px;
      border-radius: 8px;
      padding: 0 15px;
      font-weight: 600;
    }

    .search-box input {
      border: 1px solid #ccc;
      box-shadow: 0 3px 8px rgba(0,0,0,0.08);
      min-width: 240px;
    }

    /* --- Buttons --- */
    .btn {
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }

    .btn i { font-size: 16px; }

    .btn-info {
      background: linear-gradient(135deg, #17a2b8, #117a8b);
      color: #fff;
    }

    .btn-edit {
      background: linear-gradient(135deg, #ffc107, #e0a800);
      color: #000;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #b02a37);
      color: #fff;
    }

    .btn-archive {
      background-color: #fd7e14;
      color: #fff;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 12px rgba(0,0,0,0.2);
      opacity: 0.95;
    }

/* --- Table (High Resolution) --- */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 25px;
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    font-size: 16px;
  }

  table th, table td {
    padding: 14px 18px;
    text-align: center;
    vertical-align: middle;
  }

  table th {
    background-color: rgb(0, 0, 0);
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
  }

  table tr:nth-child(even) {
    background-color: #f7f7f7;
  }

  table tr:hover {
    background-color: rgba(0,0,0,0.05);
  }

/* --- Modal --- */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  max-width: 500px;
  width: 100%;
  position: relative;
  box-shadow: var(--shadow);
}

.modal-content h2 {
  margin-bottom: 1rem;
}

.modal-content input,
.modal-content select {
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  cursor: pointer;
  font-size: 20px;
  color: red;
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
        <li><a href="{{ route('parent.list') }}" class="active"><i class="fas fa-user-friends"></i> Parent List</a></li>

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
    <div class="header">
      <h1>Parent List</h1>
      <div class="actions">
        <input type="text" id="searchInput" placeholder="Search parent...">
        <button class="btn btn-info" onclick="openAddModal()">
          <i class="fas fa-plus-circle"></i> Add Parent/Guardian
        </button>
        <button class="btn btn-archive">
          <i class="fas fa-archive"></i> Archives
        </button>
      </div>
    </div>

   <table id="parentTable">
  <thead>
    <tr>
      <th>
        <input type="checkbox" id="selectAll"> 
        <button id="trashBtn" class="btn btn-danger" style="margin-left:6px;">
          <i class="fas fa-trash-alt"></i>
        </button>
      </th>
      <th>Parent/Guardian Name</th>
      <th>Birthdate</th>
      <th>Contact Number</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($parents as $parent)
    <tr data-id="{{ $parent->parent_id }}"
        data-students='@json(
            $parent->students->map(fn($s) => [
                "name" => $s->student_fname . " " . $s->student_lname,
                "contact" => $s->student_contactinfo,
                "adviser_id" => $s->adviser_id
            ])
        )'>
      <td><input type="checkbox" class="rowCheckbox"></td>
      <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
      <td>{{ $parent->parent_birthdate }}</td>
      <td>{{ $parent->parent_contactinfo }}</td>
      <td class="actions">
        <button class="btn btn-info" onclick="showInfo(
          '{{ $parent->parent_fname }} {{ $parent->parent_lname }}',
          '{{ $parent->parent_birthdate }}',
          '{{ $parent->parent_contactinfo }}',
          this.closest('tr').dataset.students
        )"><i class="fas fa-info-circle"></i> Info</button>
        <button class="btn btn-edit" onclick="editGuardian(this)">
          <i class="fas fa-edit"></i> Edit
        </button>
        <form method="POST" action="{{ route('parents.destroy', $parent->parent_id) }}" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> Delete
          </button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>


  </div>
  
<!-- ADD / EDIT MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addModal')">&times;</span>
    <h2 id="modalTitle">Add Parent/Guardian</h2>
    <form id="addParentForm" method="POST" action="{{ route('parents.store') }}">
      @csrf
      <input type="hidden" name="parent_id" id="parent_id" value="">

      <!-- First Name -->
      <input type="text" name="parent_fname" id="parent_fname" placeholder="First Name"
             required pattern="^[A-Za-z\s]+$"
             title="Only letters and spaces are allowed">

      <!-- Last Name -->
      <input type="text" name="parent_lname" id="parent_lname" placeholder="Last Name"
             required pattern="^[A-Za-z\s]+$"
             title="Only letters and spaces are allowed">

      <!-- Birthdate -->
      <input type="date" name="parent_birthdate" id="parent_birthdate"
             max="<?php echo date('Y-m-d'); ?>" required
             title="Birthdate cannot be in the future">

      <!-- Contact Info -->
      <input type="text" name="parent_contactinfo" id="parent_contactinfo" placeholder="Contact Number"
             required pattern="^[0-9]{11}$"
             title="Contact number must be exactly 11 digits (e.g., 09123456789)">

      <span id="methodField"></span>
      <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Save</button>
    </form>
  </div>
</div>

  <!-- INFO MODAL -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('infoModal')">&times;</span>
      <h2>Children Info</h2>
      <p><strong>Name:</strong> <span id="infoName"></span></p>
      <p><strong>Birthdate:</strong> <span id="infoBirthdate"></span></p>
      <p><strong>Contact:</strong> <span id="infoContact"></span></p>
      <p><strong>Children:</strong></p>
      <ul id="infoChildren"></ul>
      <button class="btn btn-info" id="smsBtn"><i class="fas fa-sms"></i> Send SMS</button>
    </div>
  </div>
<!-- ARCHIVE MODAL -->
<div class="modal" id="archiveModal">
  <div class="modal-content" style="max-width:900px;">
    <span class="close" onclick="closeModal('archiveModal')">&times;</span>
    <h2>Archived Parents</h2>

    <!-- Search bar -->
    <input type="text" id="archiveSearch" placeholder="Search by name..." 
           style="width: 100%; padding: 8px; margin: 10px 0;">

    <!-- Restore All button -->
    <button class="btn btn-success" onclick="restoreAllRows()" style="margin-bottom:10px;">
      <i class="fas fa-undo"></i> Restore All
    </button>

    <table id="archiveTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Birthdate</th>
          <th>Contact</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Filled dynamically -->
      </tbody>
    </table>
  </div>
</div>



  <script>

  // --- Dropdown functionality - auto close others & scroll ---
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
  btn.addEventListener('click', function (e) {
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
    if (container.classList.contains('show')) {
      container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  });
});

// --- Sidebar active link ---
document.querySelectorAll('.sidebar a').forEach(link => {
  link.addEventListener('click', function () {
    document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
  });
});

// --- Live search ---
document.getElementById('searchInput').addEventListener('keyup', function () {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll("#parentTable tbody tr");
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

function openAddModal() {
  document.getElementById('addParentForm').reset();
  document.getElementById('methodField').innerHTML = '';
  document.getElementById('modalTitle').innerText = 'Add Parent/Guardian';
  document.querySelector('#addParentForm button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Save';
  document.getElementById('addModal').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

function showInfo(name, birthdate, contact, studentsJson) {
  const loggedAdviserId = {{ optional(auth()->guard('adviser')->user())->adviser_id ?? 'null' }};
  document.getElementById('infoName').innerText = name;
  document.getElementById('infoBirthdate').innerText = birthdate;
  document.getElementById('infoContact').innerText = contact;

  const childrenList = document.getElementById('infoChildren');
  childrenList.innerHTML = '';
  let students = [];
  try { students = JSON.parse(studentsJson); } catch (e) { students = []; }
  const filtered = students.filter(s => s.adviser_id == loggedAdviserId);
  if (filtered.length > 0) {
    filtered.forEach(student => {
      const li = document.createElement('li');
      li.textContent = `${student.name} - Contact: ${student.contact}`;
      childrenList.appendChild(li);
    });
  } else {
    childrenList.innerHTML = '<li>No children under your supervision</li>';
  }
  document.getElementById('smsBtn').onclick = function () {
    const msg = `Hello ${name}, regarding your child(ren): ${filtered.map(s => s.name).join(', ')}.`;
    alert(`SMS to ${contact}: ${msg}`);
  };
  document.getElementById('infoModal').style.display = 'flex';
}

function editGuardian(button) {
  const row = button.closest('tr');
  const cells = row.cells;
  const fullName = cells[0].innerText.trim();
  const lastSpaceIndex = fullName.lastIndexOf(' ');
  const firstName = fullName.slice(0, lastSpaceIndex).trim();
  const lastName = fullName.slice(lastSpaceIndex + 1).trim();

  document.getElementById('parent_id').value = row.dataset.id;
  document.getElementById('parent_fname').value = firstName;
  document.getElementById('parent_lname').value = lastName;
  document.getElementById('parent_birthdate').value = cells[1].innerText.trim();
  document.getElementById('parent_contactinfo').value = cells[2].innerText.trim();

  const form = document.getElementById('addParentForm');
  form.action = `/adviser/adviser/parents/${row.dataset.id}`;
  document.getElementById('methodField').innerHTML = '@method("PUT")';
  form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update';
  document.getElementById('modalTitle').innerText = 'Edit Parent/Guardian';
  document.getElementById('addModal').style.display = 'flex';
}

function logout() {
  if (confirm('Are you sure you want to log out?')) {
    window.location.href = '/adviser/login';
  }
}

// --- SELECT ALL CHECKBOX ---
document.getElementById('selectAll').addEventListener('change', function () {
  const checkboxes = document.querySelectorAll('.rowCheckbox');
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// --- Individual Row Checkbox: update "Select All" ---
document.querySelectorAll('.rowCheckbox').forEach(cb => {
  cb.addEventListener('change', function () {
    const all = document.querySelectorAll('.rowCheckbox');
    const allChecked = [...all].every(chk => chk.checked);
    document.getElementById('selectAll').checked = allChecked;
  });
});

let archiveRows = []; // store archived rows

// --- Trash selected rows ---
document.getElementById('trashBtn').addEventListener('click', function () {
  const checkboxes = document.querySelectorAll('.rowCheckbox:checked');
  if (checkboxes.length === 0) {
    alert("Please select at least one row to trash.");
    return;
  }

  if (!confirm("Are you sure you want to move selected to archive?")) return;

  checkboxes.forEach(cb => {
    const row = cb.closest('tr');
    const cells = row.querySelectorAll('td');
    archiveRows.push({
      name: cells[1].innerText,
      birthdate: cells[2].innerText,
      contact: cells[3].innerText
    });
    row.remove(); // remove from main table
  });
  document.getElementById('selectAll').checked = false;
});

// --- Open Archive Modal ---
document.querySelector('.btn-archive').addEventListener('click', function () {
  renderArchiveTable();
  document.getElementById('archiveModal').style.display = 'flex';
});

// --- Render Archive Table with Restore buttons ---
function renderArchiveTable() {
  const archiveBody = document.querySelector('#archiveTable tbody');
  archiveBody.innerHTML = "";
  if (archiveRows.length === 0) {
    archiveBody.innerHTML = "<tr><td colspan='4'>No archived parents.</td></tr>";
  } else {
    archiveRows.forEach((r, index) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.name}</td>
        <td>${r.birthdate}</td>
        <td>${r.contact}</td>
        <td>
          <button class="btn btn-info" onclick="restoreRow(${index})">
            <i class="fas fa-undo"></i> Restore
          </button>
        </td>
      `;
      archiveBody.appendChild(tr);
    });
  }
}

// --- Restore row from archive ---
function restoreRow(index) {
  const r = archiveRows[index];

  // add back to main table
  const parentTableBody = document.querySelector("#parentTable tbody");
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="checkbox" class="rowCheckbox"></td>
    <td>${r.name}</td>
    <td>${r.birthdate}</td>
    <td>${r.contact}</td>
    <td class="actions">
      <button class="btn btn-info"><i class="fas fa-info-circle"></i> Info</button>
      <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
      <button class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
    </td>
  `;
  parentTableBody.appendChild(tr);

  // remove from archive
  archiveRows.splice(index, 1);
  renderArchiveTable();
}
// --- Restore All ---
function restoreAllRows() {
  if (archiveRows.length === 0) {
    alert("No archived parents to restore.");
    return;
  }

  if (!confirm("Restore all archived parents?")) return;

  const parentTableBody = document.querySelector("#parentTable tbody");
  archiveRows.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="checkbox" class="rowCheckbox"></td>
      <td>${r.name}</td>
      <td>${r.birthdate}</td>
      <td>${r.contact}</td>
      <td class="actions">
        <button class="btn btn-info"><i class="fas fa-info-circle"></i> Info</button>
        <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
        <button class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
      </td>
    `;
    parentTableBody.appendChild(tr);
  });

  archiveRows = []; // clear archive
  renderArchiveTable();
}

// --- Search inside Archive ---
document.getElementById('archiveSearch').addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll("#archiveTable tbody tr");
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

  </script>
</body>
</html>
