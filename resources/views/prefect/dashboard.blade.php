<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Prefect Dashboard - Bold Text</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
      font-weight: bold;
      transition: all 0.2s ease-in-out;
    }

    body {
      display: flex;
      background: #f9f9f9;
      color: #111;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: linear-gradient(45deg, #800000, #2E2E2E);
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
      overflow-y: auto;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 20px;
      letter-spacing: 1px;
      color:rgb(255, 255, 255);
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      padding: 12px 10px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 8px;
      font-size: 14px;
      color: #fff;
      transition: 0.3s;
      position: relative;
    }
    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
    }
    .sidebar ul li:hover {
      background:rgb(0, 247, 58);
      color: #111;
    }
    .sidebar ul li:hover i {
      color: #111;
    }
    .sidebar ul li.active {
      background:rgb(11, 255, 68);
      color: #111;
    }
    .sidebar ul li.active i {
      color: #111;
    }
    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }
    .section-title {
      margin: 15px 10px 5px;
      font-size: 11px;
      text-transform: uppercase;
      color: #bbb;
    }

    /* Dropdown */
    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 20px;
      transition: max-height 0.3s ease;
    }
    .dropdown-container li {
      padding: 10px;
      font-size: 13px;
      border-radius: 6px;
      cursor: pointer;
    }
    .dropdown-container li:hover {
      background:rgb(255, 255, 255);
      color: #111;
    }
    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }
    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background:rgb(255, 255, 255);
      border-radius: 3px;
    }

    /* Main Content */
    .main {
      margin-left: 220px;
      padding: 20px;
      width: calc(100% - 220px);
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 20px;
    }
    .topbar h1 {
      font-size: 22px;
      margin-bottom: 4px;
    }
    .topbar p {
      font-size: 13px;
      color: #555;
    }

    /* User Info (Profile) */
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }
    .user-info img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
      cursor: pointer;
    }
    .user-info span {
      font-weight: bold;
      color: #111;
    }
    .user-info:hover {
      opacity: 0.7;
    }

    /* Stats Cards */
    .cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }
    .card {
      border-radius: 8px;
      padding: 15px;
      border: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.02);
    }
    .card h3 {
      font-size: 12px;
      color: #000;
    }
    .card p {
      font-size: 20px;
      margin: 4px 0;
      color: #000;
    }
    .card i {
      font-size: 20px;
      color: #000;
    }
    .card:nth-child(1) { background-color: #cce5ff; } /* Blue */
    .card:nth-child(2) { background-color: #f8d7da; } /* Red */
    .card:nth-child(3) { background-color: #d4edda; } /* Green */

    /* Grid (Chart + Table) */
    .grid {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    .grid .card {
      flex-direction: column;
      align-items: flex-start;
      background: #fff;
      border: 1px solid #ddd;
    }
    .grid .card-header {
      width: 100%;
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .grid .card-header h3 {
      font-size: 14px;
      color: #111;
    }
    .grid .card-header a {
      font-size: 12px;
      color: #000;
      text-decoration: none;
    }
    .grid .card-header a:hover {
      text-decoration: underline;
    }

    /* Table */
    .table {
      width: 100%;
      border-collapse: collapse;
    }
    .table th, .table td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ccc;
      font-size: 13px;
    }
    .table tr:hover {
      background: #f2f2f2;
      cursor: pointer;
    }

    /* Solid Status Badges */
    .status {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      color: #fff;
      font-weight: bold;
    }
    .pending { background-color: #fd7e14; }   /* Orange */
    .resolved { background-color: #28a745; }  /* Green */
    .escalated { background-color: #dc3545; } /* Red */

    /* Appointments */
    .appointments {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
    }
    .appt-card {
      background: #fff;
      border-radius: 8px;
      padding: 12px;
      border: 1px solid #ddd;
      font-size: 13px;
      cursor: pointer;
    }
    .appt-card:hover {
      background: #f0f0f0;
      transform: scale(1.01);
    }
    .appt-card h4 {
      font-size: 13px;
      margin-bottom: 4px;
      color: #111;
    }
    .appt-card .time {
      font-size: 12px;
      color: #555;
      margin-bottom: 8px;
    }
    .appt-card .status {
      float: right;
      font-size: 11px;
      padding: 2px 6px;
      border-radius: 10px;
      font-weight: bold;
      color: #fff;
      background-color: #fd7e14;
    }

    /* Chart */
    #violationChart {
      max-width: 220px;
      max-height: 220px;
      margin: 0 auto;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>PREFECT DASHBOARD</h2>
    <ul>
      <div class="section-title">Main</div>
      <li class="active"><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
      <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
      <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

      <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
      <ul class="dropdown-container">
        <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
        <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
        <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
      </ul>

      <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
      <ul class="dropdown-container">
        <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
        <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
        <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
      </ul>

      <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="topbar">
      <div>
        <h1>Dashboard</h1>
        <p>Welcome back, Admin</p>
      </div>
      <div class="user-info">
        <img id="profileImage" src="https://i.pravatar.cc/35" alt="Profile" onclick="changeProfileImage()" />
        <span onclick="changeProfileName()">Angel</span>
        <input type="file" id="imageInput" accept="image/*" style="display:none" />
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="cards">
      <div class="card" onclick="menuClick('Total Students')">
        <div>
          <h3>Total Students</h3>
          <p>1,248</p>
        </div>
        <i class="fas fa-user-graduate"></i>
      </div>
      <div class="card" onclick="menuClick('Violations')">
        <div>
          <h3>Violations</h3>
          <p>42</p>
        </div>
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="card" onclick="menuClick('Complaints')">
        <div>
          <h3>Complaints</h3>
          <p>18</p>
        </div>
        <i class="fas fa-comments"></i>
      </div>
    </div>

    <!-- Chart + Table -->
    <div class="grid">
      <div class="card">
        <div class="card-header">
          <h3>Violation Types</h3>
        </div>
        <canvas id="violationChart"></canvas>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Recent Violations</h3>
          <a href="#" onclick="menuClick('View All Violations')">View All</a>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Student</th>
              <th>Violation Type</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr onclick="rowClick('Alex Johnson')">
              <td>Alex Johnson</td>
              <td>Attendance</td>
              <td>Jan 15, 2025</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr onclick="rowClick('Sarah Miller')">
              <td>Sarah Miller</td>
              <td>Behavior</td>
              <td>Jan 12, 2025</td>
              <td><span class="status resolved">Resolved</span></td>
            </tr>
            <tr onclick="rowClick('Mike Thompson')">
              <td>Mike Thompson</td>
              <td>Dress Code</td>
              <td>Jan 10, 2025</td>
              <td><span class="status escalated">Escalated</span></td>
            </tr>
            <tr onclick="rowClick('Emily Wilson')">
              <td>Emily Wilson</td>
              <td>Attendance</td>
              <td>Jan 8, 2025</td>
              <td><span class="status resolved">Resolved</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Appointments -->
    <h3 style="margin-bottom: 10px;">Upcoming Appointments</h3>
    <div class="appointments">
      <div class="appt-card" onclick="menuClick('Violation Discussion')">
        <h4>Violation Discussion</h4>
        <p class="time">Jan 18, 2025 - 10:00 AM</p>
        <p>Alex Johnson <br><small>with Mr. Peterson</small></p>
        <span class="status pending">Scheduled</span>
      </div>
      <div class="appt-card" onclick="menuClick('Complaint Review')">
        <h4>Complaint Review</h4>
        <p class="time">Jan 19, 2025 - 2:30 PM</p>
        <p>Sarah Miller <br><small>with Mrs. Johnson</small></p>
        <span class="status pending">Scheduled</span>
      </div>
      <div class="appt-card" onclick="menuClick('Parent Conference')">
        <h4>Parent Conference</h4>
        <p class="time">Jan 20, 2025 - 11:15 AM</p>
        <p>Mike Thompson <br><small>with Principal Davis</small></p>
        <span class="status pending">Pending</span>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script>
    // Chart.js
    const ctx = document.getElementById('violationChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Attendance', 'Behavior', 'Dress Code', 'Other'],
        datasets: [{
          data: [40, 25, 20, 15],
          backgroundColor: ['#ff4d4d', '#4d79ff', '#28a745', '#ffc107'],
          borderWidth: 1
        }]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown-btn');
    dropdowns.forEach(btn => {
      btn.addEventListener('click', () => {
        btn.classList.toggle('active');
        const container = btn.nextElementSibling;
        if (container.style.display === "block") {
          container.style.display = "none";
        } else {
          container.style.display = "block";
        }
      });
    });

    // Interactivity functions
    function menuClick(name) { alert("Clicked: " + name); }
    function rowClick(student) { alert("Opening details for " + student); }
    function logout() { alert("Logging out..."); }

    // Change profile image
    function changeProfileImage() {
      document.getElementById('imageInput').click();
    }

    document.getElementById('imageInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if(file){
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profileImage').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    // Change profile name
    function changeProfileName() {
      const newName = prompt("Enter new name:");
      if(newName) document.querySelector('.user-info span').innerText = newName;
    }
  </script>
</body>
</html>
