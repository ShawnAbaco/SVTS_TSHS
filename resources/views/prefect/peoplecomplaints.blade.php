<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Complaints Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/COMPLAINTS.css') }}">
<style>
/* Search bar styling */
.search-container {
  margin: 15px 0;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.search-container input {
  padding: 8px 12px;
  width: 250px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
</style>
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
        <li><a href="{{ route('complaints.appointments') }}"><i class="fas fa-cogs"></i> Complaints Appointments </a></li>
        <li><a href="{{ route('complaints.anecdotals') }}"><i class="fas fa-book"></i> Complaints Anecdotal</a></li>
        <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions </a></li>
        <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
        <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
</div>

<!-- Content -->
<div class="content">
  <h1>Complaints Management</h1>
  <button class="btn-primary" onclick="openModal()">+ Add Complainant</button>

  <!-- Search Bar -->
  <div class="search-container">
    <input type="text" id="searchInput" onkeyup="searchComplainant()" placeholder="Search Complainant Name...">
  </div>

  <table id="complaintsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Offense</th>
        <th>Sanction</th>
        <th>Date</th>
        <th>Time</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($complaints as $complaint)
      <tr>
        <td>{{ $complaint->complaints_id }}</td>
        <td>{{ $complaint->complainant->student_fname }} {{ $complaint->complainant->student_lname }}</td>
        <td>{{ $complaint->respondent->student_fname }} {{ $complaint->respondent->student_lname }}</td>
        <td>{{ $complaint->offense->offense_type }}</td>
        <td>{{ $complaint->offense->sanction_consequences }}</td>
        <td>{{ $complaint->complaints_date }}</td>
        <td>{{ \Carbon\Carbon::parse($complaint->complaints_time)->format('h:i A') }}</td>
        <td>
          <a href="{{ route('complaints.edit', $complaint->complaints_id) }}" class="btn-edit">Edit</a>
          <form action="{{ route('complaints.destroy', $complaint->complaints_id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn-delete" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
function logout() {
    fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/prefect/login');
}

// Search function for complainant name
function searchComplainant() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("complaintsTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[1]; // Complainant column
        if (td) {
            let textValue = td.textContent || td.innerText;
            tr[i].style.display = textValue.toLowerCase().includes(input) ? "" : "none";
        }
    }
}
</script>

</body>
</html>
