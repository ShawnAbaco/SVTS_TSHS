<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/adviser/parents.css') }}">
  <style>
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .header h1 { font-size: 22px; margin: 0; }
    .header .actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .search-box input {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      min-width: 200px;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td {
      padding: 10px; text-align: center; border: 1px solid #ddd;
    }
    thead { background: #343a40; color: #fff; }
    .btn {
      padding: 6px 10px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }
    .btn i { margin-right: 5px; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-edit { background: #ffc107; color: #000; }
    .btn-info { background: #17a2b8; color: #fff; }
    .btn:hover { opacity: 0.9; }
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center; align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 500px; width: 100%;
      position: relative;
    }
    .modal-content .close {
      position: absolute; top: 10px; right: 15px;
      cursor: pointer; font-size: 20px;
    }
    .modal-content input {
      width: 100%; margin-bottom: 10px;
      padding: 8px; border: 1px solid #ccc;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div style="text-align: center;">
      <img src="/images/Logo.png" alt="Logo" />
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
      <h1>Parent List</h1>
      <div class="actions">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search parent...">
        </div>
        <button class="btn btn-info" onclick="openAddModal()">
          <i class="fas fa-plus-circle"></i> Add Parent/Guardian
        </button>
      </div>
    </div>

    <table id="parentTable">
      <thead>
        <tr>
          <th>Parent/Guardian Name</th>
          <th>Birthdate</th>
          <th>Contact Number</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($parents as $parent)
        <tr data-id="{{ $parent->parent_id }}" 
            data-students='@json(
                $parent->students->map(fn($s) => [
                    "name" => $s->student_fname . " " . $s->student_lname,
                    "contact" => $s->student_contactinfo,
                    "adviser_id" => $s->adviser_id
                ])
            )'>
          <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
          <td>{{ $parent->parent_birthdate }}</td>
          <td>{{ $parent->parent_contactinfo }}</td>
          <td class="actions">
            <button class="btn btn-info" onclick="showInfo(
              '{{ $parent->parent_fname }} {{ $parent->parent_lname }}',
              '{{ $parent->parent_birthdate }}',
              '{{ $parent->parent_contactinfo }}',
              this.closest('tr').dataset.students
            )"><i class="fas fa-info-circle"></i> Info</button>
            <button class="btn btn-edit" onclick="editGuardian(this)">
              <i class="fas fa-edit"></i> Edit
            </button>
            <form method="POST" action="{{ route('parents.destroy', $parent->parent_id) }}" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> Delete
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- ADD / EDIT MODAL -->
  <div class="modal" id="addModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('addModal')">&times;</span>
      <h2 id="modalTitle">Add Parent/Guardian</h2>
      <form id="addParentForm" method="POST" action="{{ route('parents.store') }}">
        @csrf
        <input type="hidden" name="parent_id" id="parent_id" value="">
        <input type="text" name="parent_fname" id="parent_fname" placeholder="First Name" required>
        <input type="text" name="parent_lname" id="parent_lname" placeholder="Last Name" required>
        <input type="date" name="parent_birthdate" id="parent_birthdate" required>
        <input type="text" name="parent_contactinfo" id="parent_contactinfo" placeholder="Contact Number" required>
        <span id="methodField"></span>
        <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Save</button>
      </form>
    </div>
  </div>

  <!-- INFO MODAL -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('infoModal')">&times;</span>
      <h2>Children Info</h2>
      <p><strong>Name:</strong> <span id="infoName"></span></p>
      <p><strong>Birthdate:</strong> <span id="infoBirthdate"></span></p>
      <p><strong>Contact:</strong> <span id="infoContact"></span></p>
      <p><strong>Children:</strong></p>
      <ul id="infoChildren"></ul>
      <button class="btn btn-info" id="smsBtn"><i class="fas fa-sms"></i> Send SMS</button>
    </div>
  </div>

  <script>
    // live search
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll("#parentTable tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    function openAddModal() {
      document.getElementById('addParentForm').reset();
      document.getElementById('methodField').innerHTML = '';
      document.getElementById('modalTitle').innerText = 'Add Parent/Guardian';
      document.querySelector('#addParentForm button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Save';
      document.getElementById('addModal').style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function showInfo(name, birthdate, contact, studentsJson) {
      const loggedAdviserId = {{ optional(auth()->guard('adviser')->user())->adviser_id ?? 'null' }};
      document.getElementById('infoName').innerText = name;
      document.getElementById('infoBirthdate').innerText = birthdate;
      document.getElementById('infoContact').innerText = contact;

      const childrenList = document.getElementById('infoChildren');
      childrenList.innerHTML = '';
      let students = [];
      try { students = JSON.parse(studentsJson); } catch(e) { students = []; }
      const filtered = students.filter(s => s.adviser_id == loggedAdviserId);
      if(filtered.length > 0) {
        filtered.forEach(student => {
          const li = document.createElement('li');
          li.textContent = `${student.name} - Contact: ${student.contact}`;
          childrenList.appendChild(li);
        });
      } else {
        childrenList.innerHTML = '<li>No children under your supervision</li>';
      }
      document.getElementById('smsBtn').onclick = function() {
        const msg = `Hello ${name}, regarding your child(ren): ${filtered.map(s => s.name).join(', ')}.`;
        alert(`SMS to ${contact}: ${msg}`);
      };
      document.getElementById('infoModal').style.display = 'flex';
    }

    function editGuardian(button) {
      const row = button.closest('tr');
      const cells = row.cells;
      const fullName = cells[0].innerText.trim();
      const lastSpaceIndex = fullName.lastIndexOf(' ');
      const firstName = fullName.slice(0, lastSpaceIndex).trim();
      const lastName = fullName.slice(lastSpaceIndex + 1).trim();

      document.getElementById('parent_id').value = row.dataset.id;
      document.getElementById('parent_fname').value = firstName;
      document.getElementById('parent_lname').value = lastName;
      document.getElementById('parent_birthdate').value = cells[1].innerText.trim();
      document.getElementById('parent_contactinfo').value = cells[2].innerText.trim();

      const form = document.getElementById('addParentForm');
      form.action = `/adviser/adviser/parents/${row.dataset.id}`;
      document.getElementById('methodField').innerHTML = '@method("PUT")';
      form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update';
      document.getElementById('modalTitle').innerText = 'Edit Parent/Guardian';
      document.getElementById('addModal').style.display = 'flex';
    }

    function logout() {
      if(confirm('Are you sure you want to log out?')){
        window.location.href = '/adviser/login';
      }
    }
  </script>
</body>
</html>
