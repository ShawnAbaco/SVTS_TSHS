<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Appointment</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/adviser/violationappointment.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .table-actions button {
      margin: 0 2px;
      padding: 5px 8px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-edit { background: #3498db; color: white; }
    .btn-delete { background: #e74c3c; color: white; }
    .toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    #searchInput {
      padding: 7px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      flex: 1;
      max-width: 250px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <nav class="sidebar">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px;">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}" class="active"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Violation Appointments</h1>

    <div class="toolbar">
      <button class="btn-primary" onclick="openModal('createModal')">
        <i class="fas fa-plus"></i> Create Appointment
      </button>
      <input type="text" id="searchInput" placeholder="Search appointments...">
    </div>

    <!-- Appointment Table -->
    <div class="card">
      <div class="card-header">Scheduled Appointments</div>
      <div class="card-body">
        <table id="appointmentTable">
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
              <th>Actions</th>
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
              <td class="table-actions">
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

  <!-- Create Appointment Modal -->
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
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" name="violation_app_date" id="date" required>
        </div>
        <div class="form-group">
          <label for="time">Time</label>
          <input type="time" name="violation_app_time" id="time" required>
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <select name="violation_app_status" id="status" required>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
        <button type="submit" class="btn-primary">
          <i class="fas fa-save"></i> Save Appointment
        </button>
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
      }).then(() => window.location.href = '/admin/login')
        .catch(error => console.error('Logout failed:', error));
    }

    // Live search filter
    document.getElementById("searchInput").addEventListener("keyup", function() {
      let filter = this.value.toLowerCase();
      document.querySelectorAll("#appointmentTable tbody tr").forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });
  </script>

</body>
</html>
