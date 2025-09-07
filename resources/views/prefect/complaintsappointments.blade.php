<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Appointments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link rel="stylesheet" href="{{ asset('css/admin/COMPLAINTSAPPOINTMENT.css') }}">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <p>PREFECT DASHBOARD</p>
    <ul>
       <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
        <li><a href="{{ route('parent.lists') }}"><i class="fas fa-user-graduate"></i> Parent List </a></li>
        <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
        <li><a href="{{ route('violation.records') }}"><i class="fas fa-gavel"></i> Violation Record </a></li>
        <li><a href="{{ route('violation.appointments') }}"><i class="fas fa-bell"></i> Violation Appointments </a></li>
        <li><a href="{{ route('violation.anecdotals') }}"><i class="fas fa-chart-line"></i> Violation Anecdotal </a></li>
        <li><a href="{{ route('people.complaints') }}"><i class="fas fa-users"></i> Complaints</a></li>
        <li><a href="{{ route('complaints.appointments') }}"><i class="fas fa-cogs"></i> Complaints Appointments</a></li>
        <li><a href="{{ route('complaints.anecdotals') }}"><i class="fas fa-book"></i> Complaints Anecdotal</a></li>
        <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense&Sanctions </a></li>
        <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
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
              <!-- Edit Button -->
              <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $appointment->comp_app_id }}, '{{ $appointment->complaints_id }}', '{{ $appointment->comp_app_date }}', '{{ $appointment->comp_app_time }}', '{{ $appointment->comp_app_status }}')">Edit</button>

              <!-- Delete Button -->
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

<!-- Create Appointment Modal -->
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
            <option value="{{ $complaint->complaints_id }}">
              {{ $complaint->complaints_incident }}
            </option>
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

<!-- Edit Appointment Modal -->
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
            <option value="{{ $complaint->complaints_id }}">
              {{ $complaint->complaints_incident }}
            </option>
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
  // Open Create Modal
  function openAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'flex';
  }

  function closeAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'none';
  }

  // Open Edit Modal
  function openEditModal(id, complaintId, date, time, status) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editForm').action = '/complaints/appointments/' + id;
    document.getElementById('editComplaint').value = complaintId;
    document.getElementById('editDate').value = date;
    document.getElementById('editTime').value = time;
    document.getElementById('editStatus').value = status;
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // Search Table
  function searchTable() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let table = document.getElementById('appointmentsTable');
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
      let idCell = rows[i].getElementsByTagName('td')[0];
      let complaintCell = rows[i].getElementsByTagName('td')[1];
      if (idCell && complaintCell) {
        let idText = idCell.textContent || idCell.innerText;
        let complaintText = complaintCell.textContent || complaintCell.innerText;
        rows[i].style.display = (idText.toLowerCase().includes(input) || complaintText.toLowerCase().includes(input)) ? '' : 'none';
      }
    }
  }
</script>

</body>
</html>
