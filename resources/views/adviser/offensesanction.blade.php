<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Offense & Sanction</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/adviser/offense&sanction.css') }}">
</head>
<body>
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px; height: auto; margin-bottom: 0;">
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
    <h2>Offense & Sanction</h2>

    <button id="addBtn" class="btn btn-blue">
      <i class="fas fa-plus"></i> Add Offense & Sanction
    </button>

    <!-- Modal -->
    <div id="offenseModal">
      <div class="modal-content">
        <h3>Add Offense & Sanction</h3>
        <label>Offense Type:</label>
        <input type="text" id="offenseType">
        <label>Description:</label>
        <input type="text" id="description">
        <label>Consequence:</label>
        <input type="text" id="consequence">
        <div style="text-align: right;">
          <button id="closeModal" class="btn btn-gray" style="margin-right: 0.5rem;">Cancel</button>
          <button id="saveBtn" class="btn btn-blue">Save</button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <table id="offenseTable">
      <thead>
        <tr>
          <th>Offense Type</th>
          <th>Description</th>
          <th>Consequence</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Dynamic rows go here -->
      </tbody>
    </table>
  </div>

  <script>
    const addBtn = document.getElementById('addBtn');
    const offenseModal = document.getElementById('offenseModal');
    const closeModal = document.getElementById('closeModal');
    const saveBtn = document.getElementById('saveBtn');
    const tableBody = document.querySelector('#offenseTable tbody');

    addBtn.addEventListener('click', () => {
      offenseModal.style.display = 'flex';
    });

    closeModal.addEventListener('click', () => {
      offenseModal.style.display = 'none';
      clearModalInputs();
    });

    function clearModalInputs() {
      document.getElementById('offenseType').value = '';
      document.getElementById('description').value = '';
      document.getElementById('consequence').value = '';
    }

    saveBtn.addEventListener('click', () => {
      const type = document.getElementById('offenseType').value;
      const description = document.getElementById('description').value;
      const consequence = document.getElementById('consequence').value;

      if (!type || !description || !consequence) {
        alert('Please fill all fields.');
        return;
      }

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${type}</td>
        <td>${description}</td>
        <td>${consequence}</td>
        <td>
          <button class="editBtn">
            <i class="fas fa-edit"></i> Edit
          </button>
          <button class="deleteBtn">
            <i class="fas fa-trash"></i> Delete
          </button>
        </td>
      `;
      tableBody.appendChild(tr);

      // Edit functionality
      tr.querySelector('.editBtn').addEventListener('click', () => {
        document.getElementById('offenseType').value = type;
        document.getElementById('description').value = description;
        document.getElementById('consequence').value = consequence;
        offenseModal.style.display = 'flex';
        tableBody.removeChild(tr);
      });

      // Delete functionality
      tr.querySelector('.deleteBtn').addEventListener('click', () => {
        tableBody.removeChild(tr);
      });

      offenseModal.style.display = 'none';
      clearModalInputs();
    });

    const menuLinks = document.querySelectorAll('.sidebar a');
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
        if (!this.href.includes('profile.settings')) {
          localStorage.setItem('activeMenu', this.href);
        }
      });
    });

    function logout() {
      alert('Logging out...');
    }
  </script>
</body>
</html>
