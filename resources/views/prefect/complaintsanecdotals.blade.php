<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Anecdotals</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link rel="stylesheet" href="{{ asset('css/admin/COMPLAINTS.css') }}">
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

<!-- Content -->
<div class="content">
  <h1>Complaints Anecdotals</h1>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Solution</th>
        <th>Recommendation</th>
        <th>Date</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      @foreach($complaint_anecdotals as $anec)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $anec->complaint->complainant->student_fname }} {{ $anec->complaint->complainant->student_lname }}</td>
        <td>{{ $anec->complaint->respondent->student_fname }} {{ $anec->complaint->respondent->student_lname }}</td>
        <td>{{ $anec->comp_anec_solution }}</td>
        <td>{{ $anec->comp_anec_recommendation }}</td>
        <td>{{ $anec->comp_anec_date }}</td>
        <td>{{ $anec->comp_anec_time }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  function logout() {
    fetch('/logout', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    }).then(() => window.location.href = '/prefect/login')
      .catch(error => console.error('Logout failed:', error));
  }
</script>

</body>
</html>
