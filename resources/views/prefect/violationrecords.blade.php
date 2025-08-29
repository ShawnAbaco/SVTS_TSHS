<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Violation Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/VIOLATIONRECORD.css') }}">
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

<div class="container">
    <h1>Student Violations</h1>
    <div class="card">
        <div class="card-header">All Violations</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Violation</th>
                        <th>Adviser</th>
                        <th>Location</th>
                        <th>Date / Year</th>
                        <th>Time</th>
                        <th>Info</th>
                    </tr>
                </thead>
                <tbody id="violationsList">
                    <tr>
                        <td>1</td>
                        <td>Juan Dela Cruz</td>
                        <td>Late Submission</td>
                        <td>Ms. Santos</td>
                        <td>Room 101</td>
                        <td>2025</td>
                        <td>08:00 AM</td>
                        <td>
                            <button class="btn-info" onclick="openModal('Juan Dela Cruz', 'Mr. Dela Cruz', '09171234567', 'Ms. Santos')">Info</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Maria Clara</td>
                        <td>Classroom Misconduct</td>
                        <td>Mr. Reyes</td>
                        <td>Room 102</td>
                        <td>2025</td>
                        <td>09:30 AM</td>
                        <td>
                            <button class="btn-info" onclick="openModal('Maria Clara', 'Mrs. Clara', '09179876543', 'Mr. Reyes')">Info</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="infoModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3>Student Information</h3>
        <p><strong>Student Name:</strong> <span id="modalStudent"></span></p>
        <p><strong>Parent Name:</strong> <span id="modalParent"></span></p>
        <p><strong>Parent Number:</strong> <span id="modalNumber"></span></p>
        <p><strong>Reported By:</strong> <span id="modalReporter"></span></p>
    </div>
</div>

<script>
function logout() {
    fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(() => window.location.href = '/prefect/login')
    .catch(err => console.error('Logout failed:', err));
}

function openModal(student, parent, number, reporter) {
    document.getElementById('modalStudent').innerText = student;
    document.getElementById('modalParent').innerText = parent;
    document.getElementById('modalNumber').innerText = number;
    document.getElementById('modalReporter').innerText = reporter;
    document.getElementById('infoModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('infoModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('infoModal')) {
        closeModal();
    }
}
</script>

</body>
</html>
