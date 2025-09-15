<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Reports</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/reports.css') }}">
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #ffffff;
      --sidebar-bg: rgb(48, 48, 50);
      --sidebar-hover-bg: rgba(255,255,255,0.12);
      --sidebar-border-color: #FFD700;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: "Arial", sans-serif;
      background-color: var(--secondary-color);
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      position: fixed; top: 0; left: 0;
      width: 240px; height: 100%;
      background: var(--sidebar-bg); color: #ffffff;
      z-index: 1000; overflow-y: auto;
      transition: all 0.3s ease;
    }
    .sidebar img { width: 180px; display: block; margin: 0 auto 0.25rem; }
    .sidebar p {
      font-size: 0.9rem; font-weight: bold;
      text-align: center; text-transform: uppercase;
      letter-spacing: 0.5px; color: #ffffff; margin-bottom: 1rem;
    }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li a {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 20px; color: #ffffff; text-decoration: none;
      font-size: 0.95rem; font-weight: bold;
      border-left: 4px solid transparent; border-radius: 8px;
      transition: all 0.3s ease;
    }
    .sidebar ul li a i { font-size: 1.1rem; min-width: 22px; text-align: center; }
    .sidebar ul li a:hover, .sidebar ul li a.active {
      background-color: var(--sidebar-hover-bg);
      border-left-color: var(--sidebar-border-color);
    }

    /* Dropdown */
    .dropdown-container {
      max-height: 0; overflow: hidden;
      background-color: rgba(255,255,255,0.05);
      transition: max-height 0.4s ease, padding 0.4s ease;
      border-left: 2px solid rgba(255,255,255,0.1);
      border-radius: 0 8px 8px 0;
    }
    .dropdown-container.show { max-height: 400px; padding-left: 12px; }
    .dropdown-container li a { font-size: 0.85rem; padding: 10px 20px; }
    .dropdown-container li a:hover { background-color: var(--sidebar-hover-bg); }
    .dropdown-btn .fa-caret-down { margin-left: auto; transition: transform 0.3s ease; }

    /* Main content */
    .main-content {
      margin-left: 260px; padding: 2rem;
      flex-grow: 1; display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

   /* Report boxes with darker colors */
.report-box {
  border-radius: 10px; padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer; color: #fff;
  display: flex; flex-direction: column; align-items: flex-start; gap: 8px;
}

/* Individual box colors */
.report-box:nth-child(1) { background-color: #8B0000; }
.report-box:nth-child(2) { background-color: #2F4F4F; }
.report-box:nth-child(3) { background-color: #556B2F; }
.report-box:nth-child(4) { background-color: #4B0082; }
.report-box:nth-child(5) { background-color: #800000; }
.report-box:nth-child(6) { background-color: #006400; }
.report-box:nth-child(7) { background-color: #483D8B; }
.report-box:nth-child(8) { background-color: #8B4513; }
.report-box:nth-child(9) { background-color: #2E8B57; }
.report-box:nth-child(10) { background-color: #4682B4; }
.report-box:nth-child(11) { background-color: #800080; }
.report-box:nth-child(12) { background-color: #708090; }
.report-box:nth-child(13) { background-color: #FF8C00; }
.report-box:nth-child(14) { background-color: #B22222; }
.report-box:nth-child(15) { background-color: #556B2F; }
.report-box:nth-child(16) { background-color: #2F4F4F; }
.report-box:nth-child(17) { background-color: #8B0000; }
.report-box:nth-child(18) { background-color: #4B0082; }
.report-box:nth-child(19) { background-color: #483D8B; }
.report-box:nth-child(20) { background-color:rgb(30, 42, 52); }

/* All report box icons black */
.report-box i {
  color: #000000;
}

.modal {
  display: none; position: fixed; z-index: 100;
  left: 0; top: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5);
  overflow-y: auto;
}

.modal-content {
  background-color: var(--secondary-color);
  margin: 40px auto; padding: 20px; border-radius: 10px;
  width: 90%; max-width: 900px;
  position: relative; box-shadow: 0 6px 15px rgba(0,0,0,0.3);
  max-height: 90vh; display: flex; flex-direction: column;
}

.close { color:#aaa; float:right; font-size:22px; font-weight:bold; cursor:pointer; }
.close:hover { color:black; }

.toolbar {
  display: flex; justify-content: flex-end; align-items: center; gap: 8px; margin-bottom: 1rem; flex-wrap: wrap;
}

.toolbar input {
  padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 140px; font-size: 0.85rem;
}

.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 10px; border: none; border-radius: 4px;
  cursor: pointer; font-weight: bold; font-size: 0.85rem;
}
.btn-blue { background-color: #3498db; color: #fff; }
.btn-warning { background-color: #f39c12; color: #fff; }
.btn-danger { background-color: #e74c3c; color: #fff; }
.btn:hover { opacity: 0.9; }

table {
  width: 100%; border-collapse: collapse; margin-top:15px; font-size: 0.85rem; table-layout: fixed;
}

th, td { border:1px solid #ccc; padding:6px; text-align:left; word-wrap: break-word; }
th { background-color: #303032; color: #fff; cursor: pointer; }

.modal table-container {
  overflow-x: auto; flex-grow: 1;
}

tr:nth-child(even){ background-color:#f9f9f9; }
  </style>
</head>
<body>
<!-- SIDEBAR -->
<nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem;">
        <img src="/images/Logo.png" alt="Logo">
        <p>ADVISER</p>
    </div>
    <ul>
        <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
        <li><a href="{{ route('parent.list') }}" ><i class="fas fa-user-friends"></i> Parent List</a></li>
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-exclamation-triangle"></i> Violations <i class="fas fa-caret-down"></i></a>
            <ul class="dropdown-container">
                <li><a href="{{ route('violation.record') }}">Violation Record</a></li>
                <li><a href="{{ route('violation.appointment') }}">Violation Appointment</a></li>
                <li><a href="{{ route('violation.anecdotal') }}">Violation Anecdotal</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down"></i></a>
            <ul class="dropdown-container">
                <li><a href="{{ route('complaints.all') }}">Complaints</a></li>
                <li><a href="{{ route('complaints.appointment') }}">Complaint Appointment</a></li>
                <li><a href="{{ route('complaints.anecdotal') }}">Complaints Anecdotal</a></li>
            </ul>
        </li>
        <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
        <li><a href="{{ route('adviser.reports') }}" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<div class="main-content">
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
  <div class="report-box" data-modal="modal15"><i class="fas fa-search"></i><h3>Violation Records Involving Specific Offense Types</h3></div>
  <div class="report-box" data-modal="modal16"><i class="fas fa-exclamation-circle"></i><h3>Violation Records with Violator Information</h3></div>
</div>


<!-- Modals -->
@for($i=1; $i<=16; $i++)
<div id="modal{{ $i }}" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title"></h2>

    <div class="toolbar">
      <input type="text" placeholder="Search..." oninput="liveSearch('modal{{ $i }}', this.value)">
      <button class="btn btn-warning" onclick="printModal('modal{{ $i }}')"><i class="fa fa-print"></i> Print</button>
      <button class="btn btn-danger" onclick="exportCSV('modal{{ $i }}')"><i class="fa fa-file-export"></i> Export CSV</button>
    </div>

    <!-- Table ID -->
    <h2 class="text-xl font-semibold mb-3 text-center">
        @switch($i)
            @case(1) Anecdotal Records per Complaint Case @break
            @case(2) Anecdotal Records per Violation Case @break
            @case(3) Appointments Scheduled for Complaints @break
            @case(4) Appointments Scheduled for Violation Cases @break
            @case(5) Complaint Records with Complainant and Respondent @break
            @case(6) Complaints Filed within the Last 30 Days @break
            @case(7) Common Offenses by Frequency @break
            @case(8) List of Violators with Repeat Offenses @break
            @case(9) Offenses and Their Sanction Consequences @break
            @case(10) Parent Contact Info for Students with Active Violations @break
            @case(11) Sanction Trends Across Time Periods @break
            @case(12) Students and Their Parents @break
            @case(13) Students with Both Violation and Complaint Records @break
            @case(14) Students with the Most Violation Records @break
            @case(15) Violation Records Involving Specific Offense Types @break
            @case(16) Violation Records with Violator Information @break
        @endswitch
    </h2>

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
                <th>Section</th>
                <th>Grade Level</th>
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
                <th>Adviser Section</th>
                <th>Grade Level</th>
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
@endfor

<script>
  // Dropdown
  const dropdowns = document.querySelectorAll('.dropdown-btn');
  dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      dropdowns.forEach(otherBtn => {
        if (otherBtn !== this) {
          otherBtn.nextElementSibling.classList.remove('show');
          const icon = otherBtn.querySelector('.fa-caret-down');
          if(icon) icon.style.transform = 'rotate(0deg)';
        }
      });
      const container = this.nextElementSibling;
      container.classList.toggle('show');
      const icon = this.querySelector('.fa-caret-down');
      if(icon) icon.style.transform = container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
      if(container.classList.contains('show')) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
  });

  // Open modal
  function openReportModal(reportId){
    const modal = document.getElementById('modal'+reportId);
    const table = modal.querySelector('table');
    const tbody = table.querySelector('tbody');
    const thead = table.querySelector('thead');
    const title = document.querySelector('.report-box[data-modal="modal'+reportId+'"] h3').textContent;
    modal.querySelector('.modal-title').textContent = title;

    tbody.innerHTML = '';
    thead.innerHTML = '';

    fetch(`/adviser/reports/data/${reportId}`)
      .then(res => res.ok ? res.json() : Promise.reject('Fetch failed'))
      .then(data => {
        if(!data.length){
          tbody.innerHTML = '<tr><td colspan="20" style="text-align:center;">No records found.</td></tr>';
          modal.style.display = 'block';
          return;
        }

        // Build table header dynamically based on reportId
        let headers = [];
        switch(parseInt(reportId)){
          case 1: headers = ['Anecdotal ID','Complainant Name','Respondent Name','Solution','Recommendation','Date Recorded','Time Recorded']; break;
          case 2: headers = ['Student Name','Solution','Recommendation','Date','Time']; break;
          case 3: headers = ['Appointment ID','Complainant Name','Respondent Name','Appointment Date','Appointment Status']; break;
          case 4: headers = ['Student Name','Appointment Date','Appointment Time','Appointment Status']; break;
          case 5: headers = ['Complaint ID','Complainant Name','Respondent Name','Incident Description','Complaint Date','Complaint Time']; break;
          case 6: headers = ['Complaint ID','Complainant Name','Respondent Name','Type of Offense','Complaint Date','Complaint Time']; break;
          case 7: headers = ['Offense ID','Offense Type','Description','Total Occurrences']; break;
          case 8: headers = ['Student Name','Section','Grade Level','Total Violations','First Violation Date','Most Recent Violation Date']; break;
          case 9: headers = ['Offense Type','Offense Description','Sanction Consequences']; break;
          case 10: headers = ['Student Name','Parent Name','Parent Contact Info','Violation Date','Violation Time','Violation Status']; break;
          case 11: headers = ['Offense Sanction ID','Offense Type','Sanction Consequences','Month and Year','Number of Sanctions Given']; break;
          case 12: headers = ['Student Name','Parent Name','Parent Contact Info']; break;
          case 13: headers = ['First Name','Last Name','Violation Count','Complaint Involvement Count']; break;
          case 14: headers = ['Student Name','Adviser Section','Grade Level','Total Violations']; break;
          case 15: headers = ['Offense Sanction ID','Offense Type','Sanction Consequences','Month and Year','Number of Sanctions Given']; break;
          case 16: headers = ['Violation ID','Student Name','Offense Type','Sanction','Incident Description','Violation Date','Violation Time']; break;
        }

        const headerRow = document.createElement('tr');
        headers.forEach(h=>{ const th = document.createElement('th'); th.textContent = h; headerRow.appendChild(th); });
        thead.appendChild(headerRow);

        // Build table body
        data.forEach(row=>{
          const tr = document.createElement('tr');
          headers.forEach(h=>{
            let key = h.toLowerCase().replace(/ /g,'_');
            tr.innerHTML += `<td>${row[key] ?? ''}</td>`;
          });
          tbody.appendChild(tr);
        });

        modal.style.display='block';
      })
      .catch(err=>{
        tbody.innerHTML='<tr><td colspan="20" style="text-align:center;">Error loading data.</td></tr>';
        modal.style.display='block';
        console.error(err);
      });
  }

  // Attach modal open to report boxes
  document.querySelectorAll('.report-box').forEach(box=>{
    box.addEventListener('click', ()=>{
      const reportId = box.dataset.modal.replace('modal','');
      openReportModal(reportId);
    });
  });

  // Close modal
  document.addEventListener('click', e=>{
    if(e.target.classList.contains('close')) e.target.closest('.modal').style.display='none';
    if(e.target.classList.contains('modal')) e.target.style.display='none';
  });

  // Search
  function liveSearch(modalId, query){
    const tbody = document.querySelector('#'+modalId+' tbody');
    query = query.toLowerCase();
    tbody.querySelectorAll('tr').forEach(tr=>{
      tr.style.display = Array.from(tr.cells).some(td=>td.textContent.toLowerCase().includes(query)) ? '' : 'none';
    });
  }

  // Print modal
  function printModal(modalId){
    const modal = document.getElementById(modalId);
    const clone = modal.querySelector('.modal-content').cloneNode(true);
    clone.querySelectorAll('input,button,.close').forEach(el=>el.remove());
    const w = window.open('','','width=900,height=700');
    w.document.write('<html><head><title>Print</title></head><body>');
    w.document.write(clone.innerHTML);
    w.document.write('</body></html>');
    w.document.close(); w.focus(); w.print(); w.close();
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
</script>

</body>
</html>
