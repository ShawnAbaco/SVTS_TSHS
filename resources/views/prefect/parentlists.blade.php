<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
   /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
      font-weight: bold;
      transition: all 0.2s ease-in-out;
    }

    body {
      display: flex;
      background: #f9f9f9;
      color: #111;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: linear-gradient(180deg, #111, #222);
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
      overflow-y: auto;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 20px;
      letter-spacing: 1px;
      color:rgb(255, 255, 255);
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      padding: 12px 10px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 8px;
      font-size: 14px;
      color: #fff;
      transition: 0.3s;
      position: relative;
    }
    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
    }
    .sidebar ul li:hover {
      background:rgb(30, 255, 9);
      color: #111;
    }
    .sidebar ul li:hover i {
      color: #111;
    }
    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }
    .section-title {
      margin: 15px 10px 5px;
      font-size: 11px;
      text-transform: uppercase;
      color: #bbb;
    }
    /* Active sidebar item styling */
  .sidebar ul li.active {
    background: rgb(30, 255, 9);
    color: #111;
  }
  .sidebar ul li.active i {
    color: #111;
  }
  /* Prevent hover from overriding active item */
  .sidebar ul li.active:hover {
    background: rgb(30, 255, 9);
    color: #111;
  }
  .sidebar ul li.active:hover i {
    color: #111;
  }
    /* Dropdown */
    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 20px;
    }
    .dropdown-container li {
      padding: 10px;
      font-size: 13px;
      border-radius: 6px;
      cursor: pointer;
    }
    .dropdown-container li:hover {
      background:rgb(5, 255, 47);
      color: #111;
    }
    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }
    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background:rgb(255, 255, 255);
      border-radius: 3px;
    }

    /* Main Content */
    .main-content { margin-left: 250px; padding: 30px; }
    .crud-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .crud-container h2 {
      font-size: 28px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: none; /* line removed */
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    table th, table td { padding: 10px; text-align: center; border: 1px solid #ccc; }
    table thead { background-color: #007BFF; color: #fff; }
    table tr:nth-child(even) { background-color: #f9f9f9; }
    table tr:hover { background-color: #eef3fb; }

    /* Buttons */
    .btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
    .btn-info { background-color: #17a2b8; color: #fff; }
    .btn-warning { background-color: #ffc107; color: #000; }
    .btn-danger { background-color: #dc3545; color: #fff; }
    .btn i { margin-right: 5px; }

    /* Modal */
    .modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .modal.show { display: flex; }
    .modal-content {
      background: #fff;
      padding: 20px;
      width: 100%;
      max-width: 500px;
      border-radius: 8px;
      position: relative;
    }
    .modal-content h5 { margin-bottom: 15px; }
    .modal-content .close {
      position: absolute;
      top: 10px;
      right: 15px;
      cursor: pointer;
      font-size: 18px;
    }
    .info-box p { margin: 8px 0; font-size: 16px; }
  </style>
</head>
<body>

  <!-- Sidebar -->
<div class="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>

    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <!-- Active Page -->
    <li class="active"><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
    </ul>

    <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="crud-container">
      <h2>Parent List</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Parent Fullname</th>
            <th>Contact Number</th>
            <th>Birthdate</th>
            <th>Student</th>
            <th>Adviser</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($parents as $index => $parent)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
            <td>{{ $parent->parent_contactinfo }}</td>
            <td>{{ $parent->parent_birthdate }}</td>
            <td>
              @forelse($parent->students as $student)
                {{ $student->student_fname }} {{ $student->student_lname }}<br>
              @empty N/A @endforelse
            </td>
            <td>
              @forelse($parent->students as $student)
                @if($student->adviser)
                  {{ $student->adviser->adviser_fname }} {{ $student->adviser->adviser_lname }}<br>
                @else N/A<br> @endif
              @empty N/A @endforelse
            </td>
            <td>
              <button class="btn btn-info" onclick="showInfo(
                `@foreach($parent->students as $student){{ $student->student_fname }} {{ $student->student_lname }}<br>@endforeach`,
                `@foreach($parent->students as $student){{ $student->adviser ? $student->adviser->adviser_fname.' '.$student->adviser->adviser_lname : 'N/A' }}<br>@endforeach`,
                '{{ $parent->parent_fname }} {{ $parent->parent_lname }}'
              )"><i class="fas fa-info-circle"></i> Info</button>
              <button class="btn btn-warning"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="text-align:center;">No parents found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Info Modal -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('infoModal').classList.remove('show')">&times;</span>
      <h5>Student Information</h5>
      <div class="info-box">
        <p><strong>Student Name:</strong> <span id="studentName">N/A</span></p>
        <p><strong>Adviser:</strong> <span id="adviserName">N/A</span></p>
        <p><strong>Parent:</strong> <span id="parentName">N/A</span></p>
      </div>
    </div>
  </div>

  <script>
    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown-btn');
    dropdowns.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            const container = btn.nextElementSibling;
            container.style.display = container.style.display === "block" ? "none" : "block";
        });
    });

    function showInfo(student, adviser, parent) {
      document.getElementById("studentName").innerHTML = student;
      document.getElementById("adviserName").innerHTML = adviser;
      document.getElementById("parentName").textContent = parent;
      document.getElementById("infoModal").classList.add("show");
    }

    function logout() {
      fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      }).then(() => window.location.href='/prefect/login')
        .catch(error => console.error('Logout failed:', error));
    }
  </script>
</body>
</html>
