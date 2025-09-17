<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adviser Dashboard - Offense & Sanction</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <style>
:root {
  --sidebar-bg: rgb(48, 48, 50);
  --sidebar-hover-bg: rgba(255,255,255,0.12);
  --sidebar-border-color: #FFD700;
  --table-header-bg: #000000;
  --table-header-text: #ffffff;
  --btn-blue: #007bff;
  --btn-red: #e63946;
  --btn-gray: #6c757d;
  --btn-warning: #ffb703;
}

/* Reset & body */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  font-family: "Segoe UI", Arial, sans-serif;
  display: flex;
  min-height: 100vh;
  background-color: #ffffff;
}

/* --- Sidebar --- */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100%;
  /* Gradient background */
  background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
  font-family: "Segoe UI", Tahoma, sans-serif;
  z-index: 1000;
  overflow-y: auto;
  transition: all 0.3s ease;
  color: #ffffff;
  font-weight: bold;
  -webkit-font-smoothing: antialiased; /* smooth fonts for high-res */
  -moz-osx-font-smoothing: grayscale;
  image-rendering: optimizeQuality; /* high-res image rendering */
}

/* Sidebar scroll */
.sidebar::-webkit-scrollbar { width: 8px; }
.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}
.sidebar::-webkit-scrollbar-track {
  background-color: rgba(255, 255, 255, 0.05);
}

/* Logo */
.sidebar img {
  width: 180px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}

/* Sidebar Title */
.sidebar p {
  font-size: 1.6rem;
  font-weight: 900;
  margin: 0 0 1rem;
  color: #ffffff;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0,0,0,0.4);
}

/* Sidebar Links */
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar ul li a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 22px;
  color: #ffffff;
  text-decoration: none;
  font-size: 1rem;
  font-weight: bold;
  border-left: 4px solid transparent;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.sidebar ul li a i {
  font-size: 1.2rem;
  min-width: 22px;
  text-align: center;
  color: #ffffff;
  transition: color 0.3s ease;
}

/* Hover & Active */
.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: rgba(255,255,255,0.15);
  border-left-color: #FFD700;
  color: #ffffff !important;
}

/* Dropdown */
.dropdown-container {
  max-height: 0;
  overflow: hidden;
  background-color: rgba(255,255,255,0.05);
  transition: max-height 0.4s ease, padding 0.4s ease;
  border-left: 2px solid rgba(255,255,255,0.1);
  border-radius: 0 8px 8px 0;
}
.dropdown-container.show { 
  max-height: 400px; 
  padding-left: 12px; 
}
.dropdown-container li a {
  font-size: 0.9rem;
  padding: 10px 20px;
  color: #ffffff;
  font-weight: bold;
}
.dropdown-container li a:hover {
  background-color: rgba(255,255,255,0.15);
  color: #ffffff;
}
.dropdown-btn .fa-caret-down {
  margin-left: auto;
  transition: transform 0.3s ease;
  color: #ffffff;
}


/* Main content */
.main-content { margin-left: 260px; padding: 2rem; flex-grow: 1; }

/* Toolbar */
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 1rem;
  gap: 10px;
}
.toolbar-left h2 {
  margin: 0;
  font-size: 1.8rem;
}
.toolbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
#searchInput {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  max-width: 220px;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border-radius: 8px;
  overflow: hidden;
}
table th, table td {
  padding: 14px 16px;
  text-align: left;
  font-size: 0.95rem;
  font-weight: bold;
}
table th {
  background-color: var(--table-header-bg);
  color: var(--table-header-text);
  font-size: 1rem;
  letter-spacing: 0.5px;
}
table tbody tr {
  border-bottom: 1px solid #ddd;
  transition: background-color 0.3s ease;
}
table tbody tr:hover { background-color: #f9f9f9; }

/* Buttons */
.btn {
  padding: 0.55rem 1.2rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  transition: all 0.25s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(0,0,0,0.18); }
.btn-blue { background-color: var(--btn-blue); color: #fff; }
.btn-red { background-color: var(--btn-red); color: #fff; }
.btn-gray { background-color: var(--btn-gray); color: #fff; }
.btn-warning { background-color: var(--btn-warning); color: #000; }
  </style>

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
        <li><a href="{{ route('offense.sanction') }}" class="active"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
        <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<div class="main-content">
  <div class="crud-container">
    <!-- Toolbar with title on left and buttons on right -->
    <div class="toolbar">
      <div class="toolbar-left">
        <h2>Offense & Sanction</h2>
      </div>
      <div class="toolbar-right">
        <input type="text" id="searchInput" placeholder="Search offenses...">
        <button id="printBtn" class="btn btn-warning"><i class="fa fa-print"></i> Print</button>
        <button id="exportBtn" class="btn btn-red"><i class="fa fa-file-export"></i> Export CSV</button>
      </div>
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
// Dropdown functionality
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

// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

// Search filter
document.getElementById("searchInput").addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#offenseTable tbody tr");
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
});

// Print functionality
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

// Export CSV
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

// Logout
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
