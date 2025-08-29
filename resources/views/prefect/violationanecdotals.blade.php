<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Reports</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/VIOLATIONANECDOTAL.css') }}">
</head>
<body>

 <!-- Sidebar -->
    <div class="sidebar">
        <p>PREFECT DASHBOARD</p>
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
    </div>

<!-- Main Content -->
<div class="main-content">
  <h2>Violation Reports</h2>
  <table>
    <thead>
      <tr>
        <th>Student Name</th>
        <th>Violation</th>
        <th>Respondent</th>
        <th>Solution</th>
        <th>Punishment</th>
        <th>Date/Year</th>
        <th>Time</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Juan Dela Cruz</td>
        <td>Late to Class</td>
        <td>Prefect</td>
        <td>Advised student</td>
        <td>None</td>
        <td>2025-08-20</td>
        <td>08:15 AM</td>
        <td class="status-pending">Pending</td>
      </tr>
      <tr>
        <td>Maria Santos</td>
        <td>Disrespect to Teacher</td>
        <td>Adviser</td>
        <td>Apology Letter</td>
        <td>Detention</td>
        <td>2025-08-21</td>
        <td>10:30 AM</td>
        <td class="status-resolved">Resolved</td>
      </tr>
      <tr>
        <td>Carlos Reyes</td>
        <td>Fighting</td>
        <td>Prefect</td>
        <td>Mediation Session</td>
        <td>Suspension</td>
        <td>2025-08-22</td>
        <td>01:45 PM</td>
        <td class="status-pending">Pending</td>
      </tr>
    </tbody>
  </table>
</div>

<script>
function logout() {
  fetch('/logout', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => window.location.href = '/prefect/login');
}
</script>

</body>
</html>
