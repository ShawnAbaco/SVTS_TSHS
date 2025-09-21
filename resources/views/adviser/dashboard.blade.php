<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adviser Dashboard</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="{{ asset('css/adviser/dashboard.css') }}">

</head>
<body>

<nav class="sidebar">
  <div>
    <img src="/images/Logo.png" alt="Logo">
    <p>ADVISER</p>
  </div>
  <ul>
    <li><a href="{{ route('adviser.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
</li>  </ul>
</nav>


<main class="main-content">

  <!-- Main topbar -->
  <div class="main-topbar">
    <h2 class="dashboard-title">Dashboard Overview</h2>
     <div class="user-info" onclick="toggleProfileDropdown()">
        <img id="profileImage" src="https://i.pravatar.cc/70" alt="Profile" />
        <span>junald gwapo</span>
        <ul class="profile-dropdown" id="profileDropdown">
          <li><a href="#">Change Profile</a></li>
          <li><a href="#">Change Password</a></li>
          <li><a href="#">Change Email</a></li>
        </ul>
    </div>
  </div>

  <section id="overview">
    <div class="dashboard-overview">
      <div class="overview-card total-students">
        <h3>Total Students</h3>
        <p>1,247</p>
        <small>+12% from last month</small>
      </div>
      <div class="overview-card active-violations">
        <h3>Active Violations</h3>
        <p>23</p>
        <small>+5% from last week</small>
      </div>
      <div class="overview-card pending-complaints">
        <h3>Pending Complaints</h3>
        <p>8</p>
        <small>-3% from last week</small>
      </div>
      <div class="overview-card appointments-today">
        <h3>Appointments Today</h3>
        <p>12</p>
        <small>+8% from yesterday</small>
      </div>
    </div>

    <div class="chart-section">
      <div class="chart-card">
        <h4>Violation Types Distribution</h4>
        <canvas id="pieChart"></canvas>
      </div>
      <div class="chart-card">
        <h4>Monthly Violations Trend</h4>
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <div class="recent-activities">
      <h4>Recent Activities</h4>
      <div class="activity"><i class="fas fa-exclamation-triangle"></i><p>New violation reported - Student John Doe - Behavioral violation (2 hours ago)</p></div>
      <div class="activity"><i class="fas fa-comments"></i><p>New complaint filed - Parent complaint about cafeteria service (4 hours ago)</p></div>
      <div class="activity"><i class="fas fa-calendar-check"></i><p>Appointment scheduled - Meeting with Sarah Johnson's parents (6 hours ago)</p></div>
    </div>
  </section>
</main>

<script>
// Sidebar dropdown
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    const container = btn.nextElementSibling;
    container.classList.toggle('show');
    btn.querySelector('.fa-caret-down').style.transform = container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    document.querySelectorAll('.dropdown-container').forEach(dc => {
      if(dc !== container) dc.classList.remove('show');
    });
  });
});

// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
  link.addEventListener('click', function(){
    document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
  });
});

// Profile dropdown toggle
function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown if clicked outside
document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('profileDropdown');
  const userInfo = document.querySelector('.user-info');
  if (!userInfo.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

// Logout
function logout() {
  if(confirm('Are you sure you want to log out?')) alert('Logged out!'); // placeholder
}

// Pie Chart
new Chart(document.getElementById('pieChart').getContext('2d'), {
  type:'doughnut',
  data:{
    labels:['overall','Complaints','Violations'],
    datasets:[{
      data:[12,8,3],
      backgroundColor:['#FFD700','#1E3A8A','#EF4444'],
      borderColor:'#fff',
      borderWidth:2,
      hoverOffset:10
    }]
  },
  options:{
    responsive:true,
    plugins:{ legend:{ position:'bottom', labels:{ font:{ size:12 }, padding:10 } } },
    cutout:'40%'
  }
});

// Bar Chart
new Chart(document.getElementById('barChart').getContext('2d'), {
  type:'bar',
  data:{
    labels:['Jan','Feb','Mar','Apr','May','Jun'],
    datasets:[{
      label:'Monthly Violations',
      data:[15,18,12,20,25,32],
      backgroundColor:'#000',
      borderRadius:3,
      barPercentage:0.45
    }]
  },
  options:{
    responsive:true,
    plugins:{ legend:{ display:false } },
    scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true, ticks:{ stepSize:5 } } }
  }
});
</script>
</body>
</html>
