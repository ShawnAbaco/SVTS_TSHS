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
  <nav class="sidebar" role="navigation">
    <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
      <img src="/images/Logo.png" alt="Logo" style="width: 200px; height: auto; margin-bottom: 0;">
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
    <h2>Offense & Sanction</h2>

    <!-- Search + Print -->
    <div style="margin-bottom: 1rem; display:flex; justify-content: space-between; align-items: center;">
      <input type="text" id="searchInput" placeholder="Search offenses..." style="padding: 6px; width: 250px;">
      <button id="printBtn" class="btn btn-blue">Print / Export</button>
    </div>

    <!-- Table -->
    <table>
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

  <!-- Scripts -->
  <script>
    // Search filter
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const value = this.value.toLowerCase();
      const rows = document.querySelectorAll("tbody tr");

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
      });
    });

    // Print / Export
    document.getElementById("printBtn").addEventListener("click", function () {
      const table = document.querySelector("table").outerHTML;
      const style = `
        <style>
          table { width: 100%; border-collapse: collapse; }
          table, th, td { border: 1px solid #000; padding: 8px; text-align: left; }
          th { background: #f1f1f1; }
        </style>
      `;
      const win = window.open("", "", "height=700,width=900");
      win.document.write("<html><head><title>Offenses & Sanctions</title>");
      win.document.write(style);
      win.document.write("</head><body>");
      win.document.write("<h2>Offenses & Sanctions</h2>");
      win.document.write(table);
      win.document.write("</body></html>");
      win.document.close();
      win.print();
    });

    // Logout
    function logout() {
      fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      }).then(() => window.location.href='/prefect/login')
        .catch(error => console.error('Logout failed:', error));
    }
  </script>
</body>
</html>
