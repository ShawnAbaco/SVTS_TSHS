<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Appointments</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
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

    * {
      color: black !important;
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
    }

    /* --- Sidebar (unchanged except colors) --- */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      height: 100%;
      background: linear-gradient(180deg, rgb(48, 48, 50));
      font-family: "Segoe UI", Tahoma, sans-serif;
      z-index: 1000;
      overflow-y: auto;
      transition: all 0.3s ease;
      font-weight: bold;
      color: #ffffff;
    }
    .sidebar, .sidebar * { color: #ffffff !important; }
    .sidebar img { width: 180px; margin: 0 auto 0.1rem; display: block; }
    .sidebar p { font-size: 0.9rem; text-transform: uppercase; text-align: center; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li a {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 20px; text-decoration: none;
      font-size: 0.95rem; border-left: 4px solid transparent;
      border-radius: 8px; transition: all 0.3s ease;
    }
    .sidebar ul li a:hover { background-color: rgba(255,255,255,0.12); border-left-color: #FFD700; }
    .dropdown-container { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
    .dropdown-container.show { max-height: 400px; padding-left: 12px; }
    .dropdown-container li a { font-size: 0.85rem; padding: 10px 20px; }
    .dropdown-btn .fa-caret-down { margin-left: auto; transition: transform 0.3s ease; }

    /* --- Main content --- */
    .main-content {
      margin-left: 260px;
      padding: 2rem;
      flex-grow: 1;
    }

    /* --- High-res Buttons --- */
    button, .btn-primary, .btn-red, .btn-orange {
      font-size: 0.95rem;
      padding: 10px 18px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
    }
    .btn-primary { background-color: var(--button-blue); color: #fff; }
    .btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
    .btn-red { background-color: var(--button-red); color: #fff; }
    .btn-red:hover { background-color: #b52a37; transform: translateY(-2px); }
    .btn-orange { background-color: var(--button-orange); color: #fff; }
    .btn-orange:hover { background-color: #e76a05; transform: translateY(-2px); }

    /* --- Table High-res --- */
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
  background-color: #000000; /* Black background */
  color: #ffffff !important; /* White text */
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


    /* --- Modal High-res --- */
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
</head>
<body>
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
          <li><a href="{{ route('complaints.appointment') }}" class="active">Complaints Appoinment</a></li>
          <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
        </ul>
      </li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- MAIN -->
  <div class="main-content">
    <h1>Complaints Appointments</h1>

    <!-- Top Controls -->
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:15px;">
      <input type="text" id="searchInput" placeholder="Search appointments..." style="padding:8px 10px;border-radius:6px;border:1px solid #ccc;box-shadow:var(--shadow);margin-right:10px;">
      <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create Appointment</button>
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
