<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Prefect Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/createViolation.css') }}">

</head>
<body>

<!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
    <ul>

      <li ><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li class="active"><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
      <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
      <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
      <li><a href="{{ route('violation.records') }}"><i class="fas fa-book"></i> Violation Record</a></li>
        <li><a href="{{ route('people.complaints') }}"><i class="fas fa-comments"></i>Complaints</a></li>
      <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>




 <!-- ✅ Main content area (for child pages) -->
  <main class="main-content">
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
      if (response.ok) window.location.href = "{{ route('auth.login') }}";
    })
    .catch(err => console.error('Logout failed:', err));
  };
});
</script>
</body>
</html>
