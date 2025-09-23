<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Anecdotals</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
/* ======================= RESET & BASE ======================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}
body {
    display: flex;
    background: #f0f2f5;
    color: #111;
}
/* ======================= SIDEBAR ======================= */
 .sidebar {
      width: 230px;
background: linear-gradient(135deg, #002200, #004400, #006600, #008800);

background-repeat: no-repeat;
background-attachment: fixed;
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

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px 14px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 10px;
      font-size: 15px;
      color:rgb(255, 255, 255);
      transition: background 0.3s, transform 0.2s;
    }

    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
      font-size: 16px;
    }

    .sidebar ul li:hover {
      background: #2d3f55;
      transform: translateX(5px);
      color: #fff;
    }

    .sidebar ul li:hover i {
      color: #00e0ff;
    }

    .sidebar ul li.active {
      background: #00aaff;
      color: #fff;
      border-left: 4px solid #ffffff;
    }

    .sidebar ul li.active i {
      color: #fff;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }

    .section-title {
      margin: 20px 10px 8px;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: bold;
      color: rgba(255, 255, 255, 0.6);
      letter-spacing: 1px;
    }

    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 25px;
    }

    .dropdown-container li {
      padding: 10px;
      font-size: 14px;
      border-radius: 8px;
      color: #ddd;
    }

    .dropdown-container li:hover {
      background: #3a4c66;
      color: #fff;
    }

    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.25);
      border-radius: 3px;
    }
/* ======================= CONTENT ======================= */
.content { margin-left: 270px; padding: 30px; }
h1 { text-align: center; margin-bottom: 25px; font-size: 28px; color: #333; }

/* ======================= TOP CONTROLS ======================= */
.top-controls { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
.top-controls input { padding: 10px 15px; width: 300px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; height: 40px; }
.top-controls .btn-primary,
.top-controls .btn-archive {
    padding: 10px 16px;
    font-size: 15px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    height: 40px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
.top-controls .btn-primary { background: linear-gradient(135deg,#007bff,#00aaff); color: #fff; }
.top-controls .btn-primary:hover { background: linear-gradient(135deg,#0056b3,#007bbf); transform: translateY(-2px); }
.top-controls .btn-archive { background-color: orange; color: #fff; }
.top-controls .btn-archive:hover { background-color: darkorange; transform: translateY(-2px); }

/* ======================= TABLE ======================= */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    background: #fff;
    font-size: 16px;
    table-layout: fixed;
}

th {
    background: #000;
    color: #fff;
    padding: 16px 14px;
    text-align: center;
    font-weight: bold;
    border: none;
}

td {
    padding: 14px 12px;
    border-bottom: 1px solid #e3e3e3;
    vertical-align: middle;
    text-align: center;
    word-wrap: break-word;
    font-weight: bold;  /* <-- Added this line */
}

td:first-child { text-align: center; }
td:nth-child(2) { text-align: left; }
tr:nth-child(even) { background: #f5f8ff; }
tr:hover { background: #d0e7ff; transform: scale(1.01); transition: all 0.2s ease-in-out; }


/* ======================= ACTION BUTTONS ======================= */
.btn-action.btn-update {
    background-color: orange;
    color: #fff;
    padding: 6px 12px;
    font-size: 13px;
    margin: 2px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-action.btn-update:hover { background-color: darkorange; transform: translateY(-1px) scale(1.05); }

/* ======================= RESPONSIVE ======================= */
@media(max-width: 900px){
    .content { margin-left: 20px; padding: 20px; }
    table { font-size: 14px; }
    th, td { font-size: 12px; padding: 10px 8px; }
    .top-controls { flex-direction: column; align-items: flex-start; }
    .top-controls input[type="text"] { width: 100%; }
}

/* ======================= LOGO ======================= */
.sidebar img { width: 150px; height: auto; margin: 0 auto 0.5rem; display: block; transition: transform 0.3s ease; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
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

  <div class="top-controls">
    <input type="text" id="searchInput" placeholder="Search Complainant Name..." onkeyup="searchTable()">
    <button class="btn btn-primary">Create</button>
    <button class="btn-archive" onclick="openArchive()"><i class="fas fa-archive"></i> Archive</button>
  </div>

  <table>
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
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
        <td><input type="checkbox" class="student-checkbox"></td>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $anec->complaint->complainant->student_fname }} {{ $anec->complaint->complainant->student_lname }}</td>
        <td>{{ $anec->complaint->respondent->student_fname }} {{ $anec->complaint->respondent->student_lname }}</td>
        <td>{{ $anec->comp_anec_solution }}</td>
        <td>{{ $anec->comp_anec_recommendation }}</td>
        <td>{{ $anec->comp_anec_date }}</td>
        <td>{{ $anec->comp_anec_time }}</td>
        <td>
          <button class="btn-action btn-update"><i class="fas fa-edit"></i> Update</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.student-checkbox');

selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
});
checkboxes.forEach(cb => {
  cb.addEventListener('change', () => {
    if (!cb.checked) selectAll.checked = false;
    else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) selectAll.checked = true;
  });
});

const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.nextElementSibling;
    dropdowns.forEach(otherBtn => {
      const otherContainer = otherBtn.nextElementSibling;
      if(otherBtn !== btn){ otherBtn.classList.remove('active'); otherContainer.style.display='none'; }
    });
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });
});

function searchTable() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        let text = row.cells[2].textContent.toLowerCase(); // Complainant column
        row.style.display = text.includes(input) ? '' : 'none';
    });
}

function logout() {
    if(!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json' }
    }).then(resp => {
        if(resp.ok) window.location.href="{{ route('auth.login') }}";
        else console.error('Logout failed:', resp.statusText);
    }).catch(err=>console.error('Logout failed:', err));
}
</script>

</body>
</html>
