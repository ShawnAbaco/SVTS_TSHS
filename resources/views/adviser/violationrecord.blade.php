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

 <!-- SIDEBAR -->

<nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
        <img src="/images/Logo.png" alt="Logo">
        <p>ADVISER</p>
    </div>
    <ul>
        <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
        <li><a href="{{ route('parent.list') }}" ><i class="fas fa-user-friends"></i> Parent List</a></li>

        <!-- Violations Dropdown -->
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
            <ul class="dropdown-container">
                 <li><a href="{{ route('violation.record') }}" class="active">Violation Records</a></li>
                <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
                <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
            </ul>
        </li>

        <!-- Complaints Dropdown -->
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

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="header">
    <h2>Violation Logging</h2>
    <div class="actions">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search records...">
      </div>
      <button class="btn btn-info" onclick="openAddModal()">
        <i class="fas fa-plus-circle"></i> Add Violation Record
        <button class="btn-archive" id="archivesBtn">
  <i class="fas fa-archive"></i> Archives
</button>

    </div>
  </div>

  <!-- VIOLATION RECORDS TABLE -->
<div class="violation-table-container">
  <table id="violationTable">
    <thead>
        <th style="text-align: center;">
      <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
        <input type="checkbox" id="selectAll">
        <button class="btn-trash-small" title="Delete Selected">
          <i class="fas fa-trash"></i>

        <th>Student</th>
        <th>Violation</th>
        <th>Incident</th>
        <th>Date</th>
        <th>Time</th>
        <th>Sanction</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($violations as $v)
      <tr data-id="{{ $v->violation_id }}" data-student-id="{{ $v->student->student_id }}">
        <td><input type="checkbox" class="rowCheckbox"></td>
        <td>{{ $v->student->student_fname }} {{ $v->student->student_lname }}</td>
        <td>{{ $v->offense->offense_type }}</td>
        <td>{{ $v->violation_incident }}</td>
        <td>{{ $v->violation_date }}</td>
        <td>{{ $v->violation_time }}</td>
        <td>{{ $v->offense->sanction_consequences }}</td>
        <td>
          <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- ADD VIOLATION MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Violation Record</h2>
    <form id="addViolationForm" method="POST" action="{{ route('adviser.storeViolation') }}">
      @csrf

      <!-- Student Selection -->
      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <!-- Offense Selection -->
      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <!-- Incident -->
      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" placeholder="Incident Details" required
               pattern="^[A-Za-z0-9\s.,#()!?-]+$"
               title="Only letters, numbers, spaces, commas, periods, dashes, (), !, and ? are allowed">
      </div>

      <!-- Date -->
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date"
               max="<?php echo date('Y-m-d'); ?>" required
               title="Date cannot be in the future">
      </div>

      <!-- Time -->
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" required>
      </div>

      <!-- Save Button -->
      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT VIOLATION MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Violation Record</h2>
    <form id="editViolationForm">
      @csrf
      @method('PUT')
      <input type="hidden" name="violation_id" id="editViolationId">

      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" id="editStudent" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" id="editOffense" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" id="editIncident" required>
      </div>

      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date" id="editDate" required>
      </div>

      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" id="editTime" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Update</button>
      </div>
    </form>
  </div>
</div>
<!-- ARCHIVE MODAL -->
<div class="modal" id="archiveModal">
  <div class="modal-content" style="max-width: 800px; width: 90%;">
    <button class="close-btn" onclick="closeModal('archiveModal')">&times;</button>
    <h2>Archived Violations</h2>

    <!-- Search in archive -->
    <div class="search-box" style="margin-bottom: 10px;">
      <input type="text" id="archiveSearch" placeholder="Search archived records...">
    </div>

    <!-- Archive Table -->
    <div class="violation-table-container">
      <table id="archiveTable">
        <thead>
          <tr>
            <th>Student</th>
            <th>Violation</th>
            <th>Incident</th>
            <th>Date</th>
            <th>Time</th>
            <th>Sanction</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Filled dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="{{ asset('js/adviser/violationrecord.js') }}"></script>


</body>
</html>
