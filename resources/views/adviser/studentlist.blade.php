<!-- adviser/studentlist.blade.php -->
    @extends('adviser.layout')

    @section('content')
      <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar -->
        <div class="toolbar">
          <h2>Student Management</h2>
          <div class="actions">
            <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
            <a href="{{ route('adviser.create.student') }}" class="btn-primary" id="createBtn">
              <i class="fas fa-plus"></i> Add Student
            </a>
            <button class="btn-info" id="archiveBtn">🗃️ Archive</button>
          </div>
        </div>

        <!-- Bulk Action / Select Options -->
        <div class="select-options">
          <div class="right-controls">
            <button class="btn-danger" id="moveToTrashBtn">🗑️ Move to Trash</button>
            <button class="btn-success" id="markGraduatedBtn">✅ Mark as Graduated</button>
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
                <th>Sex</th>
                <th>Birthdate</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              @forelse($students as $student)
                <tr data-student-id="{{ $student->student_id }}" data-fname="{{ $student->student_fname }}"
                  data-lname="{{ $student->student_lname }}" data-sex="{{ $student->student_sex }}"
                  data-birthdate="{{ $student->student_birthdate }}" data-address="{{ $student->student_address }}"
                  data-contact="{{ $student->student_contactinfo }}" data-status="{{ $student->status }}" class="clickable-row">
                  <td><input type="checkbox" class="rowCheckbox" value="{{ $student->student_id }}"></td>
                  <td>{{ $student->student_id }}</td>
                  <td>{{ $student->student_fname }}</td>
                  <td>{{ $student->student_lname }}</td>
                  <td>{{ ucfirst($student->student_sex) }}</td>
                  <td>{{ \Carbon\Carbon::parse($student->student_birthdate)->format('F j, Y') }}</td>
                  <td>{{ $student->student_address }}</td>
                  <td>{{ $student->student_contactinfo }}</td>
                  <td>
                    <span class="status-badge {{ $student->status === 'active' ? 'status-active' : 'status-inactive' }}">
                      {{ ucfirst($student->status) }}
                    </span>
                  </td>
                  <td>
                    <button class="btn-primary edit-btn">✏️ Edit</button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" style="text-align:center;">⚠️ No students found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
          <div class="pagination-links">
            @if ($students->hasPages())
              <nav class="pagination-nav">
                <ul class="pagination">
                  {{-- Previous Page Link --}}
                  @if ($students->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                      <span class="page-link">‹ Previous</span>
                    </li>
                  @else
                    <li class="page-item">
                      <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev">‹ Previous</a>
                    </li>
                  @endif

                  {{-- Next Page Link --}}
                  @if ($students->hasMorePages())
                    <li class="page-item">
                      <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next">Next ›</a>
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
          <div class="pagination-info">
            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} entries
          </div>
        </div>

<!-- 👤 Student Info Modal (Updated with Tabs) -->
<div class="modal modern-modal" id="infoModal">
  <div class="modal-content modern-content">
    <!-- Title Header -->
    <div class="modal-title-header">
      <h3>👤 Student Information</h3>
    </div>
    
    <!-- Tab Navigation -->
    <div class="info-modal-tabs">
      <button class="tab-button active" data-tab="info">Information</button>
      <button class="tab-button" data-tab="violation">Violations</button>
      <button class="tab-button" data-tab="complaint">Complaints</button>
    </div>
    
    <div class="modal-body">
      <!-- Information Tab Content -->
      <div class="tab-content active" id="info-tab">
        <div class="info-header">
          <div class="avatar-placeholder">
            <span class="avatar-text" id="avatarInitials"></span>
          </div>
          <div class="header-info">
            <h4 class="student-name" id="info_fullname"></h4>
            <span class="status-badge" id="info_status"></span>
          </div>
        </div>
        
        <div class="info-container">
          <!-- Student Information Section -->
          <div class="info-section">
            <h5 class="section-title">Student Information</h5>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">Student ID</span>
                <span class="info-value" id="info_student_id"></span>
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
                <span class="info-label">Grade Level</span>
                <span class="info-value" id="info_grade"></span>
              </div>
              <div class="info-item">
                <span class="info-label">Section</span>
                <span class="info-value" id="info_section"></span>
              </div>
              <div class="info-item">
                <span class="info-label">Adviser</span>
                <span class="info-value" id="info_adviser"></span>
              </div>
            </div>
          </div>
          
          <!-- Contact Details Section -->
          <div class="info-section">
            <h5 class="section-title">Contact Details</h5>
            <div class="contact-grid">
              <div class="contact-item">
                <div class="contact-icon">🏠</div>
                <div class="contact-details">
                  <span class="contact-label">Address</span>
                  <span class="contact-value" id="info_address"></span>
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
          
          <!-- Parent/Guardian Information Section -->
          <div class="info-section">
            <h5 class="section-title">Parent/Guardian Information</h5>
            <div class="parents-container" id="parentsContainer">
              <!-- Parent information will be loaded here dynamically -->
              <div class="empty-state">
                <p>No parent information available.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Violations Tab Content -->
      <div class="tab-content" id="violation-tab">
        <div class="tab-header">
          <h4>📋 Student Violations</h4>
        </div>
        
        <div class="violations-container">
          <div class="violations-list" id="violationsList">
            <!-- Violations will be loaded here -->
            <div class="empty-state">
              <p>No violations recorded for this student.</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Complaints Tab Content -->
      <div class="tab-content" id="complaint-tab">
        <div class="tab-header">
          <h4>📝 Student Complaints</h4>
        </div>
        
        <div class="complaints-container">
          <div class="complaints-list" id="complaintsList">
            <!-- Complaints will be loaded here -->
            <div class="empty-state">
              <p>No complaints recorded for this student.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions modern-actions">
<button type="button" class="btns-close" id="closeInfoBtn">Close</button>
    </div>
  </div>
</div>  

        <!-- ✏️ Edit Student Modal -->
        <div class="modal" id="editModal">
          <div class="modal-content">
            <button class="close-btn" id="closeEditModal">✖</button>
            <h2>Edit Student</h2>

            <form id="editStudentForm" method="POST" action="">
              @csrf
              <!-- Remove @method('PUT') line -->
              <input type="hidden" name="student_id" id="edit_student_id">

              <div class="form-grid">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="student_fname" id="edit_student_fname" required>
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="student_lname" id="edit_student_lname" required>
                </div>
                <div class="form-group">
                  <label>Sex</label>
                  <select name="student_sex" id="edit_student_sex" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Birthdate</label>
                  <input type="date" name="student_birthdate" id="edit_student_birthdate" required>
                </div>
                <div class="form-group">
                  <label>Address</label>
                  <input type="text" name="student_address" id="edit_student_address" required>
                </div>
                <div class="form-group">
                  <label>Contact Info</label>
                  <input type="text" name="student_contactinfo" id="edit_student_contactinfo" required>
                </div>
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" id="edit_student_status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="transferred">Transferred</option>
                    <option value="graduated">Graduated</option>
                  </select>
                </div>
              </div>

              <div class="actions">
                <button type="submit" class="btn-primary" id="saveEditBtn">💾 Save Changes</button>
                <button type="button" class="btn-secondary" id="cancelEditBtn">❌ Cancel</button>
              </div>
            </form>
          </div>
        </div>

       <!-- 🗃️ Archive Modal -->
<div class="modal" id="archiveModal">
  <div class="modal-content large-modal">
    <div class="modal-header">
      🗃️ Archived Students
    </div>
    <div class="modal-body">
      <div class="modal-actions">
        <div class="search-container">
          <input type="search" id="archiveSearch" placeholder="🔍 Search archived..." class="search-input">
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
              <th>Sex</th>
              <th>Birthdate</th>
              <th>Address</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Date Archived</th>
            </tr>
          </thead>
          <tbody id="archiveTableBody">
            <!-- Archived students will be loaded here via AJAX -->
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
        /* Tab Navigation */
        .modal-body {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

.info-modal-tabs {
  display: flex;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
}

.tab-button {
  flex: 1;
  padding: 12px 15px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}

.tab-button:hover {
  background-color: #e9ecef;
  color: #333;
}

.tab-button.active {
  color: #007bff;
  border-bottom: 3px solid #007bff;
  background-color: white;
}

/* Tab Content */
.tab-content {
  display: none;
  padding: 0;
  flex: 1;
  overflow-y: auto;
}

.tab-content.active {
  display: flex;
  flex-direction: column;
}

.modal-body {
  padding: 0;
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.info-container {
  margin: 0;
  padding: 25px;
  flex: 1;
  overflow-y: auto;
}

/* Violations and Complaints Styles */
.tab-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 25px;
  border-bottom: 1px solid #f0f0f0;
  flex-shrink: 0;
}

.tab-header h4 {
  margin: 0;
  font-size: 1.1rem;
  color: #333;
}

.violations-container, .complaints-container {
  padding: 20px 25px;
  flex: 1;
  overflow-y: auto;
}

.violations-list, .complaints-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.violation-item, .complaint-item {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 15px;
  border-left: 4px solid #dc3545;
}

.complaint-item {
  border-left-color: #007bff;
}

.violation-header, .complaint-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.violation-title, .complaint-title {
  font-weight: 600;
  color: #333;
  font-size: 1rem;
}

.violation-date, .complaint-date {
  font-size: 0.85rem;
  color: #666;
}

.violation-details, .complaint-details {
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 10px;
}

.violation-status, .complaint-status {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-resolved {
  background-color: #d4edda;
  color: #155724;
}

.status-escalated {
  background-color: #f8d7da;
  color: #721c24;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #666;
}

.empty-state p {
  margin: 0;
  font-style: italic;
}

/* Parent Information Styles */
.parents-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.parent-item {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 15px;
  border-left: 4px solid #28a745;
}

.parent-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.parent-name {
  font-weight: 600;
  color: #333;
  font-size: 1rem;
}

.parent-relationship {
  font-size: 0.85rem;
  color: #666;
  background-color: #e9ecef;
  padding: 4px 8px;
  border-radius: 12px;
}

.parent-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.parent-contact, .parent-email {
  display: flex;
  align-items: center;
  gap: 8px;
}

.parent-contact .contact-icon, .parent-email .contact-icon {
  font-size: 1rem;
}

.parent-contact .contact-value, .parent-email .contact-value {
  font-size: 0.9rem;
  color: #007bff;
  text-decoration: none;
}

.parent-contact .contact-value:hover, .parent-email .contact-value:hover {
  text-decoration: underline;
}
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

      /* Fix for modal header and close button */
.modal-title-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px 25px;
  position: relative;
}

.header-title-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.modal-title-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 600;
  flex: 1;
}

/* Close button styles - similar to your image */
.close-btn {
  background: rgba(255, 255, 255, 0.2) !important;
  border: 2px solid rgba(255, 255, 255, 0.5) !important;
  color: white !important;
  font-size: 18px !important;
  cursor: pointer !important;
  width: 35px !important;
  height: 35px !important;
  border-radius: 8px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: all 0.3s ease !important;
  font-weight: bold !important;
  margin: 0 !important;
  padding: 0 !important;
  flex-shrink: 0;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3) !important;
  border-color: rgba(255, 255, 255, 0.8) !important;
  transform: scale(1.1) !important;
}

.close-btn:active {
  transform: scale(0.95) !important;
}

/* Ensure modal content has proper positioning */
.modal-content {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
}

/* Fix modal body scrolling */
.modal-body {
  max-height: 60vh;
  overflow-y: auto;
  padding-right: 10px;
}

/* Tab Navigation */
.info-modal-tabs {
  display: flex;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
}

.tab-button {
  flex: 1;
  padding: 12px 15px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}

.tab-button:hover {
  background-color: #e9ecef;
  color: #333;
}

.tab-button.active {
  color: #007bff;
  border-bottom: 3px solid #007bff;
  background-color: white;
}

/* Tab Content */
.tab-content {
  display: none;
  padding: 0;
  flex: 1;
  overflow-y: auto;
}

.tab-content.active {
  display: flex;
  flex-direction: column;
}

/* Info container styling */
.info-container {
  margin: 0;
  padding: 25px;
  flex: 1;
  overflow-y: auto;
}

/* Info header styling */
.info-header {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f0f0f0;
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

.student-name {
  margin: 0 0 8px 0;
  font-size: 1.3rem;
  font-weight: 600;
  color: #333;
}

/* Status badges */
.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  display: inline-block;
}

.status-active {
  background-color: #e6f7ee;
  color: #0d6832;
  border: 1px solid #0d6832;
}

.status-inactive {
  background-color: #fef3e2;
  color: #92400f;
  border: 1px solid #92400f;
}

/* Section styling */
.info-section {
  margin-bottom: 25px;
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

/* Contact grid */
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

        /* Parent Information Styles */
        .parents-container {
          display: flex;
          flex-direction: column;
          gap: 10px;
        }

        .parent-item {
          display: flex;
          align-items: flex-start;
          padding: 12px;
          background: #f8f9fa;
          border-radius: 8px;
          border: 1px solid #e9ecef;
          transition: background-color 0.2s ease;
        }

        .parent-item:hover {
          background: #e9ecef;
        }

        .parent-avatar {
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

        .parent-avatar-text {
          color: white;
          font-weight: bold;
          font-size: 0.9rem;
        }

        .parent-info {
          display: flex;
          flex-direction: column;
          flex: 1;
        }

        .parent-name {
          font-weight: 600;
          color: #333;
          margin-bottom: 4px;
          font-size: 0.95rem;
        }

        .parent-details {
          display: flex;
          flex-wrap: wrap;
          gap: 12px;
          margin-top: 4px;
        }

        .parent-detail {
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

        .no-parents {
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

        /* Parent Count Badge */
        .parent-count {
          display: inline-block;
          background: #007bff;
          color: white;
          padding: 2px 8px;
          border-radius: 12px;
          font-size: 12px;
          font-weight: bold;
          margin-right: 8px;
        }

        /* Button Colors */
        .btn-danger {
          background-color: #dc3545;
          color: white;
          border: none;
          padding: 10px 20px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 14px;
          transition: background-color 0.2s;
          
        }

        .btn-danger:hover {
          background-color: #c82333;
        }

        .btn-success {
          background-color: #28a745;
          color: white;
          border: none;
          padding: 6px 12px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 12px;
          transition: background-color 0.2s;
              margin-right: 10px;
        }

        .btn-success:hover {
          background-color: #218838;
        }

        .btn-sm {
          padding: 4px 8px;
          font-size: 11px;
        }

        .btn-full {
          width: 100%;
          margin-top: 10px;
        }

        /* Manage Parents Modal Styles */
        .parents-section {
          margin-bottom: 25px;
        }

        .parents-list {
          max-height: 200px;
          overflow-y: auto;
          border: 1px solid #e0e0e0;
          border-radius: 8px;
          padding: 10px;
        }

        .parent-list-item {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 8px 12px;
          border-bottom: 1px solid #f0f0f0;
        }

        .parent-list-item:last-child {
          border-bottom: none;
        }

        .parent-list-info {
          flex: 1;
        }

        .parent-list-name {
          font-weight: 600;
          color: #333;
        }

        .parent-list-details {
          font-size: 12px;
          color: #666;
        }

        .remove-parent-btn {
          background: #dc3545;
          color: white;
          border: none;
          padding: 4px 8px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 11px;
        }

        .remove-parent-btn:hover {
          background: #c82333;
        }

        /* Search Results */
        .search-container {
          position: relative;
        }

        .search-results {
          position: absolute;
          top: 100%;
          left: 0;
          right: 0;
          background: white;
          border: 1px solid #ddd;
          border-radius: 4px;
          max-height: 150px;
          overflow-y: auto;
          z-index: 1000;
          display: none;
        }

        .search-result-item {
          padding: 8px 12px;
          cursor: pointer;
          border-bottom: 1px solid #f0f0f0;
        }

        .search-result-item:hover {
          background: #f5f5f5;
        }

        .search-result-item:last-child {
          border-bottom: none;
        }

        /* Table Scrollable Styles */
        .table-container {
          max-height: 600px;
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

        .right-controls {
          display: flex;
          gap: 10px;
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
        }

        .pagination-links {
          order: 1;
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
        /* Fix modal spacing */
.modern-content {
    position: relative;
    padding-bottom: 90px !important;
}

/* Scrollable modal content */
.modal-body {
    max-height: 65vh;
    overflow-y: auto;
    padding-right: 10px;
}

/* Footer container (centered) */
.modal-actions.modern-actions {
    position: absolute;
    left: 20px;
    right: 20px;
    bottom: 15px;
    display: flex;
    justify-content: center; /* CENTER THE BUTTON */
    background: transparent;
}

/* BLUE Close Button */
.btns-close {
    background: #007bff;      /* Blue */
    color: #fff;
    padding: 10px 22px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s ease;
}

.btns-close:hover {
    background: #0056b3;     /* Darker blue on hover */
    transform: translateY(-2px);
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
              this.notificationActions.innerHTML = '';
              this.notificationModal.style.display = 'flex';

              this.autoCloseTimeout = setTimeout(() => {
                this.hideNotification();
              }, 1000);
            } else {
              this.notificationActions.innerHTML = '<button class="btn-confirm" id="notificationConfirm">OK</button>';

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
// 👤 Student Info Modal with Tabs
// ==========================
document.addEventListener('DOMContentLoaded', function () {
  const infoModal = document.getElementById('infoModal');
  const closeInfoBtn = document.getElementById('closeInfoBtn');
  const tabButtons = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');
  let currentStudentId = null;
  
  // Tab switching functionality
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      const tabId = this.getAttribute('data-tab');
      
      // Update active tab button
      tabButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Show corresponding tab content
      tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === `${tabId}-tab`) {
          content.classList.add('active');
          
          // Load data for the selected tab
          if (tabId === 'violation' && currentStudentId) {
            loadViolations(currentStudentId);
          } else if (tabId === 'complaint' && currentStudentId) {
            loadComplaints(currentStudentId);
          }
        }
      });
    });
  });
  
  // Clickable rows to show info modal - USING DATA ATTRIBUTES
  document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', function(e) {
      // Don't trigger if clicking on checkbox or edit button
      if (e.target.type === 'checkbox' || e.target.classList.contains('edit-btn')) {
        return;
      }
      
      // Get student data from attributes
      const studentData = {
        student_id: this.getAttribute('data-student-id'),
        student_fname: this.getAttribute('data-fname'),
        student_lname: this.getAttribute('data-lname'),
        student_sex: this.getAttribute('data-sex'),
        student_birthdate: this.getAttribute('data-birthdate'),
        student_address: this.getAttribute('data-address'),
        student_contactinfo: this.getAttribute('data-contact'),
        status: this.getAttribute('data-status'),
        grade: this.getAttribute('data-grade'),
        section: this.getAttribute('data-section'),
        adviser: this.getAttribute('data-adviser')
      };
      
      // Store current student ID for tab data loading
      currentStudentId = studentData.student_id;
      
      // Fill basic info from data attributes
      fillStudentInfoFromAttributes(studentData);
      
      // Show modal
      document.getElementById('infoModal').style.display = 'flex';
      
      // Fetch additional details (parents, violations, complaints) via API
      fetchStudentDetails(currentStudentId);
    });
  });
  
  // Close info modal
  closeInfoBtn.addEventListener('click', () => {
    infoModal.style.display = 'none';
  });
  
  // Close info modal when clicking outside
  document.addEventListener('click', function(event) {
    if (event.target === infoModal) {
      infoModal.style.display = 'none';
    }
  });
});

// Function to fill student information from data attributes (immediate display)
function fillStudentInfoFromAttributes(studentData) {
  // Format birthdate
  let formattedBirthdate = 'N/A';
  if (studentData.student_birthdate && studentData.student_birthdate !== 'null') {
    formattedBirthdate = new Date(studentData.student_birthdate).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  
  // Fill basic student info
  document.getElementById('info_student_id').textContent = studentData.student_id;
  document.getElementById('info_fullname').textContent = `${studentData.student_fname} ${studentData.student_lname}`;
  document.getElementById('info_sex').textContent = studentData.student_sex;
  document.getElementById('info_birthdate').textContent = formattedBirthdate;
  document.getElementById('info_address').textContent = studentData.student_address || 'N/A';
  
  // Set grade, section, and adviser from data attributes
  document.getElementById('info_grade').textContent = studentData.grade || 'N/A';
  document.getElementById('info_section').textContent = studentData.section || 'N/A';
  document.getElementById('info_adviser').textContent = studentData.adviser || 'N/A';
  
  // Set avatar initials
  const avatarInitials = document.getElementById('avatarInitials');
  avatarInitials.textContent = (studentData.student_fname.charAt(0) + studentData.student_lname.charAt(0)).toUpperCase();
  
  // Set status badge
  const statusBadge = document.getElementById('info_status');
  statusBadge.textContent = studentData.status.charAt(0).toUpperCase() + studentData.status.slice(1);
  statusBadge.className = `status-badge ${studentData.status === 'active' ? 'status-active' : 'status-inactive'}`;
  
  // Contact with clickable link
  const contactLink = document.getElementById('info_contact');
  if (studentData.student_contactinfo && studentData.student_contactinfo !== 'null') {
    contactLink.textContent = studentData.student_contactinfo;
    contactLink.href = `tel:${studentData.student_contactinfo}`;
    contactLink.style.display = 'inline';
  } else {
    contactLink.textContent = 'N/A';
    contactLink.href = '#';
    contactLink.style.display = 'none';
  }
}

// Function to fetch student details including parents, violations, and complaints
async function fetchStudentDetails(studentId) {
  try {
    const response = await fetch(`/adviser/students/${studentId}/details`);
    const result = await response.json();

    if (result.success) {
      const student = result.student;
      const parent = result.parent;
      const adviser = result.adviser;
      const violations = result.violations || [];
      const complaints = result.complaints || [];
      
      // Update parent information
      fillParentInfo(parent);
      
      // Pre-load violations and complaints data
      fillViolationsData(violations);
      fillComplaintsData(complaints);
      
      // Update grade, section, and adviser from API response (more accurate)
      if (adviser) {
        document.getElementById('info_grade').textContent = adviser.adviser_gradelevel || 'N/A';
        document.getElementById('info_section').textContent = adviser.adviser_section || 'N/A';
        document.getElementById('info_adviser').textContent = `${adviser.adviser_fname} ${adviser.adviser_lname}`;
      }
    } else {
      console.error('Error loading student details:', result.message);
    }
  } catch (error) {
    console.error('Error fetching student details:', error);
  }
}

// Function to fill parent information
function fillParentInfo(parent) {
  const parentsContainer = document.getElementById('parentsContainer');
  
  if (parent) {
    let parentHTML = '';
    
    parentHTML += `
      <div class="parent-item">
        <div class="parent-header">
          <div class="parent-name">${parent.parent_fname} ${parent.parent_lname}</div>
          <div class="parent-relationship">${parent.parent_relationship || 'Parent/Guardian'}</div>
        </div>
        <div class="parent-details">
          ${parent.parent_contactinfo ? `
            <div class="parent-contact">
              <span class="contact-icon">📱</span>
              <a href="tel:${parent.parent_contactinfo}" class="contact-value">${parent.parent_contactinfo}</a>
            </div>
          ` : ''}
          ${parent.parent_email ? `
            <div class="parent-email">
              <span class="contact-icon">✉️</span>
              <a href="mailto:${parent.parent_email}" class="contact-value">${parent.parent_email}</a>
            </div>
          ` : ''}
        </div>
      </div>
    `;
    
    parentsContainer.innerHTML = parentHTML;
  } else {
    parentsContainer.innerHTML = '<div class="empty-state"><p>No parent information available.</p></div>';
  }
}

// Function to fill violations data from API response
function fillViolationsData(violations) {
  const violationsList = document.getElementById('violationsList');
  
  if (violations.length > 0) {
    let violationsHTML = '';
    
    violations.forEach(violation => {
      const violationDate = new Date(violation.date).toLocaleDateString();
      const statusClass = `status-${violation.status}`;
      const statusText = violation.status.charAt(0).toUpperCase() + violation.status.slice(1);
      
      violationsHTML += `
        <div class="violation-item">
          <div class="violation-header">
            <div class="violation-title">${violation.title}</div>
            <div class="violation-date">${violationDate}</div>
          </div>
          <div class="violation-details">
            <strong>Offense:</strong> ${violation.offense_type}<br>
            <strong>Sanction:</strong> ${violation.sanction}<br>
            <strong>Time:</strong> ${violation.time}
          </div>
          <div class="violation-status ${statusClass}">
            ${statusText}
          </div>
        </div>
      `;
    });
    
    violationsList.innerHTML = violationsHTML;
  } else {
    violationsList.innerHTML = '<div class="empty-state"><p>No violations recorded for this student.</p></div>';
  }
}

// Function to fill complaints data from API response
function fillComplaintsData(complaints) {
  const complaintsList = document.getElementById('complaintsList');
  
  if (complaints.length > 0) {
    let complaintsHTML = '';
    
    complaints.forEach(complaint => {
      const complaintDate = new Date(complaint.date).toLocaleDateString();
      const statusClass = `status-${complaint.status}`;
      const statusText = complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1);
      
      complaintsHTML += `
        <div class="complaint-item">
          <div class="complaint-header">
            <div class="complaint-title">${complaint.title}</div>
            <div class="complaint-date">${complaintDate}</div>
          </div>
          <div class="complaint-details">
            <strong>Complainant:</strong> ${complaint.complainant}<br>
            <strong>Details:</strong> ${complaint.details}
          </div>
          <div class="complaint-status ${statusClass}">
            ${statusText}
          </div>
        </div>
      `;
    });
    
    complaintsList.innerHTML = complaintsHTML;
  } else {
    complaintsList.innerHTML = '<div class="empty-state"><p>No complaints recorded for this student.</p></div>';
  }
}

// Load violations for a student (for tab switching)
function loadViolations(studentId) {
  // Data is already loaded from the initial API call, but you can refresh if needed
  console.log('Loading violations for student:', studentId);
}

// Load complaints for a student (for tab switching)
function loadComplaints(studentId) {
  // Data is already loaded from the initial API call, but you can refresh if needed
  console.log('Loading complaints for student:', studentId);
}

        // ==========================
        // Manage Parents Functionality
        // ==========================
        document.addEventListener('DOMContentLoaded', function () {
          const manageParentsModal = document.getElementById('manageParentsModal');
          const closeManageParentsBtn = document.getElementById('closeManageParentsBtn');
          const parentSearch = document.getElementById('parentSearch');
          const parentSearchResults = document.getElementById('parentSearchResults');
          const addParentForm = document.getElementById('addParentForm');
          const currentParentsList = document.getElementById('currentParentsList');
          const modalStudentName = document.getElementById('modalStudentName');
          const addParentStudentId = document.getElementById('addParentStudentId');

          let currentStudentId = null;
          let searchTimeout = null;

          // Open manage parents modal
          document.addEventListener('click', function (e) {
            if (e.target.classList.contains('manage-parents-btn')) {
              currentStudentId = e.target.getAttribute('data-student-id');
              const studentName = e.target.getAttribute('data-student-name');

              modalStudentName.textContent = studentName;
              addParentStudentId.value = currentStudentId;

              loadCurrentParents(currentStudentId);
              manageParentsModal.style.display = 'flex';
            }
          });

          // Parent search functionality
          parentSearch.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();

            if (searchTerm.length < 2) {
              parentSearchResults.style.display = 'none';
              return;
            }

            searchTimeout = setTimeout(() => {
              searchParents(searchTerm);
            }, 300);
          });

          // Close manage parents modal
          closeManageParentsBtn.addEventListener('click', function () {
            manageParentsModal.style.display = 'none';
            parentSearch.value = '';
            parentSearchResults.style.display = 'none';
          });

          // Close modal when clicking outside
          manageParentsModal.addEventListener('click', function (e) {
            if (e.target === manageParentsModal) {
              manageParentsModal.style.display = 'none';
              parentSearch.value = '';
              parentSearchResults.style.display = 'none';
            }
          });

          // Add parent form submission
          addParentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const parentId = formData.get('parent_id');
            const relationshipType = formData.get('relationship_type');

            if (!parentId) {
              notifications.showNotification('Please select a parent from search results', 'warning');
              return;
            }

            addParentToStudent(currentStudentId, parentId, relationshipType);
          });

          // Function to search parents
          function searchParents(searchTerm) {
            fetch(`/adviser/parents/search?search=${encodeURIComponent(searchTerm)}`)
              .then(response => response.json())
              .then(data => {
                if (data.success && data.parents.length > 0) {
                  displayParentSearchResults(data.parents);
                } else {
                  parentSearchResults.innerHTML = '<div class="search-result-item">No parents found</div>';
                  parentSearchResults.style.display = 'block';
                }
              })
              .catch(error => {
                console.error('Error searching parents:', error);
                parentSearchResults.style.display = 'none';
              });
          }

          // Function to display search results
          function displayParentSearchResults(parents) {
            parentSearchResults.innerHTML = '';

            parents.forEach(parent => {
              const resultItem = document.createElement('div');
              resultItem.className = 'search-result-item';
              resultItem.innerHTML = `
                      <strong>${parent.parent_fname} ${parent.parent_lname}</strong>
                      <br>
                      <small>ID: ${parent.parent_id} | Contact: ${parent.parent_contactinfo} | Email: ${parent.parent_email || 'N/A'}</small>
                  `;
              resultItem.addEventListener('click', function () {
                parentSearch.value = `${parent.parent_fname} ${parent.parent_lname}`;
                addParentForm.parent_id = parent.parent_id;
                parentSearchResults.style.display = 'none';
              });
              parentSearchResults.appendChild(resultItem);
            });

            parentSearchResults.style.display = 'block';
          }

          // Function to load current parents
          function loadCurrentParents(studentId) {
            fetch(`/adviser/students/${studentId}/parents`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  displayCurrentParents(data.parents);
                  // Update parent count in the table
                  document.getElementById(`parentCount-${studentId}`).textContent = data.parents.length;
                } else {
                  currentParentsList.innerHTML = '<div class="no-parents">Error loading parents</div>';
                }
              })
              .catch(error => {
                console.error('Error loading parents:', error);
                currentParentsList.innerHTML = '<div class="no-parents">Error loading parents</div>';
              });
          }

          // Function to display current parents
          function displayCurrentParents(parents) {
            if (parents.length === 0) {
              currentParentsList.innerHTML = '<div class="no-parents">No parents associated with this student</div>';
              return;
            }

            currentParentsList.innerHTML = '';
            parents.forEach(parent => {
              const parentItem = document.createElement('div');
              parentItem.className = 'parent-list-item';
              parentItem.innerHTML = `
                      <div class="parent-list-info">
                          <div class="parent-list-name">${parent.parent_fname} ${parent.parent_lname}</div>
                          <div class="parent-list-details">
                              ID: ${parent.parent_id} | 
                              Relationship: ${parent.pivot?.relationship_type || 'N/A'} |
                              Contact: ${parent.parent_contactinfo || 'N/A'}
                          </div>
                      </div>
                      <button class="remove-parent-btn" data-parent-id="${parent.parent_id}">
                          Remove
                      </button>
                  `;
              currentParentsList.appendChild(parentItem);
            });

            // Add event listeners to remove buttons
            document.querySelectorAll('.remove-parent-btn').forEach(btn => {
              btn.addEventListener('click', function () {
                const parentId = this.getAttribute('data-parent-id');
                removeParentFromStudent(currentStudentId, parentId);
              });
            });
          }

          // Function to add parent to student
          function addParentToStudent(studentId, parentId, relationshipType) {
            fetch('/adviser/students/add-parent', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({
                student_id: studentId,
                parent_id: parentId,
                relationship_type: relationshipType
              })
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  notifications.showNotification(data.message, 'success');
                  loadCurrentParents(studentId);
                  addParentForm.reset();
                  parentSearch.value = '';
                } else {
                  notifications.showNotification(data.message, 'error');
                }
              })
              .catch(error => {
                console.error('Error adding parent:', error);
                notifications.showNotification('Error adding parent', 'error');
              });
          }

          // Function to remove parent from student
          function removeParentFromStudent(studentId, parentId) {
            notifications.showConfirmation(
              'Are you sure you want to remove this parent from the student?',
              function () {
                fetch('/adviser/students/remove-parent', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  },
                  body: JSON.stringify({
                    student_id: studentId,
                    parent_id: parentId
                  })
                })
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      notifications.showNotification(data.message, 'success');
                      loadCurrentParents(studentId);
                    } else {
                      notifications.showNotification(data.message, 'error');
                    }
                  })
                  .catch(error => {
                    console.error('Error removing parent:', error);
                    notifications.showNotification('Error removing parent', 'error');
                  });
              }
            );
          }
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
            notifications.showNotification('Please select at least one student.', 'warning');
            return;
          }

          notifications.showConfirmation(
            `Are you sure you want to move ${selected.length} student(s) to archive?`,
            () => {
              fetch('{{ route("adviser.students.archive") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selected })
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
                  notifications.showNotification('An error occurred while archiving students.', 'error');
                });
            }
          );
        });

        // ==========================
        // Mark as Graduated
        // ==========================
        document.getElementById('markGraduatedBtn').addEventListener('click', function () {
          const selected = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
            .map(cb => cb.value);

          if (!selected.length) {
            notifications.showNotification('Please select at least one student.', 'warning');
            return;
          }

          notifications.showConfirmation(
            `Are you sure you want to mark ${selected.length} student(s) as graduated?`,
            () => {
              fetch('{{ route("adviser.students.markCleared") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selected })
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
                  notifications.showNotification('An error occurred while marking students as graduated.', 'error');
                });
            }
          );
        });

        // ==========================
// Load Archived Students
// ==========================
function loadArchivedStudents() {
  fetch('{{ route("adviser.students.getArchived") }}')
    .then(response => response.json())
    .then(students => {
      const archiveTableBody = document.getElementById('archiveTableBody');
      archiveTableBody.innerHTML = '';

      if (students.length === 0) {
        archiveTableBody.innerHTML = `
          <tr>
            <td colspan="10" style="text-align:center; padding:15px;">No archived students found</td>
          </tr>
        `;
        return;
      }

      students.forEach(student => {
        // Format birthdate
        let formattedBirthdate = 'N/A';
        if (student.student_birthdate && student.student_birthdate !== 'null') {
          formattedBirthdate = new Date(student.student_birthdate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        }

        // Format archive date
        const archiveDate = new Date(student.updated_at).toLocaleDateString();

        const row = document.createElement('tr');
        row.innerHTML = `
          <td><input type="checkbox" class="archiveCheckbox" value="${student.student_id}"></td>
          <td>${student.student_id}</td>
          <td>${student.student_fname}</td>
          <td>${student.student_lname}</td>
          <td>${student.student_sex}</td>
          <td>${formattedBirthdate}</td>
          <td>${student.student_address || 'N/A'}</td>
          <td>${student.student_contactinfo || 'N/A'}</td>
          <td><span class="status-badge status-inactive">${student.status}</span></td>
          <td>${archiveDate}</td>
        `;
        archiveTableBody.appendChild(row);
      });

      // Update select all functionality for archived items
      updateArchiveSelectAll();
    })
    .catch(error => {
      console.error('Error loading archived students:', error);
      notifications.showNotification('Error loading archived students.', 'error');
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
        // Restore Archived Students
        // ==========================
        document.getElementById('restoreArchiveBtn').addEventListener('click', function () {
          const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
            .map(cb => cb.value);

          if (!selected.length) {
            notifications.showNotification('Please select at least one student to restore.', 'warning');
            return;
          }

          notifications.showConfirmation(
            `Are you sure you want to restore ${selected.length} student(s)?`,
            () => {
              fetch('{{ route("adviser.students.restore") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selected })
              })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    notifications.showNotification(data.message, 'success');
                    loadArchivedStudents();
                    setTimeout(() => location.reload(), 1500);
                  } else {
                    notifications.showNotification('Error: ' + data.message, 'error');
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  notifications.showNotification('An error occurred while restoring students.', 'error');
                });
            }
          );
        });

        // ==========================
        // Delete Archived Students Permanently
        // ==========================
        document.getElementById('deleteArchiveBtn').addEventListener('click', function () {
          const selected = Array.from(document.querySelectorAll('.archiveCheckbox:checked'))
            .map(cb => cb.value);

          if (!selected.length) {
            notifications.showNotification('Please select at least one student to delete permanently.', 'warning');
            return;
          }

          notifications.showConfirmation(
            `WARNING: This will permanently delete ${selected.length} student(s). This action cannot be undone!`,
            () => {
              fetch('{{ route("adviser.students.destroyMultiple") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_ids: selected })
              })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    notifications.showNotification(data.message, 'success');
                    loadArchivedStudents();
                  } else {
                    notifications.showNotification('Error: ' + data.message, 'error');
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  notifications.showNotification('An error occurred while deleting students.', 'error');
                });
            }
          );
        });

        // ==========================
        // Open Archive Modal
        // ==========================
        document.getElementById('archiveBtn').addEventListener('click', function () {
          loadArchivedStudents();
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
          const editForm = document.getElementById('editStudentForm');
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
              const studentId = row.getAttribute('data-student-id');

              // Get current values from row data attributes
              const fname = row.getAttribute('data-fname');
              const lname = row.getAttribute('data-lname');
              const sex = row.getAttribute('data-sex');
              const birthdate = row.getAttribute('data-birthdate');
              const address = row.getAttribute('data-address');
              const contact = row.getAttribute('data-contact');
              const status = row.getAttribute('data-status');

              // Convert birthdate to YYYY-MM-DD format
              let birthdateInput = '';
              if (birthdate && birthdate !== 'null') {
                birthdateInput = birthdate.split('T')[0];
              }

              // Clear previous errors
              clearEditErrors();

              // Fill form with current data
              document.getElementById('edit_student_id').value = studentId;
              document.getElementById('edit_student_fname').value = fname;
              document.getElementById('edit_student_lname').value = lname;
              document.getElementById('edit_student_sex').value = sex;
              document.getElementById('edit_student_birthdate').value = birthdateInput;
              document.getElementById('edit_student_address').value = address;
              document.getElementById('edit_student_contactinfo').value = contact;
              document.getElementById('edit_student_status').value = status;

              // Set form action with correct route
              editForm.action = `/adviser/students/update/${studentId}`;

              // Show modal
              editModal.style.display = 'flex';
            });
          });

          // Handle form submission with AJAX
          editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const studentId = document.getElementById('edit_student_id').value;

            // Show loading state
            saveEditBtn.innerHTML = '💾 Saving...';
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
                  notifications.showNotification(data.message || 'Update failed', 'error');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                notifications.showNotification('An error occurred while updating student.', 'error');
              })
              .finally(() => {
                saveEditBtn.innerHTML = '💾 Save Changes';
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