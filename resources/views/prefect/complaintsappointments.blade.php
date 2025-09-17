<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Complaints Appointments</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
<style>
/* --- Reset --- */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
  font-weight: bold;
  transition: all 0.2s ease-in-out;
}

body {
  display: flex;
  background: #f9f9f9;
  color: #111;
}

/* --- Sidebar --- */
.sidebar {
  width: 230px;
background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
background-repeat: no-repeat;
background-attachment: fixed;
  color: #fff;
  height: 100vh;
  position: fixed;
  padding: 25px 15px;
  border-radius: 0 15px 15px 0;
  box-shadow: 2px 0 15px rgba(0,0,0,0.5);
  overflow-y: auto;
}
.sidebar h2 {
  margin-bottom: 30px;
  text-align: center;
  font-size: 22px;
  letter-spacing: 1px;
  color: #fff;
  text-transform: uppercase;
  border-bottom: 2px solid rgba(255,255,255,0.15);
  padding-bottom: 10px;
}
.sidebar ul { list-style: none; }
.sidebar ul li {
  padding: 12px 14px;
  display: flex;
  align-items: center;
  cursor: pointer;
  border-radius: 10px;
  font-size: 15px;
  color: #e0e0e0;
  transition: background 0.3s, transform 0.2s;
}
.sidebar ul li i { margin-right: 12px; color: #cfcfcf; min-width: 20px; font-size: 16px; }
.sidebar ul li:hover { background: #2d3f55; transform: translateX(5px); color: #fff; }
.sidebar ul li:hover i { color: #00e0ff; }
.sidebar ul li.active { background: #00aaff; color: #fff; border-left: 4px solid #fff; }
.sidebar ul li.active i { color: #fff; }
.sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
.section-title { margin: 20px 10px 8px; font-size: 11px; text-transform: uppercase; font-weight: bold; color: rgba(255,255,255,0.6); letter-spacing: 1px; }
.dropdown-container { display: none; list-style: none; padding-left: 25px; }
.dropdown-container li { padding: 10px; font-size: 14px; border-radius: 8px; color: #ddd; }
.dropdown-container li:hover { background: #3a4c66; color: #fff; }
.dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
.dropdown-btn.active .arrow { transform: rotate(180deg); }
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 3px; }

/* --- Content --- */
.content { margin-left: 260px; padding: 30px; }
h1 { text-align: center; margin-bottom: 25px; font-size: 28px; color: #007bff; }

/* --- Top controls --- */
.top-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 10px;
}
.top-controls input {
  padding: 10px 15px;
  width: 300px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}
.top-controls button {
  border: none;
  padding: 10px 16px;
  font-size: 15px;
  border-radius: 8px;
  cursor: pointer;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg,#007bff,#00aaff);
  transition: all 0.3s ease;
}
.top-controls button:hover {
  background: linear-gradient(135deg,#0056b3,#007bbf);
  transform: translateY(-2px);
}

/* --- Table --- */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  background: #fff;
  font-size: 16px;
  table-layout: fixed;
}
th {
  background: linear-gradient(135deg,#007bff,#00aaff);
  color: #fff;
  padding: 16px 14px;
  text-align: center;
  position: sticky;
  top: 0;
  z-index: 2;
}
td {
  padding: 14px 12px;
  border-bottom: 1px solid #e3e3e3;
  vertical-align: middle;
  text-align: center;
  word-wrap: break-word;
}
td:first-child { text-align: center; }
td:nth-child(2) { text-align: left; }
tr:nth-child(even) { background: #f5f8ff; }
tr:hover { background: #d0e7ff; transform: scale(1.01); transition: all 0.2s ease-in-out; }

/* --- Status --- */
.status-pending { color: #e67e22; background: #fff4e5; padding: 6px 12px; border-radius: 14px; font-weight: 600; display: inline-block; }
.status-confirmed { color: #007bff; background: #e5f0ff; padding: 6px 12px; border-radius: 14px; font-weight: 600; display: inline-block; }
.status-completed { color: #27ae60; background: #e6f9f0; padding: 6px 12px; border-radius: 14px; font-weight: 600; display: inline-block; }

/* --- Action buttons --- */
.btn-action {
  padding: 8px 14px;
  font-size: 15px;
  border-radius: 10px;
  cursor: pointer;
  color: #fff;
  margin-right: 6px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease-in-out;
}
.btn-action i { margin-right: 5px; }
.btn-edit { background: #ffc107; }
.btn-edit:hover { background: #e0a800; transform: translateY(-2px) scale(1.05); }
.btn-delete { background: #dc3545; }
.btn-delete:hover { background: #c82333; transform: translateY(-2px) scale(1.05); }

/* --- Modal --- */
.modal-overlay {
  position: fixed; top:0; left:0; width:100%; height:100%;
  background: rgba(0,0,0,0.6); display:none; justify-content:center; align-items:center; z-index:1000;
}
.modal {
  background: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}
.modal-header { font-size: 18px; margin-bottom: 15px; font-weight: bold; }
.modal-body label { display: block; margin-top: 10px; font-weight: 600; }
.modal-body input, .modal-body select { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
.modal-footer { text-align: right; margin-top: 15px; }
.modal-footer .btn { margin-left: 8px; }

/* --- Responsive --- */
@media screen and (max-width: 768px){
  .content { margin-left: 0; padding:15px; }
  table { font-size:14px; }
  th, td { padding:10px 8px; }
  .btn-action { padding:6px 10px; font-size:13px; }
  .top-controls { flex-direction: column; align-items:flex-start; gap:10px; }
  .top-controls input { width:100%; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
    </ul>
    <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li class="active"><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>
    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

<!-- Main Content -->
<div class="content">
  <h1>Complaints Appointments</h1>
<div class="top-controls" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 20px;">
  <input type="text" id="searchInput" placeholder="Search Complainant Name..." onkeyup="searchComplainant()" style="padding: 10px 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; width: 250px;">
   <button onclick="openAppointmentModal()"><i class="fas fa-plus"></i> Create Appointment</button>
  </button>
  <button onclick="openTrash()" style="border: none; padding: 10px 16px; font-size: 15px; border-radius: 8px; cursor: pointer; color: #fff; display: flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #dc3545, #ff4d4d); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    <i class="fas fa-trash"></i> Trash
  </button>


  <table id="appointmentsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Complaint</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($appointments as $appointment)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $appointment->complaint->complaints_incident }}</td>
        <td>{{ $appointment->comp_app_date }}</td>
        <td>{{ $appointment->comp_app_time }}</td>
        <td class="{{ strtolower($appointment->comp_app_status) }}">{{ $appointment->comp_app_status }}</td>
        <td>
          <button class="btn-action btn-edit" onclick="openEditModal({{ $appointment->comp_app_id }}, '{{ $appointment->complaints_id }}', '{{ $appointment->comp_app_date }}', '{{ $appointment->comp_app_time }}', '{{ $appointment->comp_app_status }}')"><i class="fas fa-edit"></i>Edit</button>
          <form action="{{ route('complaints.appointments.destroy', $appointment->comp_app_id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button class="btn-action btn-delete" type="submit" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i>Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Modals (Create & Edit) -->
<div id="appointmentModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">Create Appointment</div>
    <form action="{{ route('complaints.appointments.store') }}" method="POST">
      @csrf
      <div class="modal-body">
        <label for="complaint">Complaint</label>
        <select id="complaint" name="complaints_id" required>
          <option value="">Select Complaint</option>
          @foreach($complaints as $complaint)
            <option value="{{ $complaint->complaints_id }}">{{ $complaint->complaints_incident }}</option>
          @endforeach
        </select>
        <label for="date">Date</label>
        <input type="date" id="date" name="comp_app_date" required>
        <label for="time">Time</label>
        <input type="time" id="time" name="comp_app_time" required>
        <label for="status">Status</label>
        <select id="status" name="comp_app_status" required>
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeAppointmentModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
    </form>
  </div>
</div>

<div id="editModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">Edit Appointment</div>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-body">
        <label for="editComplaint">Complaint</label>
        <select id="editComplaint" name="complaints_id" required>
          <option value="">Select Complaint</option>
          @foreach($complaints as $complaint)
            <option value="{{ $complaint->complaints_id }}">{{ $complaint->complaints_incident }}</option>
          @endforeach
        </select>
        <label for="editDate">Date</label>
        <input type="date" id="editDate" name="comp_app_date" required>
        <label for="editTime">Time</label>
        <input type="time" id="editTime" name="comp_app_time" required>
        <label for="editStatus">Status</label>
        <select id="editStatus" name="comp_app_status" required>
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.nextElementSibling;
    dropdowns.forEach(otherBtn => {
      const otherContainer = otherBtn.nextElementSibling;
      if(otherBtn !== btn){ otherBtn.classList.remove('active'); otherContainer.style.display='none'; }
    });
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });
});
function openAppointmentModal(){ document.getElementById('appointmentModal').style.display='flex'; }
function closeAppointmentModal(){ document.getElementById('appointmentModal').style.display='none'; }
function openEditModal(id, complaintId, date, time, status){
  document.getElementById('editModal').style.display='flex';
  document.getElementById('editForm').action='/complaints/appointments/'+id;
  document.getElementById('editComplaint').value=complaintId;
  document.getElementById('editDate').value=date;
  document.getElementById('editTime').value=time;
  document.getElementById('editStatus').value=status;
}
function closeEditModal(){ document.getElementById('editModal').style.display='none'; }
function searchTable(){
  let input=document.getElementById('searchInput').value.toLowerCase();
  let table=document.getElementById('appointmentsTable');
  let rows=table.getElementsByTagName('tr');
  for(let i=1;i<rows.length;i++){
    let idCell=rows[i].getElementsByTagName('td')[0];
    let complaintCell=rows[i].getElementsByTagName('td')[1];
    if(idCell && complaintCell){
      let idText=idCell.textContent.toLowerCase();
      let complaintText=complaintCell.textContent.toLowerCase();
      rows[i].style.display=(idText.includes(input) || complaintText.includes(input)) ? '' : 'none';
    }
  }
}
</script>
</body>
</html>
