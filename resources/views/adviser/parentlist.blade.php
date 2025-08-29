<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/adviser/parents.css') }}">
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
      <button class="btn" onclick="openAddModal()"><i class="fas fa-plus-circle"></i> Add Parent/Guardian</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Parent/Guardian Name</th>
          <th>Birthdate</th>
          <th>Contact Number</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="parentTable">
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
            <button class="btn" onclick="showInfo(
              '{{ $parent->parent_fname }} {{ $parent->parent_lname }}',
              '{{ $parent->parent_birthdate }}',
              '{{ $parent->parent_contactinfo }}',
              this.closest('tr').dataset.students
            )">
              <i class="fas fa-info-circle"></i> Info
            </button>
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
    <h2>Add Parent/Guardian</h2>
<form id="addParentForm" method="POST" action="{{ route('parents.store') }}">
  @csrf
  <input type="hidden" name="parent_id" id="parent_id" value="">
  <input type="text" name="parent_fname" id="parent_fname" placeholder="First Name" required>
  <input type="text" name="parent_lname" id="parent_lname" placeholder="Last Name" required>
  <input type="date" name="parent_birthdate" id="parent_birthdate" placeholder="Birthdate" required>
  <input type="text" name="parent_contactinfo" id="parent_contactinfo" placeholder="Contact Number" required>
  <!-- Placeholder for dynamic PUT method -->
  <span id="methodField"></span>
  <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Save</button>
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
    function openAddModal() {
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
      try {
        students = JSON.parse(studentsJson);
      } catch(e) {
        students = [];
      }

      // Filter children for logged-in adviser
      const filtered = students.filter(s => s.adviser_id == loggedAdviserId);

      if(filtered.length > 0) {
        filtered.forEach(student => {
          const li = document.createElement('li');
          li.textContent = `${student.name} - Contact: ${student.contact}`;
          childrenList.appendChild(li);
        });
      } else {
        const li = document.createElement('li');
        li.textContent = 'No children under your supervision';
        childrenList.appendChild(li);
      }

      document.getElementById('smsBtn').onclick = function() {
        const message = `Hello ${name}, regarding your child(ren): ${filtered.map(s => s.name).join(', ')}.`;
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

  document.getElementById('parent_id').value = row.dataset.id;
  document.getElementById('parent_fname').value = firstName;
  document.getElementById('parent_lname').value = lastName;
  document.getElementById('parent_birthdate').value = cells[1].innerText.trim();
  document.getElementById('parent_contactinfo').value = cells[2].innerText.trim();

  const form = document.getElementById('addParentForm');

  // Set form action to update route
form.action = `/adviser/adviser/parents/${row.dataset.id}`;

  // Add PUT method field
  document.getElementById('methodField').innerHTML = '@method("PUT")';

  // Change button text to Update
  form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update';

  openAddModal();
}


    function logout() {
      if(confirm('Are you sure you want to log out?')){
        window.location.href = '/adviser/login';
      }
    }
  </script>
</body>
</html>
