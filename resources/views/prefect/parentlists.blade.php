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
  width: 230px;
  background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
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
.sidebar ul li i {
  margin-right: 12px;
  color: #cfcfcf;
  min-width: 20px;
  font-size: 16px;
}
.sidebar ul li:hover { background: #2d3f55; transform: translateX(5px); color: #fff; }
.sidebar ul li:hover i { color: #00e0ff; }
.sidebar ul li.active { background: #00aaff; color: #fff; border-left: 4px solid #ffffff; }
.sidebar ul li.active i { color: #fff; }
.sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
.section-title {
  margin: 20px 10px 8px;
  font-size: 11px;
  text-transform: uppercase;
  font-weight: bold;
  color: rgba(255, 255, 255, 0.6);
  letter-spacing: 1px;
}

/* Dropdown */
.dropdown-container { display: none; list-style: none; padding-left: 25px; }
.dropdown-container li { padding: 10px; font-size: 14px; border-radius: 8px; color: #ddd; }
.dropdown-container li:hover { background: #3a4c66; color: #fff; }
.dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
.dropdown-btn.active .arrow { transform: rotate(180deg); }

/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 3px; }

/* Main Content */
.main-content {
  margin-left: 250px;
  padding: 30px;
  width: calc(100% - 250px);
}

.crud-container {
  background: #fff;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.crud-container h2 {
  font-size: 28px;
  margin-bottom: 20px;
  color: #0a1e2d;
}

/* Right-aligned search */
.flex {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-bottom: 15px;
}
.flex input.form-control {
  padding: 6px 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 13px;
}
.flex input.form-control:focus {
  outline: none;
  border-color: #ffcc00;
  box-shadow: 0 0 5px #ffcc00;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

table th, table td {
  padding: 12px 10px;
  text-align: center;
  border: 1px solid #ccc;
  font-weight: 500;
}

table thead {
  background: linear-gradient(90deg, #007BFF, #00aaff);
  color: #fff;
  text-transform: uppercase;
  font-size: 13px;
}

table tr:nth-child(even) { background-color: #f7f9fc; }
table tr:hover { background-color: #e3f2fd; }

/* Buttons */
.btn {
  padding: 6px 14px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn i { margin-right: 5px; }

.btn-info { background-color: #17a2b8; color: #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-info:hover { background-color: #138496; transform: translateY(-2px); }

.btn-warning { background-color: #ffc107; color: #000; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-warning:hover { background-color: #e0a800; transform: translateY(-2px); }

.btn-danger { background-color: #dc3545; color: #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-danger:hover { background-color: #c82333; transform: translateY(-2px); }

/* Modal */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal.show { display: flex; animation: fadeIn 0.3s ease-in-out; }

@keyframes fadeIn { from{opacity:0} to{opacity:1} }

.modal-content {
  background: #fff;
  padding: 25px;
  width: 100%;
  max-width: 500px;
  border-radius: 10px;
  position: relative;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.modal-content h5 { margin-bottom: 15px; color: #007bff; }

.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  cursor: pointer;
  font-size: 20px;
  color: #555;
  transition: 0.2s;
}

.modal-content .close:hover { color: #000; }

.info-box p { margin: 8px 0; font-size: 15px; }

/* Responsive */
@media screen and (max-width:768px) {
  .main-content { margin-left: 0; padding: 15px; }
  table th, table td { font-size: 12px; padding: 8px; }
  .btn { font-size: 12px; padding: 4px 8px; }
  .flex { justify-content: flex-start; flex-direction: column; gap: 8px; }
}
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

      <h2>Parent List</h2>

      <!-- Search Bar -->
      <div class="flex">
        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>

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
      const container = btn.nextElementSibling;
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) {
          otherBtn.classList.remove('active');
          otherContainer.style.display = 'none';
        }
      });
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';
    });
  });

  function showInfo(student, adviser, parent) {
    document.getElementById("studentName").innerHTML = student;
    document.getElementById("adviserName").innerHTML = adviser;
    document.getElementById("parentName").textContent = parent;
    document.getElementById("infoModal").classList.add("show");
  }

function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (!confirmLogout) return;

    fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if(response.ok) {
            // Redirect to login after successful logout
            window.location.href = "{{ route('auth.login') }}";
        } else {
            console.error('Logout failed:', response.statusText);
        }
    })
    .catch(error => console.error('Logout failed:', error));
}
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
      const name = row.children[1].innerText.toLowerCase(); // parent fullname column
      row.style.display = name.includes(query) ? '' : 'none';
    });
  });
  </script>

</body>
</html>
