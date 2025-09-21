<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/adviser/complaintsall.css') }}">
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
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <!-- Violations Dropdown -->
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
      <!-- Complaints Dropdown -->
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}" class="active">Complaints</a></li>
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

  <!-- Main content -->
  <div class="main-content">
   <div class="toolbar">
  <h1>Complaints</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search complaints...">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add Complaint</button>
    <button class="btn-archive" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <!-- Table -->
    <table id="complaintsTable">
      <thead>
        <tr>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Offense</th>
          <th>Sanction</th>
          <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($complaints as $c)
        <tr>
          <td>{{ $c->complainant->student_fname ?? 'N/A' }} {{ $c->complainant->student_lname ?? '' }}</td>
          <td>{{ $c->respondent->student_fname ?? 'N/A' }} {{ $c->respondent->student_lname ?? '' }}</td>
          <td>{{ $c->offense->offense_type ?? 'N/A' }}</td>
          <td>{{ $c->offense->sanction_consequences ?? 'N/A' }}</td>
          <td>{{ $c->complaints_incident }}</td>
          <td>{{ $c->complaints_date }}</td>
          <td>{{ $c->complaints_time }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Modal -->
    <div id="complaintModal" class="modal">
      <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h3 id="modalTitle">Create Complaint</h3>
        <form id="complaintForm">
          <label>Complainant Name</label>
          <input type="text" name="complainant" required>
          <label>Respondent</label>
          <input type="text" name="respondent" required>
          <label>Offense</label>
          <input type="text" id="offenseSearch" name="offense" placeholder="Search offense..." list="offenseList" required>
          <datalist id="offenseList">
            @foreach($offenses as $offense)
              <option value="{{ $offense->offense_type }}">
            @endforeach
          </datalist>
          <label>Incident</label>
          <textarea name="incident" rows="3" required></textarea>
          <label>Date</label>
          <input type="date" name="date" required>
          <label>Time</label>
          <input type="time" name="time" required>
          <button type="submit"><i class="fas fa-save"></i> Submit</button>
        </form>
      </div>
    </div>
  </div>

<script src="{{ asset('js/adviser/complaintsall.js') }}"></script>

</body>
</html>
