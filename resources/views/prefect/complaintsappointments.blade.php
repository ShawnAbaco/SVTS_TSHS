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
      <tbody>
        @foreach($appointments as $appointment)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $appointment->complaint->complaints_incident }}</td>
            <td>{{ $appointment->comp_app_date }}</td>
            <td>{{ $appointment->comp_app_time }}</td>
            <td>{{ $appointment->comp_app_status }}</td>
            <td>
              <form action="{{ route('complaints.appointments.destroy', $appointment->comp_app_id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
              </form>
            </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Appointment Modal -->
<div id="appointmentModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">Create Appointment</div>
    <form action="{{ route('complaints.appointments.store') }}" method="POST">
      @csrf
      <div class="modal-body">
        <label for="complaint">Complaint</label>
        <select id="complaint" name="complaints_id" required>
          <option value="">Select Complaint</option>
          @foreach($complaints as $complaint)
            <option value="{{ $complaint->complaints_id }}">
              {{ $complaint->complaints_incident }}
            </option>
          @endforeach
        </select>

        <label for="date">Date</label>
        <input type="date" id="date" name="comp_app_date" required>

        <label for="time">Time</label>
        <input type="time" id="time" name="comp_app_time" required>

        <label for="status">Status</label>
        <select id="status" name="comp_app_status" required>
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
  function openAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'flex';
  }

  function closeAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'none';
  }
</script>
</body>
</html>
