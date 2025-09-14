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
    body { display: flex; background: #f0f2f5; color: #111; }

    /* Sidebar */
    .sidebar {
      width: 230px;
      background: rgb(73, 0, 0);
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 15px rgba(0,0,0,0.5);
      overflow-y: auto;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 22px;
      letter-spacing: 1px;
      color: #ffffff;
      text-transform: uppercase;
      border-bottom: 2px solid rgba(255, 255, 255, 0.15);
      padding-bottom: 10px;
    }
    .sidebar ul { list-style: none; }
    .sidebar ul li {
      padding: 12px 14px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 10px;
      font-size: 15px;
      color: #e0e0e0;
      transition: background 0.3s, transform 0.2s;
    }
    .sidebar ul li i { margin-right: 12px; color: #cfcfcf; min-width: 20px; font-size: 16px; }
    .sidebar ul li:hover { background: #2d3f55; transform: translateX(5px); color: #fff; }
    .sidebar ul li:hover i { color: #00e0ff; }
    .sidebar ul li.active { background: #00aaff; color: #fff; border-left: 4px solid #fff; }
    .sidebar ul li.active i { color: #fff; }
    .sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
    .section-title { margin: 20px 10px 8px; font-size: 11px; text-transform: uppercase; font-weight: bold; color: rgba(255, 255, 255, 0.6); letter-spacing: 1px; }

    /* Dropdown */
    .dropdown-container { display: none; list-style: none; padding-left: 25px; }
    .dropdown-container li { padding: 10px; font-size: 14px; border-radius: 8px; color: #ddd; }
    .dropdown-container li:hover { background: #3a4c66; color: #fff; }
    .dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
    .dropdown-btn.active .arrow { transform: rotate(180deg); }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 3px; }

    /* Content */
    .content { margin-left: 270px; padding: 30px; }
    h1 { text-align: center; margin-bottom: 25px; font-size: 28px; color: #333; }

    /* Controls */
    .controls { display: flex; justify-content: space-between; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
    .controls input[type="text"] { padding: 8px 12px; font-size: 14px; border-radius: 6px; border: 1px solid #ccc; width: 250px; }
    .btn { padding: 10px 18px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-primary { background-color: #007bff; color: #fff; }
    .btn-primary:hover { background-color: #0056b3; }

    /* Table */
    table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
    table thead { background: #3498db; color: #fff; font-size: 15px; }
    table th, table td { padding: 14px 12px; text-align: center; font-size: 14px; }
    table tbody tr { background: #fff; transition: background 0.3s; }
    table tbody tr:nth-child(even) { background: #f9f9f9; }
    table tbody tr:hover { background: #e1f0ff; }

    /* Action Buttons */
    .btn-action { padding: 6px 12px; font-size: 13px; margin: 2px; border-radius: 5px; border: none; cursor: pointer; transition: all 0.2s; }
    .btn-edit { background: #ffc107; color: #000; }
    .btn-edit:hover { background: #e0a800; }
    .btn-delete { background: #dc3545; color: #fff; }
    .btn-delete:hover { background: #a71d2a; }

    /* Responsive */
    @media(max-width: 900px){
      .content { margin-left: 20px; padding: 20px; }
      table th, table td { font-size: 12px; padding: 10px 8px; }
      .controls { flex-direction: column; align-items: flex-start; }
      .controls input[type="text"] { width: 100%; }
    }
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

  <div class="controls">
    <button class="btn btn-primary">Create</button>
    <input type="text" id="searchInput" placeholder="Search by Complainant Name..." onkeyup="searchTable()">
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
        <th>Action</th>
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
        <td>
          <button class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-action btn-delete"><i class="fas fa-trash"></i> Delete</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  // Dropdown functionality
  const sidebar = document.querySelector('.sidebar');
  const dropdowns = document.querySelectorAll('.dropdown-btn');
  dropdowns.forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) { otherBtn.classList.remove('active'); otherContainer.style.display = 'none'; }
      });
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';
    });
  });

  // Search Table
  function searchTable() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
      let text = row.cells[1].textContent.toLowerCase();
      row.style.display = text.includes(input) ? '' : 'none';
    });
  }
</script>

</body>
</html>
