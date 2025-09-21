<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Appointments</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/adviser/complaintsappointment.css') }}">
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
          <li><a href="{{ route('complaints.appointment') }}" class="active">Complaints Appointment</a></li>
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

  <!-- MAIN -->
  <div class="main-content">
    <div class="toolbar">
  <h1>Complaints Appointments</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search appointments...">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create</button>
    <button class="btn-orange" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
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
<script src="{{ asset('js/adviser/complaintsappointment.js') }}"></script>

</body>
</html>
