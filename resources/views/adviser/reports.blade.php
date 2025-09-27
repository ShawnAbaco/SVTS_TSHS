<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Reports</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/adviser/reports.css') }}">

</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.list') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-book"></i>Violation Record</a></li>
    <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i>Complaints</a></li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li class="active"><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>
<div class="main-content">
  <!-- Page Header -->
  <div class="page-header">
    <h1>REPORTS</h1>
  </div>
  <!-- 20 Report Boxes (Sorted A-Z) -->
  <div class="report-box" data-modal="modal1"><i class="fas fa-book-open"></i><h3>Anecdotal Records per Complaint Case</h3></div>
  <div class="report-box" data-modal="modal2"><i class="fas fa-book"></i><h3>Anecdotal Records per Violation Case</h3></div>
  <div class="report-box" data-modal="modal3"><i class="fas fa-calendar-check"></i><h3>Appointments Scheduled for Complaints</h3></div>
  <div class="report-box" data-modal="modal4"><i class="fas fa-calendar-alt"></i><h3>Appointments Scheduled for Violation Cases</h3></div>
  <div class="report-box" data-modal="modal5"><i class="fas fa-file-alt"></i><h3>Complaint Records with Complainant and Respondent</h3></div>
  <div class="report-box" data-modal="modal6"><i class="fas fa-clock"></i><h3>Complaints Filed within the Last 30 Days</h3></div>
  <div class="report-box" data-modal="modal7"><i class="fas fa-chart-bar"></i><h3>Common Offenses by Frequency</h3></div>
  <div class="report-box" data-modal="modal8"><i class="fas fa-exclamation-triangle"></i><h3>List of Violators with Repeat Offenses</h3></div>
  <div class="report-box" data-modal="modal9"><i class="fas fa-gavel"></i><h3>Offenses and Their Sanction Consequences</h3></div>
  <div class="report-box" data-modal="modal10"><i class="fas fa-phone-alt"></i><h3>Parent Contact Info for Students with Active Violations</h3></div>
  <div class="report-box" data-modal="modal11"><i class="fas fa-chart-line"></i><h3>Sanction Trends Across Time Periods</h3></div>
  <div class="report-box" data-modal="modal12"><i class="fas fa-user-graduate"></i><h3>Students and Their Parents</h3></div>
  <div class="report-box" data-modal="modal13"><i class="fas fa-user-shield"></i><h3>Students with Both Violation and Complaint Records</h3></div>
  <div class="report-box" data-modal="modal14"><i class="fas fa-user-friends"></i><h3>Students with the Most Violation Records</h3></div>
  <div class="report-box" data-modal="modal15"><i class="fas fa-exclamation-circle"></i><h3>Violation Records with Violator Information</h3></div>
</div>


<!-- Modals -->
@for($i=1; $i<=15; $i++)
<div id="modal{{ $i }}" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
            <div class="adviser-info" style="margin-bottom: 10px; font-weight: bold; text-align: left; font-size: 15px;">
  Adviser: <span id="adviser-name"></span> |
  Grade Level: <span id="adviser-gradelevel"></span> |
  Section: <span id="adviser-section"></span>
</div>

            <h2 class="modal-title"></h2>
    <div class="toolbar">
      <input type="text" placeholder="Search..." oninput="liveSearch('modal{{ $i }}', this.value)">
      <button class="btn btn-warning" onclick="printModal('modal{{ $i }}')"><i class="fa fa-print"></i> Print</button>
      <button class="btn btn-danger" onclick="exportCSV('modal{{ $i }}')"><i class="fa fa-file-export"></i> Export CSV</button>
    </div>

    <div class="modal-table-container">

    <table id="table-{{ $i }}" class="w-full border-collapse">
      <thead>
        @switch($i)
            @case(1)
            <tr>
                <th>Anecdotal ID</th>
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
                <th>Appointment ID</th>
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
                <th>Complaint ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Incident Description</th>
                <th>Complaint Date</th>
                <th>Complaint Time</th>
            </tr>
            @break
            @case(6)
            <tr>
                <th>Complaint ID</th>
                <th>Complainant Name</th>
                <th>Respondent Name</th>
                <th>Type of Offense</th>
                <th>Complaint Date</th>
                <th>Complaint Time</th>
            </tr>
            @break
            @case(7)
            <tr>
                <th>Offense ID</th>
                <th>Offense Type</th>
                <th>Description</th>
                <th>Total Occurrences</th>
            </tr>
            @break
            @case(8)
            <tr>
                <th>Student Name</th>
                <th>Total Violations</th>
                <th>First Violation Date</th>
                <th>Most Recent Violation Date</th>
            </tr>
            @break
            @case(9)
            <tr>
                <th>Offense Type</th>
                <th>Offense Description</th>
                <th>Sanction Consequences</th>
            </tr>
            @break
            @case(10)
            <tr>
                <th>Student Name</th>
                <th>Parent Name</th>
                <th>Parent Contact Info</th>
                <th>Violation Date</th>
                <th>Violation Time</th>
                <th>Violation Status</th>
            </tr>
            @break
            @case(11)
            <tr>
                <th>Offense Sanction ID</th>
                <th>Offense Type</th>
                <th>Sanction Consequences</th>
                <th>Month and Year</th>
                <th>Number of Sanctions Given</th>
            </tr>
            @break
            @case(12)
            <tr>
                <th>Student Name</th>
                <th>Parent Name</th>
                <th>Parent Contact Info</th>
            </tr>
            @break
            @case(13)
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Violation Count</th>
                <th>Complaint Involvement Count</th>
            </tr>
            @break
            @case(14)
            <tr>
                <th>Student Name</th>
                <th>Total Violations</th>
            </tr>
            @break
            @case(15)
            <tr>
                <th>Offense ID</th>
                <th>Offense Type</th>
                <th>Sanction Consequences</th>
                <th>Month and Year</th>
                <th>Number of Sanctions Given</th>
            </tr>
            @break
            @case(16)
            <tr>
                <th>Violation ID</th>
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
</div>
@endfor

<script>
  // Dropdown toggle
  document.querySelectorAll('.dropdown-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const container = btn.nextElementSibling;
      container.classList.toggle('show');
    });
  });

  function openReportModal(reportId) {

    const modal = document.getElementById('modal' + reportId);
      // ✅ Fill adviser info in this modal
  modal.querySelector('#adviser-name').textContent = loggedAdviser.name;
  modal.querySelector('#adviser-gradelevel').textContent = loggedAdviser.gradelevel;
  modal.querySelector('#adviser-section').textContent = loggedAdviser.section;



  // Set modal title
    const title = document.querySelector(`.report-box[data-modal="modal${reportId}"] h3`).textContent;
    modal.querySelector('.modal-title').textContent = title;

    fetch(`/adviser/reports/data/${reportId}`)
      .then(res => res.ok ? res.json() : Promise.reject('Fetch failed'))
      .then(data => {
        const thead = modal.querySelector('thead');
        const tbody = modal.querySelector('tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Build table header manually
        let headers = [];
        switch (parseInt(reportId)) {
          case 1: headers = ['Complainant Name','Respondent Name','Solution','Recommendation','Date Recorded','Time Recorded']; break;
          case 2: headers = ['Student Name','Solution','Recommendation','Date','Time']; break;
          case 3: headers = ['Complainant Name','Respondent Name','Appointment Date','Appointment Status']; break;
          case 4: headers = ['Student Name','Appointment Date','Appointment Time','Appointment Status']; break;
          case 5: headers = ['Complainant Name','Respondent Name','Incident Description','Complaint Date','Complaint Time']; break;
          case 6: headers = ['Complainant Name','Respondent Name','Type of Offense','Complaint Date','Complaint Time']; break;
          case 7: headers = ['Offense Type','Description','Total Occurrences']; break;
          case 8: headers = ['Student Name','Total Violations','First Violation Date','Most Recent Violation Date']; break;
          case 9: headers = ['Offense Type','Offense Description','Sanction Consequences']; break;
          case 10: headers = ['Student Name','Parent Name','Parent Contact Info','Violation Date','Violation Time','Violation Status']; break;
          case 11: headers = ['Offense Type','Sanction Consequences','Month and Year','Number of Sanctions Given']; break;
          case 12: headers = ['Student Name','Parent Name','Parent Contact Info']; break;
          case 13: headers = ['First Name','Last Name','Violation Count','Complaint Involvement Count']; break;
          case 14: headers = ['Student Name','Total Violations']; break;
          case 15: headers = ['Student Name','Offense Type','Sanction','Incident Description','Violation Date','Violation Time']; break;
        }

        // Insert header row
        thead.innerHTML = `<tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr>`;

        // Build table body manually (match controller column names)
        if (!data.length) {
          tbody.innerHTML = `<tr><td colspan="${headers.length}" style="text-align:center;">No records found.</td></tr>`;
          modal.style.display = 'block';
          return;
        }


        data.forEach(row => {
          let tr = document.createElement('tr');
          switch (parseInt(reportId)) {
            case 1:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.solution}</td>
                              <td>${row.recommendation}</td>
                              <td>${row.date_recorded}</td>
                              <td>${row.time_recorded}</td>`;
              break;
            case 2:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.solution}</td>
                              <td>${row.recommendation}</td>
                              <td>${row.date}</td>
                              <td>${row.time}</td>`;
              break;
            case 3:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.appointment_date}</td>
                              <td>${row.appointment_status}</td>`;
              break;
            case 4:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.appointment_date}</td>
                              <td>${row.appointment_time}</td>
                              <td>${row.appointment_status}</td>`;
              break;
            case 5:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.incident_description}</td>
                              <td>${row.complaint_date}</td>
                              <td>${row.complaint_time}</td>`;
              break;
            case 6:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.offense_type}</td>
                              <td>${row.complaint_date}</td>
                              <td>${row.complaint_time}</td>`;
              break;
            case 7:
              tr.innerHTML = `
                              <td>${row.offense_type}</td>
                              <td>${row.offense_description}</td>
                              <td>${row.total_occurrences}</td>`;
              break;
            case 8:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.total_violations}</td>
                              <td>${row.first_violation_date}</td>
                              <td>${row.most_recent_violation_date}</td>`;
              break;
            case 9:
              tr.innerHTML = `<td>${row.offense_type}</td>
                              <td>${row.offense_description}</td>
                              <td>${row.sanction_consequences}</td>`;
              break;
            case 10:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.parent_name}</td>
                              <td>${row.parent_contactinfo}</td>
                              <td>${row.violation_date}</td>
                              <td>${row.violation_time}</td>
                              <td>${row.violation_status}</td>`;
              break;
            case 11:
            tr.innerHTML = `
                            <td>${row.offense_type}</td>
                            <td>${row.sanction_consequences}</td>
                            <td>${row.month_and_year}</td>
                            <td>${row.number_of_sanctions_given}</td>`;
            break;

            case 12:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.parent_name}</td>
                              <td>${row.parent_contactinfo}</td>`;
              break;
            case 13:
              tr.innerHTML = `<td>${row.first_name}</td>
                              <td>${row.last_name}</td>
                              <td>${row.violation_count}</td>
                              <td>${row.complaint_involvement_count}</td>`;
              break;
            case 14:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.total_violations}</td>`;
              break;

            case 15:
              tr.innerHTML = `
                              <td>${row.student_name}</td>
                              <td>${row.offense_type}</td>
                              <td>${row.sanction}</td>
                              <td>${row.incident_description}</td>
                              <td>${row.violation_date}</td>
                              <td>${row.violation_time}</td>`;
              break;
          }
          tbody.appendChild(tr);
        });

        modal.style.display = 'block';
      })
      .catch(err => {
      console.error(err);
      alert('Failed to load report data. Check console for details.');
      modal.style.display = 'block'; // still open so user sees something
    });
  }

  // Attach click listeners
  document.querySelectorAll('.report-box').forEach(box => {
    box.addEventListener('click', () => {
      openReportModal(box.dataset.modal.replace('modal',''));
    });
  });

  // Close modal
  document.querySelectorAll('.modal .close').forEach(btn => {
    btn.addEventListener('click', e => {
      e.target.closest('.modal').style.display = 'none';
    });
  });
  window.onclick = e => {
    if (e.target.classList.contains('modal')) e.target.style.display = 'none';
  }
    // Search
  function liveSearch(modalId, query){
    const tbody = document.querySelector('#'+modalId+' tbody');
    query = query.toLowerCase();
    tbody.querySelectorAll('tr').forEach(tr=>{
      tr.style.display = Array.from(tr.cells).some(td=>td.textContent.toLowerCase().includes(query)) ? '' : 'none';
    });
  }

// Print modal with bordered table
function printModal(modalId) {
  const modal = document.getElementById(modalId);
  const clone = modal.querySelector('.modal-content').cloneNode(true);

  // Remove interactive elements before printing
  clone.querySelectorAll('input, button, .close').forEach(el => el.remove());

  // Open print window
  const w = window.open('', '', 'width=900,height=700');

  // Write content with print styles
  w.document.write(`
    <html>
      <head>
        <title>Print</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            margin: 20px;
          }
          h2.modal-title {
            text-align: center;
            margin-bottom: 15px;
          }
          .adviser-info {
            font-weight: bold;
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 8px;
            border-radius: 5px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }
          table, th, td {
            border: 1px solid #000;
          }
          th, td {
            padding: 8px;
            text-align: left;
          }
          th {
            background-color: #f0f0f0;
          }
        </style>
      </head>
      <body>
        ${clone.innerHTML}
      </body>
    </html>
  `);

  w.document.close();
  w.focus();
  w.print();
  w.close();
}

  // Export CSV
  function exportCSV(modalId){
    const table = document.querySelector('#'+modalId+' table');
    const rows = Array.from(table.querySelectorAll('tr')).filter(r=>r.style.display!=='none');
    const csv = rows.map((row,i)=>{
      const cells = Array.from(row.querySelectorAll(i===0?'th':'td'));
      return cells.map(c=>`"${(c.textContent||'').replace(/"/g,'""')}"`).join(',');
    }).join('\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = modalId+'.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(a.href);
  }

    // Get adviser data from backend
  const loggedAdviser = {
    name: "{{ auth()->guard('adviser')->user()->adviser_fname }} {{ auth()->guard('adviser')->user()->adviser_lname }}",
    gradelevel: "{{ auth()->guard('adviser')->user()->adviser_gradelevel }}",
    section: "{{ auth()->guard('adviser')->user()->adviser_section }}"
  };
</script>


</body>
</html>
