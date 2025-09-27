<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/prefect/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prefect/cards.css') }}">
</head>
<body>


<!-- Sidebar -->
<div class="sidebar">
  <img src="/images/Logo.png" alt="Logo">
  <h2>PREFECT</h2>
  <ul>
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


<div class="main-content">
  <!-- ======= HEADER ======= -->
  <header class="main-header">
    <div class="header-left">
      <h2>Student Management</h2>
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
      <p>{{ $students->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#28a745;"><i class="fas fa-check-circle"></i></div>
    <div class="card-content">
      <h3>Active</h3>
      <p>{{ $students->where('status', 'active')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#ffc107;"><i class="fas fa-archive"></i></div>
    <div class="card-content">
      <h3>Cleared / Archived</h3>
      <p>{{ $students->where('status', 'Cleared')->count() }}</p>
    </div>
  </div>
  <div class="summary-card">
    <div class="card-icon" style="color:#007bff;"><i class="fas fa-layer-group"></i></div>
    <div class="card-content">
      <h3>Sections</h3>
      <p>{{ $sections->count() }}</p>
    </div>
  </div>
</div>

    <!-- Table Container -->
    <div class="table-container">
      <!-- Table Header with Controls -->
      <div class="table-header">
        <div class="table-controls">
          <h3>Student Table</h3>

        <input type="text" id="searchInput" placeholder="Search parents..." class="form-control">
      </div>

         <div style="display: flex; justify-content: flex-end; gap: 5px; margin-bottom: 0px;margin-top: 0px;">
  <select id="sectionFilter" class="form-select" style="max-width:150px;">
    <option value="">All Sections</option>
    @foreach($sections as $section)
      <option value="{{ $section }}">{{ $section }}</option>
    @endforeach
  </select>
 <button id="createBtn" class="btn-create"><i class="fas fa-plus"></i> Add Violation</button>
  <button id="archiveBtn" class="btn btn-warning">
    <i class="fas fa-archive"></i> Archive
  </button>
</div>

      </div>

     <!-- Wrap your student table in a scrollable container -->
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
        <th>ID</th>
        <th>Name</th>
        <th>Grade Level</th>
        <th>Section</th>
        <th>Status</th>
        <th>Action</th> <!-- New Action column -->
      </tr>
    </thead>
     </table>
     <div class="student-table-wrapper">
    <table id="studentTable">
  <tbody>
  @foreach($students as $student)
    <tr
      data-id="{{ $student->student_id }}"
      data-name="{{ $student->student_fname }} {{ $student->student_lname }}"
      data-grade="{{ $student->adviser->adviser_gradelevel }}"
      data-section="{{ $student->adviser->adviser_section }}"
      data-status="{{ ucfirst($student->status) }}"
      data-parent-name="{{ $student->parent->name ?? '-' }}"
      data-parent-contact="{{ $student->parent->contact ?? '-' }}"
      data-parent-address="{{ $student->parent->address ?? '-' }}"
      data-adviser-name="{{ $student->adviser->adviser_name ?? '-' }}"
    >
      <td><input type="checkbox" class="student-checkbox"></td>
      <td>{{ $student->student_id }}</td>
      <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
      <td>{{ $student->adviser->adviser_gradelevel }}</td>
      <td>{{ $student->adviser->adviser_section }}</td>
      <td>
        <span class="status-badge {{ $student->status === 'active' ? 'active' : 'inactive' }}">
          {{ ucfirst($student->status) }}
        </span>
      </td>
      <td>
        <button class="btn-edit" onclick="editStudent('{{ $student->student_id }}')">
          <i class="fas fa-edit"></i> Edit
        </button>
      </td>
    </tr>
  @endforeach
</tbody>

  </table>
</div>
  </div>
    </div>
    </div>

    <!-- ARCHIVES MODAL -->
    <div class="modal" id="archivesModal">
      <div class="archive-modal-content">
        <div class="archive-modal-header">
          <span>Archived Students</span>
          <span class="close-btn" id="closeArchivesModal">&times;</span>
        </div>

        <div class="archive-modal-body">
          <!-- Search & Actions -->
          <div class="toolbar-actions">
            <input id="archiveSearch" type="search" placeholder="Search archived students..." style="flex:1;">
            <select id="archiveSectionFilter" style="max-width:200px;">
              <option value="">All Sections</option>
              @foreach($sections as $section)
                <option value="{{ $section }}">{{ $section }}</option>
              @endforeach
            </select>
            <button id="restoreBtn" class="btn-primary"><i class="fas fa-undo"></i> Restore</button>
            <button id="deleteBtn" class="btn-primary"><i class="fas fa-trash"></i> Delete</button>
          </div>

          <!-- Archived Students Table -->
          <div class="archive-table-container">
            <table id="archiveTable" class="archive-table">
              <thead>
                <tr>
                  <th><input type="checkbox" id="archiveSelectAll"></th>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Section</th>
                </tr>
              </thead>
              <tbody>
                @foreach($archivedStudents as $student)
                  @if($student->status === 'Cleared')
                    <tr
                      data-id="{{ $student->student_id }}"
                      data-name="{{ $student->student_fname }} {{ $student->student_lname }}"
                      data-status="{{ $student->status }}"
                      data-section="{{ $student->adviser->adviser_section ?? '' }}"
                    >
                      <td><input type="checkbox" class="archive-checkbox"></td>
                      <td>{{ $student->student_fname }} {{ $student->student_lname }}</td>
                      <td>
                        <span class="status-badge cleared">{{ $student->status }}</span>
                      </td>
                      <td>{{ $student->adviser->adviser_section ?? '-' }}</td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Modal -->
    <div class="modal" id="infoModal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="infoModalTitle">Student Info</h5>
          <button class="btn-close" onclick="closeInfoModal()">&times;</button>
        </div>
        <div class="modal-body" id="infoModalBody"></div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeInfoModal()">Close</button>
        </div>
      </div>
    </div>

</div>

<script>
  // Select All
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

  // Bulk Action Menu
  const bulkActionBtn = document.getElementById('bulkActionBtn');
  const bulkActionMenu = document.getElementById('bulkActionMenu');

  bulkActionBtn.addEventListener('click', () => {
    bulkActionMenu.style.display = bulkActionMenu.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', (e) => {
    if (!bulkActionBtn.contains(e.target) && !bulkActionMenu.contains(e.target)) {
      bulkActionMenu.style.display = 'none';
    }
  });

  document.querySelectorAll('.status-cell').forEach(cell => {
    if (cell.textContent.trim() === 'Trash') {
      cell.classList.add('Trash');
    }
  });

  // Bulk action dropdown handling
  document.querySelectorAll('.bulk-action-item').forEach(item => {
    item.addEventListener('click', () => {
      const action = item.dataset.action;
      const selected = document.querySelectorAll('.student-checkbox:checked');

      if (selected.length === 0) {
        alert("⚠️ Please select at least one student.");
        return;
      }

      if (action === "completed") {
        if (!confirm("Are you sure you want to mark selected students as Cleared?")) return;

        // Collect selected student IDs
        const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);
        console.log(ids); // optional: debug

        fetch(`/prefect/students/bulk-clear-status`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ ids, status: 'Cleared' })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert("✅ Selected students marked as Cleared.");
            location.reload(); // refresh to show updated status
          } else {
            alert("❌ Failed to update: " + data.message);
          }
        })
        .catch(err => {
          console.error(err);
          alert("❌ Something went wrong.");
        });

        // Hide bulk menu after action
        bulkActionMenu.style.display = 'none';
      }
    });
  });

  // Dropdowns
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.nextElementSibling;
      document.querySelectorAll('.dropdown-btn').forEach(other => {
        if (other !== btn) {
          other.classList.remove('active');
          other.nextElementSibling.style.display = 'none';
        }
      });
      btn.classList.toggle('active');
      container.style.display = container.style.display === 'block' ? 'none' : 'block';
    });
  });

  function logout() {
    if (!confirm("Are you sure you want to logout?")) return;
    fetch("{{ route('prefect.logout') }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(res => res.ok ? window.location.href = "{{ route('auth.login') }}" : console.error('Logout failed'))
      .catch(err => console.error('Logout failed:', err));
  }

  // Info Modal
  function showFullInfo(row) {
    const cells = row.children;
    const infoHtml = `
      <p><strong>ID:</strong> ${cells[1].innerText}</p>
      <p><strong>Name:</strong> ${cells[2].innerText}</p>
      <p><strong>Grade Level:</strong> ${cells[3].innerText}</p>
      <p><strong>Section:</strong> ${cells[4].innerText}</p>
      <p><strong>Status:</strong> ${cells[5].innerText}</p>
    `;
    document.getElementById('infoModalBody').innerHTML = infoHtml;
    document.getElementById('infoModal').classList.add('show-modal');
  }

  function closeInfoModal() {
    document.getElementById('infoModal').classList.remove('show-modal');
  }

  // Search & filter
  const searchInput = document.getElementById('searchInput');
  const sectionFilter = document.getElementById('sectionFilter');

  function filterTable() {
    const query = searchInput.value.toLowerCase();
    const section = sectionFilter.value;
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
      const name = row.children[2].innerText.toLowerCase();
      const sec = row.children[4].innerText;
      row.style.display = (name.includes(query) && (section === '' || sec === section)) ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterTable);
  sectionFilter.addEventListener('change', filterTable);

  // Archives Modal
  const archiveBtn = document.getElementById('archiveBtn');
  const archivesModal = document.getElementById('archivesModal');
  const closeArchivesModal = document.getElementById('closeArchivesModal');

  // Show modal when Archive button is clicked
  archiveBtn.addEventListener('click', () => {
    archivesModal.classList.add('show-modal');
  });

  // Close modal when "×" is clicked
  closeArchivesModal.addEventListener('click', () => {
    archivesModal.classList.remove('show-modal');
  });

  // Optional: Close modal when clicking outside modal content
  window.addEventListener('click', (e) => {
    if (e.target === archivesModal) {
      archivesModal.classList.remove('show-modal');
    }
  });

  // Archive search & section filter
  const archiveSearch = document.getElementById('archiveSearch');
  const archiveSectionFilter = document.getElementById('archiveSectionFilter');

  function filterArchiveTable() {
    const query = archiveSearch.value.toLowerCase();
    const selectedSection = archiveSectionFilter.value;

    document.querySelectorAll('#archiveTable tbody tr').forEach(row => {
      const name = row.dataset.name.toLowerCase();
      const rowSection = row.dataset.section;

      // Show row if name matches search AND section matches filter (or filter is empty)
      row.style.display = (name.includes(query) && (selectedSection === '' || rowSection === selectedSection)) ? '' : 'none';
    });
  }

  archiveSearch.addEventListener('input', filterArchiveTable);
  archiveSectionFilter.addEventListener('change', filterArchiveTable);

  // Select All in archive
  const archiveSelectAll = document.getElementById('archiveSelectAll');

  archiveSelectAll.addEventListener('change', () => {
    document.querySelectorAll('.archive-checkbox').forEach(cb => cb.checked = archiveSelectAll.checked);
  });

  // Restore action
  document.getElementById('restoreBtn').addEventListener('click', () => {
    const selected = document.querySelectorAll('.archive-checkbox:checked');
    if (selected.length === 0) {
      alert("⚠️ Please select at least one student to restore.");
      return;
    }

    const ids = Array.from(selected).map(cb => cb.closest('tr').dataset.id);

    fetch(`/prefect/students/bulk-update-status`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ ids, status: 'active' })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload(); // 🔄 refresh page after success
      } else {
        alert("❌ Restore failed: " + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert("❌ Something went wrong.");
    });
  });

  // Toggle profile dropdown
  function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  }

  // Close profile dropdown when clicking elsewhere
  document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('profileDropdown');
    const userInfo = document.querySelector('.user-info');

    if (!userInfo.contains(e.target) && dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
    }
  });
  // Show info modal when row is clicked (excluding checkbox)
document.querySelectorAll('#studentTable tbody tr').forEach(row => {
  row.addEventListener('click', (e) => {
    if (e.target.type === 'checkbox') return; // ignore clicks on checkboxes

    const id = row.dataset.id;
    const name = row.dataset.name;
    const grade = row.dataset.grade;
    const section = row.dataset.section;
    const status = row.dataset.status;
    const parentName = row.dataset.parentName || '-';
    const parentContact = row.dataset.parentContact || '-';
    const parentAddress = row.dataset.parentAddress || '-';
    const adviserName = row.dataset.adviserName || '-';

    const infoHtml = `
      <p><strong>ID:</strong> ${id}</p>
      <p><strong>Name:</strong> ${name}</p>
      <p><strong>Grade Level:</strong> ${grade}</p>
      <p><strong>Section:</strong> ${section}</p>
      <p><strong>Status:</strong> ${status}</p>
      <hr>
      <p><strong>Parent Name:</strong> ${parentName}</p>
      <p><strong>Parent Contact:</strong> <a href="tel:${parentContact}">${parentContact}</a></p>
      <p><strong>Parent Address:</strong> ${parentAddress}</p>
      <p><strong>Adviser Name:</strong> ${adviserName}</p>
    `;

    document.getElementById('infoModalBody').innerHTML = infoHtml;
    document.getElementById('infoModal').classList.add('show-modal');
  });
});

// Close modal function
function closeInfoModal() {
  document.getElementById('infoModal').classList.remove('show-modal');
}

</script>
</body>
</html>
