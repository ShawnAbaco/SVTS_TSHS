<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/complaints.css') }}">
</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}" class="active"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="main-content">
    <h2>Complaints</h2>

     <!-- Top Controls: Search + Create Button -->
  <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom: 15px;">
    <input type="text" id="searchInput" placeholder="Search complaints..." style="padding:5px; margin-right:10px;">
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add Complaint</button>
  </div>

    <!-- Table -->
    <table id="complaintsTable">
      <thead>
        <tr>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Offense</th>
          <th>Sanction</th>
          <th>Incident</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($complaints as $c)
        <tr>
          <td>{{ $c->complainant->student_fname ?? 'N/A' }} {{ $c->complainant->student_lname ?? '' }}</td>
          <td>{{ $c->respondent->student_fname ?? 'N/A' }} {{ $c->respondent->student_lname ?? '' }}</td>
          <td>{{ $c->offense->offense_type ?? 'N/A' }}</td>
          <td>{{ $c->offense->sanction_consequences ?? 'N/A' }}</td>
          <td>{{ $c->complaints_incident }}</td>
          <td>{{ $c->complaints_date }}</td>
          <td>{{ $c->complaints_time }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Modal (same as your current modal) -->
    <div id="complaintModal" class="modal">
      <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h3 id="modalTitle">Create Complaint</h3>
        <form id="complaintForm">
          <label>Complainant Name</label>
          <input type="text" name="complainant" required>

          <label>Respondent</label>
          <input type="text" name="respondent" required>

          <label>Offense</label>
          <input type="text" id="offenseSearch" name="offense" placeholder="Search offense..." list="offenseList" required>
          <datalist id="offenseList">
            @foreach($offenses as $offense)
              <option value="{{ $offense->offense_type }}">
            @endforeach
          </datalist>



          <label>Incident</label>
          <textarea name="incident" rows="3" required></textarea>

          <label>Date</label>
          <input type="date" name="date" required>

          <label>Time</label>
          <input type="time" name="time" required>

          <button type="submit"><i class="fas fa-save"></i> Submit</button>
        </form>
      </div>
    </div>
  </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // --- Live Search ---
  const searchInput = document.getElementById('searchInput');
  const tableBody = document.querySelector('#complaintsTable tbody');

  function filterTable(){
    const filter = searchInput.value.toLowerCase();
    Array.from(tableBody.rows).forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  }

  searchInput.addEventListener('keyup', filterTable);

  // --- Sidebar Active Menu & Logout ---
  const menuLinks = document.querySelectorAll('.sidebar a');
  const activeLink = localStorage.getItem('activeMenu');
  if (activeLink) {
    menuLinks.forEach(link => { if(link.href === activeLink) link.classList.add('active'); });
  }
  menuLinks.forEach(link => {
    link.addEventListener('click', function() {
      menuLinks.forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      if(!this.href.includes('profile.settings')) localStorage.setItem('activeMenu', this.href);
    });
  });
  function logout() { alert('Logging out...'); }

  // --- Modal & Form Handling ---
  const modal = document.getElementById("complaintModal");
  const openBtn = document.getElementById("openModalBtn");
  const closeBtn = document.getElementById("closeModalBtn");
  const form = document.getElementById('complaintForm');
  let editingRow = null;

  openBtn.onclick = () => {
    modal.style.display = "block";
    document.getElementById('modalTitle').textContent = "Create Complaint";
    editingRow = null;
    form.reset();
  };

  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; }

  function createActionButtons(row){
    const actionsCell = row.insertCell(-1);

    const editBtn = document.createElement('button');
    editBtn.className = 'btn-orange';
    editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
    editBtn.onclick = () => editRow(row);

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'btn-red';
    deleteBtn.style.marginLeft = '5px';
    deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
    deleteBtn.onclick = () => row.remove();

    actionsCell.appendChild(editBtn);
    actionsCell.appendChild(deleteBtn);
  }

  document.querySelectorAll('.btn-edit').forEach((btn, i) => {
    btn.onclick = () => editRow(tableBody.rows[i]);
  });

  function editRow(row){
    editingRow = row;
    modal.style.display = "block";
    document.getElementById('modalTitle').textContent = "Edit Complaint";
    form.complainant.value = row.cells[0].textContent;
    form.respondent.value = row.cells[1].textContent;
    form.offense.value = row.cells[2].textContent;
    form.sanction.value = row.cells[3].textContent;
    form.incident.value = row.cells[4].textContent;
    form.date.value = row.cells[5].textContent;
    form.time.value = row.cells[6].textContent;
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    if(editingRow){
      editingRow.cells[0].textContent = form.complainant.value;
      editingRow.cells[1].textContent = form.respondent.value;
      editingRow.cells[2].textContent = form.offense.value;
      editingRow.cells[3].textContent = form.sanction.value;
      editingRow.cells[4].textContent = form.incident.value;
      editingRow.cells[5].textContent = form.date.value;
      editingRow.cells[6].textContent = form.time.value;
    } else {
      const row = tableBody.insertRow();
      row.insertCell(0).textContent = form.complainant.value;
      row.insertCell(1).textContent = form.respondent.value;
      row.insertCell(2).textContent = form.offense.value;
      row.insertCell(3).textContent = form.sanction.value;
      row.insertCell(4).textContent = form.incident.value;
      row.insertCell(5).textContent = form.date.value;
      row.insertCell(6).textContent = form.time.value;
      createActionButtons(row);
    }
    modal.style.display = 'none';
    form.reset();
    filterTable(); // ensure new rows are filtered
  });
});
</script>


</body>
</html>
