<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Reports</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/adviser/reports.css') }}">

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
                <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
                <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
            </ul>
        </li>
        <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
        <li><a href="{{ route('adviser.reports') }}" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
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
  <!-- Page Header -->
  <div class="page-header">
    <h1>REPORTS</h1>
  </div>
  <!-- 20 Report Boxes (Sorted A-Z) -->
  <div class="report-box" data-modal="modal1"><i class="fas fa-book-open"></i><h3>Anecdotal Records per Complaint Case</h3></div>
  <div class="report-box" data-modal="modal2"><i class="fas fa-book"></i><h3>Anecdotal Records per Violation Case</h3></div>
  <div class="report-box" data-modal="modal3"><i class="fas fa-calendar-check"></i><h3>Appointments Scheduled for Complaints</h3></div>
  <div class="report-box" data-modal="modal4"><i class="fas fa-calendar-alt"></i><h3>Appointments Scheduled for Violation Cases</h3></div>
  <div class="report-box" data-modal="modal5"><i class="fas fa-file-alt"></i><h3>Complaint Records with Complainant and Respondent</h3></div>
  <div class="report-box" data-modal="modal6"><i class="fas fa-clock"></i><h3>Complaints Filed within the Last 30 Days</h3></div>
  <div class="report-box" data-modal="modal7"><i class="fas fa-chart-bar"></i><h3>Common Offenses by Frequency</h3></div>
  <div class="report-box" data-modal="modal8"><i class="fas fa-exclamation-triangle"></i><h3>List of Violators with Repeat Offenses</h3></div>
  <div class="report-box" data-modal="modal9"><i class="fas fa-gavel"></i><h3>Offenses and Their Sanction Consequences</h3></div>
  <div class="report-box" data-modal="modal10"><i class="fas fa-phone-alt"></i><h3>Parent Contact Info for Students with Active Violations</h3></div>
  <div class="report-box" data-modal="modal11"><i class="fas fa-chart-line"></i><h3>Sanction Trends Across Time Periods</h3></div>
  <div class="report-box" data-modal="modal12"><i class="fas fa-user-graduate"></i><h3>Students and Their Parents</h3></div>
  <div class="report-box" data-modal="modal13"><i class="fas fa-user-shield"></i><h3>Students with Both Violation and Complaint Records</h3></div>
  <div class="report-box" data-modal="modal14"><i class="fas fa-user-friends"></i><h3>Students with the Most Violation Records</h3></div>
  <div class="report-box" data-modal="modal15"><i class="fas fa-exclamation-circle"></i><h3>Violation Records with Violator Information</h3></div>
</div>


<!-- Modals -->
@for($i=1; $i<=16; $i++)
<div id="modal{{ $i }}" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title"></h2>

    <div class="toolbar">
      <input type="text" placeholder="Search..." oninput="liveSearch('modal{{ $i }}', this.value)">
      <button class="btn btn-warning" onclick="printModal('modal{{ $i }}')"><i class="fa fa-print"></i> Print</button>
      <button class="btn btn-danger" onclick="exportCSV('modal{{ $i }}')"><i class="fa fa-file-export"></i> Export CSV</button>
    </div>

    <div class="modal-table-container">
    <table id="table-{{ $i }}" class="w-full border-collapse">
      <thead>
        @switch($i)
            @case(1)
            <tr>
                <th>Anecdotal ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Solution</th>
                <th>Recommendation</th>
                <th>Date Recorded</th>
                <th>Time Recorded</th>
            </tr>
            @break
            @case(2)
            <tr>
                <th>Student Name</th>
                <th>Solution</th>
                <th>Recommendation</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
            @break
            @case(3)
            <tr>
                <th>Appointment ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Appointment Date</th>
                <th>Appointment Status</th>
            </tr>
            @break
            @case(4)
            <tr>
                <th>Student Name</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Appointment Status</th>
            </tr>
            @break
            @case(5)
            <tr>
                <th>Complaint ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Incident Description</th>
                <th>Complaint Date</th>
                <th>Complaint Time</th>
            </tr>
            @break
            @case(6)
            <tr>
                <th>Complaint ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Type of Offense</th>
                <th>Complaint Date</th>
                <th>Complaint Time</th>
            </tr>
            @break
            @case(7)
            <tr>
                <th>Offense ID</th>
                <th>Offense Type</th>
                <th>Description</th>
                <th>Total Occurrences</th>
            </tr>
            @break
            @case(8)
            <tr>
                <th>Student Name</th>
                <th>Section</th>
                <th>Grade Level</th>
                <th>Total Violations</th>
                <th>First Violation Date</th>
                <th>Most Recent Violation Date</th>
            </tr>
            @break
            @case(9)
            <tr>
                <th>Offense Type</th>
                <th>Offense Description</th>
                <th>Sanction Consequences</th>
            </tr>
            @break
            @case(10)
            <tr>
                <th>Student Name</th>
                <th>Parent Name</th>
                <th>Parent Contact Info</th>
                <th>Violation Date</th>
                <th>Violation Time</th>
                <th>Violation Status</th>
            </tr>
            @break
            @case(11)
            <tr>
                <th>Offense Sanction ID</th>
                <th>Offense Type</th>
                <th>Sanction Consequences</th>
                <th>Month and Year</th>
                <th>Number of Sanctions Given</th>
            </tr>
            @break
            @case(12)
            <tr>
                <th>Student Name</th>
                <th>Parent Name</th>
                <th>Parent Contact Info</th>
            </tr>
            @break
            @case(13)
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Violation Count</th>
                <th>Complaint Involvement Count</th>
            </tr>
            @break
            @case(14)
            <tr>
                <th>Student Name</th>
                <th>Adviser Section</th>
                <th>Grade Level</th>
                <th>Total Violations</th>
            </tr>
            @break
            @case(15)
            <tr>
                <th>Offense ID</th>
                <th>Offense Type</th>
                <th>Sanction Consequences</th>
                <th>Month and Year</th>
                <th>Number of Sanctions Given</th>
            </tr>
            @break
            @case(16)
            <tr>
                <th>Violation ID</th>
                <th>Student Name</th>
                <th>Offense Type</th>
                <th>Sanction</th>
                <th>Incident Description</th>
                <th>Violation Date</th>
                <th>Violation Time</th>
            </tr>
            @break
        @endswitch
      </thead>
      <tbody></tbody>
    </table>
</div>
  </div>
</div>
@endfor

<script src="{{ asset('js/adviser/reports.js') }}"></script>



</body>
</html>
