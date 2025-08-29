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
            <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense&Sanctions </a></li>
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
                <option value="Section A">Section A</option>
                <option value="Section B">Section B</option>
            </select>
        </div>

        <table id="studentTable">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
            <tbody id="studentTableBody"></tbody>
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

    <!-- Message Modal -->
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="messageModalTitle">Send Message</h5>
                <button class="btn-close" onclick="closeMessageModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display:flex; justify-content: flex-end; margin-bottom: 10px;">
                    <label for="contactOption" style="margin-right:5px;">Send via:</label>
                    <select id="contactOption" onchange="updateMessagePreview()">
                        <option value="email">Email Only</option>
                        <option value="contact">Contact Number Only</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <label for="emailMessage">Message:</label>
                <textarea id="emailMessage" rows="6" placeholder="Write your message here..." oninput="updateMessagePreview()"></textarea>

                <!-- Preview for "Both" -->
                <div id="messagePreview" style="margin-top:10px; font-style: italic; color:#555;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeMessageModal()">Cancel</button>
                <button type="button" class="btn btn-secondary" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <script>
        let students = [
            { 
                id: 1, 
                name: 'John Doe', 
                gradeLevel: 'Grade 11', 
                section: 'Section A', 
                email: 'johndoe@example.com', 
                contactNumber: '123-456-7890', 
                adviser: 'Mr. Smith',
                parentName: 'Mary Doe'
            },
            { 
                id: 2, 
                name: 'Jane Smith', 
                gradeLevel: 'Grade 12', 
                section: 'Section B', 
                email: 'janesmith@example.com', 
                contactNumber: '987-654-3210', 
                adviser: 'Ms. Johnson',
                parentName: 'Robert Smith'
            }
        ];

        let currentStudentId = null;

        function renderTable() {
            const tableBody = document.getElementById('studentTableBody');
            const search = document.getElementById('searchInput').value.toLowerCase();
            const section = document.getElementById('sectionFilter').value;

            tableBody.innerHTML = '';
            students
                .filter(student => {
                    const matchesSearch = (
                        student.name.toLowerCase().includes(search) ||
                        student.section.toLowerCase().includes(search) ||
                        student.gradeLevel.toLowerCase().includes(search)
                    );
                    const matchesSection = section === '' || student.section === section;
                    return matchesSearch && matchesSection;
                })
                .forEach(student => {
                    tableBody.innerHTML += `
                        <tr data-id="${student.id}">
                            <td>${student.id}</td>
                            <td>${student.name}</td>
                            <td>${student.gradeLevel}</td>
                            <td>${student.section}</td>
                            <td>
                                <div class="action-container">
                                    <button class="btn btn-secondary btn-sm" onclick="showFullInfo(${student.id})">Info</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteStudent(${student.id})">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        function showFullInfo(studentId) {
            currentStudentId = studentId;
            const student = students.find(s => s.id === studentId);
            document.getElementById('infoModalTitle').textContent = `Info: ${student.name}`;
            document.getElementById('infoModalBody').innerHTML = `
                <p><strong>Parent Name:</strong> ${student.parentName}</p>
                <p><strong>Email:</strong> ${student.email}</p>
                <p><strong>Contact Number:</strong> ${student.contactNumber}</p>
                <p><strong>Adviser:</strong> ${student.adviser}</p>
                <button class="btn btn-secondary btn-sm" onclick="openMessageModal(${student.id})">Send Message</button>
            `;
            document.getElementById('infoModal').classList.add('show-modal');
        }

        function openMessageModal(studentId) {
            currentStudentId = studentId;
            document.getElementById('emailMessage').value = '';
            document.getElementById('contactOption').value = 'email';
            document.getElementById('messagePreview').innerHTML = '';
            document.getElementById('messageModal').classList.add('show-modal');
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('show-modal');
        }

        function updateMessagePreview() {
            const option = document.getElementById('contactOption').value;
            const message = document.getElementById('emailMessage').value.trim();
            const preview = document.getElementById('messagePreview');

            if(option === 'both' && message) {
                preview.innerHTML = `Message to be sent to both Email and Contact Number: <br>${message}`;
            } else {
                preview.innerHTML = '';
            }
        }

        function sendMessage() {
            const message = document.getElementById('emailMessage').value.trim();
            const option = document.getElementById('contactOption').value;

            if (!message) {
                alert('Please write a message before sending.');
                return;
            }

            const student = students.find(s => s.id === currentStudentId);
            if (!student) return;

            if(option === 'email') {
                window.location.href = `mailto:${student.email}?body=${encodeURIComponent(message)}`;
            } else if(option === 'contact') {
                window.location.href = `sms:${student.contactNumber}?body=${encodeURIComponent(message)}`;
            } else if(option === 'both') {
                window.open(`mailto:${student.email}?body=${encodeURIComponent(message)}`, '_blank');
                window.open(`sms:${student.contactNumber}?body=${encodeURIComponent(message)}`, '_blank');
            }

            closeMessageModal();
            alert("Your message is successfully sent to parents.");
        }

        function closeInfoModal() { 
            document.getElementById('infoModal').classList.remove('show-modal'); 
        }

        function deleteStudent(id) {
            if(confirm('Are you sure you want to delete this student?')) {
                students = students.filter(s => s.id !== id);
                renderTable();
            }
        }

        function logout() {
            fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => window.location.href = '/prefect/login')
                .catch(error => console.error('Logout failed:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
            document.getElementById('searchInput').addEventListener('input', renderTable);
            document.getElementById('sectionFilter').addEventListener('change', renderTable);
        });
    </script>
</body>
</html>
