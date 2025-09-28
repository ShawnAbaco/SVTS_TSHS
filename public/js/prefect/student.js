

document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr:not(.no-data-row)'); // Ignore the "No records found" row

    let visibleCount = 0;

    rows.forEach(row => {
        const studentName = row.cells[2].innerText.toLowerCase(); // Student Name column
        const studentID = row.cells[1].innerText.toLowerCase();   // ID column
        if(studentName.includes(filter) || studentID.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Remove existing "No records found" row
    const noDataRow = tableBody.querySelector('.no-data-row');
    if(visibleCount === 0) {
        if(!noDataRow) {
            const newRow = document.createElement('tr');
            newRow.classList.add('no-data-row');
            newRow.innerHTML = `<td colspan="8" style="text-align:center; padding:15px;">⚠️ No records found</td>`;
            tableBody.appendChild(newRow);
        }
    } else {
        if(noDataRow) noDataRow.remove();
    }
});



  // Select all checkboxes
  document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
  });

  // Move to Trash
  document.getElementById('moveToTrashBtn').addEventListener('click', () => {
    const selected = [...document.querySelectorAll('.rowCheckbox:checked')];
    if (selected.length === 0) {
      alert('Please select at least one record.');
    } else {
      alert(selected.length + ' record(s) moved to Trash.');
      // Add AJAX call here to move to trash in backend
    }
  });

  // Row click -> Details Modal
// Row click -> Details Modal
document.querySelectorAll('#tableBody tr').forEach(row => {
  row.addEventListener('click', e => {
    // Ignore if checkbox or edit button is clicked
    if(e.target.type === 'checkbox' || e.target.classList.contains('editBtn')) return;

    const data = row.dataset.details.split('|');

    const detailsBody = `
      <p><strong>Student:</strong> ${data[0]}</p>
      <p><strong>Offense:</strong> ${data[1]}</p>
      <p><strong>Sanction:</strong> ${data[2]}</p>
      <p><strong>Date:</strong> ${data[3]}</p>
      <p><strong>Time:</strong> ${data[4]}</p>
    `;

    document.getElementById('detailsBody').innerHTML = detailsBody;
    document.getElementById('detailsModal').style.display = 'flex';
    document.getElementById('detailsModal').classList.add('show');
    btn.closest('.modal').classList.remove('show');


  });
});
// Close Details Modal
document.querySelectorAll('#detailsModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

// Set Schedule Button
document.getElementById('setScheduleBtn').addEventListener('click', () => {
  alert('Open schedule setup form or modal here.');
  // TODO: open your schedule modal or redirect to schedule setup
});

// Send SMS Button
document.getElementById('sendSmsBtn').addEventListener('click', () => {
  alert('Trigger SMS sending here.');
  // TODO: implement SMS sending via backend
});


  // Close modals
  document.querySelectorAll('.btn-close').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.modal').style.display = 'none';
    });
  });

  // Edit button
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const row = btn.closest('tr');
      const data = row.dataset.details.split('|');
      document.getElementById('editStudentName').value = data[0];
      document.getElementById('editOffense').value = data[1];
      document.getElementById('editSanction').value = data[2];
      document.getElementById('editDate').value = data[3];
      document.getElementById('editTime').value = data[4];
      document.getElementById('editModal').style.display = 'flex';
    });
  });

  // Open modals
  document.getElementById('createAnecBtn').addEventListener('click', () => {
    document.getElementById('anecModal').style.display = 'flex';
  });

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

  document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent row click event
    const dropdown = btn.parentElement;
    dropdown.classList.toggle('show');
  });
});

// Close dropdown if clicked outside
window.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Open archive modal
document.getElementById('archiveBtn').addEventListener('click', () => {
  document.getElementById('archiveModal').style.display = 'flex';
});

// Close modal
// Close archive modal
document.querySelectorAll('#archiveModal .btn-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

// Select all checkboxes
  // Get the select all checkbox and all individual checkboxes
  const selectAllArchived = document.getElementById('selectAllArchived');
  const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');

  // When the select all checkbox changes
  selectAllArchived.addEventListener('change', () => {
    const isChecked = selectAllArchived.checked;
    archivedCheckboxes.forEach(checkbox => {
      checkbox.checked = isChecked;
    });
  });

  // Optional: If any individual checkbox is unchecked, uncheck "Select All"
  archivedCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      if (!checkbox.checked) {
        selectAllArchived.checked = false;
      } else {
        // If all checkboxes are checked, check the "Select All" box
        const allChecked = Array.from(archivedCheckboxes).every(cb => cb.checked);
        selectAllArchived.checked = allChecked;
      }
    });
  });

// Select all archived checkboxes
const selectAllArchived = document.getElementById('selectAllArchived');
const archivedCheckboxes = document.querySelectorAll('.archivedCheckbox');

selectAllArchived.addEventListener('change', () => {
  archivedCheckboxes.forEach(cb => cb.checked = selectAllArchived.checked);
});

archivedCheckboxes.forEach(cb => {
  cb.addEventListener('change', () => {
    selectAllArchived.checked = Array.from(archivedCheckboxes).every(c => c.checked);
  });
});

// Archive search filter
const archiveSearch = document.querySelector('#archiveModal .search-input');
archiveSearch.addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#archiveTableBody tr').forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
  });
});

// Restore selected
document.getElementById('restoreArchivedBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to restore.');
  alert(`${selected.length} record(s) restored.`);
});

// Delete selected
document.getElementById('deleteArchivedBtn').addEventListener('click', () => {
  const selected = [...document.querySelectorAll('.archivedCheckbox:checked')];
  if(selected.length === 0) return alert('Please select at least one record to delete.');
  if(confirm('This will permanently delete the selected record(s). Are you sure?')) {
    alert(`${selected.length} record(s) deleted permanently.`);
  }
});


