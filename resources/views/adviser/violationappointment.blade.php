<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Appointment</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/adviser/violationappointment.css') }}">


</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar">
    <div style="text-align:center; margin-bottom:1rem;">
      <img src="/images/Logo.png" alt="Logo"><p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}" class="active">Violation Appointment</a></li>
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
  <div class="toolbar">
    <!-- Left side: title -->
    <div class="toolbar-left">
      <h1>Violation Appointments</h1>
    </div>
   <div class="toolbar">
  <div class="toolbar-right">
    <input type="text" id="searchInput" placeholder="Search appointments...">
    <button class="btn-primary" onclick="openModal('createModal')"><i class="fas fa-plus"></i> Create </button>
    <button class="btn-primary" style="background:#e63946;"><i class="fas fa-archive"></i> Archive</button>
  </div>
</div>



    <div class="card">
      <div class="card-header">Scheduled Appointments</div>
      <div class="card-body">
        <table id="appointmentTable">
          <thead>
            <tr>
              <th>#</th><th>Student</th><th>Parent</th><th>Contact</th>
              <th>Incident</th><th>Offense</th><th>Date</th><th>Time</th>
              <th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="appointmentList">
            @foreach($appointments as $index => $app)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $app->violation->student->student_fname }} {{ $app->violation->student->student_lname }}</td>
              <td>{{ $app->violation->student->parent->parent_fname ?? 'N/A' }} {{ $app->violation->student->parent->parent_lname ?? '' }}</td>
              <td>{{ $app->violation->student->parent->parent_contactinfo ?? 'N/A' }}</td>
              <td>{{ $app->violation->violation_incident }}</td>
              <td>{{ $app->violation->offense->offense_type ?? 'N/A' }}</td>
              <td>{{ $app->violation_app_date }}</td>
              <td>{{ \Carbon\Carbon::parse($app->violation_app_time)->format('h:i A') }}</td>
              <td>{{ $app->violation_app_status }}</td>
              <td>
                <button class="btn-edit"><i class="fas fa-edit"></i></button>
                <button class="btn-delete"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="createModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('createModal')">&times;</span>
      <h2>Create Schedule Appointment</h2>
      <form method="POST" action="{{ route('violation.appointment.store') }}">
        @csrf
        <div class="form-group">
          <label for="studentSelect">Select Student</label>
          <select id="studentSelect" name="violation_id" required>
            @foreach($students as $student)
              @foreach($student->violations as $violation)
                <option value="{{ $violation->violation_id }}">
                  {{ $student->student_fname }} {{ $student->student_lname }} - {{ $violation->offense->offense_type ?? '' }}
                </option>
              @endforeach
            @endforeach
          </select>
        </div>
        <div class="form-group"><label for="date">Date</label><input type="date" name="violation_app_date" id="date" required></div>
        <div class="form-group"><label for="time">Time</label><input type="time" name="violation_app_time" id="time" required></div>
        <div class="form-group">
          <label for="status">Status</label>
          <select name="violation_app_status" id="status" required>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Appointment</button>
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



// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function logout() {
      fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      }).then(() => window.location.href = '/admin/login');
    }

    // Live search
    document.getElementById("searchInput").addEventListener("keyup", function() {
      let filter = this.value.toLowerCase();
      document.querySelectorAll("#appointmentTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
      });
    });
  </script>
</body>
</html>
