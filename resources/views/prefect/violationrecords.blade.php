<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Violation Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Roboto', Arial, sans-serif; background-color: #f4f6f9; color: #333; line-height: 1.6; }
a { text-decoration: none; color: inherit; }

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background: linear-gradient(135deg,rgb(7, 184, 228),rgb(13, 141, 205));
    color: #000;
    box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    padding: 10px;
    position: fixed;
    height: 100%;
    overflow-y: auto;
    font-weight: bold;
}
.sidebar p { text-align: center; color: #000; margin-bottom: 20px; font-size: 18px; font-weight: bold; }
.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li { margin-bottom: 8px; }
.sidebar ul li a, .sidebar ul li { display: block; padding: 10px 15px; font-size: 16px; border-radius: 6px; color: #000; transition: background 0.3s ease; font-weight: bold; }
.sidebar ul li:hover, .sidebar ul li.active { background: rgb(246, 246, 246); }

.main-content { margin-left: 260px; padding: 20px; }
.crud-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.crud-container h2 { margin-bottom: 20px; font-size: 24px; display: flex; justify-content: space-between; align-items: center; }

.search-create {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}
.search-create input {
    padding: 8px;
    width: 250px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.search-create .btn-create {
    background-color: #28a745;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.search-create .btn-create i { margin-right: 5px; }

table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
thead { background-color: #343a40; color: white; }

.btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
.btn-info { background-color: #17a2b8; color: white; }
.btn-info i { margin-right: 5px; }

/* Modal Styles */
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
.modal.show { display: flex; }
.modal-content { background: #fff; padding: 20px; width: 100%; max-width: 500px; border-radius: 8px; position: relative; }
.modal-content h5 { margin-bottom: 15px; }
.modal-content .close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 18px; }
.info-box p { margin: 8px 0; font-size: 16px; }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <p>PREFECT DASHBOARD</p>
    <ul>
        <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
        <li><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List </a></li>
        <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
        <li><a href="{{ route('violation.records') }}" class="active"><i class="fas fa-gavel"></i> Violation Record </a></li>
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
<div class="main-content">
    <div class="crud-container">
        <h2>Student Violations</h2>

        <!-- Search + Create Button -->
        <div class="search-create">
            <input type="text" id="searchInput" placeholder="Search student name...">
            <button class="btn-create"><i class="fas fa-plus"></i> Create Violation</button>
        </div>

        <table id="violationTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Violation</th>
                    <th>Adviser</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}</td>
                    <td>{{ $violation->offense->offense_type }}</td>
                    <td>{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}</td>
                    <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
                    <td>
                        <button class="btn btn-info"
                            onclick="showInfo('{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}', '{{ $violation->student->parent->parent_fname }} {{ $violation->student->parent->parent_lname }}', '{{ $violation->student->parent->parent_contactinfo }}', '{{ $violation->student->adviser->adviser_fname }} {{ $violation->student->adviser->adviser_lname }}')">
                            <i class="fas fa-info-circle"></i> Info
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No violations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Modal -->
<div class="modal" id="infoModal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('infoModal').classList.remove('show')">&times;</span>
        <h5>Violation Details</h5>
        <div class="info-box">
            <p><strong>Student Name:</strong> <span id="modalStudent">N/A</span></p>
            <p><strong>Parent Name:</strong> <span id="modalParent">N/A</span></p>
            <p><strong>Parent Contact:</strong> <span id="modalNumber">N/A</span></p>
            <p><strong>Adviser:</strong> <span id="modalAdviser">N/A</span></p>
        </div>
    </div>
</div>

<script>
function showInfo(student, parent, number, adviser) {
    document.getElementById('modalStudent').textContent = student;
    document.getElementById('modalParent').textContent = parent;
    document.getElementById('modalNumber').textContent = number;
    document.getElementById('modalAdviser').textContent = adviser;
    document.getElementById('infoModal').classList.add('show');
}

function logout() {
    fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(() => window.location.href = '/prefect/login')
    .catch(err => console.error('Logout failed:', err));
}

// Close modal on outside click
window.onclick = function(event) {
    if(event.target == document.getElementById('infoModal')) {
        document.getElementById('infoModal').classList.remove('show');
    }
}

// Search Functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#violationTable tbody tr");
    rows.forEach(row => {
        let studentName = row.cells[1].textContent.toLowerCase();
        row.style.display = studentName.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>
