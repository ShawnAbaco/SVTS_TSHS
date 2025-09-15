<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Violation Reports</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* (styles unchanged from your working version) */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;font-weight:bold;transition:all .2s ease-in-out;}
body{display:flex;background:#f9f9f9;color:#111;}
.sidebar{width:230px;background:#000;color:#fff;height:100vh;position:fixed;padding:25px 15px;border-radius:0 15px 15px 0;box-shadow:2px 0 15px rgba(0,0,0,.5);overflow-y:auto;}
.sidebar h2{margin-bottom:30px;text-align:center;font-size:22px;letter-spacing:1px;color:#fff;text-transform:uppercase;border-bottom:2px solid rgba(255,255,255,.15);padding-bottom:10px;}
.sidebar ul{list-style:none;}
.sidebar ul li{padding:12px 14px;display:flex;align-items:center;cursor:pointer;border-radius:10px;font-size:15px;color:#e0e0e0;transition:background .3s,transform .2s;}
.sidebar ul li i{margin-right:12px;color:#cfcfcf;min-width:20px;font-size:16px;}
.sidebar ul li:hover{background:#2d3f55;transform:translateX(5px);color:#fff;}
.sidebar ul li:hover i{color:#00e0ff;}
.sidebar ul li.active{background:#00aaff;color:#fff;border-left:4px solid #fff;}
.sidebar ul li.active i{color:#fff;}
.sidebar ul li a{text-decoration:none;color:inherit;flex:1;}
.section-title{margin:20px 10px 8px;font-size:11px;text-transform:uppercase;font-weight:bold;color:rgba(255,255,255,.6);letter-spacing:1px;}
.dropdown-container{display:none;list-style:none;padding-left:25px;}
.dropdown-container li{padding:10px;font-size:14px;border-radius:8px;color:#ddd;}
.dropdown-container li:hover{background:#3a4c66;color:#fff;}
.dropdown-btn .arrow{margin-left:auto;transition:transform .3s;}
.dropdown-btn.active .arrow{transform:rotate(180deg);}
.sidebar::-webkit-scrollbar{width:6px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.25);border-radius:3px;}
.main-content{margin-left:220px;padding:20px;width:calc(100% - 220px);display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;}
.report-box{background:#fff;border-radius:10px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,.1);transition:transform .2s,box-shadow .2s;cursor:pointer;}
.report-box:hover{transform:translateY(-5px);box-shadow:0 6px 12px rgba(0,0,0,.2);}
.report-box i{font-size:24px;margin-bottom:10px;color:#2980b9;}
.report-box h3{margin:0;font-size:18px;}
.modal{display:none;position:fixed;z-index:100;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,.5);}
.modal-content{background:#fff;margin:50px auto;padding:20px;border-radius:10px;width:90%;max-height:80vh;overflow-y:auto;position:relative;}
.close{color:#aaa;float:right;font-size:28px;font-weight:bold;cursor:pointer;}
.close:hover{color:black;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{border:1px solid #ccc;padding:8px;text-align:left;}
th{background:#2980b9;color:#fff;position:sticky;top:0;}
tr:nth-child(even){background:#f2f2f2;}
.toolbar input{padding:6px 10px;border-radius:5px;border:1px solid #ccc;margin-right:5px;}
.toolbar button{padding:6px 10px;border-radius:5px;border:none;cursor:pointer;margin-right:5px;}
.toolbar button.btn-warning{background:#f39c12;color:#fff;}
.toolbar button.btn-warning:hover{background:#d68910;}
.toolbar button.btn-danger{background:#c0392b;color:#fff;}
.toolbar button.btn-danger:hover{background:#962d22;}
@media screen and (max-width:768px){.main-content{margin-left:0;width:100%;grid-template-columns:1fr;padding:15px;}.toolbar input{width:100%;margin-bottom:5px;}}
</style>
</head>
<body>

<!-- Sidebar (unchanged) -->
<div class="sidebar">
  <h2>PREFECT DASHBOARD</h2>
  <ul>
    <div class="section-title">Main</div>
    <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
    <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
    <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>

    <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
      <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
      <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
    </ul>

    <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
    <ul class="dropdown-container">
      <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
      <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
      <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
    </ul>

    <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li class="active"><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
  </ul>
</div>

<!-- Report boxes -->
<div class="main-content">
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

<!-- Modals -->
@for($i=1; $i<=20; $i++)
<div id="modal{{ $i }}" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title"></h2>

    <div class="toolbar">
      <input type="text" placeholder="Search..." oninput="liveSearch('modal{{ $i }}', this.value)">
      <button class="btn btn-warning" onclick="printModal('modal{{ $i }}')"><i class="fa fa-print"></i> Print</button>
      <button class="btn btn-danger" onclick="exportCSV('modal{{ $i }}')"><i class="fa fa-file-export"></i> Export CSV</button>
    </div>

    <!-- NOTE: table id is table-{{ $i }} -->
    <table id="table-{{ $i }}">
      <thead>
        @switch($i)
            @case(1)
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

            {{-- Add other cases' headers here --}}
        @endswitch
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
@endfor

<script>
/* small helper to safely get different possible property names */
function getVal(row, colIndex, reportId) {
    if (reportId === 1) {
        const keys = [
            'violation_id',
            'student_name',
            'offense_type',
            'sanction',
            'incident_description',
            'violation_date',
            'violation_time'
        ];
        return row[keys[colIndex]] ?? '';
    }
    // fallback for other reports
    return Object.values(row)[colIndex] ?? '';
}


/* dropdown */
document.querySelectorAll('.dropdown-btn').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const container = btn.nextElementSibling;
    document.querySelectorAll('.dropdown-btn').forEach(b=>{ if(b!==btn){ b.classList.remove('active'); b.nextElementSibling.style.display='none';}});
    btn.classList.toggle('active');
    container.style.display = container.style.display === 'block' ? 'none' : 'block';
  });
});

/* open modal + fetch */
async function openReportModal(reportId) {
    const modal = document.getElementById(`modal${reportId}`);
    modal.style.display = "block";

    // Fetch report data
    try {
        const res = await fetch(`/prefect/reports/data/${reportId}`);
        const data = await res.json();
        console.log("Fetched data:", data); // ✅ DEBUG

        const tbody = modal.querySelector("tbody");
        tbody.innerHTML = ""; // clear old data

        if (reportId === 1) {
            data.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.violation_id}</td>
                        <td>${row.student_name}</td>
                        <td>${row.offense_type}</td>
                        <td>${row.sanction}</td>
                        <td>${row.incident_description}</td>
                        <td>${row.violation_date}</td>
                        <td>${row.violation_time}</td>
                    </tr>
                `;
            });
        } else {
            // Fallback for other reports
            data.forEach(row => {
                const values = Object.values(row);
                tbody.innerHTML += `<tr>${values.map(v => `<td>${v ?? ''}</td>`).join('')}</tr>`;
            });
        }

    } catch (error) {
        console.error("Error fetching data:", error);
    }
}


/* attach event to boxes (report tiles) */
document.querySelectorAll('.report-box').forEach(box=>{
  box.addEventListener('click', ()=> openReportModal(box.dataset.modal.replace('modal','')));
});

/* close modals */
document.addEventListener('click', e=>{
  if(e.target.classList.contains('close')) e.target.closest('.modal').style.display='none';
  if(e.target.classList.contains('modal')) e.target.style.display='none';
});

/* search (text input passes 'modalX' so we convert to numeric id) */
function liveSearch(modalId, query){
  const id = modalId.replace('modal','');
  const table = document.getElementById('table-'+id);
  if(!table) return;
  query = query.toLowerCase();
  Array.from(table.querySelectorAll('tbody tr')).forEach(tr=>{
    tr.style.display = Array.from(tr.querySelectorAll('td')).some(td => td.textContent.toLowerCase().includes(query)) ? '' : 'none';
  });
}

/* print */
function printModal(modalId){
  const m = document.getElementById(modalId).querySelector('.modal-content');
  const clone = m.cloneNode(true);
  clone.querySelectorAll('input,button,.close').forEach(e=>e.remove());
  const w = window.open('','','width=900,height=700');
  w.document.write('<html><head><title>Print</title></head><body>');
  w.document.write(clone.innerHTML);
  w.document.write('</body></html>');
  w.document.close(); w.focus(); w.print(); w.close();
}

/* export CSV expects modal string 'modalX' */
function exportCSV(modalId){
  const id = modalId.replace('modal','');
  const table = document.getElementById('table-'+id);
  if(!table) return;
  const rows = Array.from(table.querySelectorAll('tr')).filter(r=>r.style.display!=='none');
  const csv = rows.map((row,i)=>{
    const cells = Array.from(row.querySelectorAll(i===0?'th':'td'));
    return cells.map(c=>`"${(c.textContent||'').replace(/"/g,'""')}"`).join(',');
  }).join('\n');
  const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a'); a.href = url; a.download = `report-${id}.csv`;
  document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
}

/* logout */
function logout(){
  fetch("/logout",{method:"POST",headers:{"X-CSRF-TOKEN":"{{ csrf_token() }}"}})
    .then(()=>window.location.href="/prefect/login");
}
</script>
</body>
</html>
