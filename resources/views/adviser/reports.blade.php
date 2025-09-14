<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Reports</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>

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
  <div class="report-box" data-modal="modal5"><i class="fas fa-clock"></i><h3>Complaints Filed within the Last 30 Days</h3></div>
  <!-- <div class="report-box"><i class="fas fa-user-tie"></i><h3>Complaint Records by Adviser</h3></div> -->
  <div class="report-box" data-modal="modal6"><i class="fas fa-file-alt"></i><h3>Complaint Records with Complainant and Respondent</h3></div>
  <div class="report-box" data-modal="modal7"><i class="fas fa-chart-bar"></i><h3>Common Offenses by Frequency</h3></div>
  <div class="report-box" data-modal="modal8"><i class="fas fa-exclamation-triangle"></i><h3>List of Violators with Repeat Offenses</h3></div>
  <div class="report-box" data-modal="modal9"><i class="fas fa-gavel"></i><h3>Offenses and Their Sanction Consequences</h3></div>
  <div class="report-box" data-modal="modal10"><i class="fas fa-phone-alt"></i><h3>Parent Contact Info for Students with Active Violations</h3></div>
  <div class="report-box" data-modal="modal11"><i class="fas fa-chart-line"></i><h3>Sanction Trends Across Time Periods</h3></div>
  <!-- <div class="report-box"><i class="fas fa-chalkboard-teacher"></i><h3>Students and Their Class Advisers</h3></div> -->
  <div class="report-box" data-modal="modal12"><i class="fas fa-user-graduate"></i><h3>Students and Their Parents</h3></div>
  <div class="report-box" data-modal="modal13"><i class="fas fa-user-shield"></i><h3>Students with Both Violation and Complaint Records</h3></div>
  <div class="report-box" data-modal="modal14"><i class="fas fa-user-friends"></i><h3>Students with the Most Violation Records</h3></div>
  <!-- <div class="report-box"><i class="fas fa-layer-group"></i><h3>Summary of Violations per Grade Level</h3></div> -->
  <div class="report-box" data-modal="modal15"><i class="fas fa-search"></i><h3>Violation Records Involving Specific Offense Types</h3></div>
  <!-- <div class="report-box"><i class="fas fa-users"></i><h3>Violation Records and Assigned Adviser</h3></div> -->
  <div class="report-box" data-modal="modal16"><i class="fas fa-exclamation-circle"></i><h3>Violation Records with Violator Information</h3></div>
</div>



<!-- 20 Modals -->
@for ($i = 1; $i <= 20; $i++)
  <div id="modal{{ $i }}" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 class="modal-title"></h2>
      <div class="toolbar">
        <input type="text" placeholder="Search..." oninput="liveSearch('modal{{ $i }}', this.value)">
        <button onclick="sortTable('table-modal{{ $i }}')" class="btn btn-blue"><i class="fa fa-sort"></i> Sort</button>
        <button onclick="printModal('modal{{ $i }}')" class="btn btn-warning"><i class="fa fa-print"></i> Print</button>
        <button onclick="exportCSV('modal{{ $i }}')" class="btn btn-danger"><i class="fa fa-file-export"></i> Export CSV</button>
      </div>
      <table id="table-modal{{ $i }}">
        <thead></thead>
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
          otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
        }
      });
      const container = this.nextElementSibling;
      container.classList.toggle('show');
      this.querySelector('.fa-caret-down').style.transform =
        container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
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
        const headerRow = document.createElement('tr');
        Object.keys(data[0]).forEach(key=>{
          const th = document.createElement('th'); th.textContent = key.replace(/_/g,' ').toUpperCase(); headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        data.forEach(row=>{
          const tr = document.createElement('tr');
          Object.values(row).forEach(val=>{
            const td = document.createElement('td'); td.textContent = val; tr.appendChild(td);
          });
          tbody.appendChild(tr);
        });
        modal.style.display='block';
      })
      .catch(err=>{
        tbody.innerHTML='<tr><td colspan="20" style="text-align:center;">Error loading data.</td></tr>';
        modal.style.display='block'; console.error(err);
      });
  }

  document.querySelectorAll('.report-box').forEach(box=>{
    box.addEventListener('click', ()=>{ const reportId = box.dataset.modal.replace('modal',''); openReportModal(reportId); });
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
      const text = tr.textContent.toLowerCase();
      tr.style.display = text.includes(query) ? '' : 'none';
    });
  }

  // Sort table (simple string sort)
  function sortTable(tableId){
    const table = document.getElementById(tableId);
    const tbody = table.tBodies[0];
    Array.from(tbody.rows)
      .sort((a,b)=> a.cells[0].textContent.localeCompare(b.cells[0].textContent))
      .forEach(tr=>tbody.appendChild(tr));
  }

  // Print modal
  function printModal(modalId){
    const modal = document.getElementById(modalId);
    const printContents = modal.querySelector('.modal-content').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
  }

  // Export CSV
  function exportCSV(modalId){
    const table = document.querySelector('#'+modalId+' table');
    let csvContent = Array.from(table.rows).map(r=>Array.from(r.cells).map(c=>c.textContent).join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = modalId+'.csv';
    link.click();
  }
</script>
</body>
</html>
