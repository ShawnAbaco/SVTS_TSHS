<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adviser Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* --- Sidebar --- */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100%;
  background: linear-gradient(180deg, rgb(48, 48, 50));
  font-family: "Segoe UI", Tahoma, sans-serif;
  z-index: 1000;
  overflow-y: auto;
  transition: all 0.3s ease;
  color: #ffffff;
  font-weight: bold;
}

/* Sidebar scroll */
.sidebar::-webkit-scrollbar {
  width: 8px;
}
.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.25);
  border-radius: 4px;
}
.sidebar::-webkit-scrollbar-track {
  background-color: rgba(255, 255, 255, 0.05);
}

/* Logo */
.sidebar img {
  width: 180px;
  height: auto;
  margin: 0 auto 0.1rem;
  display: block;
  transition: transform 0.3s ease;
}

.sidebar p {
  font-size: 0.9rem;
  font-weight: bold;
  margin: 0 0 1rem;
  color: #ffffff;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  text-align: center;
}

/* Sidebar Links */
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  color: #ffffff;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: bold;
  border-left: 4px solid transparent;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.sidebar ul li a i {
  font-size: 1.1rem;
  min-width: 22px;
  text-align: center;
  color: #ffffff;
  transition: color 0.3s ease;
  font-weight: bold;
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: rgba(255,255,255,0.12);
  border-left-color: #FFD700;
  color: #ffffff !important;
}

/* Dropdown */
.dropdown-container {
  max-height: 0;
  overflow: hidden;
  background-color: rgba(255,255,255,0.05);
  transition: max-height 0.4s ease, padding 0.4s ease;
  border-left: 2px solid rgba(255,255,255,0.1);
  border-radius: 0 8px 8px 0;
}

.dropdown-container.show {
  max-height: 400px;
  padding-left: 12px;
}

.dropdown-container li a {
  font-size: 0.85rem;
  padding: 10px 20px;
  color: #ffffff;
  font-weight: bold;
}

.dropdown-container li a:hover {
  background-color: rgba(255,255,255,0.12);
  color: #ffffff;
}

.dropdown-btn .fa-caret-down {
  margin-left: auto;
  transition: transform 0.3s ease;
  color: #ffffff;
  font-weight: bold;
}



/* --- Main Content --- */
.main-content {
    margin-left: 240px;
    padding: 1rem;
    transition: margin-left 0.3s ease;
}

/* Cards */
.dashboard-overview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-bottom: 1.2rem;
}

.overview-card {
    flex: 1;
    min-width: 130px;
    padding: 0.8rem;
    border-radius: 8px;
    color: #fff;
    font-weight: 700;
}

.overview-card h3 { font-size: 0.85rem; margin: 0; }
.overview-card p { font-size: 1rem; margin: 0.3rem 0 0; }
.overview-card small { font-weight: 400; }

.total-students { background-color: #F59E0B; }
.active-violations { background-color: #0062FF; }
.pending-complaints { background-color: #EF4444; }
.appointments-today { background-color: #111; }

/* Charts */
.chart-section { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.2rem; }
.chart-card {
    flex: 1;
    min-width: 250px;
    padding: 0.8rem;
    border-radius: 8px;
    background-color: #cbcbcb;
    color: #111;
}
.chart-card h4 { text-align: center; margin-bottom: 0.6rem; font-size: 0.95rem; }
canvas { max-height: 180px; }

/* Recent Activities */
.recent-activities {
    padding: 0.8rem;
    border-radius: 8px;
    background-color: #EF4444;
    color: #fff;
}
.recent-activities h4 { margin-bottom: 0.6rem; font-size: 0.95rem; }
.activity { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.3rem; }
.activity p { margin: 0; font-size: 0.85rem; }
</style>
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
        <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<main class="main-content">
    <section id="overview">
        <h2 style="font-size:1.1rem; margin-bottom:0.8rem; color:#111;">Dashboard Overview</h2>

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
// Dropdown functionality
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

// Logout
function logout() {
    if(confirm('Are you sure you want to log out?')) window.location.href='/adviser/login';
}

// Pie Chart
new Chart(document.getElementById('pieChart').getContext('2d'), {
    type:'doughnut',
    data:{
        labels:['Behavioral','Academic','Attendance'],
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
