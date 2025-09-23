<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Prefect Dashboard</title>
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
      background: linear-gradient(135deg, #002200, #004400, #006600, #008800);
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

    .sidebar ul { list-style: none; }
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

    .sidebar ul li:hover i { color: #00e0ff; }
    .sidebar ul li.active {
      background: #00aaff;
      color: #fff;
      border-left: 4px solid #ffffff;
    }

    .sidebar ul li.active i { color: #fff; }
    .sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
    .section-title {
      margin: 20px 10px 8px;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: bold;
      color: rgba(255, 255, 255, 0.6);
      letter-spacing: 1px;
    }
    .dropdown-container { display: none; list-style: none; padding-left: 25px; }
    .dropdown-container li {
      padding: 10px;
      font-size: 14px;
      border-radius: 8px;
      color: #ddd;
    }
    .dropdown-container li:hover { background: #3a4c66; color: #fff; }
    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }
    .dropdown-btn.active .arrow { transform: rotate(180deg); }
    .sidebar::-webkit-scrollbar { width: 6px; }
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

    .topbar h1 { font-size: 22px; margin-bottom: 4px; }
    .topbar p { font-size: 13px; color: #555; }

    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
      cursor: pointer;
    }

    .user-info img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #003366;
      cursor: pointer;
    }

    .user-info span {
      font-size: 20px;
      font-weight: bold;
      color: #111;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }

    .card {
      border-radius: 8px;
      padding: 20px;
      border: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .card:hover { transform: scale(1.02); }

    .card h3 { font-size: 14px; color: #fff; }
    .card p { font-size: 22px; margin: 6px 0; color: #fff; }
    .card i { font-size: 24px; color: #fff; }

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
      height: 280px;
      padding: 15px;
    }

    .grid .card-header {
      width: 100%;
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .grid .card-header h3 { font-size: 14px; color: #111; }
    .grid .card-header a {
      font-size: 12px;
      color: #000;
      text-decoration: none;
    }
    .grid .card-header a:hover { text-decoration: underline; }

    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ccc;
      font-size: 13px;
    }
    .table tr:hover { background: #f2f2f2; cursor: pointer; }

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

    #violationChart { max-width: 220px; max-height: 220px; margin: 0 auto; }

    /* Upcoming Appointments */
    .cards.upcoming {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      margin-top: 20px;
      margin-bottom: 40px;
    }

    .cards.upcoming .card {
      height: 150px;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      padding: 15px;
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

    .modal-content h2 { margin-bottom: 15px; }
    .modal-content p { font-size: 14px; }

    .close {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover { color: #000; }

    /* Logo */
    .sidebar img {
      width: 150px;
      height: auto;
      margin: 0 auto 0.5rem;
      display: block;
      transition: transform 0.3s ease;
      image-rendering: -webkit-optimize-contrast;
      image-rendering: crisp-edges;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
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
          <h3>Recent Violations & Complaints</h3>
          <a href="#">View All</a>
        </div>
        <canvas id="recentChart" style="width:100%; max-width: 100%; height: 250px;"></canvas>
      </div>
    </div>

    <!-- Upcoming Appointments BELOW charts -->
    <h2 style="margin:20px 0; font-size:18px; color:#111;">Upcoming Appointments</h2>
    <div class="cards upcoming">
      <div class="card" style="background-color:#00aaff;">
        <div><h3>John Doe</h3><p>Sep 25, 10:00 AM</p></div>
        <i class="fas fa-calendar-alt"></i>
      </div>
      <div class="card" style="background-color:#ff9900;">
        <div><h3>Jane Smith</h3><p>Sep 26, 1:30 PM</p></div>
        <i class="fas fa-calendar-alt"></i>
      </div>
      <div class="card" style="background-color:#ff3366;">
        <div><h3>Michael Lee</h3><p>Sep 27, 9:00 AM</p></div>
        <i class="fas fa-calendar-alt"></i>
      </div>
      <div class="card" style="background-color:#33cc33;">
        <div><h3>Sarah Brown</h3><p>Sep 28, 11:00 AM</p></div>
        <i class="fas fa-calendar-alt"></i>
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
  // Chart.js Doughnut
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

  // Recent Violations Line Chart
  const recentCtx = document.getElementById('recentChart').getContext('2d');
  new Chart(recentCtx, {
    type: 'line',
    data: {
        labels: ['Jan 1','Jan 5','Jan 10','Jan 15','Jan 20','Jan 25','Jan 30'],
        datasets: [
            { label: 'Violations', data: [5,8,6,10,7,9,12], borderColor: '#FF0000', backgroundColor: 'rgba(255,0,0,0.2)', fill: true, tension: 0.3 },
            { label: 'Complaints', data: [2,3,4,3,5,4,6], borderColor: '#0000FF', backgroundColor: 'rgba(0,0,255,0.2)', fill: true, tension: 0.3 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
            x: { display: true, title: { display: true, text: 'Date' } },
            y: { display: true, title: { display: true, text: 'Count' }, beginAtZero: true }
        }
    }
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

  // Logout
  function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (!confirmLogout) return;
    fetch("{{ route('adviser.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(response => { if(response.ok){ window.location.href = "{{ route('auth.login') }}"; } })
    .catch(error => console.error('Logout failed:', error));
  }

  // Info modal logic
  const modal = document.getElementById("infoModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalBody = document.getElementById("modalBody");
  const closeBtn = document.querySelector(".close");
  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (event) => { if(event.target === modal) modal.style.display = "none"; }

</script>
</body>
</html>
