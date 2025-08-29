<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Anecdotal</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/complaintsanecdotal.css') }}">
</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px; height: auto; margin-bottom: 0;">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}" class="active"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="main-content">
    <h2>Complaints Anecdotal</h2>
    <table class="complaints-table">
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
        @foreach($anecdotal as $item)
        <tr>
          <td>{{ $item->comp_anec_id }}</td>
          <td>
            {{ $item->complaint->complainant->student_fname ?? 'N/A' }}
            {{ $item->complaint->complainant->student_lname ?? '' }}
          </td>
          <td>
            {{ $item->complaint->respondent->student_fname ?? 'N/A' }}
            {{ $item->complaint->respondent->student_lname ?? '' }}
          </td>
          <td>{{ $item->comp_anec_solution }}</td>
          <td>{{ $item->comp_anec_recommendation }}</td>
          <td>{{ $item->comp_anec_date }}</td>
          <td>{{ $item->comp_anec_time }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <script>
    const menuLinks = document.querySelectorAll('.sidebar a');
    const activeLink = localStorage.getItem('activeMenu');
    if (activeLink) menuLinks.forEach(link => { if(link.href===activeLink) link.classList.add('active'); });
    menuLinks.forEach(link=>{link.addEventListener('click', function(){ menuLinks.forEach(i=>i.classList.remove('active')); this.classList.add('active'); if(!this.href.includes('profile.settings')) localStorage.setItem('activeMenu', this.href); });});
    function logout(){ alert('Logging out...'); }
  </script>
</body>
</html>
