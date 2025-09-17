  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Adviser Dashboard - Student List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>
  <body>
    <style>
      
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --hover-bg: rgb(0, 88, 240);
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      --overlay: rgba(0, 0, 0, 0.6);
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
      margin: 0;
      background-color: var(--secondary-color);
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

    /* --- MAIN CONTENT --- */
    .main-content {
      margin-left: 260px;
      padding: 3rem;
      flex-grow: 1;
      min-width: 0;
    }

    .main-content h1 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: #222;
    }

  /* Toolbar - title left, actions right */
.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between; /* pushes title left, actions right */
  margin: 15px 0;
  flex-wrap: wrap; /* wrap on small screens */
}

.toolbar h1 {
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0;
  color: #222;
}

.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toolbar-actions input,
.toolbar-actions button {
  height: 46px; /* same height for all */
  font-size: 16px;
  border-radius: 8px;
  padding: 0 15px;
  font-weight: 600;
}

#tableSearch {
  width: 280px;
  border: 1px solid #ccc;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}


    .btn-primary {
      background-color: var(--hover-bg);
      color: #fff !important;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }

    .btn-archive {
      background-color: #fd7e14;
      color: #fff !important;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
      transition: all 0.3s ease;
    }

    .btn-archive:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }

    /* --- TABLE --- */
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
    table tr:nth-child(even) { background-color: #f7f7f7; }
    table tr:hover { background-color: rgba(0,0,0,0.05); }
    .action-btn {
      border: none;
      cursor: pointer;
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #fff !important;
      transition: all 0.2s ease;
    }

    .action-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    .action-btn.info { background-color: #17a2b8; }
    .action-btn.edit { background-color: #007bff; }
    .action-btn.delete { background-color: #dc3545; }

    /* --- MODAL --- */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: var(--overlay);
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }

    .modal-content {
      background-color: #fff;
      width: 500px;
      max-width: 100%;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: var(--shadow);
      position: relative;
      font-size: 16px;
    }

    .modal-header {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-align: center;
    }

    .close-btn {
      position: absolute;
      top: 12px;
      right: 18px;
      font-size: 20px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .close-btn:hover {
      transform: scale(1.2);
    }

    .form-group { margin-bottom: 18px; }
    label { display: block; margin-bottom: 6px; font-size: 15px; }
    input, select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 15px; }

    .modal-footer { text-align: center; margin-top: 15px; }
    .btn-submit {
      background-color: var(--hover-bg);
      color: #fff !important;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
      </style>


  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}" class="active"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
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
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="main-content">
 <div class="toolbar">
  <h1>Student List</h1>
  <div class="toolbar-actions">
    <input id="tableSearch" type="search" placeholder="Search students...">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create Student</button>
    <button class="btn-archive"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <table id="studentTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Birthdate</th>
          <th>Address</th>
          <th>Contact#</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $student)
        <tr
            data-id="{{ $student->student_id }}"
            data-fname="{{ $student->student_fname }}"
            data-lname="{{ $student->student_lname }}"
            data-birthdate="{{ $student->student_birthdate }}"
            data-address="{{ $student->student_address }}"
            data-contact="{{ $student->student_contactinfo }}"
            data-parent-name="{{ $student->parent ? $student->parent->parent_fname . ' ' . $student->parent->parent_lname : '' }}"
            data-parent-contact="{{ $student->parent ? $student->parent->parent_contactinfo : '' }}"
            data-parent-id="{{ $student->parent ? $student->parent->parent_id : '' }}"
        >
          <td>{{ $student->student_fname . " " . $student->student_lname }}</td>
          <td>{{ $student->student_birthdate }}</td>
          <td>{{ $student->student_address }}</td>
          <td>{{ $student->student_contactinfo }}</td>
          <td>
            <button class="action-btn info"><i class="fas fa-info-circle"></i> Info</button>
            <button class="action-btn edit"><i class="fas fa-edit"></i> Edit</button>
            <form method="POST" action="{{ route('students.destroy', $student->student_id) }}" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="action-btn delete"><i class="fas fa-trash"></i> Delete</button>
      </form>
    </td>
  </tr>
  @endforeach
        </tbody>
      </table>
    </main>

  <!-- CREATE STUDENT MODAL -->
  <div class="modal" id="createStudentModal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <div class="modal-header">Create Student Information</div>

      <form id="studentForm" method="POST" action="{{ route('students.store') }}">
        @csrf

        <div class="form-group">
    <label>First Name</label>
    <input type="text" name="student_fname" required
          pattern="[A-Za-z\s]+"
          title="Only letters and spaces are allowed">
  </div>

  <div class="form-group">
    <label>Last Name</label>
    <input type="text" name="student_lname" required
          pattern="[A-Za-z\s]+"
          title="Only letters and spaces are allowed">
  </div>

        <div class="form-group">
    <label>Birthdate</label>
    <input type="date" name="student_birthdate" required
          max="<?php echo date('Y-m-d'); ?>"
          title="Birthdate cannot be in the future">
  </div>

  <div class="form-group">
    <label>Address</label>
    <input type="text" name="student_address" required
          pattern="^[A-Za-z0-9\s.,#/-]+$"
          title="Only letters, numbers, spaces, commas, periods, hyphens, #, and / are allowed.">
  </div>



  <div class="form-group">
    <label>Contact Info</label>
    <input type="text" name="student_contactinfo" required
          pattern="[0-9]{11}"
          title="Contact number must be 11 digits (e.g. 09123456789)">
  </div>

        <hr>

        <div class="form-group">
          <label for="parent_search">Parent</label>
          <input type="text" id="parent_search" class="form-control" placeholder="Type parent name...">
          <input type="hidden" id="parent_id" name="parent_id">
          <div id="parentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-submit">Create Student</button>
        </div>
      </form>
    </div>
  </div>

  <!-- INFO MODAL -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close-btn" id="closeInfoModalBtn">&times;</span>
      <div class="modal-header">Parent / Guardian Information</div>

      <div class="form-group">
        <label>Parent/Guardian Name:</label>
        <p><strong id="infoGuardianName"></strong></p>
      </div>
      <div class="form-group">
        <label>Contact Number:</label>
        <p><strong id="infoGuardianContact"></strong></p>
      </div>

      <div class="modal-footer">
        <button id="sendSMSBtn" class="btn-submit"><i class="fas fa-sms"></i> Send SMS</button>
      </div>
    </div>
  </div>

  <!-- EDIT MODAL -->
  <div class="modal" id="editStudentModal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditModalBtn">&times;</span>
      <div class="modal-header">Edit Student Information</div>
      <form id="editStudentForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="editStudentId">

        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="student_fname" id="editStudentFname" required>
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="student_lname" id="editStudentLname" required>
        </div>
        <div class="form-group">
          <label>Birthdate</label>
          <input type="date" name="student_birthdate" id="editStudentBirthdate" required>
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" name="student_address" id="editStudentAddress" required>
        </div>
        <div class="form-group">
          <label>Contact Info</label>
          <input type="text" name="student_contactinfo" id="editStudentContact" required>
        </div>

        <hr>

        <div class="form-group">
          <label for="edit_parent_search">Parent / Guardian</label>
          <input type="text" id="edit_parent_search" class="form-control" placeholder="Type parent name..." required>
          <input type="hidden" id="edit_parent_id" name="parent_id">
          <div id="editParentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-submit">Update Student</button>
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




  document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', function(){
          document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
      });// Sidebar active link
  });

  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const modal = document.getElementById('createStudentModal');

  const editModal = document.getElementById('editStudentModal');
  const closeEditModalBtn = document.getElementById('closeEditModalBtn');

  const infoModal = document.getElementById('infoModal');
  const closeInfoModalBtn = document.getElementById('closeInfoModalBtn');
  const sendSMSBtn = document.getElementById('sendSMSBtn');

  // TABLE SEARCH (LIVE) - filters student table rows only
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', function() {
      const q = this.value.trim().toLowerCase();
      document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        // only check cell text, not attributes — matches visible table content
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // OPEN / CLOSE MODALS
  openModalBtn.addEventListener('click', () => modal.style.display = 'flex');
  closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
  closeEditModalBtn.addEventListener('click', () => editModal.style.display = 'none');
  closeInfoModalBtn.addEventListener('click', () => infoModal.style.display = 'none');

  // Close modals when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
    if (e.target === editModal) editModal.style.display = 'none';
    if (e.target === infoModal) infoModal.style.display = 'none';
  });

  // EDIT MODAL
  document.querySelectorAll('.edit').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const studentId = row.dataset.id;

      document.getElementById('editStudentId').value = studentId;
      document.getElementById('editStudentFname').value = row.dataset.fname;
      document.getElementById('editStudentLname').value = row.dataset.lname;
      document.getElementById('editStudentBirthdate').value = row.dataset.birthdate;
      document.getElementById('editStudentAddress').value = row.dataset.address;
      document.getElementById('editStudentContact').value = row.dataset.contact;

      document.getElementById('edit_parent_search').value = row.dataset.parentName;
      document.getElementById('edit_parent_id').value = row.dataset.parentId || '';

      document.getElementById('editStudentForm').action = `/adviser/students/${studentId}`;
      editModal.style.display = 'flex';
    });
  });

  // INFO BUTTON
  document.querySelectorAll('.info').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const guardianName = row.dataset.parentName;
      const guardianContact = row.dataset.parentContact;

      document.getElementById('infoGuardianName').innerText = guardianName;
      document.getElementById('infoGuardianContact').innerText = guardianContact;

      infoModal.style.display = 'flex';

      sendSMSBtn.onclick = () => {
        alert(`📩 SMS will be sent to ${guardianName} (${guardianContact})`);
      };
    });
  });

  // LIVE SEARCH PARENT (Create)
  document.getElementById('parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('parentList').style.display = 'none';
      document.getElementById('parentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          // sanitize name when injecting
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('parentList').innerHTML = results;
        document.getElementById('parentList').style.display = 'block';
      });
  });

  function selectParent(id, name){
    document.getElementById('parent_id').value = id;
    document.getElementById('parent_search').value = name;
    document.getElementById('parentList').style.display = 'none';
  }

  // LIVE SEARCH for EDIT MODAL
  document.getElementById('edit_parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('editParentList').style.display = 'none';
      document.getElementById('editParentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectEditParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('editParentList').innerHTML = results;
        document.getElementById('editParentList').style.display = 'block';
      });
  });

  function selectEditParent(id, name){
    document.getElementById('edit_parent_id').value = id;
    document.getElementById('edit_parent_search').value = name;
    document.getElementById('editParentList').style.display = 'none';
  }

  function logout(){
    if(confirm('Are you sure you want to log out?')){
      window.location.href = '/adviser/login';
    }
  }
  </script>

  </body>
  </html>
