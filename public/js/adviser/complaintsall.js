
  // Sidebar dropdown toggle
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
    });
  });

  function logout() { alert('Logging out...'); }

  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#complaintsTable tbody');
    function filterTable(){
      const filter = searchInput.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    }
    searchInput.addEventListener('keyup', filterTable);

    const modal = document.getElementById("complaintModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = document.getElementById("closeModalBtn");
    const form = document.getElementById('complaintForm');
    let editingRow = null;

    openBtn.onclick = () => {
      modal.style.display = "block";
      document.getElementById('modalTitle').textContent = "Create Complaint";
      editingRow = null;
      form.reset();
    };
    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; }

    function createActionButtons(row){
      const actionsCell = row.insertCell(-1);
      const editBtn = document.createElement('button');
      editBtn.className = 'btn-orange';
      editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      editBtn.onclick = () => editRow(row);
      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'btn-red';
      deleteBtn.style.marginLeft = '5px';
      deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
      deleteBtn.onclick = () => row.remove();
      actionsCell.appendChild(editBtn);
      actionsCell.appendChild(deleteBtn);
    }

    document.querySelectorAll('.btn-edit').forEach((btn, i) => {
      btn.onclick = () => editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow = row;
      modal.style.display = "block";
      document.getElementById('modalTitle').textContent = "Edit Complaint";
      form.complainant.value = row.cells[0].textContent;
      form.respondent.value = row.cells[1].textContent;
      form.offense.value = row.cells[2].textContent;
      form.incident.value = row.cells[4].textContent;
      form.date.value = row.cells[5].textContent;
      form.time.value = row.cells[6].textContent;
    }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      if(editingRow){
        editingRow.cells[0].textContent = form.complainant.value;
        editingRow.cells[1].textContent = form.respondent.value;
        editingRow.cells[2].textContent = form.offense.value;
        editingRow.cells[4].textContent = form.incident.value;
        editingRow.cells[5].textContent = form.date.value;
        editingRow.cells[6].textContent = form.time.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = form.complainant.value;
        row.insertCell(1).textContent = form.respondent.value;
        row.insertCell(2).textContent = form.offense.value;
        row.insertCell(3).textContent = "N/A";
        row.insertCell(4).textContent = form.incident.value;
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        createActionButtons(row);
      }
      modal.style.display = "none";
      form.reset();
      editingRow = null;
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('tr').remove();
      });
    });
  });

