<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
 
</head>
<body>

  <!-- Sidebar -->
<div class="sidebar">
    <img src="/images/Logo.png" alt="Logo">
    <h2>PREFECT</h2>
    <ul>
        <div class="section-title">Main</div>
        <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
        <li class="active"><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
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

  <!-- Main Content -->
<div class="main-content">
  <header class="main-header">
    <div class="header-left">
      <h2>Parent List</h2>
    </div>
    <div class="header-right">
      <div class="user-info" onclick="toggleProfileDropdown()">
        <img src="/images/user.jpg" alt="User">
        <span>{{ Auth::user()->name }}</span>
        <i class="fas fa-caret-down"></i>
      </div>
      <div class="profile-dropdown" id="profileDropdown">
        <a href="{{ route('profile.settings') }}">Profile</a>
      </div>
    </div>
  </header>
      

  <!-- Table Container -->
   
  <div class="table-container">
    <!-- Table Header with Search and Archive Button -->
    <div class="table-header">
      
      <div class="table-controls">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>
      <div class="table-controls">
        <button id="archiveBtn">
          <i class="fas fa-archive"></i> Archive
        </button>
      </div>
    </div>

    <!-- Table -->
   <div class="table-container">
    
      <table id="studentTable" class="fixed-header">
      <thead>
        <tr>
        
          <th>
            <input type="checkbox" id="selectAll">
            <!-- 3-dots trash dropdown -->
            <div class="bulk-action-dropdown" style="display:inline-block; position: relative; margin-left:5px;">
                <button id="bulkActionBtn">&#8942;</button>
                <div id="bulkActionMenu" style="display:none; position:absolute; top:25px; left:0; background:#fff; border:1px solid #ccc; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:10;">
                  <div class="bulk-action-item" data-action="completed">Trash</div>
                </div>
              </div>
          </th>
          <th>#</th>
          <th>Parent Fullname</th>
          <th>Contact Number</th>
          <th>Birthdate</th>
          <th>Student</th>
          <th>Adviser</th>
        </tr>
      </thead>
</table>
 <div class="student-table-wrapper">
    <table id="studentTable">
      <tbody>
        @forelse($parents as $index => $parent)
          <tr class="clickable-row">
            <td><input type="checkbox" class="student-checkbox"></td>
            <td>{{ $index + 1 }}</td>
            <td>{{ $parent->parent_fname }} {{ $parent->parent_lname }}</td>
            <td>{{ $parent->parent_contactinfo }}</td>
            <td>{{ $parent->parent_birthdate }}</td>
            <td>
              @forelse($parent->students as $student)
                {{ $student->student_fname }} {{ $student->student_lname }}<br>
              @empty N/A @endforelse
            </td>
            <td>
              @forelse($parent->students as $student)
                @if($student->adviser)
                  {{ $student->adviser->adviser_fname }} {{ $student->adviser->adviser_lname }}<br>
                @else N/A<br> @endif
              @empty N/A @endforelse
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;">No parents found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
</div>
  </div>
</div>

<!-- Info Modal -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <button class="btn-close" onclick="document.getElementById('infoModal').classList.remove('show-modal')">&times;</button>
    <h5>Student Information</h5>
    <div class="modal-body">
      <p><strong>Student Name:</strong> <span id="studentName">N/A</span></p>
      <p><strong>Adviser:</strong> <span id="adviserName">N/A</span></p>
      <p><strong>Parent:</strong> <span id="parentName">N/A</span></p>
    </div>
  </div>
</div>

  <script>
    // Make rows clickable but ignore clicks on checkboxes
document.querySelectorAll('.clickable-row').forEach(row => {
  row.addEventListener('click', (e) => {
    if(e.target.type !== 'checkbox') { // ignore if clicked on checkbox
      const student = row.children[5].innerHTML;
      const adviser = row.children[6].innerHTML;
      const parent = row.children[2].innerText;
      showInfo(student, adviser, parent);
    }
  });
});

    // Select All functionality
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.student-checkbox');

selectAll.addEventListener('change', () => {
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
});

checkboxes.forEach(cb => {
  cb.addEventListener('change', () => {
    if (!cb.checked) {
      selectAll.checked = false;
    } else if (document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length) {
      selectAll.checked = true;
    }
  });
});

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

function showInfo(student, adviser, parent) {
    document.getElementById("studentName").innerHTML = student;
    document.getElementById("adviserName").innerHTML = adviser;
    document.getElementById("parentName").textContent = parent;
    document.getElementById("infoModal").classList.add("show");
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
        if(response.ok) {
            window.location.href = "{{ route('auth.login') }}";
        } else {
            console.error('Logout failed:', response.statusText);
        }
    })
    .catch(error => console.error('Logout failed:', error));
}

// Search functionality
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        const name = row.children[2].innerText.toLowerCase(); // parent fullname column
        row.style.display = name.includes(query) ? '' : 'none';
    });
});

// Bulk action functionality
const bulkActionBtn = document.getElementById('bulkActionBtn');
const bulkActionMenu = document.getElementById('bulkActionMenu');

bulkActionBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
});

// Trash functionality
bulkActionMenu.querySelector('.bulk-action-item').addEventListener('click', () => {
  document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
    cb.closest('tr').remove();
  });
  bulkActionMenu.style.display = 'none';
});

// Close dropdown if clicked outside
document.addEventListener('click', (e) => {
  if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
    bulkActionMenu.style.display = 'none';
  }
});

// Profile dropdown functionality
function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close profile dropdown if clicked outside
document.addEventListener('click', (e) => {
  const userInfo = document.querySelector('.user-info');
  const dropdown = document.getElementById('profileDropdown');
  if (!userInfo.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

  </script>

</body>
</html>