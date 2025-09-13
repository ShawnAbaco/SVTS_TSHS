<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Profile Settings</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --sidebar-bg: rgb(48, 48, 50);
      --sidebar-hover-bg: rgba(255,255,255,0.12);
      --sidebar-border-color: #FFD700;
      --hover-bg: rgb(0, 88, 240);
      --hover-active-bg: rgb(0, 120, 255);
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: "Arial", sans-serif;
      background-color: var(--secondary-color);
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      height: 100%;
      background: var(--sidebar-bg);
      color: #ffffff;
      font-family: "Segoe UI", Tahoma, sans-serif;
      z-index: 1000;
      overflow-y: auto;
    }
    .sidebar img { width: 180px; display: block; margin: 0 auto 0.25rem; }
    .sidebar p {
      font-size: 0.9rem;
      font-weight: bold;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 1rem;
      color: #ffffff;
    }
    .sidebar ul { list-style: none; padding: 0; }
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
    }
    .sidebar ul li a i {
      font-size: 1.1rem;
      min-width: 22px;
      text-align: center;
      color: #ffffff;
    }
    .sidebar ul li a.active {
      background-color: var(--sidebar-hover-bg);
      border-left-color: var(--sidebar-border-color);
      color: #ffffff;
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
    .dropdown-container.show { max-height: 400px; padding-left: 12px; }
    .dropdown-container li a { font-size: 0.85rem; padding: 10px 20px; color: #ffffff; font-weight: normal; }
    .dropdown-btn .fa-caret-down { margin-left: auto; transition: transform 0.3s ease; color: #ffffff; }

    /* Main content */
    .main-content { margin-left: 260px; padding: 2rem; flex-grow: 1; }
    h2 { margin-bottom: 1rem; }

    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    table th { background-color: var(--hover-bg); color: var(--secondary-color); }

    .btn {
      padding: 8px 15px;
      margin-right: 10px;
      background-color: var(--hover-bg);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    /* Profile section */
    .profile-section {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .profile-section img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary-color);
    }
    .profile-section input[type="file"] { display: block; margin-top: 0.5rem; }

    /* Modal styling */
    .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content {
      background-color: var(--secondary-color);
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      box-shadow: var(--shadow);
    }
    .modal-content label { display: block; margin: 10px 0 5px; }
    .modal-content input { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
    .modal-header { font-weight: bold; margin-bottom: 10px; }
    .modal-buttons { text-align: right; }
    .modal-buttons button { margin-left: 5px; }

  </style>
</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
        <img src="/images/Logo.png" alt="Logo">
        <p>ADVISER</p>
    </div>
    <ul>
        <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
        <li><a href="{{ route('parent.list') }}" ><i class="fas fa-user-friends"></i> Parent List</a></li>
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
        <li><a href="{{ route('profile.settings') }}" class="active"><i class="fas fa-cog"></i> Profile Settings</a></li>
        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="main-content">
    <h2>Profile Settings</h2>
    <div class="profile-section">
      <img src="{{ $adviser->profile_picture ?? '/images/user-placeholder.png' }}" alt="Profile Picture">
      <div>
        <label for="profilePic">Change Profile Picture:</label>
        <input type="file" id="profilePic" accept="image/*">
      </div>
    </div>
    <button class="btn" onclick="openFullInfoModal()">Change Full Info</button>
    <button class="btn" onclick="openPasswordModal()">Change Password</button>
    <table id="profileTable">
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Password</th>
          <th>Contact Number</th>
          <th>Section</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}</td>
          <td>{{ $adviser->adviser_email }}</td>
          <td>*******</td>
          <td>{{ $adviser->adviser_contactinfo }}</td>
          <td>{{ $adviser->adviser_section }}</td>
          <td>{{ $adviser->adviser_gradelevel }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modals -->
  <div id="fullInfoModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Change Full Info</div>
      <label>Full Name</label><input type="text" id="modalFullname">
      <label>Email</label><input type="email" id="modalEmail">
      <label>Contact Number</label><input type="text" id="modalContact">
      <label>Section</label><input type="text" id="modalSection">
      <label>Grade</label><input type="text" id="modalGrade">
      <div class="modal-buttons">
        <button onclick="closeModal('fullInfoModal')">Cancel</button>
        <button onclick="saveFullInfo()">Save</button>
      </div>
    </div>
  </div>

  <div id="passwordModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Change Password</div>
      <label>New Password</label><input type="password" id="modalPassword">
      <label><input type="checkbox" id="showPassword"> Show Password</label>
      <div class="modal-buttons">
        <button onclick="closeModal('passwordModal')">Cancel</button>
        <button onclick="savePassword()">Save</button>
      </div>
    </div>
  </div>

  <script>
    // Dropdown
    document.querySelectorAll('.dropdown-btn').forEach(btn=>{
      btn.addEventListener('click', e=>{
        e.preventDefault();
        const container = btn.nextElementSibling;
        container.classList.toggle('show');
        btn.querySelector('.fa-caret-down').style.transform = container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        document.querySelectorAll('.dropdown-container').forEach(dc=>{ if(dc!==container) dc.classList.remove('show'); });
      });
    });


    function logout(){ alert('Logging out...'); }

    function openFullInfoModal(){
      document.getElementById('modalFullname').value="{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}";
      document.getElementById('modalEmail').value="{{ $adviser->adviser_email }}";
      document.getElementById('modalContact').value="{{ $adviser->adviser_contactinfo }}";
      document.getElementById('modalSection').value="{{ $adviser->adviser_section }}";
      document.getElementById('modalGrade').value="{{ $adviser->adviser_gradelevel }}";
      document.getElementById('fullInfoModal').style.display='block';
    }

    function openPasswordModal(){
      document.getElementById('modalPassword').value='';
      document.getElementById('showPassword').checked=false;
      document.getElementById('modalPassword').type='password';
      document.getElementById('passwordModal').style.display='block';
    }

    function closeModal(id){ document.getElementById(id).style.display='none'; }

    function saveFullInfo(){ closeModal('fullInfoModal'); alert('Full info updated successfully!'); }

    function savePassword(){
      const pwd=document.getElementById('modalPassword').value;
      if(pwd.trim()!==''){ closeModal('passwordModal'); alert('Password updated successfully!'); }
      else alert('Password cannot be empty.');
    }

    document.getElementById('showPassword').addEventListener('change', function(){
      document.getElementById('modalPassword').type=this.checked?'text':'password';
    });
  </script>
</body>
</html>
