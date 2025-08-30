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
<nav class="sidebar">
  <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
    <img src="/images/Logo.png" alt="Logo" style="width: 200px;">
    <p>ADVISER</p>
  </div>
  <ul style="list-style: none; padding: 0; margin: 0;">
    <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
    <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
    <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
    <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
    <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
    <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
    <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
    <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
    <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
    <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
    <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="header">
    <h2>Violation Logging</h2>
    <div class="actions">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search records...">
      </div>
      <button class="btn btn-info" onclick="openAddModal()">
        <i class="fas fa-plus-circle"></i> Add Violation Record
      </button>
    </div>
  </div>

  <!-- VIOLATION RECORDS TABLE -->
  <div class="violation-table-container">
    <table id="violationTable">
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
        @foreach($violations as $v)
        <tr data-id="{{ $v->violation_id }}" data-student-id="{{ $v->student->student_id }}">
          <td>{{ $v->student->student_fname }} {{ $v->student->student_lname }}</td>
          <td>{{ $v->offense->offense_type }}</td>
          <td>{{ $v->violation_incident }}</td>
          <td>{{ $v->violation_date }}</td>
          <td>{{ $v->violation_time }}</td>
          <td>{{ $v->offense->sanction_consequences }}</td>
       
          <td>
            <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- ADD VIOLATION MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Violation Record</h2>
    <form id="addViolationForm" method="POST" action="{{ route('adviser.storeViolation') }}">
      @csrf
      <div class="form-group">
        <label for="student">Student</label>
        <select name="student_id" required>
          <option value="">Select Student</option>
          @foreach($students as $s)
            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="offense">Offense</label>
        <select name="offense_sanc_id" required>
          <option value="">Select Offense</option>
          @foreach($offenses as $o)
            <option value="{{ $o->offense_sanc_id }}">{{ $o->offense_type }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="incident">Incident</label>
        <input type="text" name="violation_incident" placeholder="Incident Details" required>
      </div>
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" name="violation_date" required>
      </div>
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" name="violation_time" required>
      </div>
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
<script>
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
</script>


</body>
</html>
