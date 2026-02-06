@extends('adviser.layout')
@section('content')
<div class="main-container">

  <!-- Toolbar -->
  <div class="toolbar">
    <h2>Offense and Sanctions</h2>
    <div class="actions">
      <input type="search" placeholder="🔍 Search by offense type or description..." id="searchInput">
      <button class="btn-print" id="printBtn">🖨️ Print to PDF</button>
      <button class="btn-export" id="exportBtn">📤 Export to Excel</button>
    </div>
  </div>

  <!-- Offense & Sanction Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Offense Type</th>
          <th>Offense Description</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse ($offenses as $offense)
          <tr data-details="{{ $offense->offense_type }}|{{ $offense->offense_description }}">
            <td>{{ $loop->iteration }}</td>
            <td><span title="{{ $offense->offense_type }}">{{ $offense->offense_type }}</span></td>
            <td>{{ $offense->offense_description }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center; padding:15px;">⚠️ No offenses found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      {{-- {{ $offenses->links() }} --}}
    </div>
  </div>

  <!-- 📝 Details Modal -->
  <div class="modal" id="detailsModal">
    <div class="modal-content">
      <div class="modal-header">📄 Offense & Sanction Details</div>
      <div class="modal-body" id="detailsBody">
        <!-- Content will be filled dynamically via JS -->
      </div>
      <div class="modal-footer">
        <button class="btn-close">❌ Close</button>
      </div>
    </div>
  </div>

  <!-- 🗃️ Archive Modal -->
  <div class="modal" id="archiveModal">
    <div class="modal-content">
      <div class="modal-header">🗃️ Archived Offenses</div>
      <div class="modal-body">
        <div class="modal-actions">
          <label class="select-all-label">
            <input type="checkbox" id="selectAllArchived" class="select-all-checkbox">
            <span>Select All</span>
          </label>

          <div class="search-container">
            <input type="search" placeholder="🔍 Search archived..." id="archiveSearch" class="search-input">
          </div>
        </div>

        <div class="archive-table-container">
          <table class="archive-table">
            <thead>
              <tr>
                <th>✔</th>
                <th>ID</th>
                <th>Offense Type</th>
                <th>Description</th>
                <th>Date Archived</th>
              </tr>
            </thead>
            <tbody id="archiveTableBody">
              <tr>
                <td><input type="checkbox" class="archivedCheckbox"></td>
                <td>O001</td>
                <td>Tardiness</td>
                <td>Late to class</td>
                <td>Warning, Parent Notification</td>
                <td>2025-09-22</td>
              </tr>
              <tr>
                <td><input type="checkbox" class="archivedCheckbox"></td>
                <td>O002</td>
                <td>Cutting Classes</td>
                <td>Unauthorized absence</td>
                <td>Conference with Adviser</td>
                <td>2025-09-23</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-note">⚠️ Note: Deleting records will permanently remove them.</div>

        <div class="modal-footer">
          <button class="btn-secondary" id="restoreArchivedBtn">🔄 Restore</button>
          <button class="btn-danger" id="deleteArchivedBtn">🗑️ Delete</button>
          <button class="btn-close" id="closeArchive">❌ Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 📝 Create Anecdotal Modal (if needed) -->
  <div class="modal" id="anecModal">
    <div class="modal-content">
      <div class="modal-header">📝 Create Anecdotal Record</div>
      <div class="modal-body">
        <p>Anecdotal record form would go here...</p>
      </div>
      <div class="modal-footer">
        <button class="btn-primary">Save</button>
        <button class="btn-close">❌ Close</button>
      </div>
    </div>
  </div>

  <!-- 🔔 Success Notification Modal -->
  <div class="modal" id="successModal">
    <div class="modal-content success-modal-content">
      <div class="modal-header success-modal-header">
        <span class="success-icon">✅</span>
        <span class="success-title">Success</span>
      </div>
      <div class="modal-body success-modal-body" id="successMessage">
        PDF opened in new tab successfully!
      </div>
    </div>
  </div>

  <!-- 🔔 Error Notification Modal -->
  <div class="modal" id="errorModal">
    <div class="modal-content error-modal-content">
      <div class="modal-header error-modal-header">
        <span class="error-icon">❌</span>
        <span class="error-title">Error</span>
      </div>
      <div class="modal-body error-modal-body" id="errorMessage">
        PDF generation failed. Please try again.
      </div>
    </div>
  </div>
</div>

<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// Modal notification functions
function showSuccessModal(message = 'PDF opened in new tab successfully!') {
  const modal = document.getElementById('successModal');
  document.getElementById('successMessage').textContent = message;
  modal.style.display = 'flex';
  
  // Auto close after 1 second
  setTimeout(() => {
    modal.style.display = 'none';
  }, 1000);
}

function showErrorModal(message = 'PDF generation failed. Please try again.') {
  const modal = document.getElementById('errorModal');
  document.getElementById('errorMessage').textContent = message;
  modal.style.display = 'flex';
  
  // Auto close after 1 second
  setTimeout(() => {
    modal.style.display = 'none';
  }, 1000);
}

// Close modal functions
function setupModalClose() {
  // Close modals when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
      document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
      });
    }
  });
}

// Initialize modal close functionality
document.addEventListener('DOMContentLoaded', setupModalClose);

// 🔍 Search filter for main table
document.getElementById('searchInput')?.addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#tableBody tr');
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});

// ✅ Select All Main Table
document.getElementById('selectAll')?.addEventListener('change', function() {
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// 🔹 Row click for Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    // Ignore clicks on checkboxes or Edit button
    if (e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

    // Split the dataset details: offense_type | offense_description | sanctions
    const [offenseType, offenseDescription, sanctions] = row.dataset.details.split('|');

    // Convert sanctions string into a bullet list
    const sanctionList = sanctions.split(',').map(s => `<li>${s.trim()}</li>`).join('');

    document.getElementById('detailsBody').innerHTML = `
      <p><strong>Offense Type:</strong> ${offenseType}</p>
      <p><strong>Description:</strong> ${offenseDescription}</p>
      <ul>${sanctionList}</ul>
    `;

    document.getElementById('detailsModal').style.display = 'flex';
  });
});

// 🔹 Close Modals
document.querySelectorAll('.btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = btn.closest('.modal');
    if (modal) modal.style.display = 'none';
  });
});

// 🔹 Open Create Anecdotal Modal
document.getElementById('createAnecBtn')?.addEventListener('click', () => {
  document.getElementById('anecModal').style.display = 'flex';
});

// 🔹 Open Archive Modal
document.getElementById('archiveBtn')?.addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// 🔹 Close Archive Modal
document.getElementById('closeArchive')?.addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'none';
});

// 🔹 Dropdown toggle
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    btn.parentElement.classList.toggle('show');
  });
});

window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// 🔹 Archive Select All
const selectAllArchived = document.getElementById('selectAllArchived');
const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');
selectAllArchived?.addEventListener('change', () => {
  archivedCheckboxes.forEach(cb => cb.checked = selectAllArchived.checked);
});

archivedCheckboxes.forEach(cb => cb.addEventListener('change', () => {
  selectAllArchived.checked = Array.from(archivedCheckboxes).every(c => c.checked);
}));

// 🔹 Archive Search
document.getElementById('archiveSearch')?.addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#archiveTableBody tr').forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
  });
});

// ================= BEAUTIFUL PRINT & EXPORT =================

// 🖨️ Print Table as PDF (Open in New Tab)
document.getElementById('printBtn')?.addEventListener('click', () => {
  const table = document.querySelector('.table-container table');
  if (!table) return;

  const currentDate = new Date().toLocaleDateString('en-PH', {
    year: 'numeric', month: 'long', day: 'numeric'
  });

  const currentTime = new Date().toLocaleTimeString('en-PH', {
    hour: '2-digit', minute: '2-digit'
  });

  // Create a temporary element for PDF generation
  const element = document.createElement('div');
  element.innerHTML = `
    <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #2d3748; background: #ffffff; padding: 25px;">
      <!-- Date at the top LEFT -->
      <div style="text-align: left; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
        <div style="font-size: 14px; color: #000000; font-weight: 500;">
          📅 Generated on: <strong>${currentDate}</strong> at <strong>${currentTime}</strong>
        </div>
      </div>

      <!-- Professional Header with Logo -->
      <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px;">
        <div style="flex: 1;">
          <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
          <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Student Violation Tracking System</h2>
          <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Official Offense and Sanctions Report</p>
        </div>
        <div style="text-align: right;">
          <img src="/images/logo.png" alt="School Logo" style="width: 80px; height: 80px; object-fit: contain;" onerror="this.style.display='none'"/>
        </div>
      </div>

      <!-- Report Summary -->
      <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">Offense and Sanctions Report</h3>
            <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
              Total Records: <strong style="color: #000000;">${document.querySelectorAll('#tableBody tr').length}</strong>
            </p>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 12px; color: #000000;">Document ID</div>
            <div style="font-size: 14px; font-weight: 600; color: #000000;">OSR-${Date.now().toString().slice(-6)}</div>
          </div>
        </div>
      </div>

      <!-- Enhanced Table -->
      <div style="overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        ${table.outerHTML.replace('<table', '<table style="width: 100%; border-collapse: collapse; font-size: 12px;"')}
      </div>

      <!-- Footer Section -->
      <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
        <div style="display: flex; justify-content: center; align-items: flex-start;">
          <div style="text-align: center;">
            <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
            <div style="border-bottom: 1px solid #cbd5e0; width: 200px; padding: 25px 0 5px 0; margin: 0 auto;"></div>
            <div style="font-size: 12px; color: #000000; margin-top: 5px;">Class Adviser</div>
          </div>
        </div>
        
        <!-- Confidential Notice -->
        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
          <div style="font-size: 11px; color: #c53030; font-weight: 600;">
            🔒 CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
          </div>
          <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
            This document contains sensitive student information. Unauthorized distribution is prohibited.
          </div>
        </div>
      </div>
    </div>
  `;

  // Enhanced table styling for PDF
  const tables = element.getElementsByTagName('table');
  for (let table of tables) {
    table.style.width = '100%';
    table.style.borderCollapse = 'collapse';
    table.style.fontSize = '12px';
    
    // Style table headers
    const headers = table.getElementsByTagName('th');
    for (let header of headers) {
      header.style.backgroundColor = '#1e3a8a';
      header.style.color = 'white';
      header.style.padding = '12px 10px';
      header.style.textAlign = 'left';
      header.style.fontWeight = '600';
      header.style.border = '1px solid #2d3748';
      header.style.fontSize = '11px';
      header.style.textTransform = 'uppercase';
      header.style.letterSpacing = '0.5px';
    }
    
    // Style table cells
    const cells = table.getElementsByTagName('td');
    for (let cell of cells) {
      cell.style.padding = '10px 8px';
      cell.style.border = '1px solid #e2e8f0';
      cell.style.fontSize = '11px';
      cell.style.color = '#000000';
    }
    
    // Style table rows
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
      if (i % 2 === 0) {
        rows[i].style.backgroundColor = '#ffffff';
      } else {
        rows[i].style.backgroundColor = '#f7fafc';
      }
    }
  }

  // PDF options with custom footer
  const options = {
    margin: [15, 15, 30, 15], // Increased bottom margin for footer
    filename: `Offense_Sanctions_Report_${new Date().toISOString().slice(0,10)}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { 
      scale: 2,
      useCORS: true,
      logging: false
    },
    jsPDF: { 
      unit: 'mm', 
      format: 'a4', 
      orientation: 'portrait',
      compress: true
    },
    // Add page numbers and system name to footer
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
  };

  // Generate PDF and open in new tab for preview
  html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
    const totalPages = pdf.internal.getNumberOfPages();
    
    for (let i = 1; i <= totalPages; i++) {
      pdf.setPage(i);
      
      // Add footer with system name on left and page number on right
      pdf.setFontSize(10);
      pdf.setTextColor(100, 100, 100);
      
      // System name on left footer
      pdf.text('Tagoloan Senior High School - Student Violation Tracking System', 
               pdf.internal.pageSize.getWidth() / 2 - 70, 
               pdf.internal.pageSize.getHeight() - 10);
      
      // Page number on right footer
      pdf.text(`Page ${i} of ${totalPages}`, 
               pdf.internal.pageSize.getWidth() - 30, 
               pdf.internal.pageSize.getHeight() - 10);
    }
    
    // Open PDF in new tab instead of downloading
    const pdfBlob = pdf.output('blob');
    const pdfUrl = URL.createObjectURL(pdfBlob);
    window.open(pdfUrl, '_blank');
    
  }).then(() => {
    // Show success modal notification
    showSuccessModal('PDF opened in new tab successfully!');
  }).catch(error => {
    console.error('PDF generation error:', error);
    showErrorModal('PDF generation failed. Please try again.');
  });
});

// 📤 Export Table to Excel
document.getElementById('exportBtn')?.addEventListener('click', () => {
  const table = document.querySelector('.table-container table');
  if (!table) return;

  const currentDate = new Date().toLocaleDateString('en-PH', {
    year: 'numeric', month: 'long', day: 'numeric'
  });

  const header = `
    <table style="width:100%; border-collapse:collapse; text-align:center; margin-bottom:20px;">
      <tr>
        <td colspan="4">
          <h2 style="margin:0; color:#000000;">Tagoloan Senior High School</h2>
          <h4 style="margin:0; color:#000000;">Student Violation Tracking System</h4>
        </td>
      </tr>
    </table>
  `;

  const footer = `
    <table style="margin-top:40px;">
      <tr>
        <td style="border-top:1px solid #444; width:200px; padding-top:10px;">Authorized Signature</td>
      </tr>
      <tr>
        <td style="padding-top:20px; color:#000000;">📅 Date Generated: ${currentDate}</td>
      </tr>
    </table>
  `;

  const tableHTML = header + table.outerHTML + footer;
  const filename = `Offense_and_Sanctions_${new Date().toISOString().slice(0,10)}.xls`;

  const downloadLink = document.createElement('a');
  document.body.appendChild(downloadLink);
  downloadLink.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tableHTML);
  downloadLink.download = filename;
  downloadLink.click();
  document.body.removeChild(downloadLink);

  // Show success modal
  showSuccessModal('Excel file downloaded successfully!');
});

</script>

<style>
/* Base Modal Styles */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  animation: modalAppear 0.3s ease-out;
  max-width: 90%;
  width: auto;
}

@keyframes modalAppear {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Success Modal Styles */
.success-modal-content {
  max-width: 400px;
  text-align: center;
}

.success-modal-header {
  background: #10b981;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 20px;
  border-radius: 8px 8px 0 0;
}

.success-icon {
  font-size: 24px;
}

.success-title {
  font-size: 20px;
  font-weight: 600;
}

.success-modal-body {
  padding: 30px 20px;
  font-size: 16px;
  color: #374151;
  background: white;
  border-radius: 0 0 8px 8px;
}

/* Error Modal Styles */
.error-modal-content {
  max-width: 400px;
  text-align: center;
}

.error-modal-header {
  background: #ef4444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 20px;
  border-radius: 8px 8px 0 0;
}

.error-icon {
  font-size: 24px;
}

.error-title {
  font-size: 20px;
  font-weight: 600;
}

.error-modal-body {
  padding: 30px 20px;
  font-size: 16px;
  color: #374151;
  background: white;
  border-radius: 0 0 8px 8px;
}

/* Button Styles - Fixed and Enhanced */
.btn-print, 
.btn-export, 
.btn-primary, 
.btn-secondary, 
.btn-danger, 
.btn-close {
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  min-height: 44px;
}

.btn-print {
  background: #1e40af;
  color: white;
}

.btn-print:hover {
  background: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
}

.btn-export {
  background: #059669;
  color: white;
}

.btn-export:hover {
  background: #047857;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.btn-primary {
  background: #1e3a8a;
  color: white;
}

.btn-primary:hover {
  background: #1e40af;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
  transform: translateY(-2px);
}

.btn-danger {
  background: #dc2626;
  color: white;
}

.btn-danger:hover {
  background: #b91c1c;
  transform: translateY(-2px);
}

.btn-close {
  background: #6b7280;
  color: white;
}

.btn-close:hover {
  background: #4b5563;
  transform: translateY(-2px);
}

/* Toolbar Button Container */
.actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

/* Modal Header Styles */


/* Modal Body Styles */
.modal-body {
  padding: 20px;
  max-height: 60vh;
  overflow-y: auto;
}

/* Modal Footer Styles */
.modal-footer {
  padding: 20px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  background: #f9fafb;
  border-radius: 0 0 8px 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .actions {
    flex-direction: column;
    width: 100%;
  }
  
  .btn-print, 
  .btn-export {
    width: 100%;
    justify-content: center;
  }
  
  .modal-content {
    max-width: 95%;
    margin: 20px;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .modal-footer button {
    width: 100%;
    justify-content: center;
  }
}

/* Focus states for accessibility */
.btn-print:focus,
.btn-export:focus,
.btn-primary:focus,
.btn-secondary:focus,
.btn-danger:focus,
.btn-close:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Active states */
.btn-print:active,
.btn-export:active,
.btn-primary:active,
.btn-secondary:active,
.btn-danger:active,
.btn-close:active {
  transform: translateY(0);
}
</style>

@endsection