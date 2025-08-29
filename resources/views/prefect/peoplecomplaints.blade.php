<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/admin/COMPLAINTS.css') }}">
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
<!-- Content -->
<div class="content">
  <h1>Complaints Management</h1>

  <button class="btn-primary" onclick="openModal()">+ Add Complainant</button>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Offense</th>
        <th>Sanction</th>
        <th>Date</th>
        <th>Time</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="complaintTable">
      <tr>
        <td>1</td>
        <td>John Doe</td>
        <td>Jane Smith</td>
        <td>Bullying</td>
        <td>Warning</td>
        <td>2025-08-25</td>
        <td>10:30 AM</td>
        <td>
          <button class="btn-edit" onclick="editRow(this)">Edit</button>
          <button class="btn-delete" onclick="deleteRow(this)">Delete</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal" id="complaintModal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Add Complaint</h2>
      <span class="close" onclick="closeModal()">&times;</span>
    </div>

    <div class="modal-form">
      <div class="form-group">
        <label>Complainant Name</label>
        <input type="text" id="complainantName">
      </div>

      <div class="form-group">
        <label>Respondent</label>
        <input type="text" id="respondentName">
      </div>

      <div class="form-group">
        <label>Offense</label>
        <select id="offenseSelect">
          <option value="">Select Offense</option>
          <option>Bullying</option>
          <option>Cheating</option>
          <option>Disrespect</option>
          <option>Fighting</option>
        </select>
      </div>

      <div class="form-group">
        <label>Sanction</label>
        <select id="sanctionSelect">
          <option value="">Select Sanction</option>
          <option>Warning</option>
          <option>Suspension</option>
          <option>Expulsion</option>
        </select>
      </div>

      <div class="form-group full-width">
        <label>Incident</label>
        <textarea id="incidentDetails" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label>Date</label>
        <input type="date" id="incidentDate">
      </div>

      <div class="form-group">
        <label>Time</label>
        <input type="time" id="incidentTime">
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn-primary" onclick="addComplaint()">Save</button>
    </div>
  </div>
</div>

<script>
  let editRowObj = null;

  function openModal() {
    document.getElementById('complaintModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('complaintModal').style.display = 'none';
    editRowObj = null;
    clearForm();
  }

  function clearForm() {
    document.getElementById('complainantName').value = '';
    document.getElementById('respondentName').value = '';
    document.getElementById('offenseSelect').value = '';
    document.getElementById('sanctionSelect').value = '';
    document.getElementById('incidentDetails').value = '';
    document.getElementById('incidentDate').value = '';
    document.getElementById('incidentTime').value = '';
  }

  function addComplaint() {
    const name = document.getElementById('complainantName').value;
    const respondent = document.getElementById('respondentName').value;
    const offense = document.getElementById('offenseSelect').value;
    const sanction = document.getElementById('sanctionSelect').value;
    const incident = document.getElementById('incidentDetails').value;
    const date = document.getElementById('incidentDate').value;
    const time = document.getElementById('incidentTime').value;

    if (!name || !respondent || !offense || !sanction || !date || !time) {
      alert('Please fill all fields!');
      return;
    }

    const table = document.getElementById('complaintTable');

    if (editRowObj) {
      // Update existing row
      editRowObj.cells[1].innerText = name;
      editRowObj.cells[2].innerText = respondent;
      editRowObj.cells[3].innerText = offense;
      editRowObj.cells[4].innerText = sanction;
      editRowObj.cells[5].innerText = date;
      editRowObj.cells[6].innerText = time;
    } else {
      // Add new row
      const newRow = table.insertRow();
      newRow.innerHTML = `
        <td>${table.rows.length}</td>
        <td>${name}</td>
        <td>${respondent}</td>
        <td>${offense}</td>
        <td>${sanction}</td>
        <td>${date}</td>
        <td>${time}</td>
        <td>
          <button class="btn-edit" onclick="editRow(this)">Edit</button>
          <button class="btn-delete" onclick="deleteRow(this)">Delete</button>
        </td>
      `;
    }

    closeModal();
  }

  function deleteRow(btn) {
    if (confirm('Are you sure you want to delete this complaint?')) {
      const row = btn.parentNode.parentNode;
      row.parentNode.removeChild(row);
      // Update IDs
      const table = document.getElementById('complaintTable');
      for (let i = 0; i < table.rows.length; i++) {
        table.rows[i].cells[0].innerText = i + 1;
      }
    }
  }

  function editRow(btn) {
    const row = btn.parentNode.parentNode;
    editRowObj = row;
    document.getElementById('complainantName').value = row.cells[1].innerText;
    document.getElementById('respondentName').value = row.cells[2].innerText;
    document.getElementById('offenseSelect').value = row.cells[3].innerText;
    document.getElementById('sanctionSelect').value = row.cells[4].innerText;
    document.getElementById('incidentDate').value = row.cells[5].innerText;
    document.getElementById('incidentTime').value = row.cells[6].innerText;
    openModal();
  }

  function logout() {
    fetch('/logout', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/prefect/login')
      .catch(error => console.error('Logout failed:', error));
  }
</script>

</body>
</html>
