<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Appointments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
 <link rel="stylesheet" href="{{ asset('css/admin/COMPLAINTSAPPOINTMENT.css') }}">
</head>
<body>

 <!-- Sidebar -->
    <div class="sidebar">
        <p>PREFECT DASHBOARD</p>
        <ul>
           <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
            <li><a href="{{ route('parent.lists') }}"><i class="fas fa-user-graduate"></i> Parent List </a></li>
            <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
            <li><a href="{{ route('violation.records') }}"><i class="fas fa-gavel"></i> Violation Record </a></li>
            <li><a href="{{ route('violation.appointments') }}"><i class="fas fa-bell"></i> Violation Appointments </a></li>
            <li><a href="{{ route('violation.anecdotals') }}"><i class="fas fa-chart-line"></i> Violation Anecdotal </a></li>
            <li><a href="{{ route('people.complaints') }}"><i class="fas fa-users"></i> Complaints</a></li>
            <li><a href="{{ route('complaints.appointments') }}"><i class="fas fa-cogs"></i> Complaints Appointments</a></li>
            <li><a href="{{ route('complaints.anecdotals') }}"><i class="fas fa-book"></i> Complaints Anecdotal</a></li>
            <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense&Sanctions </a></li>
             <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
            <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>

<!-- Main Content -->
<div class="content">
  <div class="container">
    <h1>Complaints Appointments</h1>
    <button class="btn btn-primary" onclick="openAppointmentModal()">Create Appointment</button>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Complaint</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="appointmentTableBody"></tbody>
    </table>
  </div>
</div>

<!-- Appointment Modal -->
<div id="appointmentModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">Create Appointment</div>
    <form id="appointmentForm">
      <div class="modal-body">
        <label for="complaint">Complaint</label>
        <select id="complaint" required>
          <option value="">Select Complaint</option>
          <option value="Bullying">Bullying</option>
          <option value="Harassment">Harassment</option>
          <option value="Discrimination">Discrimination</option>
        </select>

        <label for="date">Date</label>
        <input type="date" id="date" required>

        <label for="time">Time</label>
        <input type="time" id="time" required>

        <label for="status">Status</label>
        <select id="status" required>
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeAppointmentModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
    </form>
  </div>
</div>

<script>
  let appointments = [];

  function openAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'flex';
  }

  function closeAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'none';
    document.getElementById('appointmentForm').reset();
  }

  document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const complaint = document.getElementById('complaint').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const status = document.getElementById('status').value;

    const newAppointment = { id: appointments.length + 1, complaint, date, time, status };
    appointments.push(newAppointment);
    renderAppointments();

    closeAppointmentModal();
  });

  function renderAppointments() {
    const tableBody = document.getElementById('appointmentTableBody');
    tableBody.innerHTML = '';
    appointments.forEach((appt, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${appt.id}</td>
        <td>${appt.complaint}</td>
        <td>${appt.date}</td>
        <td>${appt.time}</td>
        <td>${appt.status}</td>
        <td><button class="btn btn-danger btn-sm" onclick="deleteAppointment(${index})">Delete</button></td>
      `;
      tableBody.appendChild(row);
    });
  }

  function deleteAppointment(index) {
    if (confirm('Are you sure you want to delete this appointment?')) {
      appointments.splice(index, 1);
      renderAppointments();
    }
  }
</script>
</body>
</html>
