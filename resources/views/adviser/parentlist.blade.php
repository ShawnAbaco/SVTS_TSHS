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
      <th>Sex</th>
      <th>Relationship</th>
      <th>Birthdate</th>
      <th>Contact Number</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($parents as $parent)
<tr data-id="{{ $parent->parent_id }}"
    data-parent='@json([
        "sex" => $parent->parent_sex,
        "relationship" => $parent->parent_relationship,
        "status" => $parent->status
    ])'
    data-students='@json($parent->students)'>
    <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
    <td>{{ $parent->parent_sex }}</td>
    <td>{{ $parent->parent_relationship }}</td>
    <td>{{ $parent->parent_birthdate }}</td>
    <td>{{ $parent->parent_contactinfo }}</td>
    <td>
        <span class="status-badge {{ $parent->status }}">
            {{ ucfirst($parent->status) }}
        </span>
    </td>
    <td class="actions">
<button class="btn btn-info" onclick="showInfo(
    '{{ $parent->parent_fname }} {{ $parent->parent_lname }}',
    '{{ $parent->parent_birthdate }}',
    '{{ $parent->parent_contactinfo }}',
    this.closest('tr').dataset.parent,
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


  <!-- ADD  MODAL -->
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

      <!-- Sex -->
      <select name="parent_sex" id="parent_sex" required>
        <option value="" disabled selected>Select Sex</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>

      <!-- Relationship -->
      <input type="text" name="parent_relationship" id="parent_relationship"
             placeholder="Relationship (e.g., Father, Mother, Guardian)"
             required pattern="^[A-Za-z\s]+$"
             title="Only letters and spaces are allowed">

      <!-- Birthdate -->
      <input type="date" name="parent_birthdate" id="parent_birthdate"
             max="<?php echo date('Y-m-d'); ?>" required
             title="Birthdate cannot be in the future">

      <!-- Contact Info -->
      <input type="text" name="parent_contactinfo" id="parent_contactinfo"
             placeholder="Contact Number"
             required pattern="^[0-9]{11}$"
             title="Contact number must be exactly 11 digits (e.g., 09123456789)">

      <!-- Status -->
      <select name="status" id="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>

      <span id="methodField"></span>
      <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Save</button>
    </form>
  </div>
</div>

<!-- INFO MODAL -->
<div class="modal" id="infoModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('infoModal')">&times;</span>
        <h2>Parent/Guardian Info</h2>
        <div class="info-section">
            <p><strong>Name:</strong> <span id="infoName"></span></p>
            <p><strong>Sex:</strong> <span id="infoSex"></span></p>
            <p><strong>Relationship:</strong> <span id="infoRelationship"></span></p>
            <p><strong>Birthdate:</strong> <span id="infoBirthdate"></span></p>
            <p><strong>Contact:</strong> <span id="infoContact"></span></p>
            <p><strong>Status:</strong> <span id="infoStatus" class="status-badge"></span></p>
        </div>

        <h3>Children under this parent</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="infoChildren">
                <!-- dynamically populated -->
            </tbody>
        </table>

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

   function showInfo(name, birthdate, contact, parentJson, studentsJson) {
    const loggedAdviserId = {{ optional(auth()->guard('adviser')->user())->adviser_id ?? 'null' }};
    const parentData = JSON.parse(parentJson);
    let students = [];
    try { students = JSON.parse(studentsJson); } catch(e) { students = []; }

    const filteredStudents = students.filter(s => s.adviser_id == loggedAdviserId);

    // Populate modal fields
    document.getElementById('infoName').innerText = name;
    document.getElementById('infoSex').innerText = parentData.sex || 'N/A';
    document.getElementById('infoRelationship').innerText = parentData.relationship || 'N/A';
    document.getElementById('infoBirthdate').innerText = birthdate;
    document.getElementById('infoContact').innerText = contact;
    document.getElementById('infoStatus').innerText = parentData.status || 'active';
    document.getElementById('infoStatus').className = `status-badge ${parentData.status || 'active'}`;

    const childrenList = document.getElementById('infoChildren');
    childrenList.innerHTML = '';

    if(filteredStudents.length > 0) {
        filteredStudents.forEach(student => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${student.student_fname} ${student.student_lname}</td>
                <td>${student.student_contactinfo}</td>
                <td><span class="status-badge ${student.status || 'active'}">${student.status || 'Active'}</span></td>
            `;
            childrenList.appendChild(tr);
        });
    } else {
        childrenList.innerHTML = '<tr><td colspan="4" style="text-align:center;">No children under your advisory</td></tr>';
    }

    // SMS button dynamically bound
    const smsBtn = document.getElementById('smsBtn');
    smsBtn.onclick = function() {
        const message = `Hello ${name}, regarding your child(ren): ${filteredStudents.map(s => s.student_fname + ' ' + s.student_lname).join(', ')}.`;
        alert(`SMS to ${contact}: ${message}`);
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

  // Parse JSON from data-parent for extra fields
  const parentData = JSON.parse(row.dataset.parent || '{}');

  // Fill modal inputs
  document.getElementById('parent_id').value = row.dataset.id;
  document.getElementById('parent_fname').value = firstName;
  document.getElementById('parent_lname').value = lastName;
  document.getElementById('parent_birthdate').value = cells[3].innerText.trim();
  document.getElementById('parent_contactinfo').value = cells[4].innerText.trim();

  // Populate new fields from JSON
  document.getElementById('parent_sex').value = parentData.sex || '';
  document.getElementById('parent_relationship').value = parentData.relationship || '';
  document.getElementById('status').value = parentData.status || 'active';

  // Update form for PUT
  const form = document.getElementById('addParentForm');
  form.action = `/adviser/adviser/parents/${row.dataset.id}`;
  document.getElementById('methodField').innerHTML = '@method("PUT")';
  form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update';
  document.getElementById('modalTitle').innerText = 'Edit Parent/Guardian';
  document.getElementById('addModal').style.display = 'flex';
}



document.getElementById('smsBtn').onclick = function() {
    const parentId = {{ $parent->parent_id }};
    const message = `Hello ${name}, regarding your child(ren): ${filtered.map(s => s.name).join(', ')}.`;

    fetch('{{ route('send.sms.to.parent') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ parent_id: parentId, message: message })
    }).then(res => res.json()).then(data => alert(data.message));
};



    function logout() {
      if(confirm('Are you sure you want to log out?')){
        window.location.href = '/adviser/login';
      }
    }
  </script>
</body>
</html>
