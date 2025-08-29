<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Profile Settings</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/profile.css') }}">
</head>
<body>
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

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
          <td id="fullname">{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}</td>
          <td id="email">{{ $adviser->adviser_email }}</td>
          <td id="password">*******</td>
          <td id="contact">{{ $adviser->adviser_contactinfo }}</td>
          <td id="section">{{ $adviser->adviser_section }}</td>
          <td id="grade">{{ $adviser->adviser_gradelevel }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Full Info Modal -->
  <div id="fullInfoModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Change Full Info</div>
      <label>Full Name</label>
      <input type="text" id="modalFullname">
      <label>Email</label>
      <input type="email" id="modalEmail">
      <label>Contact Number</label>
      <input type="text" id="modalContact">
      <label>Section</label>
      <input type="text" id="modalSection">
      <label>Grade</label>
      <input type="text" id="modalGrade">
      <div class="modal-buttons">
        <button onclick="closeModal('fullInfoModal')">Cancel</button>
        <button onclick="saveFullInfo()">Save</button>
      </div>
    </div>
  </div>

  <!-- Password Modal -->
  <div id="passwordModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Change Password</div>
      <label>New Password</label>
      <input type="password" id="modalPassword">
      <label><input type="checkbox" id="showPassword"> Show Password</label>
      <div class="modal-buttons">
        <button onclick="closeModal('passwordModal')">Cancel</button>
        <button onclick="savePassword()">Save</button>
      </div>
    </div>
  </div>

  <script>
    const menuLinks = document.querySelectorAll('.sidebar a');

    // Active menu
    const activeLink = localStorage.getItem('activeMenu');
    if (activeLink) {
      menuLinks.forEach(link => {
        if (link.href === activeLink) link.classList.add('active');
      });
    }

    menuLinks.forEach(link => {
      link.addEventListener('click', function () {
        menuLinks.forEach(item => item.classList.remove('active'));
        this.classList.add('active');
        if (!this.href.includes('profile.settings')) localStorage.setItem('activeMenu', this.href);
      });
    });

    function logout() { alert('Logging out...'); }

    function openFullInfoModal() {
      document.getElementById('modalFullname').value = "{{ $adviser->adviser_fname }} {{ $adviser->adviser_lname }}";
      document.getElementById('modalEmail').value = "{{ $adviser->adviser_email }}";
      document.getElementById('modalContact').value = "{{ $adviser->adviser_contactinfo }}";
      document.getElementById('modalSection').value = "{{ $adviser->adviser_section }}";
      document.getElementById('modalGrade').value = "{{ $adviser->adviser_gradelevel }}";
      document.getElementById('fullInfoModal').style.display = 'block';
    }

    function openPasswordModal() {
      document.getElementById('modalPassword').value = '';
      document.getElementById('showPassword').checked = false;
      document.getElementById('modalPassword').type = 'password';
      document.getElementById('passwordModal').style.display = 'block';
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function saveFullInfo() {
      // Add AJAX call here if you want to save to database
      closeModal('fullInfoModal');
      alert('Full info updated successfully!');
    }

    function savePassword() {
      const newPassword = document.getElementById('modalPassword').value;
      if(newPassword.trim() !== "") {
        closeModal('passwordModal');
        alert('Password updated successfully!');
      } else {
        alert('Password cannot be empty.');
      }
    }

    document.getElementById('showPassword').addEventListener('change', function() {
      const pwdInput = document.getElementById('modalPassword');
      pwdInput.type = this.checked ? 'text' : 'password';
    });
  </script>
</body>
</html>
