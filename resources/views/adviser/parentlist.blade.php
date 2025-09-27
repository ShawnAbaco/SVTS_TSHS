<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adviser/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adviser/cards.css') }}">

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
    <li class="active"><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List</a></li>
    <li><a href="{{ route('violation.record') }}"><i class="fas fa-book"></i>Violation Record</a></li>
    <li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i>Complaints</a></li>
    <li><a href="{{ route('offense.sanction') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
    <li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
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
      <!-- Summary Cards -->
<div class="summary-cards">
  <div class="summary-card">
    <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
    <div class="card-content">
      <h3>Total Students</h3>
      <p>{{ $parents->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>{{ $parents->where('status', 'active')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>{{ $parents->where('status', 'Cleared')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>{{ $parents->count() }}</p>
    </div>
  </div>
</div>


  <!-- Table Container -->
   
  <div class="table-container">
    <!-- Table Header with Search and Archive Button -->
    <div class="table-header">
      
      <div class="table-controls">
         <h3>Parents Table</h3>

        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>
      
      <div class="table-controls">
        <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Create</button>  
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
          <th>Action</th> <!-- New Action column -->
        </tr>
      </thead>
</table>
 <div class="student-table-wrapper">
    <table id="studentTable">
      <tbody>
  @forelse($parents as $index => $parent)
    <tr class="clickable-row" data-id="{{ $parent->id }}">
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
      <!-- Action column -->
      <td>
        <button class="btn-edit" onclick="editParent('{{ $parent->id }}')">
          <i class="fas fa-edit"></i> Edit
        </button>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="8" style="text-align:center;">No parents found.</td>
    </tr>
  @endforelse
</tbody>

    </table>
</div>
  </div>
</div>

<!-- Info Modal for Parent Details -->
<div class="modal" id="infoModal">
  <div class="modal-content">
    <div class="modal-header">
      <h5>Parent Information</h5>
      <button class="btn-close" onclick="closeInfoModal()">&times;</button>
    </div>
    <div class="modal-body" id="infoModalBody">
      <p><strong>Parent Name:</strong> <span id="infoParentName">N/A</span></p>
      <p><strong>Contact:</strong> <span id="infoContact">N/A</span></p>
      <p><strong>Birthdate:</strong> <span id="infoBirthdate">N/A</span></p>
      <p><strong>Gender:</strong> <span id="infoGender">N/A</span></p>
      <p><strong>Address:</strong> <span id="infoAddress">N/A</span></p>
      <hr>
      <p><strong>Children:</strong></p>
      <ul id="infoChildren">
        <li>N/A</li>
      </ul>
      <p><strong>Advisers:</strong></p>
      <ul id="infoAdvisers">
        <li>N/A</li>
      </ul>
    </div>
  </div>
</div>

<div class="modal" id="editModal">
  <div class="modal-content">
    <div class="modal-header">
      <h5>Edit Parent</h5>
      <button class="btn-close" onclick="closeEditModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="editForm">
        <input type="hidden" name="parentId" id="editParentId">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="firstName" id="editFirstName" required>
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="lastName" id="editLastName" required>
        </div>
        <div class="form-group">
          <label>Contact</label>
          <input type="text" name="contact" id="editContact">
        </div>
        <div class="form-group">
          <label>Birthdate</label>
          <input type="date" name="birthdate" id="editBirthdate">
        </div>
        <div class="form-actions" style="margin-top:10px;">
          <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="btn-create">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
  <script>
  /* ==================== EDIT PARENT ==================== */
  function editParent(id) {
    const row = document.querySelector(`tr[data-id='${id}']`);
    if (!row) return;

    // Fill modal with existing data
    document.getElementById('editParentId').value = id;
    document.getElementById('editFirstName').value = row.children[2].innerText.split(' ')[0];
    document.getElementById('editLastName').value = row.children[2].innerText.split(' ').slice(1).join(' ');
    document.getElementById('editContact').value = row.children[3].innerText;
    document.getElementById('editBirthdate').value = row.children[4].innerText;

    // Show modal
    document.getElementById('editModal').classList.add('show-modal');
  }

  function closeEditModal() {
    document.getElementById('editModal').classList.remove('show-modal');
  }

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editParentId').value;
    const firstName = document.getElementById('editFirstName').value;
    const lastName = document.getElementById('editLastName').value;
    const contact = document.getElementById('editContact').value;
    const birthdate = document.getElementById('editBirthdate').value;

    const row = document.querySelector(`tr[data-id='${id}']`);
    row.children[2].innerText = `${firstName} ${lastName}`;
    row.children[3].innerText = contact;
    row.children[4].innerText = birthdate;

    closeEditModal();
  });

  /* ==================== INFO MODAL ==================== */
  function showParentInfo(row) {
    document.getElementById("infoParentName").textContent = row.dataset.name || "N/A";
    document.getElementById("infoContact").textContent = row.dataset.contact || "N/A";
    document.getElementById("infoBirthdate").textContent = row.dataset.birthdate || "N/A";
    document.getElementById("infoGender").textContent = row.dataset.gender || "N/A";
    document.getElementById("infoAddress").textContent = row.dataset.address || "N/A";

    // Populate children
    const childrenUl = document.getElementById("infoChildren");
    childrenUl.innerHTML = "";
    const childrenNames = row.dataset.children ? row.dataset.children.split('|') : [];
    if (childrenNames.length) {
      childrenNames.forEach(child => childrenUl.innerHTML += `<li>${child}</li>`);
    } else childrenUl.innerHTML = "<li>N/A</li>";

    // Populate advisers
    const advisersUl = document.getElementById("infoAdvisers");
    advisersUl.innerHTML = "";
    const advisers = row.dataset.advisers ? row.dataset.advisers.split('|') : [];
    if (advisers.length) {
      advisers.forEach(ad => advisersUl.innerHTML += `<li>${ad}</li>`);
    } else advisersUl.innerHTML = "<li>N/A</li>";

    document.getElementById("infoModal").classList.add("show-modal");
  }

  function closeInfoModal() {
    document.getElementById("infoModal").classList.remove("show-modal");
  }

  // Only trigger info modal for certain columns
  document.querySelectorAll('#studentTable tbody tr').forEach(row => {
    row.addEventListener('click', e => {
      if(e.target.type === 'checkbox' || e.target.closest('.btn-edit')) return;
      showParentInfo(row);
    });
  });

  /* ==================== SELECT ALL CHECKBOX ==================== */
  const selectAll = document.getElementById('selectAll');
  const checkboxes = document.querySelectorAll('.student-checkbox');

  selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
  });

  checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      selectAll.checked = document.querySelectorAll('.student-checkbox:checked').length === checkboxes.length;
    });
  });

  /* ==================== DROPDOWN ==================== */
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;
      document.querySelectorAll('.dropdown-btn').forEach(otherBtn => {
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

  /* ==================== SEARCH ==================== */
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
      const name = row.children[2].innerText.toLowerCase();
      row.style.display = name.includes(query) ? '' : 'none';
    });
  });

  /* ==================== BULK ACTION ==================== */
  const bulkActionBtn = document.getElementById('bulkActionBtn');
  const bulkActionMenu = document.getElementById('bulkActionMenu');

  bulkActionBtn.addEventListener('click', e => {
    e.stopPropagation();
    bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
  });

  bulkActionMenu.querySelector('.bulk-action-item').addEventListener('click', () => {
    document.querySelectorAll('.student-checkbox:checked').forEach(cb => cb.closest('tr').remove());
    bulkActionMenu.style.display = 'none';
  });

  document.addEventListener('click', e => {
    if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
      bulkActionMenu.style.display = 'none';
    }
  });

  /* ==================== PROFILE DROPDOWN ==================== */
  function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  }

  document.addEventListener('click', e => {
    const userInfo = document.querySelector('.user-info');
    const dropdown = document.getElementById('profileDropdown');
    if (!userInfo.contains(e.target)) dropdown.style.display = 'none';
  });

  /* ==================== LOGOUT ==================== */
  function logout() {
    if (!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('prefect.logout') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    }).then(res => res.ok ? window.location.href = "{{ route('auth.login') }}" : console.error('Logout failed'))
      .catch(err => console.error(err));
  }

  /* ==================== ARCHIVE FUNCTIONALITY ==================== */
  const archiveBtn = document.getElementById('archiveBtn');
  archiveBtn.addEventListener('click', () => {
    document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
      const row = cb.closest('tr');
      row.style.display = 'none'; // hide archived
      row.dataset.status = 'Archived'; // optional: mark as archived
    });
    selectAll.checked = false;
  });
</script>


</body>
</html>