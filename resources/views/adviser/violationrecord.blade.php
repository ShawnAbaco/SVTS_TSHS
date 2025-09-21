<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violation Logging</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adviser/violationrecord.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

 <!-- SIDEBAR -->

<nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
        <img src="/images/Logo.png" alt="Logo">
        <p>ADVISER</p>
    </div>
    <ul>
        <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
        <li><a href="{{ route('parent.list') }}" ><i class="fas fa-user-friends"></i> Parent List</a></li>

        <!-- Violations Dropdown -->
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
            <ul class="dropdown-container">
                 <li><a href="{{ route('violation.record') }}" class="active">Violation Records</a></li>
                <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
                <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
            </ul>
        </li>

        <!-- Complaints Dropdown -->
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
            <ul class="dropdown-container">
                <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
                <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
                <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
            </ul>
        </li>

        <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
        <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
<li>
    <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>    </ul>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="header">
    <h2>Violation Logging</h2>
    <div class="actions">

         @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search records...">
      </div>
      <button class="btn btn-info" onclick="openAddModal()">
        <i class="fas fa-plus-circle"></i> Add Violation Record
        <button class="btn-archive" id="archivesBtn">
  <i class="fas fa-archive"></i> Archives
</button>

    </div>
  </div>

<!-- VIOLATION RECORDS TABLE -->
<div class="violation-table-container">
  <table id="violationTable">
    <thead>
      <tr>
        <th style="text-align: center;">
          <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
            <input type="checkbox" id="selectAll">
            <button type="button" class="btn-trash-small" title="Delete Selected">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </th>
        <th>Student</th>
        <th>Violation</th>
        <th>Incident</th>
        <th>Date</th>
        <th>Time</th>
        <th>Sanction</th>
        <th>Status</th> <!-- ✅ Added Status Column -->
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($violations as $v)
        <tr data-id="{{ $v->violation_id }}" data-student-id="{{ $v->student->student_id }}">
          <td><input type="checkbox" class="rowCheckbox"></td>
          <td>{{ $v->student->student_fname }} {{ $v->student->student_lname }}</td>
          <td>{{ $v->offense->offense_type ?? 'N/A' }}</td>
          <td>{{ $v->violation_incident }}</td>
          <td>{{ \Carbon\Carbon::parse($v->violation_date)->format('M d, Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($v->violation_time)->format('h:i A') }}</td>
          <td>{{ $v->offense->sanction_consequences ?? 'N/A' }}</td>
          <td>
            @if($v->status === 'active')
              <span class="status-badge active">Active</span>
            @else
              <span class="status-badge inactive">Inactive</span>
            @endif
          </td>
          <td>
            <button class="btn-edit" data-id="{{ $v->violation_id }}"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-delete" data-id="{{ $v->violation_id }}"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" style="text-align:center; padding:10px;">No violation records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>


<!-- ADD VIOLATION MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Violation Record</h2>
    <form id="addViolationForm" method="POST" action="{{ route('adviser.storeViolation') }}">
      @csrf

      <!-- Student Selection -->
      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <!-- Offense Selection -->
      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <!-- Incident -->
      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" placeholder="Incident Details" required
               pattern="^[A-Za-z0-9\s.,#()!?-]+$"
               title="Only letters, numbers, spaces, commas, periods, dashes, (), !, and ? are allowed">
      </div>

      <!-- Date -->
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date"
               max="<?php echo date('Y-m-d'); ?>" required
               title="Date cannot be in the future">
      </div>

      <!-- Time -->
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" required>
      </div>

      <!-- Save Button -->
      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT VIOLATION MODAL -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Violation Record</h2>
    <form id="editViolationForm">
      @csrf
      @method('PUT')
      <input type="hidden" name="violation_id" id="editViolationId">

      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" id="editStudent" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" id="editOffense" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" id="editIncident" required>
      </div>

      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date" id="editDate" required>
      </div>

      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" id="editTime" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn"><i class="fas fa-save"></i> Update</button>
      </div>
    </form>
  </div>
</div>
<!-- ARCHIVE MODAL -->
<div class="modal" id="archiveModal">
  <div class="modal-content" style="max-width: 800px; width: 90%;">
    <button class="close-btn" onclick="closeModal('archiveModal')">&times;</button>
    <h2>Archived Violations</h2>

    <!-- Search in archive -->
    <div class="search-box" style="margin-bottom: 10px;">
      <input type="text" id="archiveSearch" placeholder="Search archived records...">
    </div>

    <!-- Archive Table -->
    <div class="violation-table-container">
      <table id="archiveTable">
        <thead>
          <tr>
            <th>Student</th>
            <th>Violation</th>
            <th>Incident</th>
            <th>Date</th>
            <th>Time</th>
            <th>Sanction</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Filled dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
  const archive = []; // store archived rows

  // Trash button (move selected to archive)
  document.querySelector(".btn-trash-small").addEventListener("click", function () {
    const rows = document.querySelectorAll("#violationTable tbody tr");
    rows.forEach(row => {
      const checkbox = row.querySelector(".rowCheckbox");
      if (checkbox && checkbox.checked) {
        archive.push(row.innerHTML); // save row content
        row.remove(); // remove from main table
      }
    });
    alert("Selected records moved to archive.");
  });

  // Open Archive Modal
  document.getElementById("archivesBtn").addEventListener("click", function () {
    const tbody = document.querySelector("#archiveTable tbody");
    tbody.innerHTML = "";

    archive.forEach((rowHtml, index) => {
      const tr = document.createElement("tr");
      tr.innerHTML = rowHtml;

      // replace Actions column with Restore button
      tr.querySelector("td:last-child").innerHTML =
        `<button class="btn-info restoreBtn" data-index="${index}">
           <i class="fas fa-undo"></i> Restore
         </button>`;

      tbody.appendChild(tr);
    });

    openModal("archiveModal");
  });

  // Restore from archive
  document.querySelector("#archiveTable tbody").addEventListener("click", function (e) {
    if (e.target.closest(".restoreBtn")) {
      const btn = e.target.closest(".restoreBtn");
      const index = btn.getAttribute("data-index");

      // restore row to main table
      const tbody = document.querySelector("#violationTable tbody");
      const tr = document.createElement("tr");
      tr.innerHTML = archive[index];
      tbody.appendChild(tr);

      // remove from archive
      archive.splice(index, 1);
      btn.closest("tr").remove();
    }
  });

  // Search inside archive
  document.getElementById("archiveSearch").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#archiveTable tbody tr").forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // --- Keep modal helpers ---
  window.openModal = function(id) {
    document.getElementById(id).classList.add("show");
  };
  window.closeModal = function(id) {
    document.getElementById(id).classList.remove("show");
  };
});
     // Dropdown functionality - auto close others & scroll
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // close all other dropdowns
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this) {
                otherBtn.nextElementSibling.classList.remove('show');
                otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
            }
        });

        // toggle clicked dropdown
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
            container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

        // scroll into view if dropdown is opened
        if(container.classList.contains('show')){
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  // --- Modal controls ---
  function openModal(id) { document.getElementById(id).classList.add("show"); }
  function closeModal(id) { document.getElementById(id).classList.remove("show"); }
  window.onclick = function(event) {
    let addModal = document.getElementById("addModal");
    let editModal = document.getElementById("editModal");
    if (event.target === addModal) closeModal("addModal");
    if (event.target === editModal) closeModal("editModal");
  };

  // --- Open Add Modal ---
  window.openAddModal = function() { openModal("addModal"); };

  // --- Live Search ---
  document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#violationTable tbody tr").forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // --- Logout ---
  window.logout = function() {
    if (confirm("Are you sure you want to log out?")) {
      window.location.href = "/adviser/login";
    }
  };

  // --- Handle Add (AJAX POST) ---
  document.getElementById("addViolationForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const v = data.violation;
          let tbody = document.querySelector("#violationTable tbody");

          let newRow = document.createElement("tr");
          newRow.setAttribute("data-id", v.violation_id);
          newRow.setAttribute("data-student-id", v.violator_id);

          newRow.innerHTML = `
            <td>${v.student ? v.student.student_fname + " " + v.student.student_lname : "N/A"}</td>
            <td>${v.offense ? v.offense.offense_type : "N/A"}</td>
            <td>${v.violation_incident}</td>
            <td>${v.violation_date}</td>
            <td>${v.violation_time}</td>
            <td>${v.offense ? v.offense.sanction_consequences : "N/A"}</td>
            <td>
              <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </td>
          `;
          tbody.appendChild(newRow);

          closeModal("addModal");
          document.getElementById("addViolationForm").reset();
          alert("Violation added successfully!");
        }
      })
      .catch(err => console.error("Error:", err));
  });

  // --- Event Delegation for Edit & Delete ---
  document.querySelector("#violationTable tbody").addEventListener("click", function(e) {
    const row = e.target.closest("tr");
    if (!row) return;

    // Handle Edit
    if (e.target.closest(".btn-edit")) {
      const id = row.getAttribute("data-id");
      const studentId = row.getAttribute("data-student-id");
      const cells = row.querySelectorAll("td");

      document.getElementById("editViolationId").value = id;
      document.getElementById("editStudent").value = studentId;
      document.getElementById("editIncident").value = cells[2].innerText;
      document.getElementById("editDate").value = cells[3].innerText;
      document.getElementById("editTime").value = cells[4].innerText;

      openModal("editModal");
    }

    // Handle Delete
    if (e.target.closest(".btn-delete")) {
      if (!confirm("Are you sure you want to delete this violation?")) return;
      const id = row.getAttribute("data-id");

      fetch(`/adviser/violations/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrfToken }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation deleted successfully!");
          row.remove();
        }
      })
      .catch(err => console.error("Error:", err));
    }
  });

  // --- Handle Update (AJAX PUT) ---
  document.getElementById("editViolationForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const id = document.getElementById("editViolationId").value;
    const formData = new FormData(this);

    fetch(`/adviser/violation/${id}`, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken, "X-HTTP-Method-Override": "PUT" },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation updated successfully!");
          location.reload();
        }
      });
  });
});
// --- Select All Checkboxes ---
document.getElementById("selectAll").addEventListener("change", function () {
  const checkboxes = document.querySelectorAll(".rowCheckbox");
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// --- Keep Select All in sync ---
document.addEventListener("change", function (e) {
  if (e.target.classList.contains("rowCheckbox")) {
    const all = document.querySelectorAll(".rowCheckbox");
    const checked = document.querySelectorAll(".rowCheckbox:checked");
    document.getElementById("selectAll").checked = all.length === checked.length;
  }
});

</script>


</body>
</html>
