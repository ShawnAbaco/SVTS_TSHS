<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Anecdotal</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --hover-bg: rgb(0, 88, 240);
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
      font-weight: bold !important;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Arial", sans-serif;
      margin: 0;
      background-color: var(--secondary-color);
      min-height: 100vh;
      display: flex;
    }

    /* --- Sidebar (unchanged) --- */
    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 240px; height: 100%;
      background: linear-gradient(180deg,rgb(48, 48, 50));
      font-family: "Segoe UI", Tahoma, sans-serif;
      z-index: 1000; overflow-y: auto;
      transition: all 0.3s ease;
      color: #ffffff;
    }
    .sidebar::-webkit-scrollbar { width: 8px; }
    .sidebar::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.25); border-radius: 4px; }
    .sidebar::-webkit-scrollbar-track { background-color: rgba(255, 255, 255, 0.05); }
    .sidebar img { width: 180px; margin: 0 auto 0.10rem; display: block; }
    .sidebar p { font-size: 0.9rem; font-weight: 700; margin: 0 0 1rem; color: #ffffff; text-align: center; }
    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar ul li a {
      display: flex; align-items: center; gap: 12px; padding: 12px 20px;
      color: #ffffff; text-decoration: none; font-size: 0.95rem;
      border-left: 4px solid transparent; border-radius: 8px;
      transition: all 0.3s ease;
    }
    .sidebar ul li a:hover, .sidebar ul li a.active {
      background-color: rgba(255,255,255,0.12);
      border-left-color: #FFD700;
    }
    .dropdown-container { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
    .dropdown-container.show { max-height: 400px; padding-left: 12px; }

    /* --- Main Content --- */
    .main-content {
      margin-left: 260px;
      padding: 2rem;
      width: calc(100% - 260px);
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .header h1 { font-size: 22px; margin: 0; }
    .header .actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .search-box input {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      min-width: 200px;
    }

    /* --- Buttons --- */
    .btn {
      padding: 10px 16px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }
    .btn i { font-size: 16px; }
    .btn-edit { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
    .btn-danger { background: linear-gradient(135deg, #dc3545, #b02a37); color: #fff; }
    .btn-info { background: linear-gradient(135deg, #17a2b8, #117a8b); color: #fff; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 12px rgba(0,0,0,0.2); opacity: 0.95; }

    /* --- Table --- */
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 25px;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--shadow);
      font-size: 16px;
    }
    table th, table td {
      padding: 14px 18px;
      text-align: center;
      vertical-align: middle;
    }
    table th {
      background-color: rgb(0, 0, 0);
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
    }
    table tr:nth-child(even) { background-color: #f7f7f7; }
    table tr:hover { background-color: rgba(0,0,0,0.05); }

    /* --- Modal --- */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center; align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 500px;
      width: 100%;
      position: relative;
      box-shadow: var(--shadow);
    }
    .modal-content h2 { margin-bottom: 1rem; font-size: 20px; }
    .modal-content .close {
      position: absolute; top: 10px; right: 15px;
      cursor: pointer; font-size: 20px; color: red;
    }
    .modal-content form { display: flex; flex-direction: column; gap: 12px; }
    .modal-content label { font-size: 14px; }
    .modal-content input, .modal-content textarea {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .modal-content textarea { resize: none; height: 80px; }
    .modal-content button { align-self: flex-end; }
  </style>
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
