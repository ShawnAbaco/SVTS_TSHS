<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
      font-weight: bold;
      transition: all 0.2s ease-in-out;
    }

    body {
      display: flex;
      background: #f9f9f9;
      color: #111;
    }

    /* Sidebar */
    .sidebar {
      width: 230px;
background: linear-gradient(135deg, #001818, #002222, #002f3f, #00394d);background-repeat: no-repeat;
background-attachment: fixed;
      color: #fff;
      height: 100vh;
      position: fixed;
      padding: 25px 15px;
      border-radius: 0 15px 15px 0;
      box-shadow: 2px 0 15px rgba(0,0,0,0.5);
      overflow-y: auto;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
      font-size: 22px;
      letter-spacing: 1px;
      color: #ffffff;
      text-transform: uppercase;
      border-bottom: 2px solid rgba(255, 255, 255, 0.15);
      padding-bottom: 10px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px 14px;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 10px;
      font-size: 15px;
      color: #e0e0e0;
      transition: background 0.3s, transform 0.2s;
    }

    .sidebar ul li i {
      margin-right: 12px;
      color: #cfcfcf;
      min-width: 20px;
      font-size: 16px;
    }

    .sidebar ul li:hover {
      background: #2d3f55;
      transform: translateX(5px);
      color: #fff;
    }

    .sidebar ul li:hover i {
      color: #00e0ff;
    }

    .sidebar ul li.active {
      background: #00aaff;
      color: #fff;
      border-left: 4px solid #ffffff;
    }

    .sidebar ul li.active i {
      color: #fff;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      flex: 1;
    }

    .section-title {
      margin: 20px 10px 8px;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: bold;
      color: rgba(255, 255, 255, 0.6);
      letter-spacing: 1px;
    }

    /* Dropdown */
    .dropdown-container {
      display: none;
      list-style: none;
      padding-left: 25px;
    }

    .dropdown-container li {
      padding: 10px;
      font-size: 14px;
      border-radius: 8px;
      color: #ddd;
    }

    .dropdown-container li:hover {
      background: #3a4c66;
      color: #fff;
    }

    .dropdown-btn .arrow {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .dropdown-btn.active .arrow {
      transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.25);
      border-radius: 3px;
    }

    /* Main content */
    main {
      margin-left: 230px;
      padding: 20px;
      width: calc(100% - 230px);
    }
    main h2 {
      margin-bottom: 15px;
    }
    .flex {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
    }
    input.form-control, select.form-select {
      padding: 6px 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 13px;
    }
    input.form-control:focus, select.form-select:focus {
      outline: none;
      border-color: #ffcc00;
      box-shadow: 0 0 5px #ffcc00;
    }

    /* Improved Table UI */
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 14px;
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    table thead {
      background: linear-gradient(90deg, #007bff, #00c6ff);
      color: #fff;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 0.5px;
    }

    table th, table td {
      padding: 14px 16px;
      text-align: left;
    }

    table tbody tr {
      transition: all 0.3s ease;
    }

    table tbody tr:nth-child(even) {
      background: #f5f7fa;
    }

    table tbody tr:hover {
      background: #d0f0ff;
      transform: scale(1.01);
    }

    /* Action Buttons */
    .action-container {
      display: flex;
      gap: 5px;
      justify-content: flex-start;
    }

    .action-container button {
      padding: 6px 14px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: all 0.25s ease-in-out;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .btn-secondary {
      background: linear-gradient(90deg, #007bff, #00c6ff);
      color: #fff;
    }

    .btn-secondary:hover {
      background: linear-gradient(90deg, #0056b3, #00a1cc);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .btn-danger {
      background: linear-gradient(90deg, #ff4d4f, #ff7875);
      color: #fff;
    }

    .btn-danger:hover {
      background: linear-gradient(90deg, #c82333, #ff4d4f);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal.show-modal { display: flex; }
    .modal-content {
      background: #fff;
      border-radius: 10px;
      width: 450px;
      max-width: 90%;
      padding: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .modal-header, .modal-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .modal-header h5 { font-size: 16px; }
    .modal-body p { margin-bottom: 10px; font-size: 14px; }
    .btn-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #555;
    }
    .modal-footer .btn {
      min-width: 90px;
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
      table th, table td {
        padding: 10px 12px;
        font-size: 12px;
      }
      .flex {
        flex-direction: column;
        gap: 8px;
      }
      .action-container {
        flex-direction: column;
        align-items: flex-start;
      }
    }
    /* Logo */
.sidebar img {
  width: 150px;
  height: auto;
  margin: 0 auto 0.5rem;
  display: block;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
        <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>    <ul>
      <div class="section-title">Main</div>

      <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li class="active"><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
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
      <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>

  <!-- Main -->
  <main>

    <h2>Student Management</h2>
    <div class="flex" style="justify-content: flex-end; gap: 10px; margin-bottom: 15px;">
  <input type="text" id="searchInput" placeholder="Search students..." class="form-control">
  <select id="sectionFilter" class="form-select" style="max-width:200px;">
    <option value="">All Sections</option>
    @foreach($sections as $section)
      <option value="{{ $section }}">{{ $section }}</option>
    @endforeach
  </select>
</div>


    <table id="studentTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Grade Level</th>
          <th>Section</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $student)
        <tr data-section="{{ $student->adviser->adviser_section }}">
          <td>{{ $student->student_id }}</td>
          <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
          <td>{{ $student->adviser->adviser_gradelevel }}</td>
          <td>{{ $student->adviser->adviser_section }}</td>
          <td>
            <div class="action-container">
              <button class="btn btn-secondary btn-sm" onclick="showFullInfo({{ $student->student_id }})">Info</button>
              <form action="{{ route('student.delete', $student->student_id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </main>

  <!-- Info Modal -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="infoModalTitle">Info</h5>
        <button class="btn-close" onclick="closeInfoModal()">&times;</button>
      </div>
      <div class="modal-body" id="infoModalBody"></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeInfoModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown-btn');
    dropdowns.forEach(btn => {
      btn.addEventListener('click', () => {
        const container = btn.nextElementSibling;
        dropdowns.forEach(otherBtn => {
          const otherContainer = otherBtn.nextElementSibling;
          if (otherBtn !== btn) {
            otherBtn.classList.remove('active');
            otherContainer.style.display = 'none';
          }
        });
        btn.classList.toggle('active');
        container.style.display = container.style.display === 'block' ? 'none' : 'block';
      });
    });

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
        if(response.ok) {
            // Redirect to login after successful logout
            window.location.href = "{{ route('auth.login') }}";
        } else {
            console.error('Logout failed:', response.statusText);
        }
    })
    .catch(error => console.error('Logout failed:', error));
}
    // Modal functions
    function showFullInfo(studentId) {
      const row = Array.from(document.querySelectorAll('#studentTable tbody tr'))
        .find(tr => tr.children[0].innerText == studentId).children;

      document.getElementById('infoModalTitle').textContent = `Info: ${row[1].innerText}`;
      document.getElementById('infoModalBody').innerHTML = `
        <p><strong>Grade Level:</strong> ${row[2].innerText}</p>
        <p><strong>Section:</strong> ${row[3].innerText}</p>
      `;
      document.getElementById('infoModal').classList.add('show-modal');
    }

    function closeInfoModal() {
      document.getElementById('infoModal').classList.remove('show-modal');
    }

    // Delete confirmation
    function confirmDelete() {
      return confirm("Are you sure you want to delete this student?");
    }

    // Search and filter
    const searchInput = document.getElementById('searchInput');
    const sectionFilter = document.getElementById('sectionFilter');

    function filterTable() {
      const query = searchInput.value.toLowerCase();
      const section = sectionFilter.value;
      document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        const name = row.children[1].innerText.toLowerCase();
        const sec = row.children[3].innerText;
        row.style.display = (name.includes(query) && (section === '' || sec === section)) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', filterTable);
    sectionFilter.addEventListener('change', filterTable);
  </script>

</body>
</html>
