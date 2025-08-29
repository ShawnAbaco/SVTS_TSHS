<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violation Logging</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/adviser/violationrecord.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<nav class="sidebar">
  <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
    <img src="/images/Logo.png" alt="Logo" style="width: 200px;">
    <p>ADVISER</p>
  </div>
  <ul style="list-style: none; padding: 0; margin: 0;">
    <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
    <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
    <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
    <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
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

<div class="main-content">
  <h2>Violation Logging</h2>


  

  <!-- VIOLATION RECORDS TABLE -->
  <div class="violation-table-container">
    <h3>Violation Records</h3>
    <table id="violationTable">
      <thead>
        <tr>
          <th>Student</th>
          <th>Violation</th>
          <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Sanction</th>
          <th>Adviser</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($violations as $v)
        <tr data-id="{{ $v->violation_id }}" data-student-id="{{ $v->student->student_id }}">
          <td>{{ $v->student->student_fname }} {{ $v->student->student_lname }}</td>
          <td>{{ $v->offense->offense_type }}</td>
          <td>{{ $v->violation_incident }}</td>
          <td>{{ $v->violation_date }}</td>
          <td>{{ $v->violation_time }}</td>
          <td>{{ $v->offense->sanction_consequences }}</td>
          <td>{{ $v->student->adviser->adviser_fname }} {{ $v->student->adviser->adviser_lname }}</td>
          <td>
            <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>


</body>
</html>
