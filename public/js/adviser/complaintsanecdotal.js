
    // Dropdown toggle
    const dropdowns = document.querySelectorAll('.dropdown-btn');
    dropdowns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        dropdowns.forEach(other => {
          if (other !== this) {
            other.nextElementSibling.classList.remove('show');
            other.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
          }
        });
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
          container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
      });
    });

    // Logout
    function logout(){ alert('Logging out...'); }

    // Search filter
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#anecdotalTable tbody');
    searchInput.addEventListener('keyup', function(){
      const filter = this.value.toLowerCase();
      Array.from(tableBody.rows).forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
      });
    });

    // Modal logic
    const modal = document.getElementById("anecdotalModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = document.querySelector(".close");
    const form = document.getElementById("anecdotalForm");
    let editingRow = null;

    openBtn.onclick = () => {
      modal.style.display="block";
      form.reset();
      editingRow=null;
      document.getElementById('modalTitle').textContent='Add Anecdotal Complaint';
    }
    closeBtn.onclick = () => modal.style.display="none";
    window.onclick = e => { if(e.target==modal) modal.style.display="none"; }

    document.querySelectorAll('.btn-edit').forEach((btn,i)=>{
      btn.onclick=()=> editRow(tableBody.rows[i]);
    });

    function editRow(row){
      editingRow=row;
      modal.style.display='block';
      document.getElementById('modalTitle').textContent='Edit Anecdotal Complaint';
      form.complainant.value=row.cells[1].textContent;
      form.respondent.value=row.cells[2].textContent;
      form.solution.value=row.cells[3].textContent;
      form.recommendation.value=row.cells[4].textContent;
      form.date.value=row.cells[5].textContent;
      form.time.value=row.cells[6].textContent;
    }

    form.addEventListener('submit', e=>{
      e.preventDefault();
      if(editingRow){
        editingRow.cells[1].textContent=form.complainant.value;
        editingRow.cells[2].textContent=form.respondent.value;
        editingRow.cells[3].textContent=form.solution.value;
        editingRow.cells[4].textContent=form.recommendation.value;
        editingRow.cells[5].textContent=form.date.value;
        editingRow.cells[6].textContent=form.time.value;
      } else {
        const row = tableBody.insertRow();
        row.insertCell(0).textContent = tableBody.rows.length+1;
        row.insertCell(1).textContent = form.complainant.value;
        row.insertCell(2).textContent = form.respondent.value;
        row.insertCell(3).textContent = form.solution.value;
        row.insertCell(4).textContent = form.recommendation.value;
        row.insertCell(5).textContent = form.date.value;
        row.insertCell(6).textContent = form.time.value;
        const actions = row.insertCell(7);
        actions.innerHTML = '<button class="btn-orange"><i class="fas fa-edit"></i> Edit</button> <button class="btn-red"><i class="fas fa-trash"></i> Delete</button>';
      }
      modal.style.display='none';
      form.reset();
    });
