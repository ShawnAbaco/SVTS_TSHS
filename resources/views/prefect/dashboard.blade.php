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

    .sidebar {
      width: 230px;
background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);


background-repeat: no-repeat;
background-attachment: fixed;
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 15px rgba(0,0,0,0.5);
      overflow-y: auto;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 22px;
      letter-spacing: 1px;
      color: #ffffff;
      text-transform: uppercase;
      border-bottom: 2px solid rgba(255, 255, 255, 0.15);
      padding-bottom: 10px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px 14px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 10px;
      font-size: 15px;
      color:rgb(255, 255, 255);
      transition: background 0.3s, transform 0.2s;
    }

    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
      font-size: 16px;
    }

    .sidebar ul li:hover {
      background: #2d3f55;
      transform: translateX(5px);
      color: #fff;
    }

    .sidebar ul li:hover i {
      color: #00e0ff;
    }

    .sidebar ul li.active {
      background: #00aaff;
      color: #fff;
      border-left: 4px solid #ffffff;
    }

    .sidebar ul li.active i {
      color: #fff;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }

    .section-title {
      margin: 20px 10px 8px;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: bold;
      color: rgba(255, 255, 255, 0.6);
      letter-spacing: 1px;
    }

    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 25px;
    }

    .dropdown-container li {
      padding: 10px;
      font-size: 14px;
      border-radius: 8px;
      color: #ddd;
    }

    .dropdown-container li:hover {
      background: #3a4c66;
      color: #fff;
    }

    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.25);
      border-radius: 3px;
    }

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

   .user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
  }

  .user-info img {
    width: 70px;   /* bigger size */
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #003366; /* adds border for emphasis */
    cursor: pointer;
  }

  .user-info span {
    font-size: 20px;  /* bigger name text */
    font-weight: bold;
    color: #111;
  }

  .user-info:hover {
    opacity: 0.8;
  }

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

    .card:nth-child(1) { background-color:rgb(0, 145, 255); }
    .card:nth-child(2) { background-color:rgb(246, 3, 3); }
    .card:nth-child(3) { background-color:rgb(0, 255, 60); }

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

    .status {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      color: #fff;
      font-weight: bold;
    }

    .pending { background-color:rgb(0, 0, 0); }
    .resolved { background-color:rgb(1, 255, 60); }
    .escalated { background-color:rgb(255, 0, 25); }

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
      background-color:rgb(0, 0, 0);
    }

    #violationChart {
      max-width: 220px;
      max-height: 220px;
      margin: 0 auto;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 10000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 40%;
      min-width: 300px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .modal-content h2 {
      margin-bottom: 15px;
    }

    .modal-content p {
      font-size: 14px;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #000;
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

      </div>
      <div class="user-info">
        <img id="profileImage" src="https://i.pravatar.cc/35" alt="Profile" onclick="changeProfileImage()" />
        <span onclick="changeProfileName()">Angel</span>
        <input type="file" id="imageInput" accept="image/*" style="display:none" />
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="cards">
      <div class="card">
        <div>
          <h3>Total Students</h3>
          <p>1,248</p>
        </div>
        <i class="fas fa-user-graduate"></i>
      </div>
      <div class="card">
        <div>
          <h3>Violations</h3>
          <p>42</p>
        </div>
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="card">
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
          <a href="#">View All</a>
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
            <tr>
              <td>Alex Johnson</td>
              <td>Attendance</td>
              <td>Jan 15, 2025</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
              <td>Sarah Miller</td>
              <td>Behavior</td>
              <td>Jan 12, 2025</td>
              <td><span class="status resolved">Resolved</span></td>
            </tr>
            <tr>
              <td>Mike Thompson</td>
              <td>Dress Code</td>
              <td>Jan 10, 2025</td>
              <td><span class="status escalated">Escalated</span></td>
            </tr>
            <tr>
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
      <div class="appt-card">
        <h4>Violation Discussion</h4>
        <p class="time">Jan 18, 2025 - 10:00 AM</p>
        <p>Alex Johnson <br><small>with Mr. Peterson</small></p>
        <span class="status pending">Scheduled</span>
      </div>
      <div class="appt-card">
        <h4>Complaint Review</h4>
        <p class="time">Jan 19, 2025 - 2:30 PM</p>
        <p>Sarah Miller <br><small>with Mrs. Johnson</small></p>
        <span class="status pending">Scheduled</span>
      </div>
      <div class="appt-card">
        <h4>Parent Conference</h4>
        <p class="time">Jan 20, 2025 - 11:15 AM</p>
        <p>Mike Thompson <br><small>with Principal Davis</small></p>
        <span class="status pending">Pending</span>
      </div>
    </div>
  </div>



  <!-- Info Modal -->
  <div id="infoModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTitle">Title</h2>
      <div id="modalBody">Details go here...</div>
    </div>
  </div>

<script>
  // Chart.js
  const ctx = document.getElementById('violationChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Attendance', 'Behavior', 'Dress Code', 'Other'],
      datasets: [{
        data: [40, 25, 20, 15],
        backgroundColor: ['#00ff00', '#ff0000', '#0000ff', '#ffff00'],
        borderWidth: 1
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  // Dropdown
  const dropdowns = document.querySelectorAll('.dropdown-btn');
  dropdowns.forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) {
          otherBtn.classList.remove('active');
          otherContainer.style.display = 'none';
        }
      });
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';
    });
  });

  // Logout
  function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (confirmLogout) window.location.href = "{{ route('prefect.login') }}";
  }

  // Profile image & name
  function changeProfileImage() { document.getElementById('imageInput').click(); }
  document.getElementById('imageInput').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
      const reader = new FileReader();
      reader.onload = function(ev){ document.getElementById('profileImage').src = ev.target.result; }
      reader.readAsDataURL(file);
    }
  });
  function changeProfileName() {
    const newName = prompt("Enter new name:");
    if(newName) document.querySelector('.user-info span').innerText = newName;
  }

  // Welcome modal
  window.addEventListener('DOMContentLoaded', () => {
    const welcomeModal = document.getElementById('welcomeModal');
    if(welcomeModal){
      welcomeModal.style.display = 'block';
      setTimeout(() => { welcomeModal.style.display = 'none'; }, 3000);
    }
  });

  // Info modal logic
  const modal = document.getElementById("infoModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalBody = document.getElementById("modalBody");
  const closeBtn = document.querySelector(".close");

  function openModal(title, content){
    modalTitle.innerText = title;
    modalBody.innerHTML = content;
    modal.style.display = "block";
  }

  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (event) => { if(event.target === modal) modal.style.display = "none"; }

  // Attach modal to cards, appointments, table rows
  document.querySelectorAll('.card, .appt-card, .table tr').forEach(el => {
    el.addEventListener('click', (e) => {
      const type = el.querySelector('h3, h4, td')?.innerText || 'Details';
      let content = '';

      if(el.classList.contains('appt-card')){
        content = `
          <p><strong>Title:</strong> ${el.querySelector('h4').innerText}</p>
          <p><strong>Time:</strong> ${el.querySelector('.time').innerText}</p>
          <p>${el.querySelector('p').innerHTML}</p>
          <p><strong>Status:</strong> ${el.querySelector('.status').innerText}</p>
        `;
      } else if(el.classList.contains('card') && el.querySelector('canvas')){
        content = 'Chart content can be detailed here.';
      } else if(el.tagName === 'TR'){
        const cells = el.querySelectorAll('td');
        content = `
          <p><strong>Student:</strong> ${cells[0].innerText}</p>
          <p><strong>Violation Type:</strong> ${cells[1].innerText}</p>
          <p><strong>Date:</strong> ${cells[2].innerText}</p>
          <p><strong>Status:</strong> ${cells[3].innerText}</p>
        `;
      } else {
        content = el.innerHTML;
      }

      openModal(type, content);
    });
  });
</script>
</body>
</html>
