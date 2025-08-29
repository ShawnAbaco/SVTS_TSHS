<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Reports</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/adviser/reports.css') }}">
</head>
<body>
  <nav class="sidebar">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo">
      <p>ADVISER</p>
    </div>
    <ul>
      <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
      <li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
      <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
      <li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
      <li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
      <li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
      <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
      <li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
      <li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
      <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
      <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
      <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
      <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <div class="main-content">
    <!-- 20 Report Boxes -->
    <div class="report-box" data-modal="modal1"><i class="fas fa-exclamation-circle"></i><h3>Violation Records with Violator Information</h3></div>
    <div class="report-box" data-modal="modal2"><i class="fas fa-user-graduate"></i><h3>Students and Their Parents</h3></div>
    <div class="report-box" data-modal="modal3"><i class="fas fa-file-alt"></i><h3>Complaint Records with Complainant and Respondent</h3></div>
    <div class="report-box" data-modal="modal4"><i class="fas fa-gavel"></i><h3>Offenses and Their Sanction Consequences</h3></div>
    <div class="report-box" data-modal="modal5"><i class="fas fa-users"></i><h3>Violation Records and Assigned Adviser</h3></div>
    <div class="report-box" data-modal="modal6"><i class="fas fa-chalkboard-teacher"></i><h3>Students and Their Class Advisers</h3></div>
    <div class="report-box" data-modal="modal7"><i class="fas fa-book"></i><h3>Anecdotal Records per Violation Case</h3></div>
    <div class="report-box" data-modal="modal8"><i class="fas fa-calendar-alt"></i><h3>Appointments Scheduled for Violation Cases</h3></div>
    <div class="report-box" data-modal="modal9"><i class="fas fa-book-open"></i><h3>Anecdotal Records per Complaint Case</h3></div>
    <div class="report-box" data-modal="modal10"><i class="fas fa-calendar-check"></i><h3>Appointments Scheduled for Complaints</h3></div>
    <div class="report-box" data-modal="modal11"><i class="fas fa-user-friends"></i><h3>Students with the Most Violation Records</h3></div>
    <div class="report-box" data-modal="modal12"><i class="fas fa-chart-bar"></i><h3>Common Offenses by Frequency</h3></div>
    <div class="report-box" data-modal="modal13"><i class="fas fa-user-tie"></i><h3>Complaint Records by Adviser</h3></div>
    <div class="report-box" data-modal="modal14"><i class="fas fa-exclamation-triangle"></i><h3>List of Violators with Repeat Offenses</h3></div>
    <div class="report-box" data-modal="modal15"><i class="fas fa-layer-group"></i><h3>Summary of Violations per Grade Level</h3></div>
    <div class="report-box" data-modal="modal16"><i class="fas fa-phone-alt"></i><h3>Parent Contact Info for Students with Active Violations</h3></div>
    <div class="report-box" data-modal="modal17"><i class="fas fa-clock"></i><h3>Complaints Filed within the Last 30 Days</h3></div>
    <div class="report-box" data-modal="modal18"><i class="fas fa-search"></i><h3>Violation Records Involving Specific Offense Types</h3></div>
    <div class="report-box" data-modal="modal19"><i class="fas fa-user-shield"></i><h3>Students with Both Violation and Complaint Records</h3></div>
    <div class="report-box" data-modal="modal20"><i class="fas fa-chart-line"></i><h3>Sanction Trends Across Time Periods</h3></div>
  </div>

  <!-- 20 Modals with Tables -->
  @for ($i = 1; $i <= 20; $i++)
  <div id="modal{{ $i }}" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Report {{ $i }}</h2>
      <input type="text" placeholder="Search..." onkeyup="searchTable('modal{{ $i }}', this.value)">
      <button onclick="clearSearch('modal{{ $i }}')">Clear</button>
      <table id="table-modal{{ $i }}">
        <thead></thead>
        <tbody></tbody>
      </table>
      <button onclick="printModal('modal{{ $i }}')">Print</button>
    </div>
  </div>
  @endfor

<script>
// Open modal and fetch report data
function openReportModal(reportId){
    const modal = document.getElementById('modal'+reportId);
    const table = modal.querySelector('table');
    const tbody = table.querySelector('tbody');
    const thead = table.querySelector('thead');

    tbody.innerHTML = '';
    thead.innerHTML = '';

    fetch(`/adviser/reports/data/${reportId}`)
    .then(res => {
        if(!res.ok) throw new Error('Fetch failed: '+res.status);
        return res.json();
    })
    .then(data => {
        if(!data.length){
            tbody.innerHTML = '<tr><td colspan="20" style="text-align:center;">No records found.</td></tr>';
            modal.style.display = 'block';
            return;
        }

        // Build header
        const headerRow = document.createElement('tr');
        Object.keys(data[0]).forEach(key=>{
            const th = document.createElement('th');
            th.textContent = key.replace(/_/g,' ').toUpperCase();
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);

        // Build rows
        data.forEach(row=>{
            const tr = document.createElement('tr');
            Object.values(row).forEach(val=>{
                const td = document.createElement('td');
                td.textContent = val;
                tr.appendChild(td);
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

// Add click event to all report boxes
document.querySelectorAll('.report-box').forEach(box => {
    box.addEventListener('click', () => {
        const reportId = box.dataset.modal.replace('modal','');
        openReportModal(reportId);
    });
});

// Close modal on click
document.addEventListener('click', e => {
    if(e.target.classList.contains('close')) e.target.closest('.modal').style.display='none';
    if(e.target.classList.contains('modal')) e.target.style.display='none';
});

// Print modal content
function printModal(modalId){
    const modalContent = document.getElementById(modalId).querySelector('.modal-content');
    const clone = modalContent.cloneNode(true);
    clone.querySelectorAll('input,button,.close').forEach(e=>e.remove());
    const newWin = window.open('','','width=900,height=700');
    newWin.document.write('<html><head><title>Print</title></head><body>');
    newWin.document.write(clone.innerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close(); newWin.focus(); newWin.print(); newWin.close();
}

// Search function for any modal
function searchTable(modalId, query){
    const tableId = 'table-' + modalId.replace('modal','');
    const table = document.getElementById(tableId);
    if(!table) return;
    query = query.toLowerCase();
    Array.from(table.getElementsByTagName('tr')).forEach((tr,i)=>{
        if(i===0) return; // skip header
        let show=false;
        Array.from(tr.getElementsByTagName('td')).forEach(td=>{
            if(td.innerText.toLowerCase().includes(query)) show=true;
        });
        tr.style.display = show ? '' : 'none';
    });
}

// Clear search
function clearSearch(modalId){
    const tableId = 'table-' + modalId.replace('modal','');
    const table = document.getElementById(tableId);
    if(!table) return;
    Array.from(table.getElementsByTagName('tr')).forEach((tr,i)=>{
        if(i>0) tr.style.display='';
    });
}
</script>


</body>
</html>
