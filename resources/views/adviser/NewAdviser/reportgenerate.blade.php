@extends('adviser.NewAdviser.layout')

@section('content')
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Violation Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Include html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


    @yield('content')

    <!-- Simple Reports Title (no container, no line) -->
    <h3 class="reports-title">REPORTS</h3>

    <!-- Report Boxes Grid (EXACTLY AS BEFORE) -->
    <div class="reports-grid">
        <div class="report-box" data-modal="modal1"><i class="fas fa-book-open"></i>
            <h3>Anecdotal Records per Complaint Case</h3>
        </div>
        <div class="report-box" data-modal="modal2"><i class="fas fa-book"></i>
            <h3>Anecdotal Records per Violation Case</h3>
        </div>
        <div class="report-box" data-modal="modal3"><i class="fas fa-calendar-check"></i>
            <h3>Appointments Scheduled for Complaints</h3>
        </div>
        <div class="report-box" data-modal="modal4"><i class="fas fa-calendar-alt"></i>
            <h3>Appointments Scheduled for Violation Cases</h3>
        </div>
        <div class="report-box" data-modal="modal5"><i class="fas fa-user-tie"></i>
            <h3>Complaint Records by Adviser</h3>
        </div>
        <div class="report-box" data-modal="modal6"><i class="fas fa-file-alt"></i>
            <h3>Complaint Records with Complainant and Respondent</h3>
        </div>
        <div class="report-box" data-modal="modal7"><i class="fas fa-clock"></i>
            <h3>Complaints Filed within the Last 30 Days</h3>
        </div>
        <div class="report-box" data-modal="modal8"><i class="fas fa-chart-bar"></i>
            <h3>Common Offenses by Frequency</h3>
        </div>
        <div class="report-box" data-modal="modal9"><i class="fas fa-exclamation-triangle"></i>
            <h3>List of Violators with Repeat Offenses</h3>
        </div>
        <div class="report-box" data-modal="modal10"><i class="fas fa-gavel"></i>
            <h3>Offenses and Their Sanction Consequences</h3>
        </div>
        <div class="report-box" data-modal="modal11"><i class="fas fa-phone-alt"></i>
            <h3>Parent Contact Information for Students with Active Violations</h3>
        </div>
        <div class="report-box" data-modal="modal12"><i class="fas fa-chart-line"></i>
            <h3>Sanction Trends Across Time Periods</h3>
        </div>
        <div class="report-box" data-modal="modal13"><i class="fas fa-chalkboard-teacher"></i>
            <h3>Students and Their Class Advisers</h3>
        </div>
        <div class="report-box" data-modal="modal14"><i class="fas fa-user-graduate"></i>
            <h3>Students and Their Parents</h3>
        </div>
        <div class="report-box" data-modal="modal15"><i class="fas fa-user-shield"></i>
            <h3>Students with Both Violation and Complaint Records</h3>
        </div>
        <div class="report-box" data-modal="modal16"><i class="fas fa-user-friends"></i>
            <h3>Students with the Most Violation Records</h3>
        </div>
        <div class="report-box" data-modal="modal17"><i class="fas fa-layer-group"></i>
            <h3>Summary of Violations per Grade Level</h3>
        </div>
        <div class="report-box" data-modal="modal18"><i class="fas fa-users"></i>
            <h3>Violation Records and Assigned Adviser</h3>
        </div>
        <div class="report-box" data-modal="modal19"><i class="fas fa-exclamation-circle"></i>
            <h3>Violation Records with Violator Information</h3>
        </div>
    </div>

    <!-- Modals -->
    @for ($i = 1; $i <= 19; $i++)
        <div id="modal{{ $i }}" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>

                <div class="date-filter">
                    <label for="dateFilter{{ $i }}">Date Range:</label>
                    <select id="dateFilter{{ $i }}" onchange="handleDateFilterChange({{ $i }})">
                        <option value="all">All Time</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    <div id="dateInputs{{ $i }}" style="display: none;">
                        <input type="date" id="startDate{{ $i }}" placeholder="Start Date"
                            onchange="handleDateFilterChange({{ $i }})">
                        <input type="date" id="endDate{{ $i }}" placeholder="End Date"
                            onchange="handleDateFilterChange({{ $i }})">
                    </div>
                </div>

                <div class="toolbar">
                    <input type="text" placeholder="Search..."
                        oninput="liveSearch('modal{{ $i }}', this.value)">
                    <button class="btn btn-warning" onclick="printAsPDF('modal{{ $i }}')"><i
                            class="fas fa-print"></i> Export PDF</button>
                    <button class="btn btn-success" onclick="exportCSV('modal{{ $i }}')"><i
                            class="fas fa-file-csv"></i> Export CSV</button>
                </div>

                <div id="loading-{{ $i }}" class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading report data...</p>
                </div>

                <h2 class="text-xl font-semibold mb-3 text-center">
                    @switch($i)
                        @case(1)
                            Anecdotal Records per Complaint Case
                        @break

                        @case(2)
                            Anecdotal Records per Violation Case
                        @break

                        @case(3)
                            Appointments Scheduled for Complaints
                        @break

                        @case(4)
                            Appointments Scheduled for Violation Cases
                        @break

                        @case(5)
                            Complaint Records by Adviser
                        @break

                        @case(6)
                            Complaint Records with Complainant and Respondent
                        @break

                        @case(7)
                            Complaints Filed within the Last 30 Days
                        @break

                        @case(8)
                            Common Offenses by Frequency
                        @break

                        @case(9)
                            List of Violators with Repeat Offenses
                        @break

                        @case(10)
                            Offenses and Their Sanction Consequences
                        @break

                        @case(11)
                            Parent Contact Information for Students with Active Violations
                        @break

                        @case(12)
                            Sanction Trends Across Time Periods
                        @break

                        @case(13)
                            Students and Their Class Advisers
                        @break

                        @case(14)
                            Students and Their Parents
                        @break

                        @case(15)
                            Students with Both Violation and Complaint Records
                        @break

                        @case(16)
                            Students with the Most Violation Records
                        @break

                        @case(17)
                            Summary of Violations per Grade Level
                        @break

                        @case(18)
                            Violation Records and Assigned Adviser
                        @break

                        @case(19)
                            Violation Records with Violator Information
                        @break
                    @endswitch
                </h2>

                <table id="table-{{ $i }}" class="w-full border-collapse">
                    <thead>
                        @switch($i)
                            @case(1)
                                <tr>
                                    <th>Complainant Name</th>
                                    <th>Respondent Name</th>
                                    <th>Solution</th>
                                    <th>Recommendation</th>
                                    <th>Date Recorded</th>
                                    <th>Time Recorded</th>
                                </tr>
                            @break

                            @case(2)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Solution</th>
                                    <th>Recommendation</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            @break

                            @case(3)
                                <tr>
                                    <th>Complainant Name</th>
                                    <th>Respondent Name</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Status</th>
                                </tr>
                            @break

                            @case(4)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Appointment Status</th>
                                </tr>
                            @break

                            @case(5)
                                <tr>
                                    <th>Adviser Name</th>
                                    <th>Complainant Name</th>
                                    <th>Respondent Name</th>
                                    <th>Type of Offense</th>
                                    <th>Complaint Date</th>
                                    <th>Complaint Time</th>
                                </tr>
                            @break

                            @case(6)
                                <tr>
                                    <th>Complainant Name</th>
                                    <th>Respondent Name</th>
                                    <th>Incident Description</th>
                                    <th>Complaint Date</th>
                                    <th>Complaint Time</th>
                                </tr>
                            @break

                            @case(7)
                                <tr>
                                    <th>Complainant Name</th>
                                    <th>Respondent Name</th>
                                    <th>Offense Type</th>
                                    <th>Complaint Date</th>
                                    <th>Complaint Time</th>
                                </tr>
                            @break

                            @case(8)
                                <tr>
                                    <th>Offense Type</th>
                                    <th>Description</th>
                                    <th>Total Occurrences</th>
                                </tr>
                            @break

                            @case(9)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Section</th>
                                    <th>Grade Level</th>
                                    <th>Total Violations</th>
                                    <th>First Violation Date</th>
                                    <th>Most Recent Violation Date</th>
                                </tr>
                            @break

                            @case(10)
                                <tr>
                                    <th>Offense Type</th>
                                    <th>Offense Description</th>
                                    <th>Sanction Consequences</th>
                                </tr>
                            @break

                            @case(11)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Parent Name</th>
                                    <th>Parent Contact Info</th>
                                    <th>Violation Date</th>
                                    <th>Violation Time</th>
                                    <th>Violation Status</th>
                                </tr>
                            @break

                            @case(12)
                                <tr>
                                    <th>Offense Type</th>
                                    <th>Sanction Consequences</th>
                                    <th>Month and Year</th>
                                    <th>Number of Sanctions Given</th>
                                </tr>
                            @break

                            @case(13)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Adviser Name</th>
                                    <th>Section</th>
                                    <th>Grade Level</th>
                                </tr>
                            @break

                            @case(14)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Parent Name</th>
                                    <th>Parent Contact Info</th>
                                </tr>
                            @break

                            @case(15)
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Violation Count</th>
                                    <th>Complaint Involvement Count</th>
                                </tr>
                            @break

                            @case(16)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Adviser Section</th>
                                    <th>Grade Level</th>
                                    <th>Total Violations</th>
                                </tr>
                            @break

                            @case(17)
                                <tr>
                                    <th>Grade Level</th>
                                    <th>Offense Type</th>
                                    <th>Number of Violations</th>
                                </tr>
                            @break

                            @case(18)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Adviser Name</th>
                                    <th>Type of Offense</th>
                                    <th>Violation Date</th>
                                    <th>Violation Time</th>
                                    <th>Incident Description</th>
                                </tr>
                            @break

                            @case(19)
                                <tr>
                                    <th>Student Name</th>
                                    <th>Offense Type</th>
                                    <th>Sanction</th>
                                    <th>Incident Description</th>
                                    <th>Violation Date</th>
                                    <th>Violation Time</th>
                                </tr>
                            @break
                        @endswitch
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    @endfor
    </main>

    <script>
        /* dropdown */
        document.querySelectorAll('.dropdown-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const container = btn.nextElementSibling;
                document.querySelectorAll('.dropdown-btn').forEach(b => {
                    if (b !== btn) {
                        b.classList.remove('active');
                        b.nextElementSibling.style.display = 'none';
                    }
                });
                btn.classList.toggle('active');
                container.style.display = container.style.display === 'block' ? 'none' : 'block';
            });
        });

        /* Show notification - Updated to be modal and centered */
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const overlay = document.getElementById('notificationOverlay');

            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            overlay.style.display = 'block';

            // Auto-hide after 3 seconds
            setTimeout(() => {
                notification.style.display = 'none';
                overlay.style.display = 'none';
            }, 3000);
        }

        /* Toggle date inputs based on filter selection */
        function toggleDateInputs(modalId) {
            const filter = document.getElementById(`dateFilter${modalId}`).value;
            const dateInputs = document.getElementById(`dateInputs${modalId}`);

            if (filter === 'custom') {
                dateInputs.style.display = 'flex';
            } else {
                dateInputs.style.display = 'none';
            }
        }

        /* Handle date filter change - automatically refresh data */
        function handleDateFilterChange(modalId) {
            toggleDateInputs(modalId);
            applyDateFilter(modalId);
        }

        /* Apply date filter and refresh data */
        function applyDateFilter(modalId) {
            const filter = document.getElementById(`dateFilter${modalId}`).value;
            let startDate = '',
                endDate = '';

            if (filter === 'custom') {
                startDate = document.getElementById(`startDate${modalId}`).value;
                endDate = document.getElementById(`endDate${modalId}`).value;

                if (!startDate || !endDate) {
                    return; // Don't show error, just don't refresh
                }
            }

            // Refresh data with new filter parameters
            openReportModal(modalId, filter, startDate, endDate);
        }

        /* open modal + fetch with date filtering */
        async function openReportModal(reportId, dateFilter = 'all', startDate = '', endDate = '') {
            const modal = document.getElementById(`modal${reportId}`);
            const loading = document.getElementById(`loading-${reportId}`);
            const tableBody = modal.querySelector("tbody");

            modal.style.display = "block";
            loading.style.display = "block";
            tableBody.innerHTML = "";

            try {
                // Build query parameters for date filtering
                const params = new URLSearchParams();
                if (dateFilter && dateFilter !== 'all') {
                    params.append('date_filter', dateFilter);
                }
                if (startDate) {
                    params.append('start_date', startDate);
                }
                if (endDate) {
                    params.append('end_date', endDate);
                }

                const res = await fetch(`/adviser/reports/data/${reportId}?${params.toString()}`);
                const data = await res.json();
                console.log("Fetched data:", data);

                // Set the date filter dropdown to match current selection
                document.getElementById(`dateFilter${reportId}`).value = dateFilter;
                toggleDateInputs(reportId);

                // Populate table based on report ID
                if (reportId === 1) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.complainant_name || 'N/A'}</td>
            <td>${row.respondent_name || 'N/A'}</td>
            <td>${row.solution || 'N/A'}</td>
            <td>${row.recommendation || 'N/A'}</td>
            <td>${row.date_recorded || 'N/A'}</td>
            <td>${row.time_recorded || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 2) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.solution || 'N/A'}</td>
            <td>${row.recommendation || 'N/A'}</td>
            <td>${row.date || 'N/A'}</td>
            <td>${row.time || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 3) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.complainant_name || 'N/A'}</td>
            <td>${row.respondent_name || 'N/A'}</td>
            <td>${row.appointment_date || 'N/A'}</td>
            <td>${row.appointment_status || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 4) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.appointment_date || 'N/A'}</td>
            <td>${row.appointment_time || 'N/A'}</td>
            <td>${row.appointment_status || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 5) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.adviser_name || 'N/A'}</td>
            <td>${row.complainant_name || 'N/A'}</td>
            <td>${row.respondent_name || 'N/A'}</td>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.complaint_date || 'N/A'}</td>
            <td>${row.complaint_time || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 6) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.complainant_name || 'N/A'}</td>
            <td>${row.respondent_name || 'N/A'}</td>
            <td>${row.incident_description || 'N/A'}</td>
            <td>${row.complaint_date || 'N/A'}</td>
            <td>${row.complaint_time || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 7) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.complainant_name || 'N/A'}</td>
            <td>${row.respondent_name || 'N/A'}</td>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.complaint_date || 'N/A'}</td>
            <td>${row.complaint_time || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 8) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.description || 'N/A'}</td>
            <td>${row.total_occurrences || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 9) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.section || 'N/A'}</td>
            <td>${row.grade_level || 'N/A'}</td>
            <td>${row.total_violations || 'N/A'}</td>
            <td>${row.first_violation_date || 'N/A'}</td>
            <td>${row.most_recent_violation_date || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 10) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.offense_description || 'N/A'}</td>
            <td>${row.sanction_consequences || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 11) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.parent_name || 'N/A'}</td>
            <td>${row.parent_contact_info || 'N/A'}</td>
            <td>${row.violation_date || 'N/A'}</td>
            <td>${row.violation_time || 'N/A'}</td>
            <td>${row.violation_status || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 12) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.sanction_consequences || 'N/A'}</td>
            <td>${row.month_and_year || 'N/A'}</td>
            <td>${row.number_of_sanctions_given || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 13) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.adviser_name || 'N/A'}</td>
            <td>${row.section || 'N/A'}</td>
            <td>${row.grade_level || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 14) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.parent_name || 'N/A'}</td>
            <td>${row.parent_contact_info || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 15) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.first_name || 'N/A'}</td>
            <td>${row.last_name || 'N/A'}</td>
            <td>${row.violation_count || 'N/A'}</td>
            <td>${row.complaint_involvement_count || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 16) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.adviser_section || 'N/A'}</td>
            <td>${row.grade_level || 'N/A'}</td>
            <td>${row.total_violations || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 17) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.grade_level || 'N/A'}</td>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.number_of_violations || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 18) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.adviser_name || 'N/A'}</td>
            <td>${row.type_of_offense || 'N/A'}</td>
            <td>${row.violation_date || 'N/A'}</td>
            <td>${row.violation_time || 'N/A'}</td>
            <td>${row.incident_description || 'N/A'}</td>
          </tr>
        `;
                    });
                } else if (reportId === 19) {
                    data.forEach(row => {
                        tableBody.innerHTML += `
          <tr>
            <td>${row.student_name || 'N/A'}</td>
            <td>${row.offense_type || 'N/A'}</td>
            <td>${row.sanction || 'N/A'}</td>
            <td>${row.incident_description || 'N/A'}</td>
            <td>${row.violation_date || 'N/A'}</td>
            <td>${row.violation_time || 'N/A'}</td>
          </tr>
        `;
                    });
                } else {
                    // fallback for unlisted reports
                    data.forEach(row => {
                        const values = Object.values(row);
                        tableBody.innerHTML += `<tr>${values.map(v => `<td>${v ?? 'N/A'}</td>`).join('')}</tr>`;
                    });
                }

            } catch (error) {
                console.error("Error fetching data:", error);
                showNotification(`Error loading report data: ${error.message}`, 'error');
                tableBody.innerHTML =
                    `<tr><td colspan="10" style="text-align:center;color:red;">Error loading data. Please try again.</td></tr>`;
            } finally {
                loading.style.display = "none";
            }
        }

        /* attach event to boxes (report tiles) */
        document.querySelectorAll('.report-box').forEach(box => {
            box.addEventListener('click', () => openReportModal(box.dataset.modal.replace('modal', '')));
        });

        /* close modals */
        document.addEventListener('click', e => {
            if (e.target.classList.contains('close')) e.target.closest('.modal').style.display = 'none';
            if (e.target.classList.contains('modal')) e.target.style.display = 'none';
        });

        /* search (text input passes 'modalX' so we convert to numeric id) */
        function liveSearch(modalId, query) {
            const id = modalId.replace('modal', '');
            const table = document.getElementById('table-' + id);
            if (!table) return;
            query = query.toLowerCase();
            Array.from(table.querySelectorAll('tbody tr')).forEach(tr => {
                tr.style.display = Array.from(tr.querySelectorAll('td')).some(td => td.textContent.toLowerCase()
                    .includes(query)) ? '' : 'none';
            });
        }

        /* Print as PDF - Open in new tab for preview */
        function printAsPDF(modalId) {
            const modal = document.getElementById(modalId);
            const reportId = modalId.replace('modal', '');
            const table = document.getElementById(`table-${reportId}`);

            if (!table) {
                showNotification('No data available to export', 'error');
                return;
            }

            const currentDate = new Date().toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const currentTime = new Date().toLocaleTimeString('en-PH', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const reportTitle = modal.querySelector('h2').textContent;
            const rowCount = table.querySelectorAll('tbody tr').length;

            // Clone the table to avoid modifying the original
            const tableClone = table.cloneNode(true);

            // Create a temporary element for PDF generation
            const element = document.createElement('div');
            element.innerHTML = `
    <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
      <!-- Professional Header with Logo on Right -->
      <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
        <div style="flex: 1;">
          <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
          <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Student Violation Tracking System</h2>
          <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Official Report Document</p>
        </div>
        <div style="text-align: right;">
          <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
        </div>
      </div>

      <!-- Report Summary -->
      <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${reportTitle}</h3>
            <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
              Total Records: <strong style="color: #000000;">${rowCount}</strong>
            </p>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 12px; color: #000000;">Document ID</div>
            <div style="font-size: 14px; font-weight: 600; color: #000000;">REP-${Date.now().toString().slice(-6)}</div>
          </div>
        </div>
      </div>

      <!-- Enhanced Table Container -->
      <div style="overflow: hidden; margin: 0 25px;">
        ${tableClone.outerHTML}
      </div>

      <!-- Footer Section -->
      <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="text-align: left;">
            <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
            <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
              {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
            </div>
            <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
            <div style="font-size: 12px; color: #000000; margin-top: 5px;">
              Prefect of Discipline
            </div>
          </div>

          <!-- Date moved to bottom RIGHT -->
          <div style="text-align: right;">
            <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Generated On:</div>
            <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
              ${currentDate} at ${currentTime}
            </div>

          </div>
        </div>

        <!-- Confidential Notice -->
        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
          <div style="font-size: 11px; color: #c53030; font-weight: 600;">
            🔒 CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
          </div>
          <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
            This document contains sensitive student information. Unauthorized distribution is prohibited.
          </div>
        </div>
      </div>
    </div>
  `;

            // Enhanced table styling for PDF with proper page break handling
            const tables = element.getElementsByTagName('table');
            for (let table of tables) {
                table.style.width = '100%';
                table.style.borderCollapse = 'collapse';
                table.style.fontSize = '10px';
                table.style.tableLayout = 'fixed';

                // Style table headers with page break avoidance
                const headers = table.getElementsByTagName('th');
                for (let header of headers) {
                    header.style.backgroundColor = '#1e3a8a';
                    header.style.color = 'white';
                    header.style.padding = '8px 6px';
                    header.style.textAlign = 'left';
                    header.style.fontWeight = '600';
                    header.style.border = '1px solid #2d3748';
                    header.style.fontSize = '9px';
                    header.style.textTransform = 'uppercase';
                    header.style.letterSpacing = '0.5px';
                    header.style.pageBreakInside = 'avoid';
                    header.style.breakInside = 'avoid';
                }

                // Style table cells with proper page break handling
                const cells = table.getElementsByTagName('td');
                for (let cell of cells) {
                    cell.style.padding = '6px 4px';
                    cell.style.border = '1px solid #e2e8f0';
                    cell.style.fontSize = '9px';
                    cell.style.color = '#000000';
                    cell.style.pageBreakInside = 'avoid';
                    cell.style.breakInside = 'avoid';
                    cell.style.wordWrap = 'break-word';
                    cell.style.overflowWrap = 'break-word';
                }

                // Style table rows with page break handling
                const rows = table.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    rows[i].style.pageBreakInside = 'avoid';
                    rows[i].style.breakInside = 'avoid';

                    if (i % 2 === 0) {
                        rows[i].style.backgroundColor = '#ffffff';
                    } else {
                        rows[i].style.backgroundColor = '#f7fafc';
                    }
                }

                // Style table header row specifically
                const thead = table.getElementsByTagName('thead')[0];
                if (thead) {
                    thead.style.display = 'table-header-group';
                }
            }

            // PDF options for new tab preview
            const options = {
                margin: [10, 15, 25, 15],
                filename: `${reportTitle.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    scrollY: -window.scrollY
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait',
                    compress: true,
                    hotfixes: ["px_scaling"]
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy'],
                    before: '.page-break-before',
                    after: '.page-break-after',
                    avoid: ['tr', 'td', 'th']
                }
            };

            showNotification('Opening PDF preview...', 'info');

            // Generate PDF and open in new tab
            html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                const totalPages = pdf.internal.getNumberOfPages();

                // Add footer to each page
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);

                    // Add footer with system name on left and page number on right
                    pdf.setFontSize(8);
                    pdf.setTextColor(100, 100, 100);

                    // System name on left footer
                    pdf.text('Tagoloan Senior High School - Student Violation Tracking System',
                        pdf.internal.pageSize.getWidth() / 2 - 65,
                        pdf.internal.pageSize.getHeight() - 8);

                    // Page number on right footer
                    pdf.text(`Page ${i} of ${totalPages}`,
                        pdf.internal.pageSize.getWidth() - 25,
                        pdf.internal.pageSize.getHeight() - 8);
                }

                // Open PDF in new tab
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);
                window.open(pdfUrl, '_blank');

                showNotification('PDF opened in new tab', 'success');
            }).catch(error => {
                console.error('PDF generation error:', error);
                showNotification('PDF generation failed. Please try again.', 'error');
            });
        }

        /* export CSV expects modal string 'modalX' */
        function exportCSV(modalId) {
            const id = modalId.replace('modal', '');
            const table = document.getElementById('table-' + id);
            if (!table) return;
            const rows = Array.from(table.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
            const csv = rows.map((row, i) => {
                const cells = Array.from(row.querySelectorAll(i === 0 ? 'th' : 'td'));
                return cells.map(c => `"${(c.textContent||'').replace(/"/g,'""')}"`).join(',');
            }).join('\n');
            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report-${id}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            showNotification('CSV exported successfully!', 'success');
        }

        function logout() {
            const confirmLogout = confirm("Are you sure you want to logout?");
            if (!confirmLogout) return;

            fetch("{{ route('prefect.logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Redirect to login after successful logout
                        window.location.href = "{{ route('login') }}";
                    } else {
                        console.error('Logout failed:', response.statusText);
                    }
                })
                .catch(error => console.error('Logout failed:', error));
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const userInfo = document.querySelector('.user-info');

            if (!userInfo.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>
@endsection
