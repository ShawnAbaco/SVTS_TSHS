<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Complaints Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/cards.css') }}">
</head>
<body>

<!-- Sidebar -->
  <div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
    <ul>
      <div class="section-title">Main</div>
      <li class="active"><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
      <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
      <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
      <li><a href="{{ route('violation.records') }}"><i class="fas fa-book"></i> Violation Record</a></li>
        <li class="active"><a href="{{ route('people.complaints') }}"><i class="fas fa-comments"></i>Complaints</a></li>
      <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>

<!-- Content -->
<div class="content">
  <h1>Complaints Management</h1>

  <div class="top-controls">
    <input type="text" id="searchInput" placeholder="Search Complainant Name..." onkeyup="searchComplainant()">
    <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add Complainant</button>
    <button class="btn-archive" onclick="openArchive()"><i class="fas fa-archive"></i> Archive</button>
  </div>

  <table id="complaintsTable">
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>ID</th>
        <th>Complainant</th>
        <th>Respondent</th>
        <th>Offense</th>
        <th>Sanction</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($complaints as $complaint)
      <tr>
        <td><input type="checkbox" class="student-checkbox"></td>
        <td>{{ $complaint->complaints_id }}</td>
        <td>{{ $complaint->complainant->student_fname }} {{ $complaint->complainant->student_lname }}</td>
        <td>{{ $complaint->respondent->student_fname }} {{ $complaint->respondent->student_lname }}</td>
        <td>{{ $complaint->offense->offense_type }}</td>
        <td>{{ $complaint->offense->sanction_consequences }}</td>
        <td>{{ $complaint->complaints_date }}</td>
        <td>{{ \Carbon\Carbon::parse($complaint->complaints_time)->format('h:i A') }}</td>
        <td class="{{ $complaint->status == 'Resolved' ? 'status-resolved' : 'status-pending' }}">
            {{ $complaint->status ?? 'Pending' }}
        </td>
        <td>
          <button class="btn-action btn-update"><i class="fas fa-edit"></i>Update</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.student-checkbox');
selectAll.addEventListener('change', () => { checkboxes.forEach(cb => cb.checked = selectAll.checked); });
checkboxes.forEach(cb => cb.addEventListener('change', () => {
  selectAll.checked = document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length;
}));

const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => { btn.addEventListener('click', () => {
  const container = btn.nextElementSibling;
  dropdowns.forEach(otherBtn => { const otherContainer = otherBtn.nextElementSibling; if (otherBtn !== btn) { otherBtn.classList.remove('active'); otherContainer.style.display = 'none'; }});
  btn.classList.toggle('active');
  container.style.display = container.style.display === 'block' ? 'none' : 'block';
})});

function logout() {
    if (!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('prefect.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(response => { if(response.ok){ window.location.href = "{{ route('auth.login') }}"; } })
    .catch(error => console.error('Logout failed:', error));
}

function searchComplainant() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let table = document.getElementById("complaintsTable");
  let tr = table.getElementsByTagName("tr");
  for (let i = 1; i < tr.length; i++) {
    let td = tr[i].getElementsByTagName("td")[2];
    tr[i].style.display = td && td.textContent.toLowerCase().includes(input) ? "" : "none";
  }
}

function openModal() { alert("Open Add Complainant form/modal here."); }
function openArchive() { alert("Open Archive modal here."); }
</script>

</body>
</html>
