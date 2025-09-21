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
<li>
    <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>    </ul>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="header">
      <h1>Violation Anecdotal</h1>
      <div class="actions">
         @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif
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
          <th>ID</th>
          <th>Violator Name</th>
          <th>Parent Name</th>
          <th>Solution</th>
          <th>Recommendation</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="anecdotalTable">
        @forelse($anecdotal as $a)
        <tr data-id="{{ $a->violation_anec_id }}">
          <td>{{ $a->violation_anec_id }}</td>
          <td>{{ $a->violation->student->student_fname }} {{ $a->violation->student->student_lname }}</td>
          <td>{{ $a->violation->student->parent->parent_fname ?? '' }} {{ $a->violation->student->parent->parent_lname ?? '' }}</td>
          <td>{{ $a->violation_anec_solution }}</td>
          <td>{{ $a->violation_anec_recommendation }}</td>
          <td>{{ \Carbon\Carbon::parse($a->violation_anec_date)->format('Y-m-d') }}</td>
          <td>{{ \Carbon\Carbon::parse($a->violation_anec_time)->format('h:i A') }}</td>
          <td>
            <button class="btn-action btn-edit" onclick="editRecord({{ $a->violation_anec_id }})"><i class="fas fa-edit"></i></button>
            <button class="btn-action btn-delete" onclick="deleteRecord({{ $a->violation_anec_id }})"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;">No anecdotal records found.</td>
        </tr>
        @endforelse
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
