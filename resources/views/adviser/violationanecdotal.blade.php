<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Violation Anecdotal</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/violationanecdotal.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    .header-row input[type="text"] {
      padding: 5px;
      margin-right: 0.5rem;
    }
    .btn-action {
      margin: 0 2px;
      padding: 3px 8px;
    }
    table td, table th {
      text-align: left;
      padding: 8px;
    }
    .modal {
      display: none;
      position: fixed;
      top:0; left:0; width:100%; height:100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 5px;
      width: 500px;
      max-width: 90%;
      position: relative;
    }
    .modal-content .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      cursor: pointer;
    }
  </style>
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
      <li><a href="{{ route('violation.anecdotal') }}" class="active"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
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
    <div class="header-row">
      <h2>Violation Anecdotal Records</h2>
      <div>
        <input type="text" id="searchInput" placeholder="Search...">
        <button class="btn-primary" onclick="openModal('createModal')"><i class="fas fa-plus"></i> Create</button>
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

  <!-- Create Modal -->
  <div class="modal" id="createModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('createModal')">&times;</span>
      <h3>Create Violation Anecdotal</h3>
      <form method="POST" action="{{ route('violation.anecdotal.store') }}">
        @csrf
        <div class="form-group">
          <label for="violation_id">Select Violation</label>
          <select name="violation_id" required>
            @foreach($students as $student)
              @foreach($student->violations as $violation)
                <option value="{{ $violation->violation_id }}">
                  {{ $student->student_fname }} {{ $student->student_lname }} - {{ $violation->offense->offense_type ?? '' }}
                </option>
              @endforeach
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="solution">Solution</label>
          <input type="text" name="violation_anec_solution" required>
        </div>
        <div class="form-group">
          <label for="recommendation">Recommendation</label>
          <input type="text" name="violation_anec_recommendation" required>
        </div>
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" name="violation_anec_date" required>
        </div>
        <div class="form-group">
          <label for="time">Time</label>
          <input type="time" name="violation_anec_time" required>
        </div>
        <button type="submit" class="btn-primary">Save</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    // Live search
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      document.querySelectorAll('#anecdotalTable tr').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    function logout() { alert('Logging out...'); }

    function editRecord(id) {
      alert('Edit functionality for record ID: ' + id);
      // You can implement modal prefill and AJAX update here
    }

    function deleteRecord(id) {
      if(confirm('Are you sure you want to delete this record?')) {
        alert('Delete functionality for record ID: ' + id);
        // You can implement AJAX delete here
      }
    }
  </script>
</body>
</html>
