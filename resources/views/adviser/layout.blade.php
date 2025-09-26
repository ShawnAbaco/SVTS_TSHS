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

  {{-- Sidebar --}}
  <nav class="sidebar">
    <div>
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
      </li>
    </ul>
  </nav>

  {{-- Main content wrapper --}}
  <main class="main-content">

    {{-- Header / Topbar --}}
    <div class="main-topbar">
      <h2 class="dashboard-title">@yield('title', 'Dashboard Overview')</h2>
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

    {{-- Page content --}}
    @yield('content')

  </main>

  {{-- Scripts --}}
  <script>
  // Sidebar dropdown
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const container = btn.nextElementSibling;
      container.classList.toggle('show');
      btn.querySelector('.fa-caret-down').style.transform = container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
      document.querySelectorAll('.dropdown-container').forEach(dc => {
        if (dc !== container) dc.classList.remove('show');
      });
    });
  });

  // Active link
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar a').forEach(link => {
    if (link.href.includes(currentPath)) {
      link.classList.add('active');
    }
  });

  // Profile dropdown
  function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  }

  document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const userInfo = document.querySelector('.user-info');
    if (!userInfo.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
  </script>

  {{-- Additional page scripts --}}
  @yield('scripts')

</body>
</html>
