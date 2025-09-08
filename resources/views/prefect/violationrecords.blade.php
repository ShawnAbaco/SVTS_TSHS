<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Violation Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    background:rgb(0, 0, 0); 
    color: #fff;
    height: 100vh;
    position: fixed;
    padding: 25px 15px;
    border-radius: 0 15px 15px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
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
      background:rgb(0, 247, 239);
      color: #111;
    }
    .sidebar ul li:hover i {
      color: #111;
    }
    .sidebar ul li.active {
      background:rgb(11, 255, 235);
      color: #111;
    }
    .sidebar ul li.active i {
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
      background:rgb(5, 238, 255);
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
/* Active sidebar item styling */
  .sidebar ul li.active {
    background: rgb(9, 239, 255);
    color: #111;
  }
  .sidebar ul li.active i {
    color: #111;
  }
  /* Prevent hover from overriding active item */
  .sidebar ul li.active:hover {
    background: rgb(9, 218, 255);
    color: #111;
  }
  .sidebar ul li.active:hover i {
    color: #111;
  }

.main-content { margin-left: 260px; padding: 20px; }
.crud-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.crud-container h2 { margin-bottom: 20px; font-size: 24px; display: flex; justify-content: space-between; align-items: center; }

.search-create {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}
.search-create input {
    padding: 8px;
    width: 250px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.search-create .btn-create {
    background-color: #28a745;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.search-create .btn-create i { margin-right: 5px; }

table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
thead { background-color: #343a40; color: white; }

.btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
.btn-info { background-color: #17a2b8; color: white; }
.btn-info i { margin-right: 5px; }

/* Modal Styles */
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
.modal.show { display: flex; }
.modal-content { background: #fff; padding: 20px; width: 100%; max-width: 500px; border-radius: 8px; position: relative; }
.modal-content h5 { margin-bottom: 15px; }
.modal-content .close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 18px; }
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
      <li ><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
      <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
      <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

      <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
      <ul class="dropdown-container">
        <li class="active"><a href="{{ route('violation.records') }}">Violation Record</a></li>
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
        <h2>Student Violations</h2>

        <!-- Search + Create Button -->
        <div class="search-create">
            <input type="text" id="searchInput" placeholder="Search student name...">
            <button class="btn-create"><i class="fas fa-plus"></i> Create Violation</button>
        </div>

        <table id="violationTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Violation</th>
                    <th>Adviser</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
                    <td>{{ $violation->offense->offense_type }}</td>
                    <td>{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}</td>
                    <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
                    <td>
                        <button class="btn btn-info"
                            onclick="showInfo('{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}', '{{ $violation->student->parent->parent_fname }} {{ $violation->student->parent->parent_lname }}', '{{ $violation->student->parent->parent_contactinfo }}', '{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}')">
                            <i class="fas fa-info-circle"></i> Info
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No violations found.</td>
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
        <h5>Violation Details</h5>
        <div class="info-box">
            <p><strong>Student Name:</strong> <span id="modalStudent">N/A</span></p>
            <p><strong>Parent Name:</strong> <span id="modalParent">N/A</span></p>
            <p><strong>Parent Contact:</strong> <span id="modalNumber">N/A</span></p>
            <p><strong>Adviser:</strong> <span id="modalAdviser">N/A</span></p>
        </div>
    </div>
</div>

<script>
 // Dropdown functionality with sidebar scroll and only one open at a time
  const sidebar = document.querySelector('.sidebar');
  const dropdowns = document.querySelectorAll('.dropdown-btn');

  dropdowns.forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;

      // Close other dropdowns
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) {
          otherBtn.classList.remove('active');
          otherContainer.style.display = 'none';
        }
      });

      // Toggle current dropdown
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';

      // Sidebar scrollable when at least 1 dropdown is open
      const openDropdowns = document.querySelectorAll('.dropdown-container[style*="block"]').length;
      sidebar.style.overflowY = openDropdowns >= 1 ? 'auto' : 'hidden';
    });
  });
</script>



</body>
</html>
