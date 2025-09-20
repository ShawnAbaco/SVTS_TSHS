<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Profile Settings</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>

</head>
<body>
   <link rel="stylesheet" href="{{ asset('css/adviser/profilesettings.css') }}">
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
<li>
    <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>    </ul>
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
