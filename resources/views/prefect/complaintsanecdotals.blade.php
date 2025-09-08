<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Anecdotals</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    /* Reset & Base */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; font-weight: bold; transition: all 0.2s ease-in-out; }
    body { display: flex; background: #f9f9f9; color: #111; }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #000;
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
      overflow-y: auto;
    }
    .sidebar h2 { margin-bottom: 30px; text-align: center; font-size: 20px; letter-spacing: 1px; color: #fff; }
    .sidebar ul { list-style: none; }
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
    .sidebar ul li i { margin-right: 12px; min-width: 20px; }
    .sidebar ul li:hover { background: #00f7ef; color: #111; }
    .sidebar ul li:hover i { color: #111; }
    .sidebar ul li.active { background: #0bffeb; color: #111; }
    .sidebar ul li.active i { color: #111; }
    .sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
    .section-title { margin: 15px 10px 5px; font-size: 11px; text-transform: uppercase; color: #bbb; }

    /* Dropdown */
    .dropdown-container { display: none; list-style: none; padding-left: 20px; }
    .dropdown-container li { padding: 10px; font-size: 13px; border-radius: 6px; cursor: pointer; }
    .dropdown-container li:hover { background: #fff; color: #111; }
    .dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
    .dropdown-btn.active .arrow { transform: rotate(180deg); }

    /* Content */
    .content { margin-left: 270px; padding: 20px; }
    h1 { text-align: center; margin-bottom: 20px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
    table thead { background: #3498db; color: #fff; }
    table th, table td { padding: 12px 15px; border: 1px solid #ddd; text-align: center; }
    table tbody tr:nth-child(even) { background: #f2f2f2; }

    /* Buttons */
    .btn { padding: 8px 16px; font-size: 14px; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; }
    .btn-primary { background: #007bff; color: #fff; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
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
      <li class="active"><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

<!-- Content -->
<div class="content">
  <h1>Complaints Anecdotals</h1>

  <div style="margin: 15px 0;">
    <button class="btn btn-primary" onclick="openCreateModal()">Create</button>
    <input type="text" id="searchInput" placeholder="Search by Complainant Name..." onkeyup="searchTable()" style="padding:5px; width:250px; margin-left:10px;">
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Solution</th>
        <th>Recommendation</th>
        <th>Date</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      @foreach($complaint_anecdotals as $anec)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $anec->complaint->complainant->student_fname }} {{ $anec->complaint->complainant->student_lname }}</td>
        <td>{{ $anec->complaint->respondent->student_fname }} {{ $anec->complaint->respondent->student_lname }}</td>
        <td>{{ $anec->comp_anec_solution }}</td>
        <td>{{ $anec->comp_anec_recommendation }}</td>
        <td>{{ $anec->comp_anec_date }}</td>
        <td>{{ $anec->comp_anec_time }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
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

  // Logout
  function logout() {
    fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
      .then(() => window.location.href = '/prefect/login')
      .catch(err => console.error('Logout failed:', err));
  }

  // Search Table
  function searchTable() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
      let text = row.cells[1].textContent.toLowerCase();
      row.style.display = text.includes(input) ? '' : 'none';
    });
  }

  // Dummy Create Modal
  function openCreateModal() { alert('Open create modal here'); }
</script>

</body>
</html>
