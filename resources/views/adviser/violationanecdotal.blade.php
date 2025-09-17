<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Anecdotal</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/adviser/violationanecdotal.css') }}">

</head>
<body>
  <!-- SIDEBAR -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}" class="active">Violation Anecdotal</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
          <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
          <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
        </ul>
      </li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="header">
      <h1>Violation Anecdotal</h1>
      <div class="actions">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search...">
        </div>
        <button class="btn btn-info" onclick="openModal()">
          <i class="fas fa-plus-circle"></i> Add Record
        </button>
         
         <button class="btn-archive" id="archivesBtn">
  <i class="fas fa-archive"></i> Archives
</button>

      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Student Name</th>
          <th>Violation</th>
          <th>Date</th>
          <th>Remarks</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Example row -->
        <tr>
          <td>Juan Dela Cruz</td>
          <td>Bullying</td>
          <td>2025-09-13</td>
          <td>First offense</td>
          <td>
            <button class="btn btn-info"><i class="fas fa-info-circle"></i> Info</button>
            <button class="btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- MODAL -->
  <div id="recordModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Add Violation Anecdotal</h2>
      <form>
        <label for="studentName">Student Name</label>
        <input type="text" id="studentName" name="studentName" required>

        <label for="violation">Violation</label>
        <input type="text" id="violation" name="violation" required>

        <label for="date">Date</label>
        <input type="date" id="date" name="date" required>

        <label for="remarks">Remarks</label>
        <textarea id="remarks" name="remarks" required></textarea>

        <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Save</button>
      </form>
    </div>
  </div>

  <script>
       // Dropdown functionality - auto close others & scroll
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // close all other dropdowns
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this) {
                otherBtn.nextElementSibling.classList.remove('show');
                otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
            }
        });

        // toggle clicked dropdown
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
            container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

        // scroll into view if dropdown is opened
        if(container.classList.contains('show')){
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});

    function openModal() {
      document.getElementById("recordModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("recordModal").style.display = "none";
    }
    window.onclick = function(event) {
      const modal = document.getElementById("recordModal");
      if (event.target === modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>
