<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adviser/parentlist.css') }}">

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
            <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
            <ul class="dropdown-container">
                <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
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

  <div class="main-content">
    <div class="header">
      <h1>Parent List</h1>
      <div class="actions">
        <input type="text" id="searchInput" placeholder="Search parent...">
        <button class="btn btn-info" onclick="openAddModal()">
          <i class="fas fa-plus-circle"></i> Add Parent/Guardian
        </button>
        <button class="btn btn-archive">
          <i class="fas fa-archive"></i> Archives
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

      <!-- First Name -->
      <input type="text" name="parent_fname" id="parent_fname" placeholder="First Name"
             required pattern="^[A-Za-z\s]+$"
             title="Only letters and spaces are allowed">

      <!-- Last Name -->
      <input type="text" name="parent_lname" id="parent_lname" placeholder="Last Name"
             required pattern="^[A-Za-z\s]+$"
             title="Only letters and spaces are allowed">

      <!-- Birthdate -->
      <input type="date" name="parent_birthdate" id="parent_birthdate"
             max="<?php echo date('Y-m-d'); ?>" required
             title="Birthdate cannot be in the future">

      <!-- Contact Info -->
      <input type="text" name="parent_contactinfo" id="parent_contactinfo" placeholder="Contact Number"
             required pattern="^[0-9]{11}$"
             title="Contact number must be exactly 11 digits (e.g., 09123456789)">

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



// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
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
