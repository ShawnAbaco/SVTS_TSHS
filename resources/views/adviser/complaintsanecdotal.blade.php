<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Complaints Anecdotal</title>
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/adviser/complaintsanecdotal.css') }}">
  
  <style>
    /* Buttons */
    .btn-primary { padding: 8px 12px; background-color: #0058f0; color:white; border:none; border-radius:5px; cursor:pointer; }
    .btn-orange { padding:5px 8px; background-color: orange; color:white; border:none; border-radius:5px; cursor:pointer; }
    .btn-red { padding:5px 8px; background-color: red; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:5px; }

    /* Modal */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:1000; }
    .modal-content { background:white; margin:10% auto; padding:20px; border-radius:10px; width:400px; }
    .close { float:right; cursor:pointer; font-size:1.5rem; }

    input, textarea { width:100%; padding:5px; margin-bottom:10px; }

    table { border-collapse: collapse; width:100%; margin-top:15px; }
    table th, table td { padding:8px; border:1px solid #ccc; text-align:left; }
    th { background:#0058f0; color:white; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px; height: auto; margin-bottom: 0;">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}" class="active"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <h2>Complaints Anecdotal</h2>

    <!-- Top Controls: Search + Add Button -->
    <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
      <input type="text" id="searchInput" placeholder="Search anecdotal complaints..." style="padding:5px; margin-right:10px;">
      <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Add</button>
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
    // Sidebar Active Menu & Logout
    const menuLinks = document.querySelectorAll('.sidebar a');
    const activeLink = localStorage.getItem('activeMenu');
    if(activeLink) menuLinks.forEach(link => { if(link.href===activeLink) link.classList.add('active'); });
    menuLinks.forEach(link => { 
      link.addEventListener('click', function(){
        menuLinks.forEach(i=>i.classList.remove('active'));
        this.classList.add('active');
        if(!this.href.includes('profile.settings')) localStorage.setItem('activeMenu', this.href);
      }); 
    });
    function logout(){ alert('Logging out...'); }

    // --- Live Search ---
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#anecdotalTable tbody');
    searchInput.addEventListener('keyup', function(){
      const filter = this.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
      });
    });

    // --- Modal Logic ---
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

    // --- Edit/Delete Buttons ---
    function createActionButtons(row){
      const actionsCell = row.insertCell(-1);
      const editBtn = document.createElement('button');
      editBtn.className='btn-orange';
      editBtn.innerHTML='<i class="fas fa-edit"></i> Edit';
      editBtn.onclick=()=> editRow(row);
      const deleteBtn = document.createElement('button');
      deleteBtn.className='btn-red';
      deleteBtn.innerHTML='<i class="fas fa-trash"></i> Delete';
      deleteBtn.style.marginLeft='5px';
      deleteBtn.onclick=()=> row.remove();
      actionsCell.appendChild(editBtn);
      actionsCell.appendChild(deleteBtn);
    }

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
        createActionButtons(row);
      }
      modal.style.display='none';
      form.reset();
    });
  </script>

</body>
</html>
