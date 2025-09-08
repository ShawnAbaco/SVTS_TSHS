<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Offenses & Sanctions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link rel="stylesheet" href="{{ asset('css/admin/OFFENSE&SANCTION.css') }}">

  <!-- Small layout helpers for the toolbar -->
  <style>
    .toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin:10px 0 0}
    .toolbar .left,.toolbar .right{display:flex;align-items:center;gap:8px}
    #searchInput{min-width:260px;padding:10px;border:1px solid #ccc;border-radius:5px}
    .btn{display:inline-flex;align-items:center;gap:6px}
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
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li class="active"><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="crud-container">
      <h2>Offenses & Sanctions</h2>

      <!-- Toolbar: search left, actions right -->
      <div class="toolbar">
        <div class="left">
          <input type="text" id="searchInput" placeholder="Search offenses...">
        </div>
        <div class="right">
          <button class="btn btn-primary" id="openModalBtn"><i class="fa fa-plus"></i> Add Offense & Sanction</button>
          <button class="btn btn-warning" id="printBtn"><i class="fa fa-print"></i> Print</button>
          <button class="btn btn-danger" id="exportBtn"><i class="fa fa-file-export"></i> Export CSV</button>
        </div>
      </div>

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
  fetch('/logout', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => window.location.href='/prefect/login')
    .catch(error => console.error('Logout failed:', error));
}
</script>

</body>
</html>
