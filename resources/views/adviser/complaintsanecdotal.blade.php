<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Anecdotal</title>

    <link rel="stylesheet" href="{{ asset('css/adviser/complaintsanecdotal.css') }}">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>


</head>
<body>

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
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

  <!-- MAIN CONTENT -->
  <div class="main-content">
   <div class="toolbar">
  <h1>Complaints Anecdotal</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search..." />
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add</button>
    <button class="btn-archive" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <!-- Modal -->
    <div id="anecdotalModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle">Add Anecdotal Complaint</h3>
        <form id="anecdotalForm">
          <label>Complainant</label>
          <input type="text" name="complainant" required>

          <label>Respondent</label>
          <input type="text" name="respondent" required>

          <label>Solution</label>
          <textarea name="solution" rows="2" required></textarea>

          <label>Recommendation</label>
          <textarea name="recommendation" rows="2" required></textarea>

          <label>Date</label>
          <input type="date" name="date" required>

          <label>Time</label>
          <input type="time" name="time" required>

          <button type="submit" class="btn-primary">Save</button>
        </form>
      </div>
    </div>

    <!-- Table -->
    <table id="anecdotalTable" class="complaints-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Solution</th>
          <th>Recommendation</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($anecdotal as $item)
        <tr>
          <td>{{ $item->comp_anec_id }}</td>
          <td>{{ $item->complaint->complainant->student_fname ?? 'N/A' }} {{ $item->complaint->complainant->student_lname ?? '' }}</td>
          <td>{{ $item->complaint->respondent->student_fname ?? 'N/A' }} {{ $item->complaint->respondent->student_lname ?? '' }}</td>
          <td>{{ $item->comp_anec_solution }}</td>
          <td>{{ $item->comp_anec_recommendation }}</td>
          <td>{{ $item->comp_anec_date }}</td>
          <td>{{ $item->comp_anec_time }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
<script src="{{ asset('js/adviser/complaintsanecdotal.js') }}"></script>

</body>
</html>
