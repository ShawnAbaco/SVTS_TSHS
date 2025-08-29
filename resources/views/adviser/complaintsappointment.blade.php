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
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

    /* Sidebar */
    .sidebar {
      background-color: var(--secondary-color);
      width: 250px;
      padding: 1rem 0;
      position: fixed;
      top: 0;
      bottom: 0;
      overflow-y: auto;
      box-shadow: var(--shadow);
    }

    .sidebar img {
      width: 200px;
      height: auto;
      margin-bottom: 0;
    }

    .sidebar p {
      margin-top: 0.5rem;
      text-align: center;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      text-decoration: none;
      padding: 1rem 1.5rem;
      margin: 0.5rem 0;
      border-radius: 5px;
    }

    .sidebar a i {
      margin-right: 1rem;
      font-size: 1.2rem;
    }

    .sidebar a:hover { background-color: inherit; }
    .sidebar a.active { background-color: inherit; }
    .sidebar a.active:hover { background-color: inherit; }

    /* Main content */
    .main-content {
      margin-left: 260px;
      padding: 2rem;
      flex-grow: 1;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: var(--secondary-color);
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      box-shadow: var(--shadow);
    }

    .close {
      float: right;
      font-size: 1.5rem;
      cursor: pointer;
    }

    .modal-content form {
      display: flex;
      flex-direction: column;
    }

    .modal-content label {
      margin-top: 10px;
      margin-bottom: 5px;
    }

    .modal-content input, .modal-content select {
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .btn-primary {
      margin-top: 15px;
      padding: 10px;
      background-color: var(--hover-bg);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn-primary:hover {
      background-color: var(--hover-active-bg);
    }

    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }

    table, th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: var(--hover-bg);
      color: white;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px; height: auto; margin-bottom: 0;">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}" class="active"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="main-content">
    <h1>Complaints Appointments</h1>
    <button class="btn-primary" id="openModalBtn">Create Appointment</button>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Create Appointment</h2>
        <form id="appointmentForm" action="{{ route('complaints.appointment.store') }}" method="POST">
          @csrf
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

          <button type="submit" class="btn-primary">Save</button>
        </form>
      </div>
    </div>

    <!-- Appointment Table -->
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
        </tr>
        @endforeach
    </tbody>
</table>

  </div>

  <script>
    const menuLinks = document.querySelectorAll('.sidebar a');
    const activeLink = localStorage.getItem('activeMenu');
    if (activeLink) menuLinks.forEach(link => { if(link.href===activeLink) link.classList.add('active'); });
    menuLinks.forEach(link=>{link.addEventListener('click', function(){ menuLinks.forEach(i=>i.classList.remove('active')); this.classList.add('active'); if(!this.href.includes('profile.settings')) localStorage.setItem('activeMenu', this.href); });});
    function logout(){ alert('Logging out...'); }

    // Modal logic
    const modal = document.getElementById("appointmentModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModal = document.querySelector(".close");

    openModalBtn.onclick = () => modal.style.display = "block";
    closeModal.onclick = () => modal.style.display = "none";
    window.onclick = (event) => { if(event.target == modal) modal.style.display = "none"; };
  </script>
</body>
</html>
