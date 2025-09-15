<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Offense & Sanction</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/adviser/offense&sanction.css') }}">

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
        <li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>

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

        <li><a href="{{ route('offense.sanction') }}"class="active"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
        <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<div class="main-content">
  <div class="crud-container">
    <h2>Offense & Sanction</h2>

    <div class="toolbar" style="justify-content: flex-end; gap: 8px; margin-bottom: 1rem;">
      <input type="text" id="searchInput" placeholder="Search offenses..." style="padding: 8px; width: 220px; border:1px solid #ccc; border-radius:6px;">
      <button id="printBtn" class="btn btn-warning"><i class="fa fa-print"></i> Print</button>
      <button id="exportBtn" class="btn btn-red"><i class="fa fa-file-export"></i> Export CSV</button>
    </div>

    <table id="offenseTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Offense Type</th>
          <th>Description</th>
          <th>Consequences</th>
        </tr>
      </thead>
      <tbody>
        @forelse($offenses as $offense)
          <tr>
            <td>{{ $offense->offense_sanc_id }}</td>
            <td>{{ $offense->offense_type }}</td>
            <td>{{ $offense->offense_description }}</td>
            <td>{{ $offense->sanction_consequences }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align:center;">No offenses found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
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

document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#offenseTable tbody tr");
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
});

document.getElementById("printBtn").addEventListener("click", () => {
    const table = document.getElementById("offenseTable").cloneNode(true);
    const style = `<style>body{font-family:Arial;padding:16px;}h2{margin-bottom:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #000;padding:8px;text-align:left;}thead th{background:#f1f1f1;}</style>`;
    const win = window.open("", "", "height=800,width=1000");
    win.document.write("<html><head><title>Offenses & Sanctions</title>" + style + "</head><body>");
    win.document.write("<h2>Offenses & Sanctions</h2>");
    win.document.body.appendChild(table);
    win.document.write("</body></html>");
    win.document.close();
    win.focus();
    win.print();
});

document.getElementById("exportBtn").addEventListener("click", () => {
    const table = document.getElementById("offenseTable");
    const csv = Array.from(table.querySelectorAll("tr")).map((row, idx) =>
        Array.from(row.querySelectorAll(idx===0?"th":"td")).map(c=>`"${c.textContent.replace(/"/g,'""')}"`).join(",")
    ).join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "offenses_sanctions.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});

function logout() {
    fetch('/logout', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href='/adviser/login')
      .catch(error => console.error('Logout failed:', error));
}
</script>

</body>
</html>
