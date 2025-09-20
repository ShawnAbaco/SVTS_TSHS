<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Anecdotal</title>

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>


</head>
<body>
<style>
:root {
  --primary-color: rgb(134, 142, 142);
  --secondary-color: #ffffff;
  --hover-bg: rgb(0, 88, 240);
  --hover-active-bg: rgb(0, 120, 255);
  --shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}



/* --- Body --- */
body {
  background-color: var(--secondary-color);
  min-height: 100vh;
  display: flex;

 color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;
}

p, span, a, li, label {
  color: rgb(255, 255, 255);
  font-weight: bold;
  font-family: "Arial", sans-serif;
}

h1, h2, h3, h4, h5, h6 {
  color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;
}

button, input, textarea, select, th, td {
  color: black;
  font-weight: bold;
  font-family: "Arial", sans-serif;
}
/* --- Reset Margin & Padding --- */
body, div, p {
  margin: 0;
  padding: 0;
}

ul, ol, li {
  margin: 0;
  padding: 0;
}

h1, h2, h3, h4, h5, h6 {
  margin: 0;
  padding: 0;
}

table, th, td {
  margin: 0;
  padding: 0;
}

form {
  margin: 0;
  padding: 0;
}

/* --- Box Sizing --- */
body, div, p {
  box-sizing: border-box;
}

ul, ol, li {
  box-sizing: border-box;
}

h1, h2, h3, h4, h5, h6 {
  box-sizing: border-box;
}

table, th, td, form {
  box-sizing: border-box;
}

input, textarea, select, button {
  box-sizing: border-box;
}

/* --- Sidebar --- */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100%;
  background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
  font-family: "Segoe UI", Tahoma, sans-serif;
  z-index: 1000;
  overflow-y: auto;
  transition: all 0.3s ease;
  color: #ffffff;
  font-weight: bold;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  image-rendering: optimizeQuality;
}

/* Sidebar scroll */
.sidebar::-webkit-scrollbar { width: 8px; }
.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}
.sidebar::-webkit-scrollbar-track { background-color: rgba(255, 255, 255, 0.05); }

/* Logo */
.sidebar img {
  width: 180px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}

/* Sidebar Title */
.sidebar p {
  font-size: 1.6rem;
  font-weight: 900;
  margin: 0 0 1rem;
  color: #ffffff;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0,0,0,0.4);
}

/* Sidebar Links */
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 22px;
  color: #ffffff;
  text-decoration: none;
  font-size: 1rem;
  font-weight: bold;
  border-left: 4px solid transparent;
  border-radius: 8px;
  transition: all 0.3s ease;
}
.sidebar ul li a i {
  font-size: 1.2rem;
  min-width: 22px;
  text-align: center;
  color: #ffffff;
  transition: color 0.3s ease;
}

/* Hover & Active */
.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: rgba(255,255,255,0.15);
  border-left-color: #FFD700;
  color: #ffffff;
}

/* Dropdown */
.dropdown-container {
  max-height: 0;
  overflow: hidden;
  background-color: rgba(255,255,255,0.05);
  transition: max-height 0.4s ease, padding 0.4s ease;
  border-left: 2px solid rgba(255,255,255,0.1);
  border-radius: 0 8px 8px 0;
}
.dropdown-container.show { max-height: 400px; padding-left: 12px; }
.dropdown-container li a {
  font-size: 0.9rem;
  padding: 10px 20px;
  color: #ffffff;
  font-weight: bold;
}
.dropdown-container li a:hover { background-color: rgba(255,255,255,0.15); color: #ffffff; }
.dropdown-btn .fa-caret-down {
  margin-left: auto;
  transition: transform 0.3s ease;
  color: #ffffff;
}

/* --- Main content --- */
.main-content {
  margin-left: 260px;
  padding: 2rem;
  flex-grow: 1;
}

/* Toolbar */
.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}
.toolbar h1 {
  font-size: 1.8rem;
  font-weight: bold;
  margin: 0;
  color: #000;
}
.toolbar-actions { display: flex; align-items: center; gap: 12px; }
.toolbar-actions input,
.toolbar-actions button { height: 46px; font-size: 0.95rem; border-radius: 8px; padding: 0 14px; font-weight: bold; box-shadow: var(--shadow); }

/* Search box */
#searchInput {
  width: 280px;
  border: 1px solid #ccc;
  outline: none;
  transition: all 0.3s ease;
}
#searchInput:focus { border-color: var(--hover-bg); box-shadow: var(--shadow); }

/* Buttons */
.btn-add, .btn-primary { background-color: #0058f0; color: #fff; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
.btn-add:hover, .btn-primary:hover { background-color: var(--hover-active-bg); transform: translateY(-2px); }

.btn-archive { background: linear-gradient(135deg, #ff7f50, #e55300); color: #fff; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
.btn-archive:hover { transform: translateY(-2px); box-shadow: 0 5px 12px rgba(0,0,0,0.25); opacity: 0.95; }

.btn-orange { background-color: orange; color: #fff; }
.btn-orange:hover { background-color: darkorange; }
.btn-red { background-color: red; color: #fff; margin-left: 5px; }
.btn-red:hover { background-color: darkred; }

/* Table */
.complaints-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: var(--shadow);
}
.complaints-table th, .complaints-table td { padding: 12px; text-align: left; font-size: 0.9rem; }
.complaints-table th { background-color: black; color: white; font-size: 0.95rem; }
.complaints-table tr:nth-child(even) { background-color: #f5f5f5; }
.complaints-table tr:hover { background-color: #e9f0ff; }

/* Modal */
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:1000; }
.modal-content { background:white; margin:8% auto; padding:20px; border-radius:10px; width:400px; box-shadow: var(--shadow); }
.close { float:right; cursor:pointer; font-size:1.5rem; }
input, textarea, select { width:100%; padding:7px; margin-bottom:10px; border:1px solid #ccc; border-radius:5px; }
input:focus, textarea:focus, select:focus { border-color: var(--hover-bg); box-shadow: var(--shadow); }
.modal-content button { align-self:flex-end; }

</style>

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div style="text-align: center; margin-bottom: 1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li>
        <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
        <ul class="dropdown-container">
          <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
          <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
          <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
        </ul>
      </li>
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
   <div class="toolbar">
  <h1>Complaints Anecdotal</h1>
  <div class="toolbar-actions">
    <input type="text" id="searchInput" placeholder="Search..." />
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add</button>
    <button class="btn-archive" id="archivesBtn"><i class="fas fa-archive"></i> Archives</button>
  </div>
</div>


    <!-- Modal -->
    <div id="anecdotalModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle">Add Anecdotal Complaint</h3>
        <form id="anecdotalForm">
          <label>Complainant</label>
          <input type="text" name="complainant" required>

          <label>Respondent</label>
          <input type="text" name="respondent" required>

          <label>Solution</label>
          <textarea name="solution" rows="2" required></textarea>

          <label>Recommendation</label>
          <textarea name="recommendation" rows="2" required></textarea>

          <label>Date</label>
          <input type="date" name="date" required>

          <label>Time</label>
          <input type="time" name="time" required>

          <button type="submit" class="btn-primary">Save</button>
        </form>
      </div>
    </div>

    <!-- Table -->
    <table id="anecdotalTable" class="complaints-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Solution</th>
          <th>Recommendation</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($anecdotal as $item)
        <tr>
          <td>{{ $item->comp_anec_id }}</td>
          <td>{{ $item->complaint->complainant->student_fname ?? 'N/A' }} {{ $item->complaint->complainant->student_lname ?? '' }}</td>
          <td>{{ $item->complaint->respondent->student_fname ?? 'N/A' }} {{ $item->complaint->respondent->student_lname ?? '' }}</td>
          <td>{{ $item->comp_anec_solution }}</td>
          <td>{{ $item->comp_anec_recommendation }}</td>
          <td>{{ $item->comp_anec_date }}</td>
          <td>{{ $item->comp_anec_time }}</td>
          <td>
            <button class="btn-orange btn-edit"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn-red btn-delete"><i class="fas fa-trash"></i> Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <script>
    // Dropdown toggle
    const dropdowns = document.querySelectorAll('.dropdown-btn');
    dropdowns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        dropdowns.forEach(other => {
          if (other !== this) {
            other.nextElementSibling.classList.remove('show');
            other.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
          }
        });
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
          container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
      });
    });

    // Logout
    function logout(){ alert('Logging out...'); }

    // Search filter
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#anecdotalTable tbody');
    searchInput.addEventListener('keyup', function(){
      const filter = this.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
      });
    });

    // Modal logic
    const modal = document.getElementById("anecdotalModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = document.querySelector(".close");
    const form = document.getElementById("anecdotalForm");
    let editingRow = null;

    openBtn.onclick = () => {
      modal.style.display="block";
      form.reset();
      editingRow=null;
      document.getElementById('modalTitle').textContent='Add Anecdotal Complaint';
    }
    closeBtn.onclick = () => modal.style.display="none";
    window.onclick = e => { if(e.target==modal) modal.style.display="none"; }

    document.querySelectorAll('.btn-edit').forEach((btn,i)=>{
      btn.onclick=()=> editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow=row;
      modal.style.display='block';
      document.getElementById('modalTitle').textContent='Edit Anecdotal Complaint';
      form.complainant.value=row.cells[1].textContent;
      form.respondent.value=row.cells[2].textContent;
      form.solution.value=row.cells[3].textContent;
      form.recommendation.value=row.cells[4].textContent;
      form.date.value=row.cells[5].textContent;
      form.time.value=row.cells[6].textContent;
    }

    form.addEventListener('submit', e=>{
      e.preventDefault();
      if(editingRow){
        editingRow.cells[1].textContent=form.complainant.value;
        editingRow.cells[2].textContent=form.respondent.value;
        editingRow.cells[3].textContent=form.solution.value;
        editingRow.cells[4].textContent=form.recommendation.value;
        editingRow.cells[5].textContent=form.date.value;
        editingRow.cells[6].textContent=form.time.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = tableBody.rows.length+1;
        row.insertCell(1).textContent = form.complainant.value;
        row.insertCell(2).textContent = form.respondent.value;
        row.insertCell(3).textContent = form.solution.value;
        row.insertCell(4).textContent = form.recommendation.value;
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        const actions = row.insertCell(7);
        actions.innerHTML = '<button class="btn-orange"><i class="fas fa-edit"></i> Edit</button> <button class="btn-red"><i class="fas fa-trash"></i> Delete</button>';
      }
      modal.style.display='none';
      form.reset();
    });
  </script>
</body>
</html>
