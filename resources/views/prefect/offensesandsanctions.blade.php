<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Offenses & Sanctions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  
 <link rel="stylesheet" href="{{ asset('css/admin/OFFENSE&SANCTION.css') }}">
</head>
<body>
   <!-- Sidebar -->
    <div class="sidebar">
        <p>PREFECT DASHBOARD</p>
        <ul>
           <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
            <li><a href="{{ route('parent.lists') }}"><i class="fas fa-user-graduate"></i> Parent List </a></li>
            <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
            <li><a href="{{ route('violation.records') }}"><i class="fas fa-gavel"></i> Violation Record </a></li>
            <li><a href="{{ route('violation.appointments') }}"><i class="fas fa-bell"></i> Violation Appointments </a></li>
            <li><a href="{{ route('violation.anecdotals') }}"><i class="fas fa-chart-line"></i> Violation Anecdotal </a></li>
            <li><a href="{{ route('people.complaints') }}"><i class="fas fa-users"></i> Complaints</a></li>
            <li><a href="{{ route('complaints.appointments') }}"><i class="fas fa-cogs"></i> Complaints Appointments</a></li>
            <li><a href="{{ route('complaints.anecdotals') }}"><i class="fas fa-book"></i> Complaints Anecdotal</a></li>
            <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense&Sanctions </a></li>
             <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
            <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>
  <!-- Main Content -->
  <div class="main-content">
    <div class="crud-container">
      <h2>Offenses & Sanctions</h2>

      <button class="btn btn-primary" id="openModalBtn">Add Offense & Sanction</button>

      <table>
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
                <button class="edit-btn">Edit</button>
                <button class="delete-btn">Delete</button>
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
      <span class="close" onclick="document.getElementById('violationModal').classList.remove('show')">&times;</span>
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
    const openModalBtn = document.getElementById("openModalBtn");
    const modal = document.getElementById("violationModal");
    const tableBody = document.querySelector("tbody");
    const form = document.getElementById("violationForm");
    let editingRow = null;

    openModalBtn.addEventListener("click", () => {
      form.reset();
      document.getElementById("modalTitle").textContent = "Add Offense & Sanction";
      document.getElementById("saveBtn").textContent = "Save";
      modal.classList.add("show");
      editingRow = null;
    });

    tableBody.addEventListener("click", function(e) {
      if(e.target.classList.contains("edit-btn")) {
        editingRow = e.target.closest("tr");
        const cells = editingRow.querySelectorAll("td");
        document.getElementById("editId").value = cells[0].textContent;
        document.getElementById("offenseType").value = cells[1].textContent;
        document.getElementById("description").value = cells[2].textContent;
        document.getElementById("consequences").value = cells[3].textContent;
        document.getElementById("modalTitle").textContent = "Edit Offense & Sanction";
        document.getElementById("saveBtn").textContent = "Update";
        modal.classList.add("show");
      }
      if(e.target.classList.contains("delete-btn")) {
        e.target.closest("tr").remove();
      }
    });

    form.addEventListener("submit", function(e) {
      e.preventDefault();
      const type = document.getElementById("offenseType").value;
      const desc = document.getElementById("description").value;
      const cons = document.getElementById("consequences").value;

      if(editingRow) {
        const cells = editingRow.querySelectorAll("td");
        cells[1].textContent = type;
        cells[2].textContent = desc;
        cells[3].textContent = cons;
      } else {
        const id = tableBody.querySelectorAll("tr").length + 1;
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${id}</td>
          <td>${type}</td>
          <td>${desc}</td>
          <td>${cons}</td>
          <td>
            <button class="btn btn-warning edit-btn">Edit</button>
            <button class="btn btn-danger delete-btn">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      }

      modal.classList.remove("show");
      form.reset();
      editingRow = null;
    });

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
