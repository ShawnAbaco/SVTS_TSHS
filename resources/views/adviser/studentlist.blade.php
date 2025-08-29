<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Student List</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/adviser/studentlist.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  <main class="main-content">
    <h1>Student List</h1>
    <button class="btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> Create Student Info</button>

    <!-- Table -->
    <table id="studentTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Birthdate</th>
          <th>Address</th>
          <th>Contact#</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
@foreach($students as $student)
<tr 
    data-id="{{ $student->student_id }}"
    data-fname="{{ $student->student_fname }}"
    data-lname="{{ $student->student_lname }}"
    data-birthdate="{{ $student->student_birthdate }}"
    data-address="{{ $student->student_address }}"
    data-contact="{{ $student->student_contactinfo }}"
    data-parent-name="{{ $student->parent ? $student->parent->parent_fname . ' ' . $student->parent->parent_lname : '' }}"
    data-parent-contact="{{ $student->parent ? $student->parent->parent_contactinfo : '' }}"
    data-parent-id="{{ $student->parent ? $student->parent->parent_id : '' }}"
>
  <td>{{ $student->student_fname . " " . $student->student_lname }}</td>
  <td>{{ $student->student_birthdate }}</td>
  <td>{{ $student->student_address }}</td>
  <td>{{ $student->student_contactinfo }}</td>
  <td>
    <button class="action-btn info"><i class="fas fa-info-circle"></i> Info</button>
    <button class="action-btn edit"><i class="fas fa-edit"></i> Edit</button>
    <form method="POST" action="{{ route('students.destroy', $student->student_id) }}" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="action-btn delete"><i class="fas fa-trash"></i> Delete</button>
    </form>
  </td>
</tr>
@endforeach
      </tbody>
    </table>
  </main>

<!-- CREATE STUDENT MODAL -->
<div class="modal" id="createStudentModal">
  <div class="modal-content">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <div class="modal-header">Create Student Information</div>

    <form id="studentForm" method="POST" action="{{ route('students.store') }}">
      @csrf

      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="student_fname" required>
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="student_lname" required>
      </div>
      <div class="form-group">
        <label>Birthdate</label>
        <input type="date" name="student_birthdate" required>
      </div>
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="student_address" required>
      </div>
      <div class="form-group">
        <label>Contact Info</label>
        <input type="text" name="student_contactinfo" required>
      </div>

      <hr>

      <div class="form-group">
        <label for="parent_search">Parent</label>
        <input type="text" id="parent_search" class="form-control" placeholder="Type parent name...">
        <input type="hidden" id="parent_id" name="parent_id">
        <div id="parentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn-submit">Create Student</button>
      </div>
    </form>
  </div>
</div>

<!-- INFO MODAL -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <span class="close-btn" id="closeInfoModalBtn">&times;</span>
    <div class="modal-header">Parent / Guardian Information</div>

    <div class="form-group">
      <label>Parent/Guardian Name:</label>
      <p><strong id="infoGuardianName"></strong></p>
    </div>
    <div class="form-group">
      <label>Contact Number:</label>
      <p><strong id="infoGuardianContact"></strong></p>
    </div>

    <div class="modal-footer">
      <button id="sendSMSBtn" class="btn-submit"><i class="fas fa-sms"></i> Send SMS</button>
    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editStudentModal">
  <div class="modal-content">
    <span class="close-btn" id="closeEditModalBtn">&times;</span>
    <div class="modal-header">Edit Student Information</div>
    <form id="editStudentForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="editStudentId">

      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="student_fname" id="editStudentFname" required>
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="student_lname" id="editStudentLname" required>
      </div>
      <div class="form-group">
        <label>Birthdate</label>
        <input type="date" name="student_birthdate" id="editStudentBirthdate" required>
      </div>
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="student_address" id="editStudentAddress" required>
      </div>
      <div class="form-group">
        <label>Contact Info</label>
        <input type="text" name="student_contactinfo" id="editStudentContact" required>
      </div>

      <hr>

      <div class="form-group">
        <label for="edit_parent_search">Parent / Guardian</label>
        <input type="text" id="edit_parent_search" class="form-control" placeholder="Type parent name..." required>
        <input type="hidden" id="edit_parent_id" name="parent_id">
        <div id="editParentList" class="list-group mt-1" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn-submit">Update Student</button>
      </div>
    </form>
  </div>
</div>

<script>
const openModalBtn = document.getElementById('openModalBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const modal = document.getElementById('createStudentModal');

const editModal = document.getElementById('editStudentModal');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');

const infoModal = document.getElementById('infoModal');
const closeInfoModalBtn = document.getElementById('closeInfoModalBtn');
const sendSMSBtn = document.getElementById('sendSMSBtn');

// OPEN / CLOSE MODALS
openModalBtn.addEventListener('click', () => modal.style.display = 'flex');
closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
closeEditModalBtn.addEventListener('click', () => editModal.style.display = 'none');
closeInfoModalBtn.addEventListener('click', () => infoModal.style.display = 'none');

// Close modals when clicking outside
window.addEventListener('click', (e) => {
  if (e.target === modal) modal.style.display = 'none';
  if (e.target === editModal) editModal.style.display = 'none';
  if (e.target === infoModal) infoModal.style.display = 'none';
});

// EDIT MODAL
document.querySelectorAll('.edit').forEach(button => {
  button.addEventListener('click', (e) => {
    const row = e.target.closest('tr');
    const studentId = row.dataset.id;

    document.getElementById('editStudentId').value = studentId;
    document.getElementById('editStudentFname').value = row.dataset.fname;
    document.getElementById('editStudentLname').value = row.dataset.lname;
    document.getElementById('editStudentBirthdate').value = row.dataset.birthdate;
    document.getElementById('editStudentAddress').value = row.dataset.address;
    document.getElementById('editStudentContact').value = row.dataset.contact;

    document.getElementById('edit_parent_search').value = row.dataset.parentName;
    document.getElementById('edit_parent_id').value = row.dataset.parentId || '';

    document.getElementById('editStudentForm').action = `/adviser/students/${studentId}`;
    editModal.style.display = 'flex';
  });
});

// INFO BUTTON
document.querySelectorAll('.info').forEach(button => {
  button.addEventListener('click', (e) => {
    const row = e.target.closest('tr');
    const guardianName = row.dataset.parentName;
    const guardianContact = row.dataset.parentContact;

    document.getElementById('infoGuardianName').innerText = guardianName;
    document.getElementById('infoGuardianContact').innerText = guardianContact;

    infoModal.style.display = 'flex';

    sendSMSBtn.onclick = () => {
      alert(`📩 SMS will be sent to ${guardianName} (${guardianContact})`);
    };
  });
});

// LIVE SEARCH PARENT
document.getElementById('parent_search').addEventListener('keyup', function() {
  let query = this.value;
  if(query.length < 2){
    document.getElementById('parentList').style.display = 'none';
    document.getElementById('parentList').innerHTML = '';
    return;
  }
  fetch(`{{ route('adviser.parentsearch') }}?query=${query}`)
    .then(res => res.json())
    .then(data => {
      let results = '';
      data.forEach(parent => {
        results += `<div class="dropdown-results-item" onclick="selectParent(${parent.parent_id}, '${parent.parent_name}')">${parent.parent_name}</div>`;
      });
      document.getElementById('parentList').innerHTML = results;
      document.getElementById('parentList').style.display = 'block';
    });
});

function selectParent(id, name){
  document.getElementById('parent_id').value = id;
  document.getElementById('parent_search').value = name;
  document.getElementById('parentList').style.display = 'none';
}

// LIVE SEARCH for EDIT MODAL
document.getElementById('edit_parent_search').addEventListener('keyup', function() {
  let query = this.value;
  if(query.length < 2){
    document.getElementById('editParentList').style.display = 'none';
    document.getElementById('editParentList').innerHTML = '';
    return;
  }
  fetch(`{{ route('adviser.parentsearch') }}?query=${query}`)
    .then(res => res.json())
    .then(data => {
      let results = '';
      data.forEach(parent => {
        results += `<div class="dropdown-results-item" onclick="selectEditParent(${parent.parent_id}, '${parent.parent_name}')">${parent.parent_name}</div>`;
      });
      document.getElementById('editParentList').innerHTML = results;
      document.getElementById('editParentList').style.display = 'block';
    });
});

function selectEditParent(id, name){
  document.getElementById('edit_parent_id').value = id;
  document.getElementById('edit_parent_search').value = name;
  document.getElementById('editParentList').style.display = 'none';
}

function logout(){
  if(confirm('Are you sure you want to log out?')){
    window.location.href = '/adviser/login';
  }
}
</script>

</body>
</html>
