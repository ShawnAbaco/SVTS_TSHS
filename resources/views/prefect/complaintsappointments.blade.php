<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Appointments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    /* Reset */
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

   .sidebar {
    width: 220px;
    background:rgb(0, 0, 0); 
    color: #fff;
    height: 100vh;
    position: fixed;
    padding: 25px 15px;
    border-radius: 0 15px 15px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}

    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 20px;
      letter-spacing: 1px;
      color:rgb(255, 255, 255);
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      padding: 12px 10px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 8px;
      font-size: 14px;
      color: #fff;
      transition: 0.3s;
      position: relative;
    }
    .sidebar ul li i {
      margin-right: 12px;
      color:rgb(255, 255, 255);
      min-width: 20px;
    }
    .sidebar ul li:hover {
      background:rgb(0, 247, 239);
      color: #111;
    }
    .sidebar ul li:hover i {
      color: #111;
    }
    .sidebar ul li.active {
      background:rgb(11, 255, 235);
      color: #111;
    }
    .sidebar ul li.active i {
      color: #111;
    }
    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }
    .section-title {
      margin: 15px 10px 5px;
      font-size: 11px;
      text-transform: uppercase;
      color: #bbb;
    }

    /* Dropdown */
    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 20px;
      transition: max-height 0.3s ease;
    }
    .dropdown-container li {
      padding: 10px;
      font-size: 13px;
      border-radius: 6px;
      cursor: pointer;
    }
    .dropdown-container li:hover {
      background:rgb(255, 255, 255);
      color: #111;
    }
    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }
    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background:rgb(255, 255, 255);
      border-radius: 3px;
    }

    /* Content */
    .content { margin-left: 260px; padding: 20px; }
    .container { margin-top: 30px; }
    h1 { text-align: center; margin-bottom: 15px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
    table thead { background-color: #007BFF; color: #fff; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }

    /* Buttons */
    .btn { display: inline-block; padding: 8px 16px; font-size: 14px; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; }
    .btn-primary { background-color: #007BFF; color: #fff; }
    .btn-secondary { background-color: #6c757d; color: #fff; }
    .btn-warning { background-color: #ffc107; color: #000; }
    .btn-danger { background-color: #dc3545; color: #fff; }

    /* Modal */
    .modal-overlay {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000;
    }
    .modal {
      background: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .modal-header { font-size: 18px; margin-bottom: 15px; font-weight: bold; }
    .modal-body label { display: block; margin-top: 10px; font-weight: 600; }
    .modal-body input, .modal-body select { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
    .modal-footer { text-align: right; margin-top: 15px; }
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
  <div class="container">
    <h1>Complaints Appointments</h1>
    <button class="btn btn-primary" onclick="openAppointmentModal()">Create Appointment</button>

    <!-- Search Bar -->
    <div style="margin: 15px 0;">
      <input type="text" id="searchInput" placeholder="Search by ID or Complaint..." onkeyup="searchTable()" style="padding:5px; width: 250px;">
    </div>

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
          <td>{{ $appointment->comp_app_status }}</td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $appointment->comp_app_id }}, '{{ $appointment->complaints_id }}', '{{ $appointment->comp_app_date }}', '{{ $appointment->comp_app_time }}', '{{ $appointment->comp_app_status }}')">Edit</button>
            <form action="{{ route('complaints.appointments.destroy', $appointment->comp_app_id) }}" method="POST" style="display:inline-block;">
              @csrf
              @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Create Modal -->
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

<!-- Edit Modal -->
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
  // Dropdown functionality with sidebar scroll and only one open at a time
  const sidebar = document.querySelector('.sidebar');
  const dropdowns = document.querySelectorAll('.dropdown-btn');

  dropdowns.forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;

      // Close other dropdowns
      dropdowns.forEach(otherBtn => {
        const otherContainer = otherBtn.nextElementSibling;
        if (otherBtn !== btn) {
          otherBtn.classList.remove('active');
          otherContainer.style.display = 'none';
        }
      });

      // Toggle current dropdown
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';

      // Sidebar scrollable when at least 1 dropdown is open
      const openDropdowns = document.querySelectorAll('.dropdown-container[style*="block"]').length;
      sidebar.style.overflowY = openDropdowns >= 1 ? 'auto' : 'hidden';
    });
  });

  // Open/Close modals
  function openAppointmentModal() { document.getElementById('appointmentModal').style.display = 'flex'; }
  function closeAppointmentModal() { document.getElementById('appointmentModal').style.display = 'none'; }

  function openEditModal(id, complaintId, date, time, status) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editForm').action = '/complaints/appointments/' + id;
    document.getElementById('editComplaint').value = complaintId;
    document.getElementById('editDate').value = date;
    document.getElementById('editTime').value = time;
    document.getElementById('editStatus').value = status;
  }
  function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

  // Search table
  function searchTable() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let table = document.getElementById('appointmentsTable');
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
      let idCell = rows[i].getElementsByTagName('td')[0];
      let complaintCell = rows[i].getElementsByTagName('td')[1];
      if (idCell && complaintCell) {
        let idText = idCell.textContent.toLowerCase();
        let complaintText = complaintCell.textContent.toLowerCase();
        rows[i].style.display = (idText.includes(input) || complaintText.includes(input)) ? '' : 'none';
      }
    }
  }
</script>
</body>
</html>
