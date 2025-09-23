<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Violation Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
.main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
.crud-container { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.crud-container h2 { font-size: 28px; margin-bottom: 20px; color: #0a1e2d; }

/* ======================= SEARCH + BUTTONS ======================= */
.search-create {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}
.search-create input[type="text"] {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
    width: 250px;
    height: 40px;
}
.search-create input[type="text"]:focus {
    border-color: #007BFF;
    box-shadow: 0 0 8px rgba(0,123,255,0.2);
}
.search-create .btn-create {
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    padding: 0 16px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    background-color: #007bff;
    color: #fff;
    gap: 6px;
}
.search-create .btn-create:hover { background-color: #0069d9; transform: translateY(-1px) scale(1.02); }
.search-create .btn-archive {
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    padding: 0 16px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    background-color: orange;
    color: #fff;
    gap: 6px;
}
.search-create .btn-archive:hover { background-color: darkorange; transform: translateY(-1px) scale(1.02); }

/* ======================= TABLE ======================= */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    font-size: 14px;
}
table th, table td {
    padding: 14px 12px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
    font-weight: 500;
    color: #333;
}
table thead th {
    background: #000;
    color: #fff !important;
    font-weight: bold !important;
    text-transform: uppercase;
    font-size: 13px;
}
table tbody tr:nth-child(even) { background-color: #f4f7fa; }
table tbody tr:hover { background-color: #e0f0ff; transform: scale(1.01); transition: all 0.2s ease-in-out; }

/* ======================= BUTTONS ======================= */
.btn {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease-in-out;
}
.btn i { font-size: 14px; }
.btn-edit { background-color: #ffc107; color: #000; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
.btn-edit:hover { background-color: #e0a800; transform: translateY(-2px) scale(1.02); }

/* ======================= MODAL ======================= */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 10px; /* extra padding for small screens */
}

.modal.show {
    display: flex;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal-content {
    background: #fff;
    padding: 25px 30px;
    width: 100%;
    max-width: 500px;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Modal Header */
.modal-content h5 {
    margin-bottom: 15px;
    color: #007bff;
    font-size: 18px;
}

/* Close Button */
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

/* Info Box */
.info-box p {
    margin: 8px 0;
    font-size: 15px;
    display: flex;
    justify-content: space-between;
}

/* Modal Form (Update) */
.modal-content label {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 4px;
    color: #333;
}

.modal-content input[type="text"],
.modal-content input[type="date"],
.modal-content input[type="time"] {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
    width: 100%;
    transition: all 0.2s;
}

.modal-content input[type="text"]:focus,
.modal-content input[type="date"]:focus,
.modal-content input[type="time"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0,123,255,0.2);
}

/* Modal Buttons */
.modal-content button {
    margin-top: 10px;
    width: 100%;
    padding: 10px 0;
    font-size: 14px;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    background-color: #ffc107;
    color: #000;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.modal-content button:hover {
    background-color: #e0a800;
    transform: translateY(-1px) scale(1.02);
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


/* Responsive */
@media screen and (max-width: 500px) {
    .modal-content {
        padding: 20px;
    }
    .modal-content input,
    .modal-content button {
        font-size: 12px;
        padding: 8px;
    }
    .info-box p { font-size: 13px; }
}


/* ======================= LOGO ======================= */
.sidebar img { width: 150px; height: auto; margin: 0 auto 0.5rem; display: block; transition: transform 0.3s ease; }
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
        <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
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
    <h2>Student Violations</h2>
    <div class="search-create">
        <input type="text" id="searchInput" placeholder="Search student name...">
        <button class="btn-create">
            <i class="fas fa-plus"></i> Create Violation
        </button>
        <button class="btn btn-archive">
            <i class="fas fa-archive"></i> Archive
        </button>
    </div>

    <table id="violationTable">
      <thead>
         <tr>
         <th>
  <input type="checkbox" id="selectAll">
  <!-- 3-dots trash dropdown -->
  <div class="bulk-action-dropdown" style="display:inline-block; position: relative; margin-left:5px;">
    <button id="bulkActionBtn">&#8942;</button>
    <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
      <div class="bulk-action-item" style="padding:5px 10px; cursor:pointer;">Trash</div>
    </div>
  </div>
</th>

          <th>#</th>
          <th>Student Name</th>
          <th>Violation</th>
          <th>Adviser</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
         </tr>
      </thead>
      <tbody>
        @forelse($violations as $index => $violation)
        <tr class="violation-row" 
            data-student="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
            data-parent="{{ $violation->student->parent->parent_fname }} {{ $violation->student->parent->parent_lname }}"
            data-number="{{ $violation->student->parent->parent_contactinfo }}"
            data-adviser="{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}">
           <td><input type="checkbox" class="student-checkbox"></td>
           <td>{{ $index + 1 }}</td>
           <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
           <td>{{ $violation->offense->offense_type }}</td>
           <td>{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}</td>
           <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F d, Y') }}</td>
           <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
           <td>
               <button class="btn btn-edit" onclick="showUpdateModal('{{ $violation->id }}')">
                   <i class="fas fa-edit"></i> Update
               </button>
           </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;">No violations found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
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

<!-- Update Modal -->
<div class="modal" id="updateModal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('updateModal').classList.remove('show')">&times;</span>
    <h5>Update Violation</h5>
    <label>Violation Type:</label>
    <input type="text" id="updateViolationType" placeholder="Enter violation type">
    <label>Date:</label>
    <input type="date" id="updateViolationDate">
    <label>Time:</label>
    <input type="time" id="updateViolationTime">
    <button class="btn btn-edit" onclick="saveUpdate()">Update</button>
  </div>
</div>


<script>
  
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.student-checkbox');

selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
});
checkboxes.forEach(cb => {
  cb.addEventListener('change', () => {
    selectAll.checked = document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length;
  });
});

// Sidebar dropdowns
const sidebar = document.querySelector('.sidebar');
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.nextElementSibling;
    dropdowns.forEach(otherBtn => {
      if (otherBtn !== btn) {
        otherBtn.classList.remove('active');
        otherBtn.nextElementSibling.style.display = 'none';
      }
    });
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });
});

// Row click to show info modal
document.querySelectorAll('.violation-row').forEach(row => {
    row.addEventListener('click', e => {
        if (!e.target.closest('.btn') && !e.target.closest('input')) {
            const student = row.dataset.student;
            const parent = row.dataset.parent;
            const number = row.dataset.number;
            const adviser = row.dataset.adviser;
            showInfo(student, parent, number, adviser);
        }
    });
});

function showInfo(student, parent, number, adviser) {
  document.getElementById('modalStudent').innerText = student;
  document.getElementById('modalParent').innerText = parent;
  document.getElementById('modalNumber').innerText = number;
  document.getElementById('modalAdviser').innerText = adviser;
  document.getElementById('infoModal').classList.add('show');
}

// Show Update modal and prefill values
function showUpdateModal(row) {
    const modal = document.getElementById('updateModal');
    // Get current row data
    const violationType = row.cells[3].innerText;
    const violationDate = new Date(row.cells[5].innerText);
    const yyyy = violationDate.getFullYear();
    const mm = String(violationDate.getMonth()+1).padStart(2,'0');
    const dd = String(violationDate.getDate()).padStart(2,'0');
    const dateStr = `${yyyy}-${mm}-${dd}`;
    const timeStr = row.cells[6].innerText; // Assuming format like 03:00 PM

    document.getElementById('updateViolationType').value = violationType;
    document.getElementById('updateViolationDate').value = dateStr;
    document.getElementById('updateViolationTime').value = timeStr;

    modal.dataset.rowIndex = row.rowIndex; // Save row index to update later
    modal.classList.add('show');
}

// Save changes to the table (JS only)
function saveUpdate() {
    const modal = document.getElementById('updateModal');
    const rowIndex = modal.dataset.rowIndex;
    const table = document.getElementById('violationTable');
    const row = table.rows[rowIndex];

    row.cells[3].innerText = document.getElementById('updateViolationType').value;
    row.cells[5].innerText = document.getElementById('updateViolationDate').value;
    row.cells[6].innerText = document.getElementById('updateViolationTime').value;

    modal.classList.remove('show');
}

// Update button click
document.querySelectorAll('.btn-edit').forEach((btn, idx) => {
    btn.addEventListener('click', e => {
        e.stopPropagation(); // Prevent row click
        const row = btn.closest('tr');
        showUpdateModal(row);
    });
});


// Logout
function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (!confirmLogout) return;
    fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(response => { if(response.ok) window.location.href = "{{ route('auth.login') }}"; })
    .catch(error => console.error('Logout failed:', error));
}
const bulkActionBtn = document.getElementById('bulkActionBtn');
const bulkActionMenu = document.getElementById('bulkActionMenu');

bulkActionBtn.addEventListener('click', () => {
  bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
});

// Trash functionality
bulkActionMenu.querySelector('.bulk-action-item').addEventListener('click', () => {
  document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
    cb.closest('tr').remove();
  });
  bulkActionMenu.style.display = 'none';
});

// Close dropdown if clicked outside
document.addEventListener('click', (e) => {
  if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
    bulkActionMenu.style.display = 'none';
  }
});

</script>
</body>
</html>
