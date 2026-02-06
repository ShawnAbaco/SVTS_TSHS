@extends('adviser.layout')

@section('content')
  <div class="main-container">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Toolbar -->
    <div class="toolbar">
      <h2>Parent Management</h2>
      <div class="actions">
        <input type="search" placeholder="🔍 Search by parent name or ID..." id="searchInput">
        <a href="{{ route('adviser.create.parent') }}" class="btn-primary" id="createBtn">
          <i class="fas fa-plus"></i> Add Parent
        </a>
        <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
      </div>
    </div>

    <!-- Bulk Action / Select Options -->
    <div class="select-options">
      <div class="right-controls">
        <button class="btn-danger" id="moveToTrashBtn">🗑️ Move to Trash</button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>
              <label class="select-label">
                <input type="checkbox" id="selectAll">
              </label>
            </th>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Birthdate</th>
            <th>Email</th>
            <th>Contact Info</th>
            <th>Relationship</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          @forelse($parents as $parent)
            <tr data-parent-id="{{ $parent->parent_id }}" data-fname="{{ $parent->parent_fname }}"
              data-lname="{{ $parent->parent_lname }}" data-sex="{{ $parent->parent_sex }}"
              data-birthdate="{{ $parent->parent_birthdate }}" data-email="{{ $parent->parent_email }}"
              data-contact="{{ $parent->parent_contactinfo }}" data-relationship="{{ $parent->parent_relationship }}"
              data-status="{{ $parent->status }}" class="clickable-row">
              <td><input type="checkbox" class="rowCheckbox" value="{{ $parent->parent_id }}"></td>
              <td>{{ $parent->parent_id }}</td>
              <td>{{ $parent->parent_fname }}</td>
              <td>{{ $parent->parent_lname }}</td>
              <td>
                {{ $parent->parent_birthdate ? \Carbon\Carbon::parse($parent->parent_birthdate)->format('F j, Y') : 'N/A' }}
              </td>
              <td>{{ $parent->parent_email ?? 'N/A' }}</td>
              <td>{{ $parent->parent_contactinfo }}</td>
              <td>{{ $parent->parent_relationship ?? 'N/A' }}</td>
              <td>
                <span class="status-badge {{ $parent->status === 'active' ? 'status-active' : 'status-inactive' }}">
                  {{ ucfirst($parent->status) }}
                </span>
              </td>
              <td>
                <button class="btn-primary edit-btn">✏️ Edit</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center;">No active parents found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination Section -->
    <div class="pagination-container">
      <div class="pagination-info">
        Showing {{ $parents->firstItem() ?? 0 }} to {{ $parents->lastItem() ?? 0 }} of {{ $parents->total() }} entries
      </div>
      <div class="pagination-links">
        @if ($parents->hasPages())
          <nav class="pagination-nav">
            <ul class="pagination">
              {{-- Previous Page Link --}}
              @if ($parents->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                  <span class="page-link">‹ Previous</span>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="{{ $parents->previousPageUrl() }}" rel="prev">‹ Previous</a>
                </li>
              @endif

              {{-- Next Page Link --}}
              @if ($parents->hasMorePages())
                <li class="page-item">
                  <a class="page-link" href="{{ $parents->nextPageUrl() }}" rel="next">Next ›</a>
                </li>
              @else
                <li class="page-item disabled" aria-disabled="true">
                  <span class="page-link">Next ›</span>
                </li>
              @endif
            </ul>
          </nav>
        @endif
      </div>
    </div>

    <!-- 👤 Parent Info Modal -->
    <div class="modal modern-modal" id="infoModal">
      <div class="modal-content modern-content">
        <!-- Title Header -->
        <div class="modal-title-header">
          <h3>👤 Parent Information</h3>
        </div>

        <div class="modal-body">
          <div class="info-header">
            <div class="avatar-placeholder">
              <span class="avatar-text" id="avatarInitials"></span>
            </div>
            <div class="header-info">
              <h4 class="parent-name" id="info_fullname"></h4>
              <span class="status-badge" id="info_status"></span>
            </div>
          </div>

          <div class="info-container">
            <div class="info-section">
              <h5 class="section-title">Basic Information</h5>
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">Parent ID</span>
                  <span class="info-value" id="info_parent_id"></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Sex</span>
                  <span class="info-value" id="info_sex"></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Birthdate</span>
                  <span class="info-value" id="info_birthdate"></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Relationship</span>
                  <span class="info-value" id="info_relationship"></span>
                </div>
              </div>
            </div>

            <div class="info-section">
              <h5 class="section-title">Contact Information</h5>
              <div class="contact-grid">
                <div class="contact-item">
                  <div class="contact-icon">📧</div>
                  <div class="contact-details">
                    <span class="contact-label">Email Address</span>
                    <a href="#" class="contact-value" id="info_email"></a>
                  </div>
                </div>
                <div class="contact-item">
                  <div class="contact-icon">📱</div>
                  <div class="contact-details">
                    <span class="contact-label">Contact Number</span>
                    <a href="#" class="contact-value" id="info_contact"></a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Associated Students Section -->
            <div class="info-section">
              <h5 class="section-title">Associated Students</h5>
              <div class="students-container" id="studentsContainer">
                <!-- Student data will be populated here via JavaScript -->
              </div>
            </div>
          </div>
        </div>

        <div class="modal-actions modern-actions">
          <button type="button" class="btn-secondary" id="closeInfoBtn">Close</button>
        </div>
      </div>
    </div>

    <!-- ✏️ Edit Parent Modal -->
    <div class="modal" id="editModal">
      <div class="modal-content">
        <button class="close-btn" id="closeEditModal"></button>
        <h2>Edit Parent</h2>

        <!-- Success/Error Messages (Hidden by default) -->
        <div id="editModalMessages" style="display: none;"></div>

        <form id="editParentForm" method="POST">
          @csrf
          <input type="hidden" name="parent_id" id="edit_parent_id">

          <div class="form-grid">
            <div class="form-group">
              <label for="edit_parent_fname">First Name *</label>
              <input type="text" name="parent_fname" id="edit_parent_fname" class="form-control" required>
              <span class="error-message" id="fname_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_lname">Last Name *</label>
              <input type="text" name="parent_lname" id="edit_parent_lname" class="form-control" required>
              <span class="error-message" id="lname_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_sex">Sex *</label>
              <select name="parent_sex" id="edit_parent_sex" class="form-control" required>
                <option value="">Select Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
              <span class="error-message" id="sex_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_birthdate">Birthdate *</label>
              <input type="date" name="parent_birthdate" id="edit_parent_birthdate" class="form-control" required>
              <span class="error-message" id="birthdate_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_email">Email</label>
              <input type="email" name="parent_email" id="edit_parent_email" class="form-control">
              <span class="error-message" id="email_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_contactinfo">Contact Info *</label>
              <input type="text" name="parent_contactinfo" id="edit_parent_contactinfo" class="form-control" required>
              <span class="error-message" id="contactinfo_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_relationship">Relationship *</label>
              <select name="parent_relationship" id="edit_parent_relationship" class="form-control" required>
                <option value="">Select Relationship</option>
                <option value="father">Father</option>
                <option value="mother">Mother</option>
                <option value="guardian">Guardian</option>
                <option value="grandparent">Grandparent</option>
                <option value="other">Other</option>
              </select>
              <span class="error-message" id="relationship_error"></span>
            </div>

            <div class="form-group">
              <label for="edit_parent_status">Status *</label>
              <select name="status" id="edit_parent_status" class="form-control" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="error-message" id="status_error"></span>
            </div>
          </div>

          <div class="actions">
            <button type="submit" class="btn-primary" id="saveEditBtn">
              <i class="fas fa-save"></i> Save Changes
            </button>
            <button type="button" class="btn-secondary" id="cancelEditBtn">
              <i class="fas fa-times"></i> Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

  <!-- 🗃️ Archive Modal -->
<div class="modal" id="archiveModal">
  <div class="modal-content large-modal">
    <div class="modal-header">
      🗃️ Archived Parents
    </div>
    <div class="modal-body">
      <div class="modal-actions">
        <div class="search-container">
          <input type="search" placeholder="🔍 Search archived..." id="archiveSearch" class="search-input">
        </div>
      </div>

      <div class="archive-table-container no-scroll">
        <table class="archive-table">
          <thead>
            <tr>
              <th>
                <label class="select-label">
                  <input type="checkbox" id="selectAllArchived" class="select-all-checkbox">
                  <span>Select All</span>
                </label>
              </th>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Birthdate</th>
              <th>Email</th>
              <th>Contact Info</th>
              <th>Relationship</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="archiveTableBody">
            <!-- Archived parents will be loaded here via AJAX -->
          </tbody>
        </table>
      </div>

      <div class="modal-note">
        ⚠️ Note: Deleting records will permanently remove them.
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" id="restoreArchiveBtn">🔄 Restore</button>
        <button class="btn-danger" id="deleteArchiveBtn">🗑️ Delete</button>
        <button class="btn-close" id="closeArchive">❌ Close</button>
      </div>
    </div>
  </div>
</div>
    <!-- Notification Modal -->
    <div class="notification-modal" id="notificationModal">
      <div class="notification-content" id="notificationContent">
        <div class="notification-icon" id="notificationIcon"></div>
        <div class="notification-message" id="notificationMessage"></div>
        <div class="notification-actions" id="notificationActions">
          <!-- OK button removed for success messages -->
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="notification-modal" id="confirmationModal">
      <div class="notification-content">
        <div class="notification-icon">⚠️</div>
        <div class="notification-message" id="confirmationMessage"></div>
        <div class="notification-actions">
          <button class="btn-confirm" id="confirmAction">Confirm</button>
          <button class="btn-cancel" id="cancelAction">Cancel</button>
        </div>
      </div>
    </div>

  </div>

  <style>
    /* Additional CSS for the info modal functionality */
    .clickable-row {
      cursor: pointer;
    }

    .clickable-row:hover {
      background-color: #f5f5f5;
    }

    /* Modern Modal Styles */
    .modern-modal .modal-content {
      max-width: 500px;
      margin: 50px auto;
      padding: 0;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modern-content {
      background: white;
    }

    /* Title Header */
    .modal-title-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 25px;
      text-align: center;
    }

    .modal-title-header h3 {
      margin: 0;
      font-size: 1.3rem;
      font-weight: 600;
    }

    .modal-body {
      padding: 25px;
    }

    .info-header {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .students-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .avatar-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      border: 3px solid #f0f0f0;
    }

    .avatar-text {
      font-size: 1.5rem;
      font-weight: bold;
      color: white;
    }

    .header-info {
      flex: 1;
    }

    .parent-name {
      margin: 0 0 8px 0;
      font-size: 1.3rem;
      font-weight: 600;
      color: #333;
    }

    .info-container {
      margin: 0;
    }

    .info-section {
      margin-bottom: 25px;
    }

    .info-section:last-child {
      margin-bottom: 0;
    }

    .section-title {
      font-size: 1rem;
      color: #555;
      margin: 0 0 15px 0;
      padding-bottom: 8px;
      border-bottom: 1px solid #f0f0f0;
      font-weight: 600;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .info-item {
      display: flex;
      flex-direction: column;
    }

    .info-label {
      font-size: 0.85rem;
      color: #777;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .info-value {
      font-size: 0.95rem;
      color: #333;
      font-weight: 500;
    }

    .contact-grid {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .contact-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .contact-icon {
      font-size: 1.2rem;
      margin-top: 2px;
    }

    .contact-details {
      display: flex;
      flex-direction: column;
    }

    .contact-label {
      font-size: 0.85rem;
      color: #777;
      margin-bottom: 3px;
    }

    .contact-value {
      font-size: 0.95rem;
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
    }

    .contact-value:hover {
      text-decoration: underline;
    }

    .modern-actions {
      display: flex;
      justify-content: center;
      padding: 20px 25px;
      border-top: 1px solid #f0f0f0;
      background: #f9f9f9;
    }

    /* Table Scrollable Styles */
    .table-container {
      max-height: 600px;
      /* Adjust height as needed */
      overflow-y: auto;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .table-container table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    .table-container thead {
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
      z-index: 10;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    .table-container td {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
    }

    .table-container tbody tr:last-child td {
      border-bottom: none;
    }

    /* Make checkboxes bigger */
    .table-container input[type="checkbox"] {
      transform: scale(1.3);
      margin: 0;
      cursor: pointer;
    }

    /* Select All in table header */
    .table-container thead .select-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: bold;
      cursor: pointer;
      margin: 0;
      font-size: 14px;
    }

    /* Custom scrollbar for table container */
    .table-container::-webkit-scrollbar {
      width: 8px;
    }

    .table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Select Options Styling */
    .select-options {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 15px 0;
      margin-bottom: 10px;
      border-bottom: 1px solid #e0e0e0;
    }

    /* Student Information Styles */
    .students-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .student-item {
      display: flex;
      align-items: flex-start;
      padding: 12px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #e9ecef;
      transition: background-color 0.2s ease;
    }

    .student-item:hover {
      background: #e9ecef;
    }

    .student-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 12px;
      flex-shrink: 0;
    }

    .student-avatar-text {
      color: white;
      font-weight: bold;
      font-size: 0.9rem;
    }

    .student-info {
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .student-name {
      font-weight: 600;
      color: #333;
      margin-bottom: 4px;
      font-size: 0.95rem;
    }

    .student-details {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 4px;
    }

    .student-detail {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 0.8rem;
      color: #666;
    }

    .detail-label {
      font-weight: 500;
      color: #555;
    }

    .detail-value {
      color: #333;
    }

    .no-students {
      text-align: center;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px dashed #dee2e6;
    }

    .no-data-text {
      color: #6c757d;
      font-style: italic;
    }

    /* Ensure the modal content can accommodate the student section */
    .modern-content .modal-body {
      max-height: 70vh;
      overflow-y: auto;
    }

    /* Archive table scrollable */
    .archive-table-container {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      margin: 10px 0;
    }

    .archive-table-container table {
      width: 100%;
      border-collapse: collapse;
    }

    .archive-table-container thead {
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
      z-index: 10;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    .archive-table-container th {
      padding: 12px 15px;
      text-align: left;
      font-weight: 600;
      color: #333;
      border-bottom: 2px solid #e0e0e0;
      background-color: #f8f9fa;
    }

    .archive-table-container td {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
    }

    .archive-table-container tbody tr:last-child td {
      border-bottom: none;
    }

    /* Archive table select all */
    .archive-table thead .select-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: bold;
      cursor: pointer;
      margin: 0;
      font-size: 14px;
    }

    /* Custom scrollbar for archive table */
    .archive-table-container::-webkit-scrollbar {
      width: 6px;
    }

    .archive-table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    .archive-table-container::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .archive-table-container::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Status badges */
    .status-badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-active {
      background-color: #e6f7ee;
      color: #0d6832;
    }

    .status-inactive {
      background-color: #fef3e2;
      color: #92400f;
    }

    /* Pagination Styles */
    .pagination-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 20px;
      padding: 15px 0;
      border-top: 1px solid #e0e0e0;
      gap: 10px;
    }

    .pagination-info {
      font-size: 14px;
      color: #666;
      font-weight: 500;
      order: 2;
      /* This puts the info below the buttons */
    }

    .pagination-links {
      order: 1;
      /* This puts the buttons above the info */
      width: 100%;
      display: flex;
      justify-content: center;
    }

    .pagination-nav {
      display: flex;
      justify-content: center;
    }

    .pagination {
      display: flex;
      list-style: none;
      padding: 0;
      margin: 0;
      gap: 10px;
    }

    .page-item {
      margin: 0;
    }

    .page-link {
      display: block;
      padding: 10px 16px;
      border: 1px solid #ddd;
      border-radius: 6px;
      text-decoration: none;
      color: #007bff;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s ease;
      min-width: 80px;
      text-align: center;
    }

    .page-link:hover {
      background-color: #e9ecef;
      border-color: #dee2e6;
    }

    .page-item.active .page-link {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
    }

    .page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      background-color: #f8f9fa;
      border-color: #dee2e6;
    }
   
/* Responsive design for smaller screens */
@media (max-width: 768px) {
  .large-modal {
    max-width: 98% !important;
    width: 98% !important;
    margin: 1vh auto !important;
  }
  
  .archive-table {
    font-size: 0.9rem;
  }
  
  .archive-table th,
  .archive-table td {
    padding: 10px 8px;
  }
}

/* Archive table select all */
.archive-table thead .select-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: bold;
  cursor: pointer;
  margin: 0;
  font-size: 14px;
  white-space: nowrap;
}

/* Status badges */
.status-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-inactive {
  background-color: #fef3e2;
  color: #92400f;
}
  </style>

  <script>
    // ==========================
    // Notification System
    // ==========================
    class NotificationManager {
      constructor() {
        this.notificationModal = document.getElementById('notificationModal');
        this.confirmationModal = document.getElementById('confirmationModal');
        this.notificationMessage = document.getElementById('notificationMessage');
        this.confirmationMessage = document.getElementById('confirmationMessage');
        this.notificationIcon = document.getElementById('notificationIcon');
        this.notificationActions = document.getElementById('notificationActions');
        this.confirmAction = document.getElementById('confirmAction');
        this.cancelAction = document.getElementById('cancelAction');

        this.autoCloseTimeout = null;
        this.setupEventListeners();
      }

      setupEventListeners() {
        // Confirmation modal
        this.confirmAction.addEventListener('click', () => {
          if (this.confirmCallback) {
            this.confirmCallback();
          }
          this.hideConfirmation();
        });

        this.cancelAction.addEventListener('click', () => {
          if (this.cancelCallback) {
            this.cancelCallback();
          }
          this.hideConfirmation();
        });

        // Close modals when clicking outside
        this.notificationModal.addEventListener('click', (e) => {
          if (e.target === this.notificationModal) {
            this.hideNotification();
          }
        });

        this.confirmationModal.addEventListener('click', (e) => {
          if (e.target === this.confirmationModal) {
            this.hideConfirmation();
          }
        });
      }

      showNotification(message, type = 'info') {
        const icons = {
          success: '✅',
          error: '❌',
          warning: '⚠️',
          info: 'ℹ️'
        };

        this.notificationIcon.textContent = icons[type] || icons.info;
        this.notificationMessage.textContent = message;
        this.notificationModal.className = `notification-modal notification-${type}`;

        // Clear any existing timeout
        if (this.autoCloseTimeout) {
          clearTimeout(this.autoCloseTimeout);
        }

        // For success messages, hide OK button and auto-close after 1 second
        if (type === 'success') {
          this.notificationActions.innerHTML = ''; // Remove OK button
          this.notificationModal.style.display = 'flex';

          // Auto-close after 1 second
          this.autoCloseTimeout = setTimeout(() => {
            this.hideNotification();
          }, 1000);
        } else {
          // For other message types, show OK button
          this.notificationActions.innerHTML = '<button class="btn-confirm" id="notificationConfirm">OK</button>';

          // Add event listener for the newly created button
          const okButton = document.getElementById('notificationConfirm');
          if (okButton) {
            okButton.addEventListener('click', () => {
              this.hideNotification();
            });
          }

          this.notificationModal.style.display = 'flex';
        }
      }

      hideNotification() {
        this.notificationModal.style.display = 'none';
        if (this.autoCloseTimeout) {
          clearTimeout(this.autoCloseTimeout);
          this.autoCloseTimeout = null;
        }
      }

      showConfirmation(message, confirmCallback, cancelCallback = null) {
        this.confirmationMessage.textContent = message;
        this.confirmCallback = confirmCallback;
        this.cancelCallback = cancelCallback;
        this.confirmationModal.style.display = 'flex';
      }

      hideConfirmation() {
        this.confirmationModal.style.display = 'none';
        this.confirmCallback = null;
        this.cancelCallback = null;
      }
    }

    // Initialize notification manager
    const notifications = new NotificationManager();


    // ==========================
    // 👤 Parent Info Modal Functionality
    // ==========================
    document.addEventListener('DOMContentLoaded', function () {
      const infoModal = document.getElementById('infoModal');
      const closeInfoBtn = document.getElementById('closeInfoBtn');

      // Clickable rows to show info modal
      document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function (e) {
          // Don't trigger if clicking on checkbox or edit button
          if (e.target.type === 'checkbox' || e.target.classList.contains('edit-btn')) {
            return;
          }

          // Get data from the row attributes
          const parentId = this.getAttribute('data-parent-id');
          const fname = this.getAttribute('data-fname');
          const lname = this.getAttribute('data-lname');
          const sex = this.getAttribute('data-sex');
          const birthdate = this.getAttribute('data-birthdate');
          const email = this.getAttribute('data-email');
          const contact = this.getAttribute('data-contact');
          const relationship = this.getAttribute('data-relationship');
          const status = this.getAttribute('data-status');

          // Format birthdate
          let formattedBirthdate = 'N/A';
          if (birthdate && birthdate !== 'null') {
            formattedBirthdate = new Date(birthdate).toLocaleDateString('en-US', {
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            });
          }

          // Fill info modal
          document.getElementById('info_parent_id').textContent = parentId;
          document.getElementById('info_fullname').textContent = `${fname} ${lname}`;
          document.getElementById('info_sex').textContent = sex;
          document.getElementById('info_birthdate').textContent = formattedBirthdate;
          document.getElementById('info_relationship').textContent = relationship || 'N/A';

          // Set avatar initials
          const avatarInitials = document.getElementById('avatarInitials');
          avatarInitials.textContent = (fname.charAt(0) + lname.charAt(0)).toUpperCase();

          // Set status badge
          const statusBadge = document.getElementById('info_status');
          statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
          statusBadge.className = `status-badge ${status === 'active' ? 'status-active' : 'status-inactive'}`;

          // Email with clickable link
          const emailLink = document.getElementById('info_email');
          if (email && email !== 'null' && email !== 'N/A') {
            emailLink.textContent = email;
            emailLink.href = `mailto:${email}`;
            emailLink.style.display = 'inline';
          } else {
            emailLink.textContent = 'N/A';
            emailLink.href = '#';
            emailLink.style.display = 'none';
          }

          // Contact with clickable link
          const contactLink = document.getElementById('info_contact');
          if (contact && contact !== 'null') {
            contactLink.textContent = contact;
            contactLink.href = `tel:${contact}`;
            contactLink.style.display = 'inline';
          } else {
            contactLink.textContent = 'N/A';
            contactLink.href = '#';
            contactLink.style.display = 'none';
          }

          // Load and populate student data
          loadStudentData(parentId);

          // Show modal
          infoModal.style.display = 'flex';
        });
      });

      // Function to load student data
      function loadStudentData(parentId) {
        fetch(`/adviser/parents/${parentId}/students`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              populateStudentData(data.students);
            } else {
              document.getElementById('studentsContainer').innerHTML = `
                          <div class="no-students">
                              <span class="no-data-text">Error loading student information</span>
                          </div>
                      `;
            }
          })
          .catch(error => {
            console.error('Error loading student data:', error);
            document.getElementById('studentsContainer').innerHTML = `
                      <div class="no-students">
                          <span class="no-data-text">Error loading student information</span>
                      </div>
                  `;
          });
      }

      // Function to populate student data
      function populateStudentData(students) {
        const studentsContainer = document.getElementById('studentsContainer');

        if (!students || students.length === 0) {
          studentsContainer.innerHTML = `
                  <div class="no-students">
                      <span class="no-data-text">No students associated with this parent</span>
                  </div>
              `;
          return;
        }

        let studentsHTML = '';
        students.forEach(student => {
          // Get grade, section, and adviser from adviser relationship
          const grade = student.adviser?.adviser_gradelevel || 'N/A';
          const section = student.adviser?.adviser_section || 'N/A';
          const adviser = student.adviser ? `${student.adviser.adviser_fname} ${student.adviser.adviser_lname}` : 'N/A';

          studentsHTML += `
                  <div class="student-item">
                      <div class="student-avatar">
                          <span class="student-avatar-text">
                              ${student.student_fname.charAt(0)}${student.student_lname.charAt(0)}
                          </span>
                      </div>
                      <div class="student-info">
                          <span class="student-name">${student.student_fname} ${student.student_lname}</span>
                          <div class="student-details">
                              <div class="student-detail">
                                  <span class="detail-label">ID:</span>
                                  <span class="detail-value">${student.student_id}</span>
                              </div>
                              <div class="student-detail">
                                  <span class="detail-label">Grade:</span>
                                  <span class="detail-value">${grade}</span>
                              </div>
                              <div class="student-detail">
                                  <span class="detail-label">Section:</span>
                                  <span class="detail-value">${section}</span>
                              </div>
                              <div class="student-detail">
                                  <span class="detail-label">Adviser:</span>
                                  <span class="detail-value">${adviser}</span>
                              </div>
                          </div>
                      </div>
                  </div>
              `;
        });

        studentsContainer.innerHTML = studentsHTML;
      }

      // Close info modal
      closeInfoBtn.addEventListener('click', () => {
        infoModal.style.display = 'none';
      });

      // Close info modal when clicking outside
      document.addEventListener('click', function (event) {
        if (event.target === infoModal) {
          infoModal.style.display = 'none';
        }
      });
    });

    // ==========================
    // Search filter for main table
    // ==========================
    document.getElementById('searchInput').addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#tableBody tr');

      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    // ==========================
    // Select all checkboxes
    // ==========================
    document.getElementById('selectAll').addEventListener('change', function () {
      document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
    });

    // ==========================
    // Move to Archive (Trash)
    // ==========================
    document.getElementById('moveToTrashBtn').addEventListener('click', function () {
      const selected = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
        .map(cb => cb.value);

      if (!selected.length) {
        notifications.showNotification('Please select at least one parent.', 'warning');
        return;
      }

      notifications.showConfirmation(
        `Are you sure you want to move ${selected.length} parent(s) to archive?`,
        () => {
          fetch('{{ route("adviser.parents.archive") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ parent_ids: selected })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                notifications.showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
              } else {
                notifications.showNotification('Error: ' + data.message, 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              notifications.showNotification('An error occurred while archiving parents.', 'error');
            });
        }
      );
    });

    // ==========================
// Load Archived Parents
// ==========================
function loadArchivedParents() {
  fetch('{{ route("adviser.parents.archived") }}')
    .then(response => response.json())
    .then(parents => {
      const archiveTableBody = document.getElementById('archiveTableBody');
      archiveTableBody.innerHTML = '';

      if (parents.length === 0) {
        archiveTableBody.innerHTML = `
          <tr>
            <td colspan="9" style="text-align:center; padding:15px;">No archived parents found</td>
          </tr>
        `;
        return;
      }

      parents.forEach(parent => {
        // Format birthdate
        let formattedBirthdate = 'N/A';
        if (parent.parent_birthdate && parent.parent_birthdate !== 'null') {
          formattedBirthdate = new Date(parent.parent_birthdate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        }

        const row = document.createElement('tr');
        row.innerHTML = `
          <td><input type="checkbox" class="archiveCheckbox" value="${parent.parent_id}"></td>
          <td>${parent.parent_id}</td>
          <td>${parent.parent_fname}</td>
          <td>${parent.parent_lname}</td>
          <td>${formattedBirthdate}</td>
          <td>${parent.parent_email || 'N/A'}</td>
          <td>${parent.parent_contactinfo || 'N/A'}</td>
          <td>${parent.parent_relationship || 'N/A'}</td>
          <td><span class="status-badge status-inactive">Inactive</span></td>
        `;
        archiveTableBody.appendChild(row);
      });

      // Update select all functionality for archived items
      updateArchiveSelectAll();
    })
    .catch(error => {
      console.error('Error loading archived parents:', error);
      notifications.showNotification('Error loading archived parents.', 'error');
    });
}

    // ==========================
    // Update Archive Select All
    // ==========================
    function updateArchiveSelectAll() {
      const selectAllArchived = document.getElementById('selectAllArchived');
      const archivedCheckboxes = document.querySelectorAll('.archiveCheckbox');

      selectAllArchived.addEventListener('change', function () {
        archivedCheckboxes.forEach(cb => cb.checked = this.checked);
      });

      // Update select all when individual checkboxes change
      archivedCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
          const allChecked = Array.from(archivedCheckboxes).every(cb => cb.checked);
          selectAllArchived.checked = allChecked;
        });
      });
    }

    // ==========================
    // Restore Archived Parents
    // ==========================
    document.getElementById('restoreArchiveBtn').addEventListener('click', function () {
      const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
        .map(cb => cb.value);

      if (!selected.length) {
        notifications.showNotification('Please select at least one parent to restore.', 'warning');
        return;
      }

      notifications.showConfirmation(
        `Are you sure you want to restore ${selected.length} parent(s)?`,
        () => {
          fetch('{{ route("adviser.parents.restore") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ parent_ids: selected })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                notifications.showNotification(data.message, 'success');
                loadArchivedParents();
                setTimeout(() => location.reload(), 1500);
              } else {
                notifications.showNotification('Error: ' + data.message, 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              notifications.showNotification('An error occurred while restoring parents.', 'error');
            });
        }
      );
    });

    // ==========================
    // Delete Archived Parents Permanently
    // ==========================
    document.getElementById('deleteArchiveBtn').addEventListener('click', function () {
      const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
        .map(cb => cb.value);

      if (!selected.length) {
        notifications.showNotification('Please select at least one parent to delete permanently.', 'warning');
        return;
      }

      notifications.showConfirmation(
        `WARNING: This will permanently delete ${selected.length} parent(s). This action cannot be undone!`,
        () => {
          fetch('{{ route("adviser.parents.destroy.permanent") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ parent_ids: selected })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                notifications.showNotification(data.message, 'success');
                loadArchivedParents();
              } else {
                notifications.showNotification('Error: ' + data.message, 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              notifications.showNotification('An error occurred while deleting parents.', 'error');
            });
        }
      );
    });

    // ==========================
    // Open Archive Modal
    // ==========================
    document.getElementById('archiveBtn').addEventListener('click', function () {
      loadArchivedParents();
      document.getElementById('archiveModal').style.display = 'flex';
    });

    // ==========================
    // Close Archive Modal
    // ==========================
    document.getElementById('closeArchive').addEventListener('click', function () {
      document.getElementById('archiveModal').style.display = 'none';
    });

    // ==========================
    // Archive Search
    // ==========================
    document.getElementById('archiveSearch').addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#archiveTableBody tr');
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    // ==========================
    // Edit Modal Functionality
    // ==========================
    document.addEventListener('DOMContentLoaded', function () {
      const editButtons = document.querySelectorAll('.edit-btn');
      const editModal = document.getElementById('editModal');
      const closeEditModal = document.getElementById('closeEditModal');
      const cancelEditBtn = document.getElementById('cancelEditBtn');
      const editForm = document.getElementById('editParentForm');
      const saveEditBtn = document.getElementById('saveEditBtn');

      // Helper functions
      function clearEditErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
          el.textContent = '';
        });
      }

      function closeEditModalFunc() {
        editModal.style.display = 'none';
        clearEditErrors();
      }

      // Open edit modal
      editButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          const row = this.closest('tr');
          const parentId = row.getAttribute('data-parent-id');

          // Get current values from row data attributes
          const fname = row.getAttribute('data-fname');
          const lname = row.getAttribute('data-lname');
          const sex = row.getAttribute('data-sex');
          const birthdate = row.getAttribute('data-birthdate');
          const email = row.getAttribute('data-email');
          const contact = row.getAttribute('data-contact');
          const relationship = row.getAttribute('data-relationship');
          const status = row.getAttribute('data-status');

          // Convert birthdate to YYYY-MM-DD format
          let birthdateInput = '';
          if (birthdate && birthdate !== 'null') {
            birthdateInput = birthdate.split('T')[0];
          }

          // Clear previous errors
          clearEditErrors();

          // Fill form with current data
          document.getElementById('edit_parent_id').value = parentId;
          document.getElementById('edit_parent_fname').value = fname;
          document.getElementById('edit_parent_lname').value = lname;
          document.getElementById('edit_parent_sex').value = sex;
          document.getElementById('edit_parent_birthdate').value = birthdateInput;
          document.getElementById('edit_parent_email').value = email === 'N/A' ? '' : email;
          document.getElementById('edit_parent_contactinfo').value = contact;
          document.getElementById('edit_parent_relationship').value = relationship === 'N/A' ? '' : relationship;
          document.getElementById('edit_parent_status').value = status;

          // Set form action with correct route
          editForm.action = `/adviser/parents/update/${parentId}`;

          // Show modal
          editModal.style.display = 'flex';
        });
      });

      // Handle form submission with AJAX
      editForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const parentId = document.getElementById('edit_parent_id').value;

        // Show loading state
        saveEditBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveEditBtn.disabled = true;

        fetch(this.action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(Object.fromEntries(formData))
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              notifications.showNotification(data.message, 'success');
              closeEditModalFunc();
              setTimeout(() => {
                location.reload();
              }, 1500);
            } else {
              // Handle validation errors
              if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                  const errorElement = document.getElementById(field + '_error');
                  if (errorElement) {
                    errorElement.textContent = data.errors[field][0];
                  }
                });
                notifications.showNotification('Please fix the validation errors.', 'error');
              } else {
                notifications.showNotification(data.message || 'Update failed', 'error');
              }
            }
          })
          .catch(error => {
            console.error('Error:', error);
            notifications.showNotification('An error occurred while updating parent.', 'error');
          })
          .finally(() => {
            saveEditBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
            saveEditBtn.disabled = false;
          });
      });

      // Close modal functions
      closeEditModal.addEventListener('click', closeEditModalFunc);
      cancelEditBtn.addEventListener('click', closeEditModalFunc);

      // Close modal when clicking outside
      editModal.addEventListener('click', function (e) {
        if (e.target === editModal) {
          closeEditModalFunc();
        }
      });
    });
  </script>

@endsection