@extends('adviser.layout')

@section('content')
<div class="main-container">

<style>

.toolbar {
    display: flex;             /* make it a flex container */
    justify-content: center;   /* horizontal center */
    align-items: center;       /* vertical center */
    height: 80px;              /* adjust as needed */
}

.toolbar h3 {
    font-size: 2rem;
    color: #4b0000;
    font-weight: 600;
    text-align: center;        /* centers text inside h3 if needed */
}
</style>

    </style>
  <!-- Toolbar -->
  <div class="toolbar">
        <h3>Dashboard Overview</h3>
  </div>

<br>
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
    <h2 style="margin:20px 0; margin-left:20px; font-size:18px; color:#111;">Upcoming Appointments</h2>
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
    .then(response => { if(response.ok){ window.location.href = "{{ route('login') }}"; } })
    .catch(error => console.error('Logout failed:', error));
  }

  // Info modal logic
  const modal = document.getElementById("infoModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalBody = document.getElementById("modalBody");
  const closeBtn = document.querySelector(".close");
  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (event) => { if(event.target === modal) modal.style.display = "none"; }


// Logout - Now using modal notification
  function logout() {
    showNotification('🚪 Logout', 'Are you sure you want to logout?', 'confirm', {
      yesText: 'Yes, Logout',
      noText: 'Cancel',
      onYes: () => {
        fetch("{{ route('adviser.logout') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        })
        .then(response => {
          if(response.ok){
            showNotification('👋 Goodbye', 'Logging out...', 'success', {
              yesText: 'OK',
              noText: null,
              onYes: () => {
                window.location.href = "{{ route('login') }}";
              }
            });
          }
        })
        .catch(error => {
          console.error('Logout failed:', error);
          showNotification('❌ Error', 'Logout failed. Please try again.', 'danger', {
            yesText: 'OK',
            noText: null,
            onYes: () => {
              document.getElementById('notificationModal').style.display = 'none';
            }
          });
        });
      },
      onNo: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
  }
</script>

@endsection
