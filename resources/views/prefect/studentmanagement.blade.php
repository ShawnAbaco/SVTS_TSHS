<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/STUDENTS.css') }}">
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
            <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions </a></li>
            <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
            <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>

    <main>
        <h2>Student Management</h2>
        <div class="flex gap-2 mb-3 w-100">
            <input type="text" id="searchInput" class="form-control" placeholder="Search students...">
            <select id="sectionFilter" class="form-select" style="max-width: 200px;">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                    <option value="{{ $section }}">{{ $section }}</option>
                @endforeach
            </select>
        </div>

        <table id="studentTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr data-section="{{ $student->adviser->adviser_section }}">
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
                        <td>{{ $student->adviser->adviser_gradelevel }}</td>
                        <td>{{ $student->adviser->adviser_section }}</td>
                        <td>
                            <div class="action-container">
                                <button class="btn btn-secondary btn-sm" onclick="showFullInfo({{ $student->student_id }})">Info</button>
                                <form action="{{ route('student.delete', $student->student_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <!-- Info Modal -->
    <div class="modal" id="infoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="infoModalTitle">Info</h5>
                <button class="btn-close" onclick="closeInfoModal()">&times;</button>
            </div>
            <div class="modal-body" id="infoModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeInfoModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function showFullInfo(studentId) {
            const row = Array.from(document.querySelectorAll('#studentTable tbody tr'))
                .find(tr => tr.children[0].innerText == studentId).children;

            document.getElementById('infoModalTitle').textContent = `Info: ${row[1].innerText}`;
            document.getElementById('infoModalBody').innerHTML = `
                <p><strong>Grade Level:</strong> ${row[2].innerText}</p>
                <p><strong>Section:</strong> ${row[3].innerText}</p>
            `;
            document.getElementById('infoModal').classList.add('show-modal');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('show-modal');
        }

        const searchInput = document.getElementById('searchInput');
        const sectionFilter = document.getElementById('sectionFilter');

        function filterTable() {
            const query = searchInput.value.toLowerCase();
            const section = sectionFilter.value;

            document.querySelectorAll('#studentTable tbody tr').forEach(row => {
                const name = row.children[1].innerText.toLowerCase();
                const sec = row.children[3].innerText;
                row.style.display = (name.includes(query) && (section === '' || sec === section)) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTable);
        sectionFilter.addEventListener('change', filterTable);
    </script>
</body>
</html>
