<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/adviser(1)/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adviser/cards.css') }}">
     <link rel="stylesheet" href="{{ asset('css/adviser(1)/createViolation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adviser(1)/toolbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adviser(1)/modal.css') }}">
     <link rel="stylesheet" href="{{ asset('css/adviser(1)/createParent.css') }}">
</head>
<body>

<!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>Adviser</h2>
 <ul>
  <li class="{{ request()->routeIs('adviser.dashboard') ? 'active' : '' }}">
    <a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a>
  </li>

  <li class="{{ request()->routeIs('parent.list') ? 'active' : '' }}">
    <a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parents</a>
  </li>

  <li class="{{ request()->routeIs('student.list') ? 'active' : '' }}">
    <a href="{{ route('student.list') }}"><i class="fas fa-user-graduate"></i> Students</a>
  </li>

  {{-- <li class="{{ request()->routeIs('user.management') ? 'active' : '' }}">
    <a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Advisers</a>
  </li> --}}

  <li class="{{ request()->routeIs('violation.record') ? 'active' : '' }}">
    <a href="{{ route('violation.record') }}"><i class="fas fa-book"></i> Violations</a>
  </li>

  <li class="{{ request()->routeIs('complaints.all') ? 'active' : '' }}">
    <a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a>
  </li>

  <li class="{{ request()->routeIs('offense.sanction') ? 'active' : '' }}">
    <a href="{{ route('offense.sanction') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a>
  </li>

  <li class="{{ request()->routeIs('adviser.reports') ? 'active' : '' }}">
    <a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-line"></i> Reports</a>
  </li>

<li>
  <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</li>

<form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
  @csrf
</form>


</ul>

  </div>




 <!-- ✅ Main content area (for child pages) -->
  <main class="main-content">
     <!-- ======= HEADER ======= -->
  <header class="main-header">
    <div class="header-left">
      <h2>Student Violation Tracking System</h2>
    </div>
    <div class="header-right">
      <div class="user-info" onclick="toggleProfileDropdown()">
        <img src="/images/user.jpg" alt="User">
        {{-- <span>{{ Auth::prefect()->prefect_fname }}</span> --}}
        <i class="fas fa-caret-down"></i>
      </div>
      <div class="profile-dropdown" id="profileDropdown">
        {{-- <a href="{{ route('profile.settings') }}">Profile</a> --}}
      </div>
    </div>
  </header>

    @yield('content')
  </main>



<script>
document.addEventListener("DOMContentLoaded", () => {
  /** -------------------------------
   * 📂 Sidebar Dropdown Menu Toggle
   * ------------------------------- */
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;
      document.querySelectorAll('.dropdown-container').forEach(el => {
        if (el !== container) el.style.display = 'none';
      });
      container.style.display = (container.style.display === 'block') ? 'none' : 'block';
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


  /** -------------------------------
   * 🚪 Logout Confirmation & Action
   * ------------------------------- */
  window.logout = () => {
    if (!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('adviser.logout') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (response.ok) window.location.href = "{{ route('login') }}";
    })
    .catch(err => console.error('Logout failed:', err));
  };
});
</script>
</body>
</html>
