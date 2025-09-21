<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Offenses & Sanctions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>


  <!-- Small layout helpers for the toolbar -->
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
background: linear-gradient(135deg, #001818, #002222, #002f3f, #00394d);background-repeat: no-repeat;
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
  color: #e0e0e0;
  transition: background 0.3s, transform 0.2s;
}

.sidebar ul li i {
  margin-right: 12px;
  color: #cfcfcf;
  min-width: 20px;
  font-size: 16px;
}

.sidebar ul li:hover {
  background: #2d3f55; /* lighter block color hover */
  transform: translateX(5px);
  color: #fff;
}

.sidebar ul li:hover i {
  color: #00e0ff; /* neon blue icon highlight */
}

.sidebar ul li.active {
  background: #00aaff; /* strong solid highlight */
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

/* Dropdown */
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
  background: #3a4c66; /* block hover inside dropdown */
  color: #fff;
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
  background: rgba(255, 255, 255, 0.25);
  border-radius: 3px;
}

/* Main Content */
.main-content {
  margin-left: 260px;
  padding: 20px;
}

.crud-container {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.crud-container h2 {
  margin-bottom: 20px;
  font-size: 24px;
}

.form-row {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.form-row input,
.form-row button {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  flex: 1;
}

.form-row button {
  background-color: #007BFF;
  color: #fff;
  border: none;
  cursor: pointer;
  transition: background 0.3s;
}

.form-row button:hover {
  background-color: #0056b3;
}

/* ===== High Resolution Table (Updated) ===== */
table {
  width: 100%;
  min-width: 900px; /* ensure table doesn’t shrink too small */
  max-width: 1400px; /* optional, to keep it visually balanced */
  margin-top: 20px;
  font-size: 16px; /* bigger font */
  background: #fff;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

thead {
  background: linear-gradient(90deg, #007BFF, #00aaff);
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 15px;
}

th, td {
  padding: 18px 20px; /* more spacing */
  text-align: center;
}

tbody tr:nth-child(even) {
  background: #f2f6ff; /* slightly different for better readability */
}

tbody tr:hover {
  background: #e6f0ff; /* subtle hover effect */
  transform: scale(1.001);
  transition: 0.2s ease-in-out;
}

th:first-child {
  border-top-left-radius: 10px;
}

th:last-child {
  border-top-right-radius: 10px;
}


/* ===== High Resolution Buttons ===== */
.btn {
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: bold;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: all 0.25s ease-in-out;
}

.btn-primary {
  background: linear-gradient(135deg, #007BFF, #0056b3);
  color: #fff;
}
.btn-primary:hover {
  background: linear-gradient(135deg, #0056b3, #003d80);
  transform: translateY(-2px);
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  color: #000;
}
.btn-warning:hover {
  background: linear-gradient(135deg, #e0a800, #c69500);
  transform: translateY(-2px);
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545, #a71d2a);
  color: #fff;
}
.btn-danger:hover {
  background: linear-gradient(135deg, #a71d2a, #7a101d);
  transform: translateY(-2px);
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}

.modal.show {
  display: flex;
}

.modal-content {
  background: #fff;
  padding: 20px;
  width: 100%;
  max-width: 500px;
  border-radius: 8px;
  position: relative;
}

.modal-content h5 {
  margin-bottom: 15px;
}

.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  cursor: pointer;
  font-size: 18px;
}

.mb-3 {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
}

input[type="text"],
input[type="date"] {
  width: 100%;
  padding: 8px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

/* Toolbar */
.toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin: 10px 0 20px;
}
th, td {
  padding: 20px 25px;    /* more spacing for bigger table */
  text-align: center;
}

thead {
  font-size: 16px;       /* slightly bigger headers */
}

tbody tr:hover {
  background: #dbeaff;    /* slightly more visible hover */
}
/* Left group: Add button + Search + other buttons */
.toolbar .left {
  display: flex;
  align-items: center;
  gap: 10px; /* spacing between Add, Search, and other buttons */
  flex-wrap: wrap;
}

/* Remove the right group since search is now beside Add */
.toolbar .right {
  display: none;
}

#searchInput {
  width: 180px;
  padding: 8px 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
/* Logo */
.sidebar img {
  width: 150px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}

  </style>
</head>
<body>
  <!-- Sidebar -->
<div class="sidebar" id="sidebar">
        <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>  <ul>
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
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li class="active"><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

  <!-- Main Content -->
  <div class="main-content">

      <h2>Offenses & Sanctions</h2>
<!-- Toolbar: Add + Search together, all buttons right-aligned -->
<div class="toolbar" style="justify-content: flex-end;">
  <div class="left" style="display: flex; align-items: center; gap: 10px;">
    <input type="text" id="searchInput" placeholder="Search offenses..." style="width: 180px; padding: 8px; font-size: 14px;">
    <button class="btn btn-primary" id="openModalBtn"><i class="fa fa-plus"></i> Add</button>

    <button class="btn btn-warning" id="printBtn"><i class="fa fa-print"></i> Print</button>
    <button class="btn btn-danger" id="exportBtn"><i class="fa fa-file-export"></i> Export</button>
    <button onclick="openTrash()" style="border: none; padding: 8px 12px; font-size: 14px; border-radius: 6px; cursor: pointer; color: #fff; display: flex; align-items: center; gap: 5px; background: linear-gradient(135deg, #dc3545, #ff4d4d); box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
      <i class="fas fa-trash"></i> Trash
    </button>
  </div>
</div>

<!-- Table -->
<table id="offenseTable" style="min-width: 1000px; max-width: 1500px; font-size: 16px;">
  <thead>
    <tr>



      <table id="offenseTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Offense Type</th>
            <th>Description</th>
            <th>Consequences</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($offenses as $offense)
            <tr>
              <td>{{ $offense->offense_sanc_id }}</td>
              <td>{{ $offense->offense_type }}</td>
              <td>{{ $offense->offense_description }}</td>
              <td>{{ $offense->sanction_consequences }}</td>
              <td>
                <button class="btn btn-warning edit-btn"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn btn-danger delete-btn"><i class="fa fa-trash"></i> Delete</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center;">No offenses found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div class="modal" id="violationModal">
    <div class="modal-content">
      <span class="close" id="closeModalBtn">&times;</span>
      <h5 id="modalTitle">Add Offense & Sanction</h5>
      <form id="violationForm">
        <input type="hidden" id="editId">
        <div class="mb-3">
          <label for="offenseType">Offense Type</label>
          <input type="text" id="offenseType" required>
        </div>
        <div class="mb-3">
          <label for="description">Description</label>
          <input type="text" id="description" required>
        </div>
        <div class="mb-3">
          <label for="consequences">Consequences</label>
          <input type="text" id="consequences" required>
        </div>
        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
      </form>
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

// ✅ Modal, Table, Print, Export, and Edit/Add logic
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const modal = document.getElementById("violationModal");
const tableBody = document.querySelector("#offenseTable tbody");
const form = document.getElementById("violationForm");
const searchInput = document.getElementById("searchInput");
const printBtn = document.getElementById("printBtn");
const exportBtn = document.getElementById("exportBtn");
let editingRow = null;

// Open modal for Add
openModalBtn.addEventListener("click", () => {
  form.reset();
  document.getElementById("modalTitle").textContent = "Add Offense & Sanction";
  document.getElementById("saveBtn").textContent = "Save";
  modal.classList.add("show");
  editingRow = null;
});

// Close modal
closeModalBtn.addEventListener("click", () => modal.classList.remove("show"));
window.addEventListener("click", (e) => { if (e.target === modal) modal.classList.remove("show"); });

// Live search
searchInput.addEventListener("keyup", () => {
  const filter = searchInput.value.toLowerCase();
  document.querySelectorAll("#offenseTable tbody tr").forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  });
});

// Edit & Delete
tableBody.addEventListener("click", (e) => {
  const editBtn = e.target.closest(".edit-btn");
  const deleteBtn = e.target.closest(".delete-btn");

  if (editBtn) {
    editingRow = editBtn.closest("tr");
    const cells = editingRow.querySelectorAll("td");
    document.getElementById("editId").value = cells[0].textContent.trim();
    document.getElementById("offenseType").value = cells[1].textContent.trim();
    document.getElementById("description").value = cells[2].textContent.trim();
    document.getElementById("consequences").value = cells[3].textContent.trim();
    document.getElementById("modalTitle").textContent = "Edit Offense & Sanction";
    document.getElementById("saveBtn").textContent = "Update";
    modal.classList.add("show");
  }

  if (deleteBtn) {
    deleteBtn.closest("tr").remove();
  }
});

// Save (Add/Update)
form.addEventListener("submit", (e) => {
  e.preventDefault();
  const type = document.getElementById("offenseType").value.trim();
  const desc = document.getElementById("description").value.trim();
  const cons = document.getElementById("consequences").value.trim();

  if (!type || !desc || !cons) return;

  if (editingRow) {
    const cells = editingRow.querySelectorAll("td");
    cells[1].textContent = type;
    cells[2].textContent = desc;
    cells[3].textContent = cons;
  } else {
    const id = nextRowId();
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${id}</td>
      <td>${escapeHtml(type)}</td>
      <td>${escapeHtml(desc)}</td>
      <td>${escapeHtml(cons)}</td>
      <td>
        <button class="btn btn-warning edit-btn"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn btn-danger delete-btn"><i class="fa fa-trash"></i> Delete</button>
      </td>
    `;
    tableBody.appendChild(row);
  }

  modal.classList.remove("show");
  form.reset();
  editingRow = null;
});

// Print
printBtn.addEventListener("click", () => {
  const table = document.getElementById("offenseTable").cloneNode(true);
  table.querySelectorAll("tr").forEach(tr => tr.lastElementChild && tr.lastElementChild.remove());
  const style = `<style>body{font-family:Arial;padding:16px;}h2{margin-bottom:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #000;padding:8px;text-align:left;}thead th{background:#f1f1f1;}</style>`;
  const win = window.open("", "", "height=800,width=1000");
  win.document.write("<html><head><title>Offenses & Sanctions</title>");
  win.document.write(style);
  win.document.write("</head><body>");
  win.document.write("<h2>Offenses & Sanctions</h2>");
  win.document.body.appendChild(table);
  win.document.write("</body></html>");
  win.document.close();
  win.focus();
  win.print();
});

// Export CSV
exportBtn.addEventListener("click", () => {
  const csv = tableToCSV(document.getElementById("offenseTable"));
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "offenses_sanctions.csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});

function tableToCSV(table) {
  const rows = Array.from(table.querySelectorAll("tr"));
  return rows.map((row, idx) => {
    const cells = Array.from(row.querySelectorAll(idx === 0 ? "th" : "td"));
    if (cells.length && cells[cells.length - 1].textContent.trim().match(/^(Actions|Edit|Delete)/i)) cells.pop();
    return cells.map(c => csvEscape(c.textContent.trim())).join(",");
  }).join("\n");
}

function csvEscape(text) {
  const needsQuote = /[",\n]/.test(text);
  const escaped = text.replace(/"/g, '""');
  return needsQuote ? `"${escaped}"` : escaped;
}

function nextRowId() {
  const ids = Array.from(tableBody.querySelectorAll("tr td:first-child"))
    .map(td => parseInt(td.textContent.trim(), 10))
    .filter(n => !isNaN(n));
  return ids.length ? Math.max(...ids) + 1 : 1;
}

function escapeHtml(str) {
  return str.replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#39;");
}

// ✅ Logout
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
</script>

</body>
</html>
