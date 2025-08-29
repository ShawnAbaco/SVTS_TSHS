<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Appointment</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('css/admin/VIOLATIONAPPOINTMENT.css') }}">
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

<div class="container">
  <h1>VIOLATION APPOINTMENTS</h1>
  <div style="margin-bottom: 20px;">
    <button class="btn-primary" onclick="openModal('createModal')">Create Appointment</button>
  </div>

  <div class="card">
    <div class="card-header">Scheduled Appointments</div>
    <div class="card-body">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Parent</th>
            <th>Contact</th>
            <th>Incident</th>
            <th>Offense</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="appointmentList"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Create Appointment Modal -->
<div class="modal" id="createModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('createModal')">&times;</span>
    <h2>Create Schedule Appointment</h2>
    <form id="createAppointmentForm">
      <div class="form-group">
        <label for="studentSearch">Search Student</label>
        <input type="text" id="studentSearch" placeholder="Search by name" onkeyup="filterStudents()">
        <select id="studentSelect"></select>
      </div>
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" id="date">
      </div>
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" id="time">
      </div>
      <div class="form-group">
        <label for="status">Status</label>
        <select id="status">
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <button type="button" class="btn-primary" onclick="saveAppointment()">Save Appointment</button>
    </form>
  </div>
</div>

<script>
const students = [
  {id:1, name:'John Doe', parent:'Mary Doe', contact:'09123456789', incident:'Bullying', offense:'Major'},
  {id:2, name:'Jane Smith', parent:'Robert Smith', contact:'09234567890', incident:'Late Submission', offense:'Minor'},
  {id:3, name:'Mark Johnson', parent:'Anna Johnson', contact:'09345678901', incident:'Fighting', offense:'Major'}
];

let appointments = [];

function populateStudentSelect(filter="") {
  const select = document.getElementById('studentSelect');
  select.innerHTML = "";
  students
    .filter(s => s.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = s.name;
      select.appendChild(opt);
    });
}
populateStudentSelect();

function filterStudents() {
  const search = document.getElementById('studentSearch').value;
  populateStudentSelect(search);
}

function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function formatTimeToAMPM(time) {
  let [hour, minute] = time.split(':');
  hour = parseInt(hour);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  hour = hour % 12 || 12;
  return `${hour}:${minute} ${ampm}`;
}

function saveAppointment() {
  const studentId = document.getElementById('studentSelect').value;
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;
  const status = document.getElementById('status').value;

  if(!studentId || !date || !time) {
    alert("Please fill all fields.");
    return;
  }

  const student = students.find(s => s.id == studentId);
  appointments.push({
    id: Date.now(),
    studentName: student.name,
    parentName: student.parent,
    contact: student.contact,
    incident: student.incident,
    offense: student.offense,
    date,
    time: formatTimeToAMPM(time),
    status
  });

  renderAppointments();
  document.getElementById('createAppointmentForm').reset();
  populateStudentSelect();
  closeModal('createModal');
}

function renderAppointments() {
  const tbody = document.getElementById('appointmentList');
  tbody.innerHTML = "";
  appointments.forEach((a, index) => {
    tbody.innerHTML += `
      <tr>
        <td>${index+1}</td>
        <td>${a.studentName}</td>
        <td>${a.parentName}</td>
        <td>${a.contact}</td>
        <td>${a.incident}</td>
        <td>${a.offense}</td>
        <td>${a.date}</td>
        <td>${a.time}</td>
        <td>${a.status}</td>
      </tr>
    `;
  });
}

function logout() {
  fetch('/logout', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => {
    window.location.href = '/prefect/login';
  }).catch(error => console.error('Logout failed:', error));
}
</script>

</body>
</html>
