<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/USERS.css') }}">
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
main, .main-content {
    margin-left: 250px;
    padding: 30px;
    width: calc(100% - 250px);
}

/* Cards & CRUD containers */
.card, .crud-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 25px;
}

/* ======================= TABLE ======================= */
.table, table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Black header, no hover */
.table thead, table thead {
    background-color: #000000;
    color: #ffffff;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 13px;
}

.table thead tr:hover, table thead tr:hover { background-color: #000000; }

.table th, .table td, table th, table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #eaeaea;
    font-weight: 500;
}

.table tr:nth-child(even), table tr:nth-child(even) { background-color: #f9f9f9; }
.table tbody tr:hover, table tbody tr:hover { background-color: #eef3fb; }

table thead tr:first-child th:first-child { border-top-left-radius: 10px; }
table thead tr:first-child th:last-child { border-top-right-radius: 10px; }
table tr:last-child td:first-child { border-bottom-left-radius: 10px; }
table tr:last-child td:last-child { border-bottom-right-radius: 10px; }
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


/* ======================= BUTTONS ======================= */
.btn {
    padding: 8px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease-in-out;
}

.btn i { margin-right: 5px; }

.btn-primary { background-color: #007bff; color: #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }

.btn-warning { background-color: #ffc107; color: #212529; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-warning:hover { background-color: #e0a800; transform: translateY(-2px); }

.btn-secondary { background-color: #000; color: #fff; }

/* ======================= TOP BAR / SEARCH ======================= */
.search-create {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.search-create input[type="text"] {
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    height: 40px;
    width: 250px;
}

.search-create input[type="text"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 6px rgba(0,123,255,0.2);
    outline: none;
}

.top-buttons { display: flex; gap: 10px; }
.top-buttons .btn {
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    padding: 0 15px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: background 0.3s;
}
.top-buttons .btn.btn-warning { background-color: orange; color: #fff; }
.top-buttons .btn.btn-warning:hover { background-color: darkorange; }
.top-buttons .btn i { margin-right: 5px; }

/* ======================= MODAL ======================= */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
}

.modal.show, .show-modal { display: flex; animation: fadeIn 0.3s ease-in-out; }

@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 25px;
    border-radius: 10px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.modal-header { display: flex; justify-content: space-between; font-size: 20px; margin-bottom: 15px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; }

/* ======================= FORM ======================= */
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-weight: normal;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color: #007bff;
    outline: none;
}

/* ======================= VALIDATION ======================= */
.error-msg { color: red; font-size: 0.85rem; margin-top: 3px; display: block; }
input.invalid { border-color: red; }
#successMessage { color: green; margin-bottom: 10px; display: none; }

/* ======================= SMALL MODAL ======================= */
.small-modal { width: 350px; padding: 15px; }
.small-modal .form-group { margin-bottom: 10px; }
.small-modal label { font-size: 0.9rem; }
.small-modal input, .small-modal select { width: 100%; padding: 6px 8px; font-size: 0.9rem; }
.small-modal button { font-size: 0.85rem; padding: 6px 12px; }
.small-modal .modal-header { font-size: 1rem; margin-bottom: 8px; }

/* ======================= LOGO ======================= */
.sidebar img {
    width: 150px;
    height: auto;
    margin: 0 auto 0.5rem;
    display: block;
    transition: transform 0.3s ease;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}

/* ======================= RESPONSIVE ======================= */
@media screen and (max-width:768px) {
    main, .main-content { margin-left: 0; padding: 15px; width: 100%; }
    table th, table td, .table th, .table td { font-size: 12px; padding: 8px; }
    .btn { font-size: 12px; padding: 6px 12px; }
    .flex, .search-create { flex-direction: column; gap: 8px; }
}
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
        <li class="active"><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
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
<main>
    <h2>List of Advisers</h2>
    <div style="padding:20px;">
        <!-- Search and Create -->
        <div class="search-create">
            <input type="text" id="searchInput" placeholder="Search adviser name...">
            <div class="top-buttons">
                <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Create</button>
                <button id="archiveBtn" class="btn btn-warning"><i class="fas fa-archive"></i> Archive</button>
            </div>
        </div>

        <div id="successMessage">Created successfully!</div>

        <div class="table-responsive">
            <table class="table" id="list-advisers">
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

                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($advisers as $adviser)
                    <tr>
                        <td><input type="checkbox" class="student-checkbox"></td>
                        <td>{{ $adviser->adviser_fname . ' ' . $adviser->adviser_lname }}</td>
                        <td>{{ $adviser->adviser_email }}</td>
                        <td>{{ $adviser->adviser_contactinfo }}</td>
                        <td>{{ $adviser->adviser_gradelevel }}</td>
                        <td>{{ $adviser->adviser_section }}</td>
                        <td>
                            <button class="btn btn-primary" onclick="showEditModal('{{ $adviser->id }}','{{ $adviser->email }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-warning" onclick="showResetModal('{{ $adviser->id }}')">
                                <i class="fas fa-key"></i> Reset Password
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Small Modal -->
<div class="modal" id="createAdviserModal">
    <div class="modal-content small-modal">
        <div class="modal-header">Create Adviser</div>
        <form id="adviserForm" novalidate>
            @csrf
            <div class="form-group">
                <label for="fname">First Name:</label>
                <input type="text" name="fname" id="fname" placeholder="First name" required pattern="[A-Za-z\s]+">
                <small class="error-msg" id="fnameError"></small>
            </div>
            <div class="form-group">
                <label for="lname">Last Name:</label>
                <input type="text" name="lname" id="lname" placeholder="Last name" required pattern="[A-Za-z\s]+">
                <small class="error-msg" id="lnameError"></small>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="example@email.com" required>
                <small class="error-msg" id="emailError"></small>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact:</label>
                <input type="text" name="contact_number" id="contact_number" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$">
                <small class="error-msg" id="contactError"></small>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter password" required>
                <button type="button" id="togglePassword" class="btn btn-secondary" style="margin-top:5px;">Show</button>
                <small class="error-msg" id="passwordError"></small>
            </div>
            <div class="form-group">
                <label for="grade">Grade:</label>
                <select name="grade" id="grade" required>
                    <option value="">Select</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <small class="error-msg" id="gradeError"></small>
            </div>
            <div class="form-group">
                <label for="section">Section:</label>
                <input type="text" name="section" id="section" placeholder="Section" required>
                <small class="error-msg" id="sectionError"></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
// Sidebar dropdown
var dropdowns = document.getElementsByClassName("dropdown-btn");
for (let i = 0; i < dropdowns.length; i++) {
    dropdowns[i].addEventListener("click", function() {
        this.classList.toggle("active");
        let dropdownContent = this.nextElementSibling;
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    });
}

// Select All checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
});

// Modal controls
function openModal() { document.getElementById('createAdviserModal').classList.add('show-modal'); }
function closeModal() { document.getElementById('createAdviserModal').classList.remove('show-modal'); }

// Toggle password
document.getElementById('togglePassword').addEventListener('click', function() {
    let password = document.getElementById('password');
    if(password.type === 'password') {
        password.type = 'text';
        this.textContent = 'Hide';
    } else {
        password.type = 'password';
        this.textContent = 'Show';
    }
});
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
