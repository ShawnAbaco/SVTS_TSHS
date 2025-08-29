<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERVIEW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/DASHBOARD.css') }}">
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
    <main>
        <section id="dashboard-overview" class="content-section">
            <h2>Dashboard Overview</h2>
            
            <!-- Boxes -->
            <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; font-weight:bold;">
                
                <!-- Violations Box -->
                <div style="flex: 1; background:rgb(255, 5, 5); color: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; cursor:pointer;" 
                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.2)'" 
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'" 
                    onclick="openViolationModal()">
                    <h3>Students Violations</h3>
                    <p>Total Violations</p>
                </div>

                <!-- Reports Box -->
                <div style="flex: 1; background:rgb(0, 102, 246); color: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; cursor:pointer;" 
                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.2)'" 
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'"
                    onclick="openReportModal()">
                    <h3>Reports</h3>
                    <p>Total Reports</p>
                </div>

                <!-- Students Box -->
                <div style="flex: 1; background:rgb(0, 0, 0); color: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; cursor:pointer;" 
                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.2)'" 
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'"
                    onclick="openStudentModal()">
                    <h3>Students</h3>
                    <p>Total Students</p>
                </div>
            </div>

            <!-- Line Graph Section -->
            <div style="background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>Performance Overview</h3>
                <div style="max-width: 1500px; margin: 0 auto;">
                    <canvas id="lineGraph" width="400" height="360"></canvas>
                </div>
            </div>
        </section>
    </main>

    <!-- Violation Modal -->
    <div id="violationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeViolationModal()">&times;</span>
            <h2>Student Violations</h2>
            <div class="btn-actions">
                <button onclick="printTable()">🖨 Print</button>
            </div>
            <table id="violationTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Grade</th>
                        <th>Violation</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Juan Dela Cruz</td><td>Grade 11</td><td>Littering</td><td>2025-08-01</td></tr>
                    <tr><td>Maria Santos</td><td>Grade 12</td><td>Cutting Classes</td><td>2025-08-02</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Students Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStudentModal()">&times;</span>
            <h2>Student List</h2>
            <div class="btn-actions">
                <button onclick="showGrade11()">Grade 11 (3 Students)</button>
                <button onclick="showGrade12()">Grade 12 (3 Students)</button>
                <button onclick="printStudentTable()">🖨 Print</button>
            </div>
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Reports Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReportModal()">&times;</span>
            <h2>Reports</h2>
            <div class="btn-actions">
                <button onclick="printReportTable()">🖨 Print</button>
            </div>
            <table id="reportTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Violation</th>
                        <th>Adviser</th>
                        <th>Grade</th>
                        <th>Date of Violation</th>
                        <th>Parents</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Dela Cruz</td>
                        <td>Littering</td>
                        <td>Mr. Reyes</td>
                        <td>Grade 11</td>
                        <td>2025-08-01</td>
                        <td>Maria Dela Cruz</td>
                    </tr>
                    <tr>
                        <td>Maria Santos</td>
                        <td>Cutting Classes</td>
                        <td>Mrs. Villanueva</td>
                        <td>Grade 12</td>
                        <td>2025-08-02</td>
                        <td>Carlos Santos</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart Info Modal -->
    <div id="chartModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;">
        <div style="background:#fff; padding:20px; border-radius:10px; width:400px; text-align:center; position:relative;">
            <span onclick="closeChartModal()" style="position:absolute; top:10px; right:15px; font-size:20px; cursor:pointer;">&times;</span>
            <h2 id="chartMonth" style="margin-bottom:10px;"></h2>
            <p><strong>Total Violations:</strong> <span id="chartTotal"></span></p>
            <p><strong>Most Violations:</strong> <span id="chartHighest"></span></p>
            <button onclick="closeChartModal()" style="margin-top:15px; padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">Close</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js Line Graph
        const ctx = document.getElementById('lineGraph').getContext('2d');

        const grade11Data = [80, 85, 82, 90, 88, 92, 95, 97, 93, 96, 94, 98];
        const grade12Data = [75, 78, 80, 85, 87, 90, 92, 94, 91, 95, 93, 97];

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Grade 11',
                        data: grade11Data,
                        borderColor: 'red',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        pointBackgroundColor: 'red',
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Grade 12',
                        data: grade12Data,
                        borderColor: 'blue',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        pointBackgroundColor: 'blue',
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { weight: 'bold' }, color: 'black' }
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    x: { ticks: { font: { weight: 'bold', size: 14 }, color: 'black' } },
                    y: { ticks: { font: { weight: 'bold', size: 14 }, color: 'black' } }
                },
                onClick: (evt) => {
                    const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if (points.length) {
                        const index = points[0].index;
                        const month = months[index];
                        const grade11Value = grade11Data[index];
                        const grade12Value = grade12Data[index];

                        const total = grade11Value + grade12Value;
                        const highestGrade = grade11Value > grade12Value ? 'Grade 11' : 'Grade 12';

                        // Update modal content
                        document.getElementById('chartMonth').innerText = `📅 Month: ${month}`;
                        document.getElementById('chartTotal').innerText = total;
                        document.getElementById('chartHighest').innerText = highestGrade;

                        // Show modal
                        document.getElementById('chartModal').style.display = 'flex';
                    }
                }
            }
        });

        // Modal functions for Violation
        function openViolationModal() { document.getElementById('violationModal').style.display = 'flex'; }
        function closeViolationModal() { document.getElementById('violationModal').style.display = 'none'; }

        // Modal functions for Student
        function openStudentModal() { document.getElementById('studentModal').style.display = 'flex'; showGrade11(); }
        function closeStudentModal() { document.getElementById('studentModal').style.display = 'none'; }

        // Modal functions for Report
        function openReportModal() { document.getElementById('reportModal').style.display = 'flex'; }
        function closeReportModal() { document.getElementById('reportModal').style.display = 'none'; }

        // Chart modal close
        function closeChartModal() {
            document.getElementById('chartModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === document.getElementById('violationModal')) closeViolationModal();
            if (event.target === document.getElementById('studentModal')) closeStudentModal();
            if (event.target === document.getElementById('reportModal')) closeReportModal();
            if (event.target === document.getElementById('chartModal')) closeChartModal();
        }

        // Student Data
        const grade11Students = [
            { name: 'Juan Dela Cruz', grade: 'Grade 11' },
            { name: 'Pedro Santos', grade: 'Grade 11' },
            { name: 'Maria Lopez', grade: 'Grade 11' }
        ];
        const grade12Students = [
            { name: 'Ana Reyes', grade: 'Grade 12' },
            { name: 'Jose Bautista', grade: 'Grade 12' },
            { name: 'Carla Mendoza', grade: 'Grade 12' }
        ];

        function showGrade11() {
            const tbody = document.querySelector('#studentTable tbody');
            tbody.innerHTML = '';
            grade11Students.forEach(student => {
                tbody.innerHTML += `<tr><td>${student.name}</td><td>${student.grade}</td></tr>`;
            });
        }

        function showGrade12() {
            const tbody = document.querySelector('#studentTable tbody');
            tbody.innerHTML = '';
            grade12Students.forEach(student => {
                tbody.innerHTML += `<tr><td>${student.name}</td><td>${student.grade}</td></tr>`;
            });
        }

        // Print Violation Table
        function printTable() {
            const tableContent = document.getElementById('violationTable').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print</title></head><body>');
            newWin.document.write('<h2>Student Violations</h2>');
            newWin.document.write(tableContent);
            newWin.document.write('</body></html>');
            newWin.document.close();
            newWin.print();
        }

        // Print Student Table
        function printStudentTable() {
            const tableContent = document.getElementById('studentTable').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print Student List</title></head><body>');
            newWin.document.write('<h2>Student List</h2>');
            newWin.document.write(tableContent);
            newWin.document.write('</body></html>');
            newWin.document.close();
            newWin.print();
        }

        // Print Report Table
        function printReportTable() {
            const tableContent = document.getElementById('reportTable').outerHTML;
            const newWin = window.open('');
            newWin.document.write('<html><head><title>Print Reports</title></head><body>');
            newWin.document.write('<h2>Reports</h2>');
            newWin.document.write(tableContent);
            newWin.document.write('</body></html>');
            newWin.document.close();
            newWin.print();
        }
    </script>
</body>
</html>
