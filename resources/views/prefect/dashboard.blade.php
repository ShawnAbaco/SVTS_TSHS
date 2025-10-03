@extends('prefect.layout')

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

  <!-- 🔔 Notification Modal -->
  <div class="modal" id="notificationModal">
    <div class="modal-content notification-modal-content">
      <div class="modal-header notification-modal-header">
        <div class="notification-header-content">
          <span id="notificationIcon">🔔</span>
          <span id="notificationTitle">Notification</span>
        </div>
      </div>
      <div class="modal-body notification-modal-body" id="notificationBody">
        <!-- Content filled dynamically via JS -->
      </div>
      <div class="modal-footer notification-modal-footer">
        <div class="notification-buttons-container">
          <button class="btn-primary" id="notificationYesBtn">Yes</button>
          <button class="btn-secondary" id="notificationNoBtn">No</button>
          <button class="btn-close" id="notificationCloseBtn">Close</button>
        </div>
      </div>
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

  // Profile image & name - Now using modal notifications
  function changeProfileImage() {
    document.getElementById('imageInput').click();
  }

  document.getElementById('imageInput').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
      const reader = new FileReader();
      reader.onload = function(ev){
        document.getElementById('profileImage').src = ev.target.result;
        showNotification('✅ Profile Updated', 'Profile image changed successfully!', 'success', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
          }
        });
      }
      reader.readAsDataURL(file);
    }
  });

  function changeProfileName() {
    showNotification('✏️ Change Profile Name', 'Enter your new name:', 'info', {
      yesText: 'Change Name',
      noText: 'Cancel',
      onYes: () => {
        // Simulate name change
        setTimeout(() => {
          showNotification('✅ Profile Updated', 'Profile name changed successfully!', 'success', {
            yesText: 'OK',
            noText: null,
            onYes: () => {
              document.getElementById('notificationModal').style.display = 'none';
            }
          });
        }, 500);
      },
      onNo: () => {
        document.getElementById('notificationModal').style.display = 'none';
      }
    });
  }


  // Info modal logic
  const modal = document.getElementById("infoModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalBody = document.getElementById("modalBody");
  const closeBtn = document.querySelector(".close");
  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (event) => { if(event.target === modal) modal.style.display = "none"; }

  // Sidebar active state
  document.addEventListener("DOMContentLoaded", () => {
    const sidebarItems = document.querySelectorAll('.sidebar ul li');

    sidebarItems.forEach(item => {
      item.addEventListener('click', () => {
        // Remove 'active' from all
        sidebarItems.forEach(i => i.classList.remove('active'));

        // Add 'active' to the clicked item
        item.classList.add('active');

        // Show notification for navigation
        const pageName = item.textContent.trim();
        showNotification('📄 Navigation', `Navigating to ${pageName}`, 'info', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
          }
        });
      });
    });
  });

  // ================= NOTIFICATION MODAL FUNCTIONALITY =================

  // Notification modal function
  function showNotification(title, message, type = 'info', options = {}) {
    const modal = document.getElementById('notificationModal');
    const notificationTitle = document.getElementById('notificationTitle');
    const notificationBody = document.getElementById('notificationBody');
    const notificationIcon = document.getElementById('notificationIcon');
    const yesBtn = document.getElementById('notificationYesBtn');
    const noBtn = document.getElementById('notificationNoBtn');
    const closeBtn = document.getElementById('notificationCloseBtn');

    // Set title and message
    notificationTitle.textContent = title;
    notificationBody.textContent = message;

    // Set icon based on type
    let icon = '🔔';
    if (type === 'success') icon = '✅';
    else if (type === 'warning') icon = '⚠️';
    else if (type === 'danger') icon = '❌';
    else if (type === 'confirm') icon = '❓';
    notificationIcon.textContent = icon;

    // Configure buttons
    yesBtn.textContent = options.yesText || 'Yes';
    yesBtn.onclick = options.onYes || (() => modal.style.display = 'none');

    if (options.noText) {
      noBtn.textContent = options.noText;
      noBtn.style.display = 'inline-block';
      noBtn.onclick = options.onNo || (() => modal.style.display = 'none');
    } else {
      noBtn.style.display = 'none';
    }

    closeBtn.onclick = () => modal.style.display = 'none';

    // Show the modal
    modal.style.display = 'flex';
  }

  // Close notification modal with close button
  document.getElementById('notificationCloseBtn').addEventListener('click', () => {
    document.getElementById('notificationModal').style.display = 'none';
  });

  // Close modals when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  });

  // Demo notifications for dashboard interactions
  document.addEventListener('DOMContentLoaded', function() {
    // Show welcome notification
    setTimeout(() => {
      showNotification('👋 Welcome Back', 'Welcome to your Dashboard Overview!', 'info', {
        yesText: 'Get Started',
        noText: null,
        onYes: () => {
          document.getElementById('notificationModal').style.display = 'none';
        }
      });
    }, 1000);

    // Add click events to dashboard cards for demo
    const dashboardCards = document.querySelectorAll('.cards .card');
    dashboardCards.forEach(card => {
      card.addEventListener('click', function() {
        const cardTitle = this.querySelector('h3').textContent;
        const cardValue = this.querySelector('p').textContent;

        showNotification('📊 Dashboard Info', `${cardTitle}: ${cardValue}`, 'info', {
          yesText: 'OK',
          noText: null,
          onYes: () => {
            document.getElementById('notificationModal').style.display = 'none';
          }
        });
      });
    });

    // Appointment card interactions
    const appointmentCards = document.querySelectorAll('.upcoming .card');
    appointmentCards.forEach(card => {
      card.addEventListener('click', function() {
        const studentName = this.querySelector('h3').textContent;
        const appointmentTime = this.querySelector('p').textContent;

        showNotification('📅 Appointment Details', `Student: ${studentName}\nTime: ${appointmentTime}`, 'info', {
          yesText: 'View Details',
          noText: 'Close',
          onYes: () => {
            showNotification('📅 Appointment', `Opening details for ${studentName}...`, 'success', {
              yesText: 'OK',
              noText: null,
              onYes: () => {
                document.getElementById('notificationModal').style.display = 'none';
              }
            });
          },
          onNo: () => {
            document.getElementById('notificationModal').style.display = 'none';
          }
        });
      });
    });
  });

  // Logout - Now using modal notification
  function logout() {
    showNotification('🚪 Logout', 'Are you sure you want to logout?', 'confirm', {
      yesText: 'Yes, Logout',
      noText: 'Cancel',
      onYes: () => {
        fetch("{{ route('prefect.logout') }}", {
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
