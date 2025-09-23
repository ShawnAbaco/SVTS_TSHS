<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
/* ======================= RESET ======================= */
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
    background: #f9f9f9;
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

/* ======================= MAIN CONTENT ======================= */
main {
    margin-left: 230px;
    padding: 20px;
    width: calc(100% - 230px);
}

main h2 { margin-bottom: 15px; }

.flex {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    justify-content: flex-end;
}

input.form-control {
    height: 45px;
    font-size: 16px;
    padding: 0 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

input.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px #007bff;
}

select.form-select {
    height: 45px;
    font-size: 16px;
    padding: 0 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

select.form-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px #007bff;
}

/* ======================= FORMAL TABLE DESIGN ======================= */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    background: #fff;
}

table thead {
    background-color: #000000;
    color: #ffffff;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}

table thead tr:hover { background-color: #000000; }

table th, table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

table tr:nth-child(even) { background-color: #f9f9f9; }
table tbody tr:hover { background-color: #e6f0ff; }

table thead tr:first-child th:first-child { border-top-left-radius: 8px; }
table thead tr:first-child th:last-child { border-top-right-radius: 8px; }
table tr:last-child td:first-child { border-bottom-left-radius: 8px; }
table tr:last-child td:last-child { border-bottom-right-radius: 8px; }

/* ======================= MODAL ======================= */
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
.modal.show-modal { display: flex; }

.modal-content {
    background: #fff;
    border-radius: 10px;
    width: 450px;
    max-width: 90%;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-header, .modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.modal-header h5 { font-size: 16px; }
.modal-body p { margin-bottom: 10px; font-size: 14px; }

.btn-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #555;
}

.modal-footer .btn { min-width: 90px; }

/* ======================= ARCHIVE BUTTON ======================= */
#archiveBtn {
    background-color: orange;
    color: #fff;
    border: none;
    cursor: pointer;
    height: 45px;
    font-size: 16px;
    padding: 0 15px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
}
#archiveBtn:hover { background-color: darkorange; }
#archiveBtn i { margin-right: 5px; }

.sidebar img {
    width: 150px;
    height: auto;
    margin: 0 auto 0.5rem;
    display: block;
    transition: transform 0.3s ease;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}

/* ======================= BULK ACTION DROPDOWN ======================= */
.bulk-action-item {
    padding: 5px 10px;
    cursor: pointer;
    font-weight: bold;      /* Makes text bold */
    color: #000 !important; /* Makes text black */
}

.bulk-action-item:hover {
    background: #f0f0f0;
    color: #000 !important; /* Keep text black on hover */
}

.bulk-action-dropdown button {
    color: #fff;
    font-weight: bold;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}
.bulk-action-dropdown .bulk-action-item:hover {
    background: #f0f0f0;
}

/* ======================= STATUS COLORS ======================= */
.status-cell.not-completed { color: red; font-weight: bold; }
.status-cell.completed { color: blue; font-weight: bold; }

  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
    <ul>
      <div class="section-title">Main</div>
      <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li class="active"><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
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
        <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
      </ul>

      <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>

  <main>
    <h2>Student Management</h2>
    <div class="flex" style="justify-content: flex-end; gap: 10px; margin-bottom: 15px;">
        <input type="text" id="searchInput" placeholder="Search students..." class="form-control">
        <select id="sectionFilter" class="form-select" style="max-width:200px;">
            <option value="">All Sections</option>
            @foreach($sections as $section)
              <option value="{{ $section }}">{{ $section }}</option>
            @endforeach
        </select>
        <button id="archiveBtn" class="btn btn-warning">
            <i class="fas fa-archive"></i> Archive
        </button>
    </div>

    <table id="studentTable">
      <thead>
        <tr>
          <th>
            <input type="checkbox" id="selectAll">
            <!-- BULK ACTION 3 DOTS -->
            <div class="bulk-action-dropdown" style="display:inline-block; position: relative; margin-left:5px;">
              <button id="bulkActionBtn">&#8942;</button>
              <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
               <div class="bulk-action-item" data-action="completed">Completed</div>
               <div class="bulk-action-item" data-action="not-completed">Not Completed</div>
               <div class="bulk-action-item" data-action="trash">Trash</div>
              </div>
            </div>
          </th>
          <th>ID</th>
          <th>Name</th>
          <th>Grade Level</th>
          <th>Section</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $student)
        <tr data-section="{{ $student->adviser->adviser_section }}" 
            onclick="showFullInfo(this)" style="cursor:pointer;">
          <td><input type="checkbox" class="student-checkbox" onclick="event.stopPropagation()"></td>
          <td>{{ $student->student_id }}</td>
          <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
          <td>{{ $student->adviser->adviser_gradelevel }}</td>
          <td>{{ $student->adviser->adviser_section }}</td>
          <td class="status-cell not-completed">Not Completed</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Info Modal -->
    <div class="modal" id="infoModal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="infoModalTitle">Student Info</h5>
          <button class="btn-close" onclick="closeInfoModal()">&times;</button>
        </div>
        <div class="modal-body" id="infoModalBody"></div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeInfoModal()">Close</button>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Select All
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    selectAll.addEventListener('change', () => {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
    checkboxes.forEach(cb => {
      cb.addEventListener('change', () => {
        if (!cb.checked) {
          selectAll.checked = false;
        } else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) {
          selectAll.checked = true;
        }
      });
    });

    // Bulk Action Menu
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    const bulkActionMenu = document.getElementById('bulkActionMenu');

    bulkActionBtn.addEventListener('click', () => {
      bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
    });

    bulkActionMenu.querySelectorAll('.bulk-action-item').forEach(item => {
      item.addEventListener('click', () => {
        const action = item.getAttribute('data-action');
        const checkedRows = document.querySelectorAll('.student-checkbox:checked');

        checkedRows.forEach(cb => {
          const row = cb.closest('tr');
          const statusCell = row.querySelector('.status-cell');

          if(action === 'completed') {
            statusCell.textContent = 'Completed';
            statusCell.classList.remove('not-completed');
            statusCell.classList.add('completed');
          } else if(action === 'not-completed') {
            statusCell.textContent = 'Not Completed';
            statusCell.classList.remove('completed');
            statusCell.classList.add('not-completed');
          } else if(action === 'trash') {
            row.remove();
          }
        });

        bulkActionMenu.style.display = 'none';
      });
    });

    document.addEventListener('click', (e) => {
      if(!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
        bulkActionMenu.style.display = 'none';
      }
    });

    document.querySelectorAll('.status-cell').forEach(cell => {
      if(cell.textContent.trim() === 'Not Completed') {
        cell.classList.add('not-completed');
      }
    });

    // Dropdowns
    document.querySelectorAll('.dropdown-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const container = btn.nextElementSibling;
        document.querySelectorAll('.dropdown-btn').forEach(other => {
          if (other !== btn) {
            other.classList.remove('active');
            other.nextElementSibling.style.display = 'none';
          }
        });
        btn.classList.toggle('active');
        container.style.display = container.style.display === 'block' ? 'none' : 'block';
      });
    });

    function logout() {
      if (!confirm("Are you sure you want to logout?")) return;
      fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
      }).then(res => res.ok ? window.location.href = "{{ route('auth.login') }}" : console.error('Logout failed'))
        .catch(err => console.error('Logout failed:', err));
    }

    // Info Modal
    function showFullInfo(row) {
      const cells = row.children;
      const infoHtml = `
        <p><strong>ID:</strong> ${cells[1].innerText}</p>
        <p><strong>Name:</strong> ${cells[2].innerText}</p>
        <p><strong>Grade Level:</strong> ${cells[3].innerText}</p>
        <p><strong>Section:</strong> ${cells[4].innerText}</p>
        <p><strong>Status:</strong> ${cells[5].innerText}</p>
      `;
      document.getElementById('infoModalBody').innerHTML = infoHtml;
      document.getElementById('infoModal').classList.add('show-modal');
    }
    function closeInfoModal() {
      document.getElementById('infoModal').classList.remove('show-modal');
    }

    // Search & filter
    const searchInput = document.getElementById('searchInput');
    const sectionFilter = document.getElementById('sectionFilter');
    function filterTable() {
      const query = searchInput.value.toLowerCase();
      const section = sectionFilter.value;
      document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        const name = row.children[2].innerText.toLowerCase();
        const sec = row.children[4].innerText;
        row.style.display = (name.includes(query) && (section === '' || sec === section)) ? '' : 'none';
      });
    }
    searchInput.addEventListener('input', filterTable);
    sectionFilter.addEventListener('change', filterTable);

  </script>

</body>
</html>
