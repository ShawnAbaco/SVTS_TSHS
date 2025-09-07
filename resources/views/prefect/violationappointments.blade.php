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

  <!-- Search bar -->
  <div style="margin-bottom: 15px;">
    <input type="text" id="searchInput" placeholder="Search student or parent name..." style="padding:8px; width: 300px;">
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
       <tbody id="appointmentList">
@foreach($violation_appointments as $index => $app)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $app->violation->student->student_fname }} {{ $app->violation->student->student_lname }}</td>
    <td>{{ $app->violation->student->parent->parent_fname }} {{ $app->violation->student->parent->parent_lname }}</td>
    <td>{{ $app->violation->student->parent->parent_contactinfo }}</td>
    <td>{{ $app->violation->violation_incident }}</td>
    <td>{{ $app->violation->offense->offense_type }}</td>
    <td>{{ $app->violation_app_date }}</td>
    <td>{{ \Carbon\Carbon::parse($app->violation_app_time)->format('h:i A') }}</td>
    <td>{{ $app->violation_app_status }}</td>
</tr>
@endforeach
</tbody>
      </table>
    </div>
  </div>
</div>

<!-- Create Appointment Modal -->
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
    <button type="submit" class="btn-primary">Save Appointment</button>
</form>
  </div>
</div>

<script>
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function logout() {
  fetch('/logout', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => {
    window.location.href = '/prefect/login';
  }).catch(error => console.error('Logout failed:', error));
}

// 🔍 Search filter for student/parent
document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  let rows = document.querySelectorAll('#appointmentList tr');

  rows.forEach(row => {
    let student = row.cells[1].textContent.toLowerCase();
    let parent = row.cells[2].textContent.toLowerCase();
    if (student.includes(filter) || parent.includes(filter)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
</script>

</body>
</html>
