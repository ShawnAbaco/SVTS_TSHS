
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

    function logout(){ alert('Logging out...'); }

    // --- Live Search ---
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#appointmentTable tbody');

    searchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    // --- Modal & Form Logic ---
    const modal = document.getElementById("appointmentModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModal = document.querySelector(".close");
    const form = document.getElementById('appointmentForm');
    let editingRow = null;

    openModalBtn.onclick = () => {
      modal.style.display = "block";
      form.reset();
      editingRow = null;
    };
    closeModal.onclick = () => modal.style.display = "none";
    window.onclick = e => { if(e.target == modal) modal.style.display="none"; }

    // --- Edit/Delete Buttons ---
    function createActionButtons(row) {
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

    document.querySelectorAll('.btn-edit').forEach((btn,i)=>{
      btn.onclick = () => editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow = row;
      modal.style.display = "block";
      form.complaint.value = row.cells[0].textContent; // you may adjust mapping
      form.date.value = row.cells[5].textContent;
      form.time.value = row.cells[6].textContent;
      form.status.value = row.cells[7].textContent;
    }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      if(editingRow){
        editingRow.cells[0].textContent = form.complaint.value;
        editingRow.cells[5].textContent = form.date.value;
        editingRow.cells[6].textContent = form.time.value;
        editingRow.cells[7].textContent = form.status.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = form.complaint.value;
        row.insertCell(1).textContent = 'Complainant'; // placeholder
        row.insertCell(2).textContent = 'Respondent'; // placeholder
        row.insertCell(3).textContent = 'Incident'; // placeholder
        row.insertCell(4).textContent = 'Offense'; // placeholder
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        row.insertCell(7).textContent = form.status.value;
        createActionButtons(row);
      }
      modal.style.display = "none";
      form.reset();
    });
