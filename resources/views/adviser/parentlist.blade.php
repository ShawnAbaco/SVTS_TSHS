<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --hover-bg: rgb(0, 88, 240);
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    * {
      color: black;
      font-weight: bold;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: "Arial", sans-serif;
      background-color: var(--secondary-color);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      display: flex;
    }
    /* Sidebar */
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
    }
    .sidebar::-webkit-scrollbar { width: 8px; }
    .sidebar::-webkit-scrollbar-thumb {
      background-color: rgba(255, 255, 255, 0.3);
      border-radius: 4px;
    }
    .sidebar::-webkit-scrollbar-track {
      background-color: rgba(255, 255, 255, 0.05);
    }
    .sidebar img {
      width: 180px;
      height: auto;
      margin: 0 auto 0.5rem;
      display: block;
      transition: transform 0.3s ease;
    }
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
    .sidebar ul li a i { font-size: 1.2rem; min-width: 22px; text-align: center; color: #ffffff; }
    .sidebar ul li a:hover, .sidebar ul li a.active {
      background-color: rgba(255,255,255,0.15);
      border-left-color: #FFD700;
      color: #ffffff !important;
    }
    .dropdown-container {
      max-height: 0;
      overflow: hidden;
      background-color: rgba(255,255,255,0.05);
      transition: max-height 0.4s ease, padding 0.4s ease;
      border-left: 2px solid rgba(255,255,255,0.1);
      border-radius: 0 8px 8px 0;
    }
    .dropdown-container.show {
      max-height: 400px;
      padding-left: 12px;
    }
    .dropdown-container li a {
      font-size: 0.9rem;
      padding: 10px 20px;
      color: #ffffff;
      font-weight: bold;
    }
    .dropdown-container li a:hover { background-color: rgba(255,255,255,0.15); color: #fff; }
    .dropdown-btn .fa-caret-down {
      margin-left: auto;
      transition: transform 0.3s ease;
      color: #fff;
    }
    /* Main Content */
    .main-content { margin-left: 260px; padding: 2rem; width: calc(100% - 260px); }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
    .header h1 { font-size: 22px; margin: 0; }
    .actions { display: flex; justify-content: flex-end; align-items: center; gap: 12px; flex-wrap: wrap; }
    .actions input, .actions button { height: 44px; font-size: 15px; border-radius: 8px; padding: 0 15px; font-weight: 600; }
    .search-box input { border: 1px solid #ccc; box-shadow: 0 3px 8px rgba(0,0,0,0.08); min-width: 240px; }
    .btn { border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s ease; box-shadow: 0 3px 6px rgba(0,0,0,0.15); }
    .btn i { font-size: 16px; }
    .btn-info { background: linear-gradient(135deg, #17a2b8, #117a8b); color: #fff; }
    .btn-edit { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
    .btn-danger { background: linear-gradient(135deg, #dc3545, #b02a37); color: #fff; }
    .btn-archive { background-color: #fd7e14; color: #fff; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 12px rgba(0,0,0,0.2); opacity: 0.95; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 25px; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); font-size: 16px; }
    table th, table td { padding: 14px 18px; text-align: center; vertical-align: middle; }
    table th { background-color: rgb(0, 0, 0); color: #fff; font-weight: 600; font-size: 1rem; }
    table tr:nth-child(even) { background-color: #f7f7f7; }
    table tr:hover { background-color: rgba(0,0,0,0.05); }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; width: 100%; position: relative; box-shadow: var(--shadow); }
    .modal-content h2 { margin-bottom: 1rem; }
    .modal-content input, .modal-content select { width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
    .modal-content .close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px; color: red; }
  </style>
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
    <li><a href="{{ route('parent.list') }}" class="active"><i class="fas fa-user-friends"></i> Parent List</a></li>
    <!-- Violations Dropdown -->
    <li>
      <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down"></i></a>
      <ul class="dropdown-container">
        <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
        <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
        <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
      </ul>
    </li>
    <!-- Complaints Dropdown -->
    <li>
      <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down"></i></a>
      <ul class="dropdown-container">
        <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
        <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
        <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
      </ul>
    </li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
    <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
    <li>
      <form id="logout-form" action="{{ route('adviser.logout') }}" method="POST" style="display: none;">@csrf</form>
      <a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="header">
    <h1>Parent List</h1>
    <div class="actions">
      <input type="text" id="searchInput" placeholder="Search parent...">
      <button class="btn btn-info" onclick="openAddModal()"><i class="fas fa-plus-circle"></i> Add Parent/Guardian</button>
      <button class="btn btn-archive" onclick="openModal('archiveModal')"><i class="fas fa-trash-alt"></i> Trash</button>
    </div>
  </div>

  <!-- ACTIVE PARENTS TABLE -->
  <div id="activeParentsTable">
    <table id="parentTable-active">
      <thead>
        <tr>
          <th>Name</th>
          <th>Sex</th>
          <th>Birthdate</th>
          <th>Contact Number</th>
          <th>Email</th>
          <th>Relationship</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($parents['active'] ?? [] as $parent)
        <tr data-id="{{ $parent->parent_id }}"
            data-students='@json($parent->students->map(fn($s) => ["name"=>$s->student_fname." ".$s->student_lname,"contact"=>$s->student_contactinfo,"adviser_id"=>$s->adviser_id]))'>
          <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
          <td>{{ ucfirst($parent->parent_sex ?? 'N/A') }}</td>
          <td>{{ $parent->parent_birthdate }}</td>
          <td>{{ $parent->parent_contactinfo }}</td>
          <td>{{ $parent->parent_email ?? '-' }}</td>
          <td>{{ $parent->parent_relationship ?? '-' }}</td>
          <td><span class="badge badge-success">Active</span></td>
          <td class="actions">
            <button class="btn btn-info" onclick="showInfo(
              '{{ $parent->parent_fname }} {{ $parent->parent_lname }}',
              '{{ $parent->parent_birthdate }}',
              '{{ $parent->parent_contactinfo }}',
              '{{ $parent->parent_email ?? '' }}',
              '{{ $parent->parent_relationship ?? '' }}',
              this.closest('tr').dataset.students
            )"><i class="fas fa-info-circle"></i> Info</button>
            <button class="btn btn-edit" onclick="editGuardian(this)"><i class="fas fa-edit"></i> Edit</button>
            <form method="POST" action="{{ route('parents.destroy',$parent->parent_id) }}" style="display:inline;">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Move to Trash</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- ARCHIVES MODAL -->
<div class="modal" id="archiveModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('archiveModal')">&times;</span>
    <h2>Inactive Parents (Trash)</h2>
    <table id="parentTable-inactive">
      <thead>
        <tr>
          <th>Name</th>
          <th>Birthdate</th>
          <th>Contact</th>
          <th>Relationship</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($parents['inactive'] ?? [] as $parent)
        <tr data-id="{{ $parent->parent_id }}">
          <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
          <td>{{ $parent->parent_birthdate }}</td>
          <td>{{ $parent->parent_contactinfo }}</td>
          <td>{{ $parent->parent_relationship ?? '-' }}</td>
          <td><span class="badge badge-secondary">Inactive</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addModal')">&times;</span>
    <h2 id="modalTitle">Add Parent/Guardian</h2>
    <form id="addParentForm" method="POST" action="{{ route('parents.store') }}">
      @csrf
      <div id="methodField"></div>
      <input type="hidden" id="parent_id">
      <label>First Name</label>
      <input type="text" name="parent_fname" id="parent_fname" required>
      <label>Last Name</label>
      <input type="text" name="parent_lname" id="parent_lname" required>
      <label>Birthdate</label>
      <input type="date" name="parent_birthdate" id="parent_birthdate" max="{{ date('Y-m-d') }}" required>
      <label>Contact Number</label>
      <input type="text" name="parent_contactinfo" id="parent_contactinfo" required>
      <button type="submit" class="btn btn-info"><i class="fas fa-plus-circle"></i> Add</button>
    </form>
  </div>
</div>

<!-- INFO MODAL -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('infoModal')">&times;</span>
    <h2>Parent/Guardian Info</h2>
    <p><strong>Name:</strong> <span id="infoName"></span></p>
    <p><strong>Birthdate:</strong> <span id="infoBirthdate"></span></p>
    <p><strong>Contact:</strong> <span id="infoContact"></span></p>
    <p><strong>Email:</strong> <span id="infoEmail"></span></p>
    <p><strong>Relationship:</strong> <span id="infoRelationship"></span></p>
    <p><strong>Children:</strong></p>
    <ul id="infoChildren"></ul>
    <button class="btn btn-info" id="smsBtn"><i class="fas fa-sms"></i> Send SMS</button>
  </div>
</div>

<script>
  // DROPDOWN MENU
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.classList.toggle('active');
      const container = btn.nextElementSibling;
      container.classList.toggle('show');
      btn.querySelector('.fa-caret-down').style.transform =
        container.classList.contains('show') ? 'rotate(180deg)' : '';
    });
  });

  // MODAL HANDLING
  function openModal(id){ document.getElementById(id).style.display='flex'; }
  function closeModal(id){ document.getElementById(id).style.display='none'; }

  // ADD PARENT MODAL
  function openAddModal(){
    document.getElementById('addModal').style.display='flex';
    document.getElementById('modalTitle').innerText='Add Parent/Guardian';
    document.getElementById('addParentForm').action='{{ route("parents.store") }}';
    document.getElementById('methodField').innerHTML='';
    document.getElementById('addParentForm').querySelector('button[type="submit"]').innerHTML='<i class="fas fa-plus-circle"></i> Add';
    document.getElementById('parent_fname').value='';
    document.getElementById('parent_lname').value='';
    document.getElementById('parent_birthdate').value='';
    document.getElementById('parent_contactinfo').value='';
  }

  // EDIT PARENT
  function editGuardian(button){
    const row=button.closest('tr');
    const fullName=row.cells[0].innerText.trim();
    const lastSpace=fullName.lastIndexOf(' ');
    document.getElementById('parent_id').value=row.dataset.id;
    document.getElementById('parent_fname').value=fullName.slice(0,lastSpace);
    document.getElementById('parent_lname').value=fullName.slice(lastSpace+1);
    document.getElementById('parent_birthdate').value=row.cells[2].innerText.trim();
    document.getElementById('parent_contactinfo').value=row.cells[3].innerText.trim();
    const form=document.getElementById('addParentForm');
    form.action=`/adviser/parents/${row.dataset.id}`;
    document.getElementById('methodField').innerHTML='@method("PUT")';
    form.querySelector('button[type="submit"]').innerHTML='<i class="fas fa-save"></i> Update';
    document.getElementById('modalTitle').innerText='Edit Parent/Guardian';
    document.getElementById('addModal').style.display='flex';
  }

  // SHOW INFO MODAL
  function showInfo(name,birthdate,contact,email,relationship,studentsJson){
    const loggedAdviserId={{ optional(auth()->guard('adviser')->user())->adviser_id ?? 'null' }};
    document.getElementById('infoName').innerText=name;
    document.getElementById('infoBirthdate').innerText=birthdate;
    document.getElementById('infoContact').innerText=contact;
    document.getElementById('infoEmail').innerText=email || '-';
    document.getElementById('infoRelationship').innerText=relationship || '-';
    const childrenList=document.getElementById('infoChildren');
    childrenList.innerHTML='';
    let students=[];
    try{students=JSON.parse(studentsJson);}catch(e){students=[];}
    const filtered=students.filter(s=>s.adviser_id==loggedAdviserId);
    if(filtered.length>0){
      filtered.forEach(s=>{
        const li=document.createElement('li');
        li.textContent=`${s.name} - Contact: ${s.contact}`;
        childrenList.appendChild(li);
      });
    }else{childrenList.innerHTML='<li>No children under your supervision</li>';}
    document.getElementById('smsBtn').onclick=function(){
      alert(`SMS to ${contact}: Hello ${name}, regarding your child(ren): ${filtered.map(s=>s.name).join(', ')}.`);
    };
    document.getElementById('infoModal').style.display='flex';
  }

  // LOGOUT
  function logout(){ if(confirm('Are you sure you want to log out?')) document.getElementById('logout-form').submit(); }

  // SEARCH
  document.getElementById('searchInput').addEventListener('input',function(){
    const filter=this.value.toLowerCase();
    document.querySelectorAll('#parentTable-active tbody tr').forEach(row=>{
      row.style.display=row.innerText.toLowerCase().includes(filter)?'':'none';
    });
  });
</script>
</body>
</html>
